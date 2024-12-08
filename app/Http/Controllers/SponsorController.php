<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingProgram;
use App\Models\EducationLevel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers;
use App\Models\Enrollee;
use App\Models\PwdFeedback;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class SponsorController extends Controller
{
    public function showTrainingLists(Request $request)
    {
        $educations = EducationLevel::all();
        $query = TrainingProgram::query()->whereHas('crowdfund');

        if ($request->filled('search')) {
            $query->where("title", "LIKE", "%" . $request->search . "%");
        }

        if ($request->filled('education')) {
            $query->whereHas('education', function ($q) use ($request) {
                $q->whereIn('education_name', $request->education);
            });
        }

        $allPrograms = $query->with('crowdfund', 'agency.userInfo')->get();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $allPrograms->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedItems = new LengthAwarePaginator($currentItems, $allPrograms->count(), $perPage, $currentPage, [
            'path' => $request->url(),
        ]);
        $educationCounts = EducationLevel::withCount('program')->get()->keyBy('id');

        return view('sponsor.listTrainProg', compact('paginatedItems', 'educations', 'educationCounts'));
    }

    public function showProgDetails($id)
    {
        $program = TrainingProgram::with('agency.userInfo', 'disability', 'education', 'crowdfund')->findOrFail($id);
        $enrolleeCount = Enrollee::where('program_id', $program->id)
            ->where('completion_status', 'Ongoing')
            ->count();
        $slots = $program->participants - $enrolleeCount;
        $reviews = PwdFeedback::where('program_id', $id)->with('pwd')->latest()->get();
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

        return view('sponsor.showTrainProgDetails', compact('program', 'reviews', 'enrollees', 'slots', 'sponsors'));
    }

    public function showTransactions()
    {
        $user = Auth::user()->id;
        $transactions = Transaction::where('sponsor_id', $user)->paginate(18);
        $totalAmount = Transaction::where('sponsor_id', $user)->sum('amount');

        return view('sponsor.transactions', compact('transactions', 'totalAmount'));
    }
}
