<?php

namespace App\Http\Controllers;

use App\Models\Disability;
use App\Models\JobListing;
use App\Models\JobApplication;
use App\Models\EducationLevel;
use App\Models\PwdFeedback;
use App\Models\Enrollee;
use App\Models\Events;
use App\Models\TrainingProgram;
use App\Models\Employee;
use App\Models\Skill;
use App\Models\WorkSetup;
use App\Models\WorkType;
use App\Models\User;
use App\Notifications\JobApplicationAcceptedNotification;
use App\Notifications\JobHiredNotification;
use App\Notifications\NewJobListingNotification;
use App\Notifications\SetEventsNotification;
use App\Notifications\SetScheduleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EmployerController extends Controller
{
    public function manageJobs()
    {
        $userId = auth()->id();
        $jobs = JobListing::where('employer_id', $userId)
            ->latest()
            ->paginate(15);;

        return view('employer.manageJob', compact('jobs'));
    }

    public function showAddForm()
    {
        $disabilities = Disability::all();
        $levels = EducationLevel::all();
        $skills = Skill::all();
        $setups = WorkSetup::all();
        $types = WorkType::all();
        return view('employer.addJob', compact('disabilities', 'levels', 'skills', 'setups', 'types'));
    }

    private function convertToNumber($number)
    {
        return (float) str_replace(',', '', $number);
    }

    public function addJob(Request $request)
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'description' => 'required|string',
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
            'loc' => 'nullable|string|max:255',
            'end_date' => 'required|date',
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id',
            'disabilities' => 'required|array',
            'disabilities.*' => 'exists:disabilities,id',
            'setup' => 'exists:work_setups,id',
            'type' => 'exists:work_types,id'
        ]);

        $salary = $this->convertToNumber($request->salary);

        $jobListing = JobListing::create([
            'employer_id' => auth()->id(),
            'position' => $request->position,
            'description' => $request->description,
            'salary' => $salary,
            'latitude' => $request->lat,
            'longitude' => $request->long,
            'location' => $request->loc,
            'end_date' => $request->end_date,
            'worksetup_id' => $request->setup,
            'worktype_id' => $request->type
        ]);

        $jobListing->skill()->attach($request->skills);
        $jobListing->disability()->attach($request->disabilities);
        $pwdUsers = User::whereHas('role', function ($query) {
            $query->where('role_name', 'PWD');
        })->get();

        foreach ($pwdUsers as $user) {
            $user->notify(new NewJobListingNotification($jobListing));
        }

        return redirect()->route('manage-jobs')->with('success', 'Job listing created successfully!');
    }

    public function showJobDetails($id)
    {
        $listing = JobListing::findOrFail($id);
        $userId = auth()->id();
        $applications = JobApplication::where('job_id', $listing->id)->get();
        $requests = JobApplication::where('job_id', $listing->id)->where('application_status', 'Pending')->get();
        $interviewees = Employee::where('job_id', $listing->id)->where('hiring_status', 'Pending')->get();
        $hiredPWDs = Employee::where('job_id', $listing->id)->where('hiring_status', 'Accepted')->get();
        $totalHired = Employee::where('job_id', $listing->id)->where('hiring_status', 'Accepted')->count();

        $pendingsCount = $applications->where('application_status', 'Pending')->count();
        $intervieweeCount = Employee::where('job_id', $listing->id)->where('hiring_status', 'Pending')->count();

        return view('employer.showJob', compact('listing', 'applications', 'pendingsCount', 'intervieweeCount', 'totalHired', 'requests', 'interviewees', 'hiredPWDs'));
    }

    public function accept(Request $request)
    {
        $validatedData = $request->validate([
            'pwd_id' => 'required|exists:users,id',
            'job_id' => 'required|exists:job_listings,id',
            'job_application_id' => 'required|exists:job_applications,id',
        ]);

        $pwdId = $validatedData['pwd_id'];
        $jobId = $validatedData['job_id'];
        $applicationId = $validatedData['job_application_id'];
        $hiringStatus = 'Pending';
        $application = JobApplication::findOrFail($applicationId);
        $application->application_status = 'Approved';
        $application->save();
        $pwdUser = $application->user;
        $jobListing = $application->job;

        Employee::create([
            'pwd_id' => $pwdId,
            'job_id' => $jobId,
            'job_application_id' => $applicationId,
            'hiring_status' => $hiringStatus,
        ]);

        return back()->with('success', 'Application proceeds to interview process.');
    }

    public function deleteJob($id)
    {
        $listing = JobListing::findOrFail($id);

        if ($listing && $listing->employer_id == auth()->id()) {
            DB::table('notifications')
                ->where('data', 'like', '%"employer_id":' . $id . '%')
                ->delete();

            $listing->delete();
            return redirect()->route('manage-jobs')->with('success', 'Job listing deleted successfully.');
        } else {
            return redirect()->route('manage-jobs')->with('error', 'Failed to delete Job listing.');
        }
    }

    public function editJob($id)
    {
        $listing = JobListing::find($id);
        $setups = WorkSetup::all();
        $types = WorkType::all();

        if (!$listing || $listing->employer_id != auth()->id()) {
            return redirect()->route('manage-jobs');
        }

        $provinceResponse = file_get_contents('https://psgc.cloud/api/provinces');
        $provinces = json_decode($provinceResponse, true);
        $disabilities = Disability::all();
        $levels = EducationLevel::all();
        $skills = Skill::all();

        return view('employer.editJob', compact('listing', 'provinces', 'disabilities', 'levels', 'skills', 'setups', 'types'));
    }

    public function updateJob(Request $request, $id)
    {
        $job = JobListing::find($id);

        if ($job && $job->employer_id == auth()->id()) {
            $request->validate([
                'position' => 'required|string|max:255',
                'description' => 'required|string',
                'lat' => 'required|numeric|between:-90,90',
                'long' => 'required|numeric|between:-180,180',
                'loc' => 'nullable|string|max:255',
                'end_date' => 'required|date',
                'skills' => 'required|array',
                'skills.*' => 'exists:skills,id',
                'disabilities' => 'required|array',
                'disabilities.*' => 'exists:disabilities,id',
                'setup' => 'exists:work_setups,id',
                'type' => 'exists:work_types,id'
            ]);

            $salary = $this->convertToNumber($request->salary);

            $job->update([
                'position' => $request->position,
                'description' => $request->description,
                'salary' => $salary,
                'latitude' => $request->lat,
                'longitude' => $request->long,
                'location' => $request->loc,
                'end_date' => $request->end_date,
                'worksetup_id' => $request->setup,
                'worktype_id' => $request->type
            ]);

            $job->skill()->sync($request->skills);
            $job->disability()->sync($request->disabilities);

            return redirect()->route('jobs-show', $id)->with('success', 'Job details have been updated successfully!');
        } else {
            return back()->with('error', 'Failed to update job details. Review form.');
        }
    }

    public function showCalendar(Request $request)
    {

        $user = auth()->user()->userInfo->user_id;

        if ($request->expectsJson()) {

            $jobListings = JobListing::where('employer_id', $user)
                ->get(['id', 'employer_id', 'position', 'end_date']);
            $trainingPrograms = TrainingProgram::where('agency_id', $user)
                ->get(['agency_id', 'title', 'schedule', 'start_time', 'end_time']);
            $employerEvents = Events::where('employer_id', $user)->where('schedule', '>=', now()->format('Y-m-d'))->get(['id', 'title', 'schedule', 'start_time', 'end_time']);
            $job_id = JobListing::where('employer_id', $user)->get();
            $interviews = Employee::whereIn('job_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('job_listings')
                    ->where('employer_id', $user);
            })->where('hiring_status', '!=', 'Accepted')
                ->get(['id', 'job_id', 'schedule', 'pwd_id', 'start_time', 'end_time']);

            $events = [];

            foreach ($employerEvents as $event) {

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
                            'title' => '[Interview] ' . $interview->pwd->userInfo->name,
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

            foreach ($jobListings as $job) {
                $dateParts = explode('-', $job->end_date);

                if (count($dateParts) == 3) {
                    $formattedDate = sprintf('%04d-%02d-%02d', $dateParts[0], $dateParts[1], $dateParts[2]);
                    $events[] = [
                        'id' => $job->employer_id,
                        'title' => '[Job Listing] ' . $job->position,
                        'start' => $formattedDate,
                        'color' => '#03346E',
                        'allDay' => true
                    ];
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

        return view('employer.calendar');
    }

    public function markHired(Request $request)
    {
        $validatedData = $request->validate([
            'employeeId' => 'required|exists:employees,id',
            'userId' => 'required|exists:users,id',
            'jobId' => 'required|exists:job_listings,id'
        ]);

        $jobId = $validatedData['jobId'];
        $userId = $validatedData['userId'];
        $employeeId = $validatedData['employeeId'];
        $hiringStatus = 'Accepted';
        $employee = Employee::findOrFail($employeeId);
        $employee->update(['hiring_status' => $hiringStatus]);

        $pwdUser = $employee->pwd;
        $pwdUser->notify(new JobHiredNotification($employee));

        return back()->with('success', 'Employee is hired.');
    }

    public function setScheduleForm($id)
    {

        $employee = Employee::findOrFail($id);

        return view('employer.setSchedule', compact('employee'));
    }

    public function setSchedule(Request $request, $id)
    {

        $employee = Employee::findOrFail($id);
        $validatedData = $request->validate([
            'schedule' => 'required|date',
            'start_time' => 'required|date_format:H:i|before:end_time',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule = $validatedData['schedule'];
        $start_time = $validatedData['start_time'];
        $end_time = $validatedData['end_time'];

        $employerId = $employee->job->employer_id;

        $conflictingSchedule = Employee::whereHas('job', function ($query) use ($employerId) {
            $query->where('employer_id', $employerId);
        })->where('schedule', $schedule)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time])
                    ->orWhereRaw('? BETWEEN start_time AND end_time', [$start_time])
                    ->orWhereRaw('? BETWEEN start_time AND end_time', [$end_time]);
            })
            ->exists();

        if ($conflictingSchedule) {
            return redirect()->back()->with(['error' => 'The schedule conflicts with another interview set by the employer.']);
        }

        $employee->update([
            'schedule' => $schedule,
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);

        return redirect()->route('jobs-show', $employee->job_id)->with('success', 'Interview schedule has been set.');
    }

    public function showEvents()
    {
        $events = Events::with('users')->latest()->paginate(10);
        $participantCounts = [];

        foreach ($events as $event) {
            $participantCounts[$event->id] = $event->users()->count();
        }

        return view('employer.events', compact('events', 'participantCounts'));
    }

    public function postEvent(Request $request)
    {
        $employer_id = Auth::user()->id;
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'schedule' => 'required|date',
            'start_time' => 'required|date_format:H:i|before:end_time',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $event = Events::create([
            'title' => $request->title,
            'description' => $request->description,
            'schedule' => $request->schedule,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'employer_id' => $employer_id
        ]);

        $employees = Employee::all();

        if ($employees->isEmpty()) {
            return redirect()->route('show-post-events')->with('error', 'No users found to notify.');
        }

        foreach ($employees as $employee) {
            $employee->pwd->notify(new SetEventsNotification($event));
        }

        return redirect()->route('show-post-events');
    }

    public function deleteEvent($id)
    {
        $event = Events::findOrFail($id);

        if ($event->employer_id == Auth::id()) {
            $event->delete();
            return redirect()->back()->with('success', 'Event deleted successfully.');
        }

        return redirect()->back()->with('error', 'You are not authorized to delete this event.');
    }

    public function deny(JobApplication $jobid)
    {
        $jobid->delete();

        return back()->with('success', 'Application denied');
    }
}
