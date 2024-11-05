<?php

namespace App\Http\Controllers;

use App\Models\Disability;
use App\Models\JobListing;
use App\Models\EducationLevel;
use App\Models\Skill;
use Illuminate\Http\Request;

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
        return view('employer.addJob', compact('disabilities', 'levels', 'skills'));
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
            'end_date' => 'required|date',
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id',
            'disabilities' => 'required|array',
            'disabilities.*' => 'exists:disabilities,id',
        ]);

        $salary = $this->convertToNumber($request->salary);

        $jobListing = JobListing::create([
            'employer_id' => auth()->id(),
            'position' => $request->position,
            'description' => $request->description,
            'salary' => $salary,
            'latitude' => $request->lat,
            'longitude' => $request->long,
            'end_date' => $request->end_date,
        ]);

        $jobListing->skill()->attach($request->skills);
        $jobListing->disability()->attach($request->disabilities);

        return redirect()->route('manage-jobs')->with('success', 'Job listing created successfully!');
    }
}
