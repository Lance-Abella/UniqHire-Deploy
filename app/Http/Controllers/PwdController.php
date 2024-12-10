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
use App\Models\Participants;
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


class PwdController extends Controller
{
    public function showDetails($id)
    {
        $program = TrainingProgram::with('agency.userInfo', 'disability', 'education', 'crowdfund')->findOrFail($id);
        $userId = auth()->user()->id;
        $application = TrainingApplication::where('user_id', $userId)->get();
        $reviews = PwdFeedback::where('program_id', $id)->with('pwd')->latest()->get();
        $status = Enrollee::where('pwd_id', $userId)->get();
        $disabilityId = auth()->user()->userInfo->disability_id;
        $isCompletedProgram = Enrollee::where('program_id', $program->id)
            ->where('pwd_id', $userId)
            ->where('completion_status', 'Completed')
            ->exists();
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

        $appliedSchedules = $application->map(function ($app) use ($completedPrograms) {
            if (!in_array($app->training_program_id, $completedPrograms)) {
                return [
                    'date' => $app->program->schedule,
                    'start_time' => $app->program->start_time,
                    'end_time' => $app->program->end_time,
                ];
            }
            return null;
        })->filter()->toArray();

        $allPrograms = TrainingProgram::whereHas('disability', function ($query) use ($disabilityId) {
            $query->where('disability_id', $disabilityId);
        })->get();

        $nonConflictingPrograms = $allPrograms->filter(function ($program) use ($appliedSchedules) {
            $programDate = $program->schedule;
            $programStartTime = $program->start_time;
            $programEndTime = $program->end_time;

            foreach ($appliedSchedules as $appliedSchedule) {
                if ($programDate === $appliedSchedule['date']) {
                    if (
                        ($programStartTime < $appliedSchedule['end_time'] && $programStartTime >= $appliedSchedule['start_time']) ||
                        ($programEndTime > $appliedSchedule['start_time'] && $programEndTime <= $appliedSchedule['end_time']) ||
                        ($programStartTime <= $appliedSchedule['start_time'] && $programEndTime >= $appliedSchedule['end_time'])
                    ) {
                        return false;
                    }
                }
            }
            return true;
        })->pluck('id')->toArray();
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
                    ->where('status', 'Completed')
                    ->get(['name', 'amount']);
            }
            $raisedAmount = $program->crowdfund->raised_amount ?? 0;
            $goal = $program->crowdfund->goal ?? 1;
            $progress = ($goal > 0) ? round(($raisedAmount / $goal) * 100, 2) : 0;
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

        if ($listing->crowdfund) {
            $raisedAmount = $program->crowdfund->raised_amount ?? 0;
            $goal = $program->crowdfund->goal ?? 1;
            $progress = ($goal > 0) ? round(($raisedAmount / $goal) * 100, 2) : 0;
            $listing->crowdfund->progress = $progress;
        }

        return view('pwd.showListingDetails', compact('listing', 'status', 'applications', 'hiredPWDs'));
    }

    public function showCalendar(Request $request)
    {
        if ($request->expectsJson()) {
            $userId = auth()->user()->id;
            $trainingPrograms = TrainingProgram::whereIn('id', function ($query) use ($userId) {
                $query->select('program_id')
                    ->from('enrollees')
                    ->where('pwd_id', $userId)
                    ->where('completion_status', 'Ongoing');
            })->get(['id', 'title', 'schedule', 'start_time', 'end_time']);

            $interviews = Employee::where('pwd_id', $userId)
                ->where('hiring_status', '!=', 'Accepted')
                ->get(['id', 'job_id', 'schedule', 'start_time', 'end_time']);

            $eventIds = Participants::where('user_id', $userId)->pluck('event_id');
            $pwdEvents = Events::whereIn('id', $eventIds)->where('schedule', '>=', now()->format('Y-m-d'))->get(['id', 'title', 'schedule', 'start_time', 'end_time']);
            $events = [];

            foreach ($pwdEvents as $event) {
                $dateParts = explode('-', $event->schedule);
                $startParts = explode(':', $event->start_time);
                $endParts = explode(':', $event->end_time);

                if (count($dateParts) == 3 && count($startParts) == 3 && count($endParts) == 3) {

                    try {
                        $formattedDate = sprintf('%04d-%02d-%02d', $dateParts[0], $dateParts[1], $dateParts[2]);

                        $startFormatted = sprintf(
                            '%s %02d:%02d:%02d',
                            $formattedDate,
                            $startParts[0],
                            $startParts[1],
                            $startParts[2]
                        );

                        $endFormatted = sprintf(
                            '%s %02d:%02d:%02d',
                            $formattedDate,
                            $endParts[0],
                            $endParts[1],
                            $endParts[2]
                        );

                        $events[] = [
                            'id' => $event->id,
                            'title' => '[Event] ' . $event->title,
                            'start' => $startFormatted,
                            'end' => $endFormatted,
                            'color' => '#FB773C',
                            'allDay' => false
                        ];
                    } catch (\Exception $e) {
                        Log::error("Error formatting date and time: " . $e->getMessage());
                    }
                }
            }

            foreach ($interviews as $interview) {
                $dateParts = explode('-', $interview->schedule);
                $startParts = explode(':', $interview->start_time);
                $endParts = explode(':', $interview->end_time);

                if (count($dateParts) == 3 && count($startParts) == 3 && count($endParts) == 3) {

                    try {
                        $formattedDate = sprintf('%04d-%02d-%02d', $dateParts[0], $dateParts[1], $dateParts[2]);

                        $startFormatted = sprintf(
                            '%s %02d:%02d:%02d',
                            $formattedDate,
                            $startParts[0],
                            $startParts[1],
                            $startParts[2]
                        );

                        $endFormatted = sprintf(
                            '%s %02d:%02d:%02d',
                            $formattedDate,
                            $endParts[0],
                            $endParts[1],
                            $endParts[2]
                        );

                        $events[] = [
                            'id' => $interview->id,
                            'title' => '[Interview] ' . $interview->job->employer->userInfo->name,
                            'start' => $startFormatted,
                            'end' => $endFormatted,
                            'color' => '#9B3922',
                            'allDay' => false
                        ];
                    } catch (\Exception $e) {
                        Log::error("Error formatting date and time: " . $e->getMessage());
                    }
                }
            }

            foreach ($trainingPrograms as $program) {
                $scheduleDates = explode(',', $program->schedule);
                $startTime = $program->start_time;
                $endTime = $program->end_time;

                foreach ($scheduleDates as $date) {
                    $dateParts = explode('/', $date);
                    $startParts = explode(':', $startTime);
                    $endParts = explode(':', $endTime);

                    if (count($dateParts) == 3 && count($startParts) == 3 && count($endParts) == 3) {

                        try {
                            $formattedDate = sprintf('%04d-%02d-%02d', $dateParts[2], $dateParts[0], $dateParts[1]);

                            $startFormatted = sprintf(
                                '%s %02d:%02d:%02d',
                                $formattedDate,
                                $startParts[0],
                                $startParts[1],
                                $startParts[2]
                            );

                            $endFormatted = sprintf(
                                '%s %02d:%02d:%02d',
                                $formattedDate,
                                $endParts[0],
                                $endParts[1],
                                $endParts[2]
                            );

                            $events[] = [
                                'id' => $program->id,
                                'title' => '[Training] ' . $program->title,
                                'start' => $startFormatted,
                                'end' => $endFormatted,
                                'color' => '#347928',
                                'allDay' => false
                            ];
                        } catch (\Exception $e) {
                            Log::error("Error formatting date and time: " . $e->getMessage());
                        }
                    }
                }
            }

            return response()->json($events);
        }

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

    public function showJobs(Request $request)
    {
        $id = Auth::user()->id;
        $applications = JobApplication::where('user_id', $id)->where('application_status', 'Pending')->get();
        $interviews = Employee::where('pwd_id', $id)->get();
        $interviewCount = $interviews->where('hiring_status', 'Pending')->count();
        $pendingsCount = $applications->where('application_status', 'Pending')->count();

        if ($request->has('status') && $request->status != 'all') {
            $interviews = $interviews->where('hiring_status', ucfirst($request->status));
        }

        return view('pwd.jobs', compact('applications', 'interviews', 'interviewCount', 'pendingsCount'));
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
            $existingReview = PwdFeedback::where('program_id', $request->program_id)
                ->where('pwd_id', $userId)
                ->first();
            if ($existingReview) {
                $existingReview->update([
                    'rating' => $request->rating,
                    'content' => $request->content,
                ]);
            } else {
                PwdFeedback::create([
                    'program_id' => $request->program_id,
                    'pwd_id' => $userId,
                    'rating' => $request->rating,
                    'content' => $request->content,
                ]);
            }
            return back()->with('success', 'Thank you for leaving us a review!');
        } catch (\Exception $e) {
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
        $applicant = User::findOrFail($validatedData['user_id']);

        // Get the owner (agency/employer) of the training program
        $trainerUser = User::whereHas('userInfo', function ($query) use ($trainingProgram) {
            $query->where('user_id', $trainingProgram->agency_id);
        })->whereHas('role', function ($query) {
            $query->whereIn('role_name', ['Training Agency', 'Employer']); // Check for both roles
        })->first();

        if ($trainerUser) {
            $trainerUser->notify(new PwdApplicationNotification($trainingProgram, $applicant));
        } else {
            Log::error('No agency/employer user found for training program', ['trainingProgram' => $trainingProgram->id]);
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

    public function eventApplication(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
        ]);

        Participants::create([
            'user_id' => $validatedData['user_id'],
            'event_id' => $validatedData['event_id'],
        ]);

        return redirect()->route('events')->with('success', 'You successfully registered to this event.');
    }

    public function showEvents()
    {
        $user = auth()->user()->userInfo;
        $isCertified = DB::table("certification_details")
            ->where('user_id', $user->user_id)
            ->exists();
        $events = Events::whereNotIn('id', function ($query) use ($user) {
            $query->select('event_id')
                ->from('participants')
                ->where('user_id', $user->user_id);
        })->latest()->paginate(10);

        $participantCounts = [];

        foreach ($events as $event) {
            $participantCounts[$event->id] = $event->users()->count();
        }

        return view('pwd.events', compact('events', 'participantCounts', 'isCertified'));
    }
}
