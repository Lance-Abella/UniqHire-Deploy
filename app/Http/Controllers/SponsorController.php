<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Models\TrainingProgram;
use App\Models\Disability;
use App\Models\EducationLevel;
use App\Models\TrainingApplication;
use App\Models\User;
use App\Models\SkillUser;
use App\Http\Requests\StoreUserInfoRequest;
use App\Http\Requests\UpdateUserInfoRequest;
use App\Models\Enrollee;
use App\Models\PwdFeedback;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Notifications\PwdApplicationNotification;
use Illuminate\Support\Facades\Auth;


class SponsorController extends Controller
{
    public function showTrainingLists(Request $request)
    {
        $educations = EducationLevel::all();
        $query = TrainingProgram::query();

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

        // Fetch all available programs
        $allPrograms = $query->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = array_slice($allPrograms->toArray(), ($currentPage - 1) * $perPage, $perPage);
        $paginatedItems = new LengthAwarePaginator($currentItems, $allPrograms->count(), $perPage);
        $paginatedItems->setPath($request->url());

        $educationCounts = EducationLevel::withCount('program')->get()->keyBy('id');
        Log::info('Paginated Items:', $paginatedItems->toArray());

        return view('sponsor.listTrainProg', compact('paginatedItems', 'educations', 'educationCounts'));
    }
}
