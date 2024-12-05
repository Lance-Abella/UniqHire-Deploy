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

            $interviews = Employee::where('pwd_id', $userId)
                ->where('hiring_status', '!=', 'Accepted')
                ->get(['id', 'job_id', 'schedule']);

            $eventIds = Participants::where('user_id', $userId)->pluck('event_id');
            $pwdEvents = Events::whereIn('id', $eventIds)->get(['id', 'title', 'schedule', 'start_time']);
            Log::info("Training Programs Retrieved:", $trainingPrograms->toArray());

            $events = [];

            foreach ($pwdEvents as $event) {
                // $scheduleDates = explode(',', $job->end_date);

                // Convert MM/DD/YYYY to YYYY-MM-DD
                $dateParts = explode('-', $event->schedule);
                if (count($dateParts) == 3) {
                    Log::info("kaabot sa if");
                    $formattedDate = sprintf('%04d-%02d-%02d', $dateParts[0], $dateParts[1], $dateParts[2]);
                    Log::info("Formatted Date:", ['formattedDate' => $formattedDate]);
                    $events[] = [
                        'id' => $event->id,
                        'title' => '[Event] ' . $event->title,
                        'start' => $formattedDate,
                        'color' => '#FB773C', // FullCalendar expects start for all-day events
                        'allDay' => true
                    ];
                }
            }

            foreach ($interviews as $interview) {
                // $scheduleDates = explode(',', $job->end_date);

                // Convert MM/DD/YYYY to YYYY-MM-DD
                $dateParts = explode('-', $interview->schedule);
                if (count($dateParts) == 3) {
                    Log::info("kaabot sa if");
                    $formattedDate = sprintf('%04d-%02d-%02d', $dateParts[0], $dateParts[1], $dateParts[2]);
                    Log::info("Formatted Date:", ['formattedDate' => $formattedDate]);
                    $events[] = [
                        'id' => $interview->id,
                        'title' => '[Interview]: ' . $interview->pwd->userInfo->name,
                        'start' => $formattedDate,
                        'color' => '#9B3922', // FullCalendar expects start for all-day events
                        'allDay' => true
                    ];
                }
            }

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
                            'title' => '[Training] ' . $program->title,
                            'start' => $formattedDate,
                            'color' => '#347928', // FullCalendar expects `start` for all-day events
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

    public function showJobs(Request $request)
    {
        $id = Auth::user()->id;
        $applications = JobApplication::where('user_id', $id)->where('application_status', 'Pending')->get();
        $interviews = Employee::where('pwd_id', $id)->get();

        $interviewCount = $interviews->where('hiring_status', 'Pending')->count();
        $pendingsCount = $applications->where('application_status', 'Pending')->count();

        // $trainingsCount = $jobs->count();
        // $ongoingCount = $jobs->where('completion_status', 'Ongoing')->count();
        // $completedCount = $trainings->where('completion_status', 'Completed')->count();
        // $approvedCount = $applications->where('application_status', 'Approved')->count();
        // $pendingsCount = $applications->where('application_status', 'Pending')->count();

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


    // HIRING SIDE






    public function showEvents()
    {
        $user = auth()->user()->userInfo;

        // Check if the user is certified
        $isCertified = DB::table("certification_details")
            ->where('user_id', $user->user_id)
            ->exists();

        if (!$isCertified) {
            return view('pwd.events', ['events' => [], 'message' => 'You need to complete a training program to view jobs.']);
        }

        // Retrieve events where the user is not a participant
        $events = Events::whereNotIn('id', function ($query) use ($user) {
            $query->select('event_id')
                ->from('participants')
                ->where('user_id', $user->user_id);
        })->latest()->paginate(10);

        return view('pwd.events', compact('events'));
    }
}
