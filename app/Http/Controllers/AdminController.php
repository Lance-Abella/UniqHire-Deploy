<?php

namespace App\Http\Controllers;

use App\Models\Disability;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Role;
use App\Models\Skill;
use App\Models\Socials;
use App\Models\ProgramCriteria;
use App\Models\JobCriteria;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // public function showDashboard()
    // {
    //     $users = User::all();
    //     return view('admin.dashboard')->with('users');
    // }

    public function showPwds()
    {
        $users = User::whereHas('userInfo', function ($query) {
            $query->whereNotNull('disability_id')->where('disability_id', '!=', 1);
        })->paginate(8);

        return view('admin.pwdUsers', compact('users'));
    }

    public function showTrainers()
    {
        $trainingID = Role::where('role_name', 'Training Agency')->value('id');
        $users = User::whereHas('role', function ($query) use ($trainingID) {
            $query->where('role_id', $trainingID);
        })->paginate(8);

        return view('admin.trainingAgencies', compact('users'));
    }

    public function showEmployers()
    {
        $employeeID = Role::where('role_name', 'Employer')->value('id');

        $users = User::whereHas('role', function ($query) use ($employeeID) {
            $query->where('role_id', $employeeID);
        })->paginate(8);

        return view('admin.employeeUsers', compact('users'));
    }

    public function showSponsors()
    {
        $sponsorID = Role::where('role_name', 'Sponsor')->value('id');

        $users = User::whereHas('role', function ($query) use ($sponsorID) {
            $query->where('role_id', $sponsorID);
        })->paginate(8);

        return view('admin.sponsorUsers', compact('users'));
    }

    public function showSkills()
    {
        $skills = Skill::paginate(8);

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
        return view('admin.editSkill', compact('skill'));
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

    public function showDisabilities()
    {
        $disabilities = Disability::paginate(8);

        return view('admin.disabilityManage', compact('disabilities'));
    }

    public function addDisability(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Disability::create([
            'disability_name' => $request->name,
        ]);

        return redirect()->route('disability-list')->with('success', 'Disability added successfully.');
    }

    public function editDisability(Disability $disability)
    {
        return view('admin.editDisability', compact('disability'));
    }

    public function updateDisability(Request $request, Disability $disability)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $disability->update([
            'disability_name' => $request->name,
        ]);

        return redirect()->route('disability-list')->with('success', 'Disability updated successfully!');
    }

    public function deleteDisability(Disability $disability)
    {
        $disability->delete();

        return back()->with('success', 'Disability deleted successfully!');
    }

    public function showSocials()
    {
        $socials = Socials::paginate(18);

        return view('admin.socialManage', compact('socials'));
    }

    public function addSocial(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Socials::create([
            'name' => $request->name,
        ]);

        return redirect()->route('social-list')->with('success', 'Social media platform added successfully.');
    }

    public function editSocial(Socials $social)
    {
        return view('admin.editSocial', compact('social'));
    }

    public function updateSocial(Request $request, Socials $social)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $social->update([
            'name' => $request->name,
        ]);

        return redirect()->route('social-list')->withInput()->with('success', 'Social media platform updated successfully!');
    }

    public function deleteSocial(Socials $social)
    {
        $social->delete();

        return back()->with('success', 'Social media platform deleted successfully!');
    }

    public function deleteUser(User $id)
    {
        $id->delete();
        return back()->with('success', 'User account deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $userInfo = $user->userInfo;

        if ($userInfo->registration_status == 'Pending') {
            $userInfo->registration_status = 'Activated';
        } elseif ($userInfo->registration_status == 'Activated') {
            $userInfo->registration_status = 'Deactivated';
        } else {
            $userInfo->registration_status = 'Activated';
        }

        $userInfo->save();

        return redirect()->back()->with('success', 'User registration status updated successfully.');
    }

    public function setStatus(Request $request, $id, $status)
    {
        $user = User::findOrFail($id);
        $userInfo = $user->userInfo;

        if ($userInfo->registration_status == 'Pending') {
            $userInfo->registration_status = $status;
            $userInfo->save();

            return redirect()->back()->with('success', "User registration status set to $status.");
        }

        return redirect()->back()->with('error', 'Status cannot be changed.');
    }

    public function showProgramCriteria()
    {
        $criteria = ProgramCriteria::paginate(18);

        return view('admin.criteriaManage', compact('criteria'));
    }

    public function updateProgramCriteria(Request $request)
    {

        $request->validate([
            'weight' => ['required', 'array'], 
            'weight.*' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        if (array_sum($request->weight) !== 100) {
            return back()->withInput()->with(['weight' => 'The total weight must equal 100.']);
        }

        foreach ($request->weight as $index => $weight) {
            $criterion = ProgramCriteria::findOrFail($index);
            $criterion->update(['weight' => $weight]);
        }

        return redirect()->back()->with('success', 'Criteria updated successfully!');
    }

    public function editProgramCriteria(ProgramCriteria $criterion)
    {
        return view('admin.editCriteria', compact('criterion'));
    }

}
