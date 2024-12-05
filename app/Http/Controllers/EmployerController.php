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

        //NOTIFY PWD USERS!!! JOB
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
        // $reviews = PwdFeedback::where('program_id', $id)->with('pwd')->latest()->get();
        $applications = JobApplication::where('job_id', $listing->id)->get();
        $requests = JobApplication::where('job_id', $listing->id)->where('application_status', 'Pending')->get();
        $employees = Employee::where('job_id', $listing->id)->get();
        $hiredPWDs = Employee::where('job_id', $listing->id)->where('hiring_status', 'Accepted')->get();
        $totalHired = Employee::where('job_id', $listing->id)->where('hiring_status', 'Accepted')->count();

        $pendingsCount = $applications->where('application_status', 'Pending')->count();
        $intervieweeCount = $applications->where('application_status', 'Approved')->count();

        return view('employer.showJob', compact('listing', 'applications', 'pendingsCount', 'intervieweeCount', 'totalHired', 'requests', 'employees', 'hiredPWDs'));
    }

    public function accept(Request $request)
    {
        Log::info("Reached accept method");

        // Validate the incoming request
        $validatedData = $request->validate([
            'pwd_id' => 'required|exists:users,id',
            'job_id' => 'required|exists:training_applications,id',
            'job_application_id' => 'required|exists:job_applications,id',
        ]);

        $pwdId = $validatedData['pwd_id'];
        $jobId = $validatedData['job_id'];
        $applicationId = $validatedData['job_application_id'];
        $hiringStatus = 'Pending';

        // Find the application by job_id
        $application = JobApplication::findOrFail($jobId);
        $application->application_status = 'Pending';
        $application->save();

        $pwdUser = $application->user;
        $jobListing = $application->job;

        $pwdUser->notify(new JobApplicationAcceptedNotification($jobListing));

        // Create Enrollee record
        Employee::create([
            'pwd_id' => $pwdId,
            'job_id' => $jobId,
            'job_application_id' => $applicationId,
            'hiring_status' => $hiringStatus,
        ]);

        $application->update(['application_status' => 'Approved']);
        // return response()->json(['success' => true, 'message' => 'Application submitted successfully.']);
        return back()->with('success', 'Application proceeds to interview process.');
    }

    public function deleteJob($id)
    {
        Log::info("nakasud ari");
        $listing = JobListing::findOrFail($id);



        if ($listing && $listing->employer_id == auth()->id()) {
            // Find and delete related notifications
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

        // Fetch provinces and cities
        $provinceResponse = file_get_contents('https://psgc.cloud/api/provinces');
        $provinces = json_decode($provinceResponse, true);

        // Fetch disabilities and education levels
        $disabilities = Disability::all();
        $levels = EducationLevel::all();
        $skills = Skill::all();

        // Return the view with all required data
        return view('employer.editJob', compact('listing', 'provinces', 'disabilities', 'levels', 'skills', 'setups', 'types'));

        // return redirect()->route('programs-manage');
    }

    public function updateJob(Request $request, $id)
    {
        Log::info("nakaabot sa updateJob");
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

            Log::info("lapas sa validation");

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
                ->get(['employer_id', 'position', 'end_date']);
            $trainingPrograms = TrainingProgram::where('agency_id', $user)
                ->get(['agency_id', 'title', 'schedule']);


            $events = [];



            foreach ($jobListings as $job) {
                // $scheduleDates = explode(',', $job->end_date);

                // Convert MM/DD/YYYY to YYYY-MM-DD
                $dateParts = explode('-', $job->end_date);
                if (count($dateParts) == 3) {
                    Log::info("kaabot sa if");
                    $formattedDate = sprintf('%04d-%02d-%02d', $dateParts[0], $dateParts[1], $dateParts[2]);
                    Log::info("Formatted Date:", ['formattedDate' => $formattedDate]);
                    $events[] = [
                        'id' => $job->employer_id,
                        'title' => $job->position,
                        'start' => $formattedDate, // FullCalendar expects start for all-day events
                        'allDay' => true
                    ];
                }
            }


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
                            'start' => $formattedDate, // FullCalendar expects start for all-day events
                            'allDay' => true
                        ];
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

        // Find the enrollee and update completion status
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

        // Validate the incoming request
        $validatedData = $request->validate([
            'schedule' => 'required|date',
            'start_time' => 'required|date_format:H:i|before:end_time',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule = $validatedData['schedule'];
        $start_time = $validatedData['start_time'];
        $end_time = $validatedData['end_time'];

        // $pwdUser = $application->user;
        // $jobListing = $application->job;

        // $pwdUser->notify(new JobApplicationAcceptedNotification($jobListing));

        $employee->update([
            'schedule' => $schedule,
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);

        return redirect()->route('jobs-show', $employee->job_id)->with('success', 'Interview schedule has been set.');
    }



    public function showEvents()
    {
        $events = Events::all();
        return view('employer.events', compact('events'));
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

        Events::create([
            'title' => $request->title,
            'description' => $request->description,
            'schedule' => $request->schedule,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'employer_id' => $employer_id
        ]);

        return redirect()->route('show-post-events');
    }
}
