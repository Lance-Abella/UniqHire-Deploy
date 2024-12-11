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
use App\Models\Employee;
use App\Models\Socials;
use App\Models\UserSocials;
use App\Models\Transaction;
use App\Notifications\ApplicationAcceptedNotification;
use App\Notifications\NewTrainingProgramNotification;
use App\Notifications\TrainingCompletedNotification;
use App\Notifications\PwdApplicationNotification;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

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
            $enrolleeCount = Enrollee::where('program_id', $program->id)
                ->where('completion_status', 'Ongoing')
                ->count();

            $totalEnrolleeCount = Enrollee::where('program_id', $program->id)->count();

            // Set these as object properties without saving to database
            $program->enrollee_count = $enrolleeCount;
            $program->available_slots = $program->participants - $totalEnrolleeCount;

            // Don't call update() on these properties since they're just for display

            if ($program->crowdfund) {
                $raisedAmount = $program->crowdfund->raised_amount ?? 0;
                $goal = $program->crowdfund->goal ?? 1;
                $progress = ($goal > 0) ? round(($raisedAmount / $goal) * 100, 2) : 0;
                $program->crowdfund->progress = $progress;
            }
        }
        return view('agency.manageProg', compact('programs'));
    }

    public function showProgramDetails($id)
    {
        $program = TrainingProgram::findOrFail($id);
        $userId = auth()->id();

        // Only check for 'Ended' status if the program isn't already cancelled
        if ($program->status !== 'Cancelled') {
            // Check if all enrollees have completed the program
            $totalEnrollees = Enrollee::where('program_id', $program->id)->count();
            $completedEnrollees = Enrollee::where('program_id', $program->id)
                ->where('completion_status', 'Completed')
                ->count();

            if ($totalEnrollees > 0 && $totalEnrollees === $completedEnrollees) {
                $program->status = 'Ended';
                $program->save();
            }
        }

        $reviews = PwdFeedback::where('program_id', $id)->with('pwd')->latest()->get();
        $applications = TrainingApplication::where('training_program_id', $program->id)->get();
        $requests = TrainingApplication::where('training_program_id', $program->id)->where('application_status', 'Pending')->get();
        $enrollees = Enrollee::where('program_id', $program->id)->get();
        $pendingsCount = $applications->where('application_status', 'Pending')->count();
        $ongoingCount = $enrollees->where('completion_status', 'Ongoing')->count();
        $completedCount = $enrollees->where('completion_status', 'Completed')->count();
        $enrolleesCount = $enrollees->count();
        $enrolleeCount = Enrollee::where('program_id', $program->id)->count();
        $slots = $program->participants - $enrolleeCount;

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
            'status' => 'Ongoing',
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

            $trainingProgram->competencies()->sync($competencyIds);
        }

        $programDisabilityIds = $trainingProgram->disability->pluck('id')->toArray();

        $pwdUsers = User::whereHas('role', function ($query) {
            $query->where('role_name', 'PWD');
        })
            ->whereHas('userInfo', function ($query) use ($programDisabilityIds) {
                $query->whereIn('disability_id', $programDisabilityIds);
            })
            ->get();

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

        return redirect()->route('programs-manage')->withInput()->with('success', 'Training program created successfully!');
    }

    public function deleteProgram($id)
    {
        $program = TrainingProgram::find($id);

        if ($program && $program->agency_id == auth()->id()) {
            // Delete related notifications
            DB::table('notifications')
                ->where('data', 'like', '%"training_program_id":' . $id . '%')
                ->delete();

            // Delete the program
            $program->delete();

            return redirect()->route('programs-manage')
                ->with('success', 'Training program deleted successfully');
        }

        return redirect()->route('programs-manage')
            ->with('error', 'Failed to delete training program');
    }

    public function editProgram($id)
    {
        $program = TrainingProgram::find($id);

        if (!$program || $program->agency_id != auth()->id()) {
            return redirect()->route('programs-manage');
        }

        $provinceResponse = file_get_contents('https://psgc.cloud/api/provinces');
        $provinces = json_decode($provinceResponse, true);
        $disabilities = Disability::all();
        $levels = EducationLevel::all();
        $skills = Skill::all();

        return view('agency.editProg', compact('program', 'provinces', 'disabilities', 'levels', 'skills'));
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
                'start_time' => 'required|date_format:H:i|before:end_time',
                'end_time' => 'required|date_format:H:i|after:start_time',
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
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
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

                if ($program->crowdfund) {
                    $program->crowdfund->delete();
                }
            }

            return redirect()->route('programs-show', $id)->withInput()->with('success', 'Training program has been updated successfully!');
        } else {
            return back()->withInput()->with('error', 'Failed to update training program. Review form.');
        }
    }

    public function showCalendar(Request $request)
    {
        $user = auth()->user()->userInfo->user_id;

        if ($request->expectsJson()) {
            $trainingPrograms = TrainingProgram::where('agency_id', $user)
                ->get(['agency_id', 'title', 'schedule', 'start_time', 'end_time']);

            $events = [];

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

        return view('agency.calendar');
    }

    public function accept(Request $request)
    {
        Log::info("Reached accept method");

        $validatedData = $request->validate([
            'pwd_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:training_programs,id',
            'training_application_id' => 'required|exists:training_applications,id',
        ]);

        $pwdId = $validatedData['pwd_id'];
        $programId = $validatedData['program_id'];
        $applicationId = $validatedData['training_application_id'];
        $completionStatus = 'Ongoing';
        $application = TrainingApplication::findOrFail($applicationId);
        $application->application_status = 'Approved';
        $application->save();
        $pwdUser = $application->user;
        $trainingProgram = $application->program;

        $pwdUser->notify(new ApplicationAcceptedNotification($trainingProgram));

        Enrollee::create([
            'pwd_id' => $pwdId,
            'program_id' => $programId,
            'training_application_id' => $applicationId,
            'completion_status' => $completionStatus,
        ]);

        $application->update(['application_status' => 'Approved']);

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

        // Update enrollee status
        $enrolleeId = $validatedData['enrolleeId'];
        $enrollee = Enrollee::findOrFail($enrolleeId);
        $enrollee->update(['completion_status' => 'Completed']);

        // Check if all enrollees have completed
        $totalEnrollees = Enrollee::where('program_id', $programId)->count();
        $completedEnrollees = Enrollee::where('program_id', $programId)
            ->where('completion_status', 'Completed')
            ->count();

        // Update program status if all enrollees have completed
        if ($totalEnrollees > 0 && $totalEnrollees === $completedEnrollees) {
            $program = TrainingProgram::find($programId);
            $program->status = 'Ended';
            $program->save();
        }

        // Add skills to user
        $progSkills = DB::table('program_skill')
            ->where('training_program_id', $programId)
            ->pluck('skill_id')
            ->toArray();

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

        // Create certification record
        DB::table('certification_details')->insert([
            'program_id' => $enrollee->program_id,
            'user_id' => $enrollee->pwd_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send notification
        $pwdUser = $enrollee->pwd;
        $pwdUser->notify(new TrainingCompletedNotification($enrollee));

        return back()->with('success', 'Enrollee completed the training and certification record created.');
    }

    public function showEnrolleeProfile($id)
    {
        $user = User::find($id);
        $skilluser = SkillUser::where('user_id', $id)->get();
        $certifications = Enrollee::where('pwd_id', $id)->where('completion_status', 'Completed')->get();
        $experiences = Experience::where('user_id', $id)->get();
        $socials = Socials::all();
        $userSocials = UserSocials::where('user_id', $id)->get();
        $latitude = $user->userInfo->latitude;
        $longitude = $user->userInfo->longitude;
        $isEmployed = Employee::where('pwd_id', $id)->where('hiring_status', 'Accepted')->exists();

        return view('agency.pwdProfile', compact('user', 'certifications', 'skilluser', 'experiences', 'latitude', 'longitude', 'isEmployed', 'userSocials', 'skilluser'));
    }

    public function deny(TrainingApplication $trainid)
    {
        $trainid->delete();

        return back()->with('success', 'Application denied');
    }

    public function updateProgramStatus($id, $status)
    {
        $program = TrainingProgram::find($id);

        if ($program && $program->agency_id == auth()->id()) {
            if (in_array($status, ['Ongoing', 'Ended', 'Cancelled'])) {
                $program->status = $status;
                $program->save();
                return redirect()->back()->with('success', 'Program status updated successfully');
            }
        }

        return redirect()->back()->with('error', 'Failed to update program status');
    }

    public function cancelProgram($id)
    {
        $program = TrainingProgram::find($id);

        if ($program && $program->agency_id == auth()->id()) {
            $program->status = 'Cancelled';
            $program->save();

            // Optionally notify enrolled users about cancellation
            $enrollees = Enrollee::where('program_id', $program->id)->get();
            foreach ($enrollees as $enrollee) {
                // You might want to create a new notification class for this
                // $enrollee->pwd->notify(new ProgramCancelledNotification($program));
                $enrollee->update([
                    'completion_status' => 'Not completed'
                ]);
                // $enrollee->completion_status = 'Not completed';
            }


            return redirect()->route('programs-manage')
                ->with('success', 'Training program has been cancelled');
        }

        return redirect()->route('programs-manage')
            ->with('error', 'Failed to cancel training program');
    }
}
