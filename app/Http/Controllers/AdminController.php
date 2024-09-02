<?php

namespace App\Http\Controllers;

use App\Models\Disability;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Role;
use App\Models\Skill;

class AdminController extends Controller
{
    public function showDashboard()
    {
        $users = User::all();
        return view('admin.dashboard')->with('users');
    }

    public function showPwds()
    {
        $users = User::whereHas('userInfo', function ($query) {
            $query->whereNotNull('disability_id')->where('disability_id', '!=', 1);
        })->get();

        return view('admin.pwdUsers', compact('users'));
    }

    public function showTrainers()
    {
        $trainingID = Role::where('role_name', 'Training Agency')->value('id');
        $users = User::whereHas('role', function ($query) use ($trainingID) {
            $query->where('role_id', $trainingID);
        })->get();

        return view('admin.trainingAgencies', compact('users'));
    }

    public function showEmployers()
    {
        $employeeID = Role::where('role_name', 'Employer')->value('id');

        $users = User::whereHas('role', function ($query) use ($employeeID) {
            $query->where('role_id', $employeeID);
        })->get();

        return view('admin.employeeUsers', compact('users'));
    }

    public function showSponsors()
    {
        $sponsorID = Role::where('role_name', 'Sponsor')->value('id');

        $users = User::whereHas('role', function ($query) use ($sponsorID) {
            $query->where('role_id', $sponsorID);
        })->get();

        return view('admin.sponsorUsers', compact('users'));
    }

    // SKILLS MANAGING

    public function showSkills()
    {
        $skills = Skill::all();

        return view('admin.skillManage', compact('skills'));
    }

    public function create()
    {
        return view('admin.createSkill');
    }

    public function addSkill(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Skill::create($request->all());

        return redirect()->route('skill-list')->with('success', 'Skill added successfully.');
    }

    public function editSkill(Skill $skill)
    {
        return view('admin.editSkill', compact('skill')); // Create a form view for editing a skill
    }

    public function updateSkill(Request $request, Skill $skill)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $skill->update($request->all());

        return redirect()->route('skill-list')->with('success', 'Skill updated successfully!');
    }

    public function deleteSkill(Skill $skill)
    {
        $skill->delete();

        return back()->with('success', 'Skill deleted successfully!');
    }

    public function deleteUser(User $id) 
    {
        $id->delete();
        return back()->with('success', 'User account deleted successfully!');
    }
}
