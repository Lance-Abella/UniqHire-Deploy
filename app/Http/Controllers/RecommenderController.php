<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\JobApplicationAcceptedNotification;
use App\Models\UserInfo;
use App\Models\TrainingProgram;
use App\Models\Disability;
use App\Models\EducationLevel;
use App\Models\TrainingApplication;
use App\Models\User;
use App\Models\SkillUser;
use App\Models\JobListing;
use App\Models\JobApplication;
use App\Models\CertificationDetail;
use App\Http\Requests\StoreUserInfoRequest;
use App\Http\Requests\UpdateUserInfoRequest;
use App\Models\Enrollee;
use App\Models\Employee;
use App\Models\Events;
use App\Models\PwdFeedback;
use App\Models\WorkSetup;
use App\Models\WorkType;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Notifications\PwdApplicationNotification;
use App\Notifications\PwdJobApplicationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommenderController extends Controller
{
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;

        $latDifference = deg2rad($lat2 - $lat1);
        $lngDifference = deg2rad($lng2 - $lng1);

        $a = sin($latDifference / 2) * sin($latDifference / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDifference / 2) * sin($lngDifference / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    private function calculateProgramSimilarity($user, $program)
    {
        $similarityScore = 0;
        $matchedSkillsCount = 0;
        $userSkills = SkillUser::where('user_id', $user->id)->get();
        $totalRating = PwdFeedback::where('program_id', $program->id)->sum('rating');
        $ratingCount = PwdFeedback::where('program_id', $program->id)->count();
        $filteredPrograms = TrainingProgram::whereHas('disability', function ($q) use ($user) {
            $q->where('disability_id', $user->disability_id);
        })->get();
        $averageRating = $ratingCount > 0 ? $totalRating / $ratingCount : 0;
        $distances = [];

        foreach ($filteredPrograms as $prog) {
            $distanceValue = $this->calculateDistance($user->latitude, $user->longitude, $prog->latitude, $prog->longitude);
            $distances[] = [
                'program_id' => $prog->id,
                'distance' => $distanceValue
            ];
        }


        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        Log::info("Rank ni sa distances:", $distances);
        $numberOfDistances = count($distances);
        $difference = $numberOfDistances > 1 ? 30 / ($numberOfDistances) : 0;

        foreach ($distances as $index => $distanceItem) {
            if ($distanceItem['program_id'] == $program->id) {
                $similarityScore += max(0, 30 - ($difference * $index));
                break;
            }
        }
        Log::info($similarityScore);
        if ($user->age >= $program->start_age && $user->age <= $program->end_age) {
            $similarityScore += 20;
        } else {
            $similarityScore += 10;
        }

        if ($user->educational_id >= $program->education_id) {
            $similarityScore += 25;
        } else {
            $similarityScore += 10;
        }

        foreach ($userSkills as $userSkill) {
            $matchingProgram = $program->whereHas('skill', function ($q) use ($userSkill) {
                $q->where('program_skill.skill_id', $userSkill->skill_id);
            })->exists();

            if ($matchingProgram) {
                $matchedSkillsCount++;
            }
        }

        if ($matchedSkillsCount === 1) {
            $similarityScore += 5;
        } elseif ($matchedSkillsCount === 2) {
            $similarityScore += 10;
        } elseif ($matchedSkillsCount === 3) {
            $similarityScore += 15;
        } elseif ($matchedSkillsCount >= 4) {
            $similarityScore += 20;
        }

        if ($averageRating) {
            $similarityScore += $averageRating;
        }

        return $similarityScore;
    }

    public function showPrograms(Request $request)
    {
        $user = auth()->user()->userInfo;
        $educations = EducationLevel::all();
        $query = TrainingProgram::query();
        $userDisabilityId = $user->disability_id;

        $approvedProgramIds = TrainingApplication::where('user_id', auth()->id())
            ->where('application_status', 'Approved')
            ->pluck('training_program_id')
            ->toArray();

        if ($request->filled('search')) {
            $query->where("title", "LIKE", "%" . $request->search . "%");
        }

        if (isset($request->education) && ($request->education != null)) {
            $query->whereHas('education', function ($q) use ($request) {
                $q->whereIn('education_name', $request->education);
            });
        }

        $query->whereNotIn('id', $approvedProgramIds);
        $query->where('status', 'Ongoing');

        $query->whereHas('disability', function ($q) use ($user) {
            $q->where('disability_id', $user->disability_id);
        });

        $filteredPrograms = $query->get();

        $rankedPrograms = [];

        foreach ($filteredPrograms as $program) {
            $similarity = $this->calculateProgramSimilarity($user, $program);
            Log::info("Similarity score for program ID {$program->id}: " . $similarity);
            $rankedPrograms[] = [
                'program' => $program,
                'similarity' => $similarity
            ];
        }

        usort($rankedPrograms, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 14;
        $currentItems = array_slice($rankedPrograms, ($currentPage - 1) * $perPage, $perPage);
        $paginatedItems = new LengthAwarePaginator($currentItems, count($rankedPrograms), $perPage);
        $paginatedItems->setPath($request->url());

        $educationCounts = EducationLevel::withCount(['program' => function ($query) use ($userDisabilityId) {
            $query->whereHas('disability', function ($q) use ($userDisabilityId) {
                $q->where('disabilities.id', $userDisabilityId);
            });
        }])->get()->keyBy('id');
        return view('pwd.listPrograms', compact('paginatedItems', 'educations', 'educationCounts'));
    }

    private function calculateJobSimilarity($user, $currentJob)
    {
        $similarityScore = 0;
        $matchedExistingSkillsCount = 0;
        $matchedCertifiedSkillsCount = 0;
        $filteredJobs = JobListing::whereHas('disability', function ($q) use ($user) {
            $q->where('disability_id', $user->disability_id);
        })->get();

        $distances = [];
        foreach ($filteredJobs as $job) {
            $distanceValue = $this->calculateDistance($user->latitude, $user->longitude, $job->latitude, $job->longitude);
            $distances[] = [
                'job_id' => $job->id,
                'distance' => $distanceValue
            ];
        }

        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $numberOfDistances = count($distances);
        $difference = $numberOfDistances > 1 ? 30 / ($numberOfDistances) : 0;

        foreach ($distances as $index => $distanceItem) {
            if ($distanceItem['job_id'] == $currentJob->id) {
                $similarityScore += max(0, 30 - ($difference * $index));
                break;
            }
        }

        $userSkills = SkillUser::where('user_id', $user->id)->with('skill')->get();
        $existingSkills = $userSkills->pluck('skill_id')->toArray();
        $currentJobSkillIds = $currentJob->skill->pluck('id')->toArray();
        $certifiedSkills = [];
        $certificationDetails = CertificationDetail::where('user_id', $user->id)->get();

        foreach ($certificationDetails as $certification) {
            $program = TrainingProgram::with('skill')->find($certification->program_id);

            if ($program) {
                foreach ($program->skill as $skill) {
                    $certifiedSkills[] = $skill->id;
                }
            }
        }

        foreach ($existingSkills as $existingSkillId) {
            if (in_array($existingSkillId, $currentJobSkillIds)) {
                $matchedExistingSkillsCount++;
            }
        }

        if ($matchedExistingSkillsCount == 1) {
            $similarityScore += 2;
        } else if ($matchedExistingSkillsCount == 2) {
            $similarityScore += 4;
        } else if ($matchedExistingSkillsCount == 3) {
            $similarityScore += 6;
        } else if ($matchedExistingSkillsCount == 4) {
            $similarityScore += 8;
        } else if ($matchedExistingSkillsCount >= 5) {
            $similarityScore += 10;
        }

        foreach ($certifiedSkills as $certifiedSkillId) {
            if (in_array($certifiedSkillId, $currentJobSkillIds)) {
                $matchedCertifiedSkillsCount++;
            }
        }

        if ($matchedCertifiedSkillsCount == 1) {
            $similarityScore += 10;
        } elseif ($matchedCertifiedSkillsCount == 2) {
            $similarityScore += 20;
        } elseif ($matchedCertifiedSkillsCount == 3) {
            $similarityScore += 30;
        } elseif ($matchedCertifiedSkillsCount == 4) {
            $similarityScore += 40;
        } elseif ($matchedCertifiedSkillsCount == 5) {
            $similarityScore += 50;
        } elseif ($matchedCertifiedSkillsCount >= 6) {
            $similarityScore += 60;
        }

        return $similarityScore;
    }

    public function showJobs(Request $request)
    {
        $user = auth()->user()->userInfo;
        $query = JobListing::query();
        $currentDate = now()->toDateString();
        $setups = WorkSetup::all();
        $types = WorkType::all();
        $isCertified = DB::table("certification_details")->where('user_id', $user->user_id)
            ->exists();

        if (!$isCertified) {
            $paginatedItems = new LengthAwarePaginator([], 0, 14);
            $setupCounts = WorkSetup::withCount('job')->get()->keyBy('id');
            $typeCounts = WorkType::withCount('job')->get()->keyBy('id');

            return view('pwd.listJobs', compact('paginatedItems', 'setups', 'setupCounts', 'typeCounts', 'types'))
                ->with('message', 'You need to complete a training program to view jobs.');
        }

        $approvedJobIds = JobApplication::where('user_id', auth()->id())
            ->where('application_status', 'Approved')
            ->pluck('job_id')
            ->toArray();

        if ($request->has('setup') && is_array($request->setup)) {
            $query->whereHas('setup', function ($q) use ($request) {
                $q->whereIn('name', $request->setup);
            });
        }

        if ($request->has('type') && is_array($request->type)) {
            $query->whereHas('type', function ($q) use ($request) {
                $q->whereIn('name', $request->type);
            });
        }

        $minSalary = $request->minSalary;
        $maxSalary = $request->maxSalary;
        $query->whereDate('end_date', '>=', $currentDate);
        $query->whereNotIn('id', $approvedJobIds);
        $query->where('status', 'Ongoing');
        $query->whereHas('disability', function ($q) use ($user) {
            $q->where('disability_id', $user->disability_id);
        });

        $filteredJobs = $query->get();

        $rankedJobs = [];

        foreach ($filteredJobs as $job) {
            $similarity = $this->calculateJobSimilarity($user, $job);

            $rankedJobs[] = [
                'job' => $job,
                'similarity' => $similarity
            ];
        }

        usort($rankedJobs, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 14;
        $currentItems = array_slice($rankedJobs, ($currentPage - 1) * $perPage, $perPage);
        $paginatedItems = new LengthAwarePaginator($currentItems, count($rankedJobs), $perPage);
        $paginatedItems->setPath($request->url());
        $setupCounts = WorkSetup::withCount('job')->get()->keyBy('id');
        $typeCounts = WorkType::withCount('job')->get()->keyBy('id');

        return view('pwd.listJobs', compact('paginatedItems', 'setups', 'setupCounts', 'typeCounts', 'types', 'minSalary', 'maxSalary'));
    }
}
