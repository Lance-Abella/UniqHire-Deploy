<?php

namespace App\Http\Controllers;

use App\Models\Disability;
use App\Models\JobListing;
use App\Models\JobApplication;
use App\Models\EducationLevel;
use App\Models\PwdFeedback;
use App\Models\Enrollee;
use App\Models\Skill;
use App\Models\WorkSetup;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return redirect()->route('manage-jobs')->with('success', 'Job listing created successfully!');
    }

    public function showJobDetails($id)
    {
        $listing = JobListing::findOrFail($id);
        $userId = auth()->id();
        $reviews = PwdFeedback::where('program_id', $id)->with('pwd')->latest()->get();
        $applications = JobApplication::where('job_id', $listing->id)->get();
        $requests = JobApplication::where('job_id', $listing->id)->where('application_status', 'Pending')->get();
        $enrollees = Enrollee::where('program_id', $listing->id)->get();

        $pendingsCount = $applications->where('application_status', 'Pending')->count();
        $ongoingCount = $enrollees->where('completion_status', 'Ongoing')->count();
        $completedCount = $enrollees->where('completion_status', 'Completed')->count();
        $enrolleesCount = $enrollees->count();

        $enrolleeCount = Enrollee::where('program_id', $listing->id)
            ->count();

        $slots = $listing->participants - $enrolleeCount;

        if ($listing->crowdfund) {
            $raisedAmount = $listing->crowdfund->raised_amount ?? 0;
            $goal = $listing->crowdfund->goal ?? 1;
            $progress = ($goal > 0) ? round(($raisedAmount / $goal) * 100, 2) : 0;
            $listing->crowdfund->progress = $progress;
        }
        return view('employer.showJob', compact('listing', 'applications', 'reviews', 'enrollees', 'pendingsCount', 'ongoingCount', 'completedCount', 'enrolleesCount', 'requests', 'slots'));
    }

    public function deleteJob($id)
    {
        $listing = JobListing::findOrFail($id);

        dd($listing);

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
        return view('employer.editJob', compact('listing', 'provinces', 'disabilities', 'levels', 'skills'));

        // return redirect()->route('programs-manage');
    }
}
