<?php

namespace App\Http\Controllers;

use App\Models\CrowdfundEvent;
use App\Models\Disability;
use App\Models\EducationLevel;
use App\Models\TrainingProgram;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Enrollee;
use App\Models\PwdFeedback;
use App\Models\TrainingApplication;
use App\Models\Competency;
use App\Models\Skill;
use App\Models\SkillUser;
use App\Models\Experience;
use App\Models\Transaction;
use App\Notifications\ApplicationAcceptedNotification;
use App\Notifications\NewTrainingProgramNotification;
use App\Notifications\TrainingCompletedNotification;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

use function Laravel\Prompts\table;

class AgencyController extends Controller
{
    private function convertToNumber($number)
    {
        return (float) str_replace(',', '', $number);
    }

    public function showPrograms()
    {
        $userId = auth()->id();
        $programs = TrainingProgram::where('agency_id', $userId)
            ->latest()
            ->with('crowdfund')
            ->get();



        foreach ($programs as $program) {

            $program->enrolleeCount = Enrollee::where('program_id', $program->id)
                ->where('completion_status', 'Ongoing')
                ->count();

            $program->slots = $program->participants - $program->enrolleeCount;

            if ($program->crowdfund) {
                $raisedAmount = $program->crowdfund->raised_amount ?? 0; // Default to 0 if raised_amount is null
                $goal = $program->crowdfund->goal ?? 1; // Default to 1 to avoid division by zero
                $progress = ($goal > 0) ? round(($raisedAmount / $goal) * 100, 2) : 0; // Calculate progress percentage
                $program->crowdfund->progress = $progress;
            }
        }
        return view('agency.manageProg', compact('programs'));
    }

    public function showProgramDetails($id)
    {
        $program = TrainingProgram::findOrFail($id);
        $userId = auth()->id();
        $reviews = PwdFeedback::where('program_id', $id)->with('pwd')->latest()->get();
        $applications = TrainingApplication::where('training_program_id', $program->id)->get();
        $requests = TrainingApplication::where('training_program_id', $program->id)->where('application_status', 'Pending')->get();
        $enrollees = Enrollee::where('program_id', $program->id)->get();
        $sponsors =

            $pendingsCount = $applications->where('application_status', 'Pending')->count();
        $ongoingCount = $enrollees->where('completion_status', 'Ongoing')->count();
        $completedCount = $enrollees->where('completion_status', 'Completed')->count();
        $enrolleesCount = $enrollees->count();

        $enrolleeCount = Enrollee::where('program_id', $program->id)
            ->count();

        $slots = $program->participants - $enrolleeCount;

        $sponsors = [];
        if ($program->crowdfund) {
            $crowdfundId = $program->crowdfund->id ?? null;
            if ($crowdfundId) {
                $sponsors = Transaction::where('crowdfund_id', $crowdfundId)
                    ->where('status', 'Completed') // Only include successful transactions
                    ->get(['name', 'amount']);
            }
            // dd($sponsors);
            $raisedAmount = $program->crowdfund->raised_amount ?? 0;
            $goal = $program->crowdfund->goal ?? 1;
            $progress = ($goal > 0) ? round(($raisedAmount / $goal) * 100, 2) : 0;
            $program->crowdfund->progress = $progress;
        }
        return view('agency.showProg', compact('program', 'applications', 'reviews', 'enrollees', 'pendingsCount', 'ongoingCount', 'completedCount', 'enrolleesCount', 'requests', 'slots', 'sponsors'));
    }

    public function showAddForm()
    {
        $disabilities = Disability::all();
        $levels = EducationLevel::all();
        $skills = Skill::all();
        return view('agency.addProg', compact('disabilities', 'levels', 'skills'));
    }

    public function addProgram(Request $request)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
            'loc' => 'nullable|string|max:255',
            'description' => 'required|string',
            'schedule' => 'required|string',
            'start_age' => 'integer|min:1|max:99',
            'end_age' => 'integer|min:1|max:99',
            'start_time' => 'required|date_format:H:i|before:end_time',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'participants' => 'required|max:255',
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id',
            'disabilities' => 'required|array',
            'disabilities.*' => 'exists:disabilities,id',
            'competencies' => 'array|max:4',
            'competencies.*' => 'string|distinct',
        ]);

        $participants = $this->convertToNumber($request->participants);



        // try {
        $trainingProgram = TrainingProgram::create([
            'agency_id' => auth()->id(),
            'title' => $request->title,
            'latitude' => $request->lat,
            'longitude' => $request->long,
            'location' => $request->loc,
            'description' => $request->description,
            'schedule' => $request->schedule,
            'disabilities' => $request->disability,
            'education_id' => $request->education,
            'skills' => $request->skills,
            'start_age' => $request->start_age,
            'end_age' => $request->end_age,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'participants' => $participants,
        ]);

        $trainingProgram->skill()->attach($request->skills);
        $trainingProgram->disability()->attach($request->disabilities);

        if ($request->has('competencies')) {
            $competencies = $request->competencies;
            $competencyIds = [];

            foreach ($competencies as $competency) {
                $existingCompetency = Competency::firstOrCreate(['name' => $competency]);
                $competencyIds[] = $existingCompetency->id;
            }

            // Attach competencies to the training program
            $trainingProgram->competencies()->sync($competencyIds);
        }


        //NOTIFY PWD USERS!!! TRAINING PROGRAM
        $pwdUsers = User::whereHas('role', function ($query) {
            $query->where('role_name', 'PWD');
        })->get();

        foreach ($pwdUsers as $user) {
            $user->notify(new NewTrainingProgramNotification($trainingProgram));
        }

        if ($request->has('goal') && $request->goal !== null) {
            $goal = $this->convertToNumber($request->goal);
            CrowdfundEvent::create([
                'program_id' => $trainingProgram->id,
                'goal' => $goal,
            ]);
        }

        return redirect()->route('programs-manage')->with('success', 'Training program created successfully!');
    }

    public function deleteProgram($id)
    {
        $program = TrainingProgram::find($id);

        if ($program && $program->agency_id == auth()->id()) {
            // Find and delete related notifications
            DB::table('notifications')
                ->where('data', 'like', '%"training_program_id":' . $id . '%')
                ->delete();

            $program->delete();
            return redirect()->route('programs-manage')->with('success', 'Training program deleted successfully');
        } else {
            return redirect()->route('programs-manage')->with('error', 'Failed to delete training program');
        }
    }

    public function editProgram($id)
    {
        $program = TrainingProgram::find($id);

        if (!$program || $program->agency_id != auth()->id()) {
            return redirect()->route('programs-manage');
        }

        // Fetch provinces and cities
        $provinceResponse = file_get_contents('https://psgc.cloud/api/provinces');
        $provinces = json_decode($provinceResponse, true);

        // Fetch disabilities and education levels
        $disabilities = Disability::all();
        $levels = EducationLevel::all();
        $skills = Skill::all();

        // Return the view with all required data
        return view('agency.editProg', compact('program', 'provinces', 'disabilities', 'levels', 'skills'));

        // return redirect()->route('programs-manage');
    }

    public function updateProgram(Request $request, $id)
    {
        $program = TrainingProgram::find($id);

        if ($program && $program->agency_id == auth()->id()) {
            $request->validate([
                'title' => 'required|string|max:255',
                'lat' => 'required|numeric|between:-90,90',
                'long' => 'required|numeric|between:-180,180',
                'loc' => 'nullable|string|max:255',
                'description' => 'required|string',
                'schedule' => 'required|string',
                'goal' => 'nullable|string',
                'skills' => 'required|array',
                'skills.*' => 'exists:skills,id',
                'disabilities' => 'required|array',
                'disabilities.*' => 'exists:disabilities,id',
                'competencies' => 'array|max:4',
                'competencies.*' => 'string|distinct',
                'start_age' => 'integer|min:1|max:99',
                'end_age' => 'integer|min:1|max:99',
                'participants' => 'required|max:255',
            ]);

            $participants = $this->convertToNumber($request->participants);

            $program->update([
                'title' => $request->title,
                'latitude' => $request->lat,
                'longitude' => $request->long,
                'location' => $request->loc,
                'description' => $request->description,
                'schedule' => $request->schedule,
                'education_id' => $request->education,
                'start_age' => $request->start_age,
                'end_age' => $request->end_age,
                'participants' => $participants,
            ]);

            $program->skill()->sync($request->skills);
            $program->disability()->sync($request->disabilities);

            if ($request->has('competencies')) {
                $competencies = $request->competencies;
                $competencyIds = [];

                foreach ($competencies as $competency) {
                    $existingCompetency = Competency::firstOrCreate(['name' => $competency]);
                    $competencyIds[] = $existingCompetency->id;
                }

                // Attach competencies to the training program
                $program->competencies()->sync($competencyIds);
            }

            if ($request->has('goal') && $request->goal !== null) {
                $crowdfundEvent = $program->crowdfund;
                $goal = $this->convertToNumber($request->goal);

                if ($crowdfundEvent) {
                    $crowdfundEvent->update([
                        'goal' => $goal,
                    ]);
                } else {
                    CrowdfundEvent::create([
                        'program_id' => $program->id,
                        'goal' => $goal,
                    ]);
                }
            } else {
                // If goal is not present, it means the crowdfund checkbox is unchecked, so delete the crowdfund event if it exists
                if ($program->crowdfund) {
                    $program->crowdfund->delete();
                }
            }

            return redirect()->route('programs-show', $id)->with('success', 'Training program has been updated successfully!');
        } else {
            return back()->with('error', 'Failed to update training program. Review form.');
        }
    }

    public function showCalendar(Request $request)
    {

        $user = auth()->user()->userInfo->user_id;
        log::info($user);

        if ($request->expectsJson()) {
            $trainingPrograms = TrainingProgram::where('agency_id', $user)
                ->get(['agency_id', 'title', 'schedule']);

            $events = [];

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

        return view('agency.calendar');
    }

    public function accept(Request $request)
    {
        Log::info("Reached accept method");

        // Validate the incoming request
        $validatedData = $request->validate([
            'pwd_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:training_programs,id',
            'training_application_id' => 'required|exists:training_applications,id',
        ]);

        $pwdId = $validatedData['pwd_id'];
        $programId = $validatedData['program_id'];
        $applicationId = $validatedData['training_application_id'];
        $completionStatus = 'Ongoing';

        // Find the application by training_id
        $application = TrainingApplication::findOrFail($applicationId);
        $application->application_status = 'Approved';
        $application->save();

        $pwdUser = $application->user;
        $trainingProgram = $application->program;

        $pwdUser->notify(new ApplicationAcceptedNotification($trainingProgram));


        // Create Enrollee record
        Enrollee::create([
            'pwd_id' => $pwdId,
            'program_id' => $programId,
            'training_application_id' => $applicationId,
            'completion_status' => $completionStatus,
        ]);

        $application->update(['application_status' => 'Approved']);

        // return response()->json(['success' => true, 'message' => 'Application submitted successfully.']);
        return back()->with('success', 'Application is accepted');
    }

    public function application(Request $request)
    {

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'training_program_id' => 'required|exists:training_programs,id',
            'application_status' => 'required|in:Pending,Approved,Denied',
        ]);

        TrainingApplication::create($validatedData);

        return response()->json(['success' => true, 'message' => 'Application submitted successfully.']);
    }

    public function markComplete(Request $request)
    {
        $validatedData = $request->validate([
            'enrolleeId' => 'required|exists:enrollees,id',
            'userId' => 'required|exists:users,id',
            'programId' => 'required|exists:training_programs,id'
        ]);

        $programId = $validatedData['programId'];
        $userId = $validatedData['userId'];

        $progSkills = DB::table('program_skill')
            ->where('training_program_id', $programId)
            ->pluck('skill_id') // Fetching only the skill IDs
            ->toArray();

        // Fetch skills already assigned to the user
        $userSkills = SkillUser::where('user_id', $userId)
            ->pluck('skill_id')
            ->toArray();

        foreach ($progSkills as $skillId) {
            if (!in_array($skillId, $userSkills)) {
                SkillUser::create([
                    'user_id' => $userId,
                    'skill_id' => $skillId,
                ]);
            }
        }
        $enrolleeId = $validatedData['enrolleeId'];
        $completionStatus = 'Completed';

        // Find the enrollee and update completion status
        $enrollee = Enrollee::findOrFail($enrolleeId);
        $enrollee->update(['completion_status' => $completionStatus]);

        // Insert a new row in the certification_details table
        DB::table('certification_details')->insert([
            'program_id' => $enrollee->program_id, // assuming program_id is a property of the Enrollee model
            'user_id' => $enrollee->pwd_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pwdUser = $enrollee->pwd;
        $pwdUser->notify(new TrainingCompletedNotification($enrollee));

        return back()->with('success', 'Enrollee completed the training and certification record created.');
    }




    // public function action(Request $request)
    // {
    //     log::info("calendar reach in action!");
    //     if($request->ajax())
    // 	{
    // 		if($request->type == 'add')
    // 		{
    // 			$event = TrainingProgram::create([
    // 				'title'		=>	$request->title,
    // 				'start'		=>	$request->start,
    // 				'end'		=>	$request->end
    // 			]);

    // 			return response()->json($event);
    // 		}

    // 		if($request->type == 'update')
    // 		{
    // 			$event = TrainingProgram::find($request->id)->update([
    // 				'title'		=>	$request->title,
    // 				'start'		=>	$request->start,
    // 				'end'		=>	$request->end
    // 			]);

    // 			return response()->json($event);
    // 		}

    // 		if($request->type == 'delete')
    // 		{
    // 			$event = TrainingProgram::find($request->id)->delete();

    // 			return response()->json($event);
    // 		}
    // 	}
    // }

    //TEMPORARY LOGIC
    public function showEnrolleeProfile($id)
    {
        $user = User::find($id);
        $skilluser = SkillUser::where('user_id', $id)->get();
        $certifications = Enrollee::where('pwd_id', $id)->where('completion_status', 'Completed')->get();
        $experiences = Experience::where('user_id', $id)->get();
        // $enrollees = Enrollee::where('user_id', $user)->get();
        return view('agency.pwdProfile', compact('user', 'certifications', 'skilluser', 'experiences'));
    }
}
