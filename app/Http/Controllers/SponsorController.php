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

class SponsorController extends Controller
{
    public function showTrainingLists(Request $request)
    {
        $educations = EducationLevel::all();

        // Fetch programs that have crowdfunding events
        $query = TrainingProgram::query()->whereHas('crowdfund');

        // Filter programs by search query (if provided)
        if ($request->filled('search')) {
            $query->where("title", "LIKE", "%" . $request->search . "%");
        }

        // Filter programs by education levels (if selected)
        if ($request->filled('education')) {
            $query->whereHas('education', function ($q) use ($request) {
                $q->whereIn('education_name', $request->education);
            });
        }

        // Fetch all programs with crowdfunding events
        $allPrograms = $query->with('crowdfund', 'agency.userInfo')->get();

        // Paginate results (5 programs per page)
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $allPrograms->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedItems = new LengthAwarePaginator($currentItems, $allPrograms->count(), $perPage, $currentPage, [
            'path' => $request->url(),
        ]);

        // Log the paginated items for debugging
        Log::info('Paginated Items:', $paginatedItems->toArray());

        // Get the count of programs for each education level
        $educationCounts = EducationLevel::withCount('program')->get()->keyBy('id');

        // Return the view with paginated programs and education filters
        return view('sponsor.listTrainProg', compact('paginatedItems', 'educations', 'educationCounts'));
    }

    public function showProgDetails($id)
    {
        // Fetch the program details with related data
        $program = TrainingProgram::with('agency.userInfo', 'disability', 'education', 'crowdfund')->findOrFail($id);

        // Get the count of current enrollees
        $enrolleeCount = Enrollee::where('program_id', $program->id)
            ->where('completion_status', 'Ongoing')
            ->count();

        // Calculate available slots for the program
        $slots = $program->participants - $enrolleeCount;

        // Fetch reviews for the program
        $reviews = PwdFeedback::where('program_id', $id)->with('pwd')->latest()->get();

        // Fetch all enrollees for the program
        $enrollees = Enrollee::where('program_id', $program->id)->get();

        $sponsors = [];
        // Handle crowdfunding details if applicable
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

        // Return the view for program details
        return view('sponsor.showTrainProgDetails', compact('program', 'reviews', 'enrollees', 'slots', 'sponsors'));
    }
}
