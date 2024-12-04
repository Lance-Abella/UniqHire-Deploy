<?php

namespace App\Http\Controllers;

use App\Notifications\JobApplicationAcceptedNotification;
use Illuminate\Http\Request;
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


class PwdController extends Controller
{
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $latDifference = deg2rad($lat2 - $lat1);
        $lngDifference = deg2rad($lng2 - $lng1);

        $a = sin($latDifference / 2) * sin($latDifference / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDifference / 2) * sin($lngDifference / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Distance in km

        return round($distance, 2);
    }

    private function calculateProgramSimilarity($user, $program)
    {
        $similarityScore = 0;
        $weights = [
            'age' => 5,
            'educ' => 10,
            'skills' => 10,
            'rating' => 5
        ];

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

        // Sort distances by the 'distance' value in ascending order
        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $numberOfDistances = count($distances);

        $difference = $numberOfDistances > 1 ? 30 / ($numberOfDistances) : 0;

        // Loop through distances to find the matching program ID
        foreach ($distances as $index => $distanceItem) {
            if ($distanceItem['program_id'] == $program->id) {
                $similarityScore += max(0, 30 - ($difference * $index));
                break;
            }
        }




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
                $q->where('program_skill.id', $userSkill->skill_id);
            })->exists();

            if ($matchingProgram) {
                $similarityScore += 20;
            } else {
                $similarityScore += 10;
            }
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

        // Get the collection of approved programs to not include in displaying
        $approvedProgramIds = TrainingApplication::where('user_id', auth()->id())
            ->where('application_status', 'Approved')
            ->pluck('training_program_id')
            ->toArray();

        // Filtering the programs through searching program title
        if ($request->filled('search')) {
            $query->where("title", "LIKE", "%" . $request->search . "%");
        }

        // Filtering the programs based on education [multiple selection]
        if (isset($request->education) && ($request->education != null)) {
            $query->whereHas('education', function ($q) use ($request) {
                $q->whereIn('education_name', $request->education);
            });
        }

        $query->whereNotIn('id', $approvedProgramIds);

        // Filtering the programs based on the user's disability
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

        // Sorting the programs based on similarity score [ascending]
        usort($rankedPrograms, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 14;
        $currentItems = array_slice($rankedPrograms, ($currentPage - 1) * $perPage, $perPage);
        $paginatedItems = new LengthAwarePaginator($currentItems, count($rankedPrograms), $perPage);
        $paginatedItems->setPath($request->url());

        // $disabilityCounts = Disability::withCount('program')->get()->keyBy('id');
        $educationCounts = EducationLevel::withCount('program')->get()->keyBy('id');
        Log::info('Paginated Items:', $paginatedItems->toArray());
        log::info("nakaabot ari gyuddd");
        return view('pwd.listPrograms', compact('paginatedItems', 'educations', 'educationCounts'));
    }



    public function showDetails($id)
    {
        $program = TrainingProgram::with('agency.userInfo', 'disability', 'education', 'crowdfund')->findOrFail($id);
        $userId = auth()->user()->id;
        $application = TrainingApplication::where('user_id', $userId)->get();
        $reviews = PwdFeedback::where('program_id', $id)->with('pwd')->latest()->get();
        $status = Enrollee::where('pwd_id', $userId)->get();
        $disabilityId = auth()->user()->userInfo->disability_id;

        // Check if the user has completed the current program
        $isCompletedProgram = Enrollee::where('program_id', $program->id)
            ->where('pwd_id', $userId)
            ->where('completion_status', 'Completed')
            ->exists();

        // Get all completed programs for the user
        $completedPrograms = Enrollee::where('pwd_id', $userId)
            ->where('completion_status', 'Completed')
            ->pluck('program_id')
            ->toArray();

        $userHasReviewed = PwdFeedback::where('program_id', $id)
            ->where('pwd_id', $userId)
            ->exists();

        $userReview = PwdFeedback::where('program_id', $id)
            ->where('pwd_id', $userId)
            ->first();

        $rating = $userReview ? $userReview->rating : 0;

        // Collect all schedules (dates and times) from applied programs excluding completed programs
        $appliedSchedules = $application->map(function ($app) use ($completedPrograms) {
            if (!in_array($app->training_program_id, $completedPrograms)) {
                return [
                    'date' => $app->program->schedule,
                    'start_time' => $app->program->start_time,
                    'end_time' => $app->program->end_time,
                ];
            }
            return null; // Skip completed programs
        })->filter()->toArray();


        Log::info('Applied Dates:', $appliedSchedules);



        // Fetch all programs
        $allPrograms = TrainingProgram::whereHas('disability', function ($query) use ($disabilityId) {
            $query->where('disability_id', $disabilityId);
        })->get();

        // Filter programs with non-conflicting dates
        $nonConflictingPrograms = $allPrograms->filter(function ($program) use ($appliedSchedules) {
            // Retrieve the program's date, start time, and end time
            $programDate = $program->schedule;
            $programStartTime = $program->start_time;
            $programEndTime = $program->end_time;

            foreach ($appliedSchedules as $appliedSchedule) {
                // Check if the program date matches any applied schedule date
                if ($programDate === $appliedSchedule['date']) {
                    // Check for time overlap
                    if (
                        ($programStartTime < $appliedSchedule['end_time'] && $programStartTime >= $appliedSchedule['start_time']) ||
                        ($programEndTime > $appliedSchedule['start_time'] && $programEndTime <= $appliedSchedule['end_time']) ||
                        ($programStartTime <= $appliedSchedule['start_time'] && $programEndTime >= $appliedSchedule['end_time'])
                    ) {
                        return false; // Conflict found, exclude this program
                    }
                }
            }
            return true; // No conflicts, include this program
        })->pluck('id')->toArray();

        Log::info('NonConflictPrograms:', $nonConflictingPrograms);

        $enrolleeCount = Enrollee::where('program_id', $program->id)
            ->where('completion_status', 'Ongoing')
            ->count();

        $slots = $program->participants - $enrolleeCount;

        $enrollees = Enrollee::where('program_id', $program->id)->get();

        $sponsors = [];
        if ($program->crowdfund) {
            $crowdfundId = $program->crowdfund->id ?? null;
            if ($crowdfundId) {
                $sponsors = Transaction::where('crowdfund_id', $crowdfundId)
                    ->where('status', 'Completed') // Only include successful transactions
                    ->get(['name', 'amount']);
            }
            $raisedAmount = $program->crowdfund->raised_amount ?? 0; // Default to 0 if raised_amount is null
            $goal = $program->crowdfund->goal ?? 1; // Default to 1 to avoid division by zero
            $progress = ($goal > 0) ? round(($raisedAmount / $goal) * 100, 2) : 0; // Calculate progress percentage
            $program->crowdfund->progress = $progress;
        }
        return view('pwd.show', compact('program', 'reviews', 'application', 'nonConflictingPrograms', 'enrollees', 'status', 'isCompletedProgram', 'slots', 'userHasReviewed', 'rating', 'userReview', 'sponsors'));
    }



    public function showListingDetails($id)
    {
        $listing = JobListing::with('employer.userInfo', 'disability')->findOrFail($id);
        $userId = auth()->user()->id;
        $applications = JobApplication::where('user_id', $userId)->get();
        $status = JobApplication::where('job_id', $userId)->get();
        $disabilityId = auth()->user()->userInfo->disability_id;
        $hiredPWDs = Employee::where('job_id', $listing->id)->where('hiring_status', 'Accepted')->get();

        // $enrollees = JobApplication::where('job_id', $listing->id)->get();

        if ($listing->crowdfund) {
            $raisedAmount = $program->crowdfund->raised_amount ?? 0; // Default to 0 if raised_amount is null
            $goal = $program->crowdfund->goal ?? 1; // Default to 1 to avoid division by zero
            $progress = ($goal > 0) ? round(($raisedAmount / $goal) * 100, 2) : 0; // Calculate progress percentage
            $listing->crowdfund->progress = $progress;
        }
        return view('pwd.showListingDetails', compact('listing', 'status', 'applications', 'hiredPWDs'));
    }

    public function showCalendar(Request $request)
    {
        Log::info("showCalendar method called for user ID: " . auth()->user()->id);

        // Check if the request is an AJAX request
        if ($request->expectsJson()) {
            Log::info("AJAX request detected in showCalendar.");

            $userId = auth()->user()->id;

            // Fetch ongoing training programs for the authenticated user
            $trainingPrograms = TrainingProgram::whereIn('id', function ($query) use ($userId) {
                $query->select('program_id')
                    ->from('enrollees')
                    ->where('pwd_id', $userId)
                    ->where('completion_status', 'Ongoing');
            })->get(['id', 'title', 'schedule']);

            Log::info("Training Programs Retrieved:", $trainingPrograms->toArray());

            $events = [];

            // Loop through each training program and format the schedule dates
            foreach ($trainingPrograms as $program) {
                $scheduleDates = explode(',', $program->schedule);

                foreach ($scheduleDates as $date) {
                    // Convert MM/DD/YYYY to YYYY-MM-DD
                    $dateParts = explode('/', $date);
                    if (count($dateParts) == 3) {
                        $formattedDate = sprintf('%04d-%02d-%02d', $dateParts[2], $dateParts[0], $dateParts[1]);
                        $events[] = [
                            'id' => $program->id,
                            'title' => $program->title,
                            'start' => $formattedDate, // FullCalendar expects `start` for all-day events
                            'allDay' => true
                        ];
                    }
                }
            }

            Log::info("Events:", $events);

            return response()->json($events); // Return events as JSON for AJAX request
        }

        // If not an AJAX request, return the view
        return view('pwd.calendar');
    }

    public function showTrainings(Request $request)
    {
        $id = Auth::user()->id;
        $applications = TrainingApplication::where('user_id', $id)->where('application_status', 'Pending')->get();
        $trainings = Enrollee::where('pwd_id', $id)->get();
        $trainingsCount = $trainings->count();
        $ongoingCount = $trainings->where('completion_status', 'Ongoing')->count();
        $completedCount = $trainings->where('completion_status', 'Completed')->count();
        $approvedCount = $applications->where('application_status', 'Approved')->count();
        $pendingsCount = $applications->where('application_status', 'Pending')->count();

        if ($request->has('status') && $request->status != 'all') {
            $trainings = $trainings->where('completion_status', ucfirst($request->status));
        }

        return view('pwd.trainings', compact('applications', 'trainings', 'trainingsCount', 'ongoingCount', 'completedCount', 'approvedCount', 'pendingsCount'));
    }

    public function rateProgram(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:training_programs,id',
            'rating' => 'required|integer|between:1,5',
            'content' => 'nullable|string|max:1000',
        ]);

        try {
            $userId = $request->user()->id;

            // Check if the user has already reviewed this program
            $existingReview = PwdFeedback::where('program_id', $request->program_id)
                ->where('pwd_id', $userId)
                ->first();
            if ($existingReview) {
                // Update existing review
                $existingReview->update([
                    'rating' => $request->rating,
                    'content' => $request->content,
                ]);
            } else {
                // Create a new review
                PwdFeedback::create([
                    'program_id' => $request->program_id,
                    'pwd_id' => $userId,
                    'rating' => $request->rating,
                    'content' => $request->content,
                ]);
            }
            // return response()->json(['success' => true, 'message' => 'Feedback submitted successfully.']);
            return back()->with('success', 'Thank you for leaving us a review!');
        } catch (\Exception $e) {
            // return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
            return back()->with('error', 'An error occurred while submitting your feedback. Please try again later.');
        }
    }

    public function application(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'training_program_id' => 'required|exists:training_programs,id',
        ]);

        $validatedData['application_status'] = 'Pending';
        $trainingApplication = TrainingApplication::create($validatedData);

        $trainingProgram = TrainingProgram::findOrFail($validatedData['training_program_id']);

        $trainerUser = User::whereHas('userInfo', function ($query) use ($trainingProgram) {
            $query->where('user_id', $trainingProgram->agency_id);
        })->whereHas('role', function ($query) {
            $query->where('role_name', 'Training Agency');
        })->first();

        if ($trainerUser) {
            $trainerUser->notify(new PwdApplicationNotification($trainingProgram));
        } else {
            Log::error('No agency user found for training program', ['trainingProgram' => $trainingProgram->id]);
        }

        return back()->with('success', 'Application sent successfully!');
    }

    public function jobApplication(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'job_id' => 'required|exists:job_listings,id',
        ]);

        $validatedData['application_status'] = 'Pending';
        $jobApplication = JobApplication::create($validatedData);

        $job = JobListing::findOrFail($validatedData['job_id']);

        $employer = User::find($job->employer_id);

        if ($employer) {
            $employer->notify(new PwdJobApplicationNotification($job));
        }

        return back()->with('success', 'Application sent successfully!');
    }


    // HIRING SIDE

    private function calculateJobSimilarity($user, $currentJob)
    {
        $similarityScore = 0;
        $matchedExistingSkillsCount = 0;
        $matchedCertifiedSkillsCount = 0;

        // Filter jobs based on user disability
        $filteredJobs = JobListing::whereHas('disability', function ($q) use ($user) {
            $q->where('disability_id', $user->disability_id);
        })->get();


        // Retrieve user's skills and certifications
        // $userSkills = SkillUser::where('user_id', $user->id)->with('skill')->get();
        // $certifiedSkills = DB::table('certification_details')
        // ->join('program_skill', 'certification_details.program_id', '=', 'program_skill.training_program_id')
        // ->where('certification_details.user_id', $user->user_id)
        // ->pluck('program_skill.skill_id')  // Get the skill_id associated with the program
        // ->toArray();
        //     $programIds = DB::table('certification_details')
        // ->where('user_id', $user->user_id)
        // ->pluck('program_id')
        // ->toArray();

        // $certifiedSkills = DB::table('program_skill')
        // ->whereIn('training_program_id', $programIds)
        // ->pluck('skill_id')
        // ->toArray();

        // Calculate distance scoring (assuming calculateDistance is predefined)
        $distances = [];
        foreach ($filteredJobs as $job) {
            $distanceValue = $this->calculateDistance($user->latitude, $user->longitude, $job->latitude, $job->longitude);
            $distances[] = [
                'job_id' => $job->id,
                'distance' => $distanceValue
            ];
        }
        Log::info('Distances:', $distances);

        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        Log::info('Distances after sorting (ranked): ', $distances);

        $numberOfDistances = count($distances);
        $difference = $numberOfDistances > 1 ? 30 / ($numberOfDistances) : 0;

        Log::info('Calculated difference: ' . $difference);

        foreach ($distances as $index => $distanceItem) {
            if ($distanceItem['job_id'] == $currentJob->id) {
                $similarityScore += max(0, 30 - ($difference * $index));
                break;
            }
        }
        Log::info("Nakaabot diri");


        // Retrieve existing skills of the user
        $userSkills = SkillUser::where('user_id', $user->id)->with('skill')->get();
        $existingSkills = $userSkills->pluck('skill_id')->toArray();

        $currentJobSkillIds = $currentJob->skill->pluck('id')->toArray();

        // Retrieve certified skills of the user from CertificationDetails
        $certifiedSkills = [];
        $certificationDetails = CertificationDetail::where('user_id', $user->id)->get();

        foreach ($certificationDetails as $certification) {
            // Find the training program and load its skills
            $program = TrainingProgram::with('skill')->find($certification->program_id);

            if ($program) {
                // Iterate over the skills associated with the program
                foreach ($program->skill as $skill) {
                    $certifiedSkills[] = $skill->id; // Add skill IDs to the array
                }
            }
        }


        // Remove duplicates from certified skills but count duplicates for scoring
        // $certifiedSkillsCount = array_count_values($certifiedSkills);

        // $currentJob->load('skill');

        // Compare existing skills with current job skills
        foreach ($existingSkills as $existingSkillId) {
            if (in_array($existingSkillId, $currentJobSkillIds)) {
                $matchedExistingSkillsCount++;
            }
        }

        // Calculate the score based on the number of matched skills
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

        // Compare certified skills with current job skills
        foreach ($certifiedSkills as $certifiedSkillId) {
            if (in_array($certifiedSkillId, $currentJobSkillIds)) {
                $matchedCertifiedSkillsCount++;
            }
        }

        // Calculate the score based on the number of matched skills
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

        Log::info('Existing Skills:', $existingSkills);
        Log::info('Certified Skills:', $certifiedSkills);
        Log::info('Current Job Skills:', $currentJobSkillIds);

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

        // Get the collection of approved jobs to not include in displaying
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

        // if ($request->has('minSalary') && $request->has('maxSalary')) {
        //     $query->whereBetween('salary', [$request->minSalary, $request->maxSalary]);
        // } elseif ($request->has('minSalary')) {
        //     $query->where('salary', '>=', $request->minSalary);
        // } elseif ($request->has('maxSalary')) {
        //     $query->where('salary', '<=', $request->maxSalary);
        // }


        // if (isset($request->type) && ($request->setup != null)) {
        //     $query->whereHas('type', function ($q) use ($request) {
        //         $q->whereIn('name', $request->type);
        //     });
        // }
        $query->whereDate('end_date', '>=', $currentDate);
        $query->whereNotIn('id', $approvedJobIds);

        // Filtering the jobs based on the user's disability
        $query->whereHas('disability', function ($q) use ($user) {
            $q->where('disability_id', $user->disability_id);
        });

        $filteredJobs = $query->get();

        $rankedJobs = [];

        foreach ($filteredJobs as $job) {
            $similarity = $this->calculateJobSimilarity($user, $job);
            Log::info("Similarity score for program ID {$job->id}: " . $similarity);
            $rankedJobs[] = [
                'job' => $job,
                'similarity' => $similarity
            ];
        }

        // Sorting the programs based on similarity score [ascending]
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
        // $disabilityCounts = Disability::withCount('program')->get()->keyBy('id');
        Log::info('Paginated Items:', $paginatedItems->toArray());
        log::info("nakaabot ari gyuddd");

        return view('pwd.listJobs', compact('paginatedItems', 'setups', 'setupCounts', 'typeCounts', 'types', 'minSalary', 'maxSalary'));
    }

    public function showEvents()
    {

        return view('pwd.events');
    }
}
