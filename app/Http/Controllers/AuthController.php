<?php

namespace App\Http\Controllers;

use App\Models\CertificationDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Enrollee;
use App\Models\Valid;
use App\Models\Role;
use App\Models\Disability;
use App\Models\EducationLevel;
use App\Models\Experience;
use App\Models\Skill;
use App\Models\UserInfo;
use App\Models\SkillUser;
use App\Models\Socials;
use App\Models\UserSocials;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AuthController extends Controller
{
    public function showProfile()
    {
        $id = Auth::user()->id;
        $user = User::find($id);
        $levels = EducationLevel::all();
        $disabilities = Disability::all();
        $addedSkillIds = SkillUser::where('user_id', $id)->pluck('skill_id')->toArray();
        $skills = Skill::whereNotIn('id', $addedSkillIds)->get();
        $socials = Socials::all();
        $userSocials = UserSocials::where('user_id', $id)->get();
        $skilluser = SkillUser::where('user_id', $id)->get();
        $experiences = Experience::where('user_id', $id)->get();
        $certifications = CertificationDetail::where('user_id', $id)->get();
        $latitude = $user->userInfo->latitude;
        $longitude = $user->userInfo->longitude;
        $isEmployed = Employee::where('pwd_id', $id)->where('hiring_status', 'Accepted')->exists();

        return view('auth.profile', compact('levels', 'disabilities', 'user', 'certifications', 'skills', 'skilluser', 'experiences', 'latitude', 'longitude', 'socials', 'userSocials', 'isEmployed'));
    }

    public function editProfile(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);
        $userInfo = UserInfo::where('user_id', $id)->firstOrFail();
        $request->validate([
            'name' => 'required|string|max:255',
            'contactnumber' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:99',
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
            'loc' => 'nullable|string|max:255',
            'founder' => 'nullable|string|max:255',
            'year_established' => 'nullable|integer|min:1000|max:3000',
            'about' => 'nullable|string',
            'awards' => 'nullable|string',
            'affiliations' => 'nullable|string',
            'paypal' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'socials' => 'nullable|array',
            'socials.*' => 'integer|exists:socials,id',
            'social_links' => 'nullable|array',
            'social_links.*' => 'string|url|max:255',
        ]);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'profile_photos/' . $fileName;

            Storage::disk('profile_photos')->put($fileName, file_get_contents($file));

            $userInfo->profile_path = 'storage/' . $filePath;
            $userInfo->save();
        }

        if ($user->hasRole('Training Agency') || $user->hasRole('Sponsor') || $user->hasRole('Employer')) {
            $user->userInfo->update([
                'name' => $request->name,
                'contactnumber' => $request->contactnumber,
                'latitude' => $request->lat,
                'longitude' => $request->long,
                'location' => $request->loc,
                'about' => $request->about,
                'founder' => $request->founder,
                'year_established' => $request->year_established,
                'awards' => $request->awards ?? '',
                'affiliations' => $request->affiliations ?? '',
                'paypal_account' => $request->paypal ?? '',
            ]);
        } else {
            $user->userInfo->update([
                'name' => $request->name,
                'contactnumber' => $request->contactnumber,
                'age' => $request->age,
                'latitude' => $request->lat,
                'longitude' => $request->long,
                'location' => $request->loc,
                'about' => $request->about,
                'disability_id' => $request->disability,
                'educational_id' => $request->education,
                'paypal_account' => $request->paypal ?? '',
            ]);
        }

        if ($request->has('socials') && $request->has('social_links')) {
            $socialLinks = $request->input('social_links', []);
            $socials = $request->input('socials', []);

            $userSocials = [];
            foreach ($socials as $index => $socialId) {
                $userSocials[] = [
                    'social_id' => $socialId,
                    'link' => $socialLinks[$index] ?? '',
                    'user_id' => $user->id,
                ];
            }
            UserSocials::where('user_id', $user->id)->delete();
            UserSocials::insert($userSocials);
        }

        return back()->with('success', 'Your profile has been changed successfully!');
    }

    public function removePicture(Request $request)
    {
        $user = auth()->user(); // Assuming user is authenticated
        if (!empty($user->userInfo->profile_path)) {
            Storage::disk('profile_photos')->delete(str_replace('storage/', '', $user->userInfo->profile_path));
        }

        $user->userInfo->profile_path = null;
        $user->userInfo->save();

        return redirect()->back()->with('success', 'Profile picture removed successfully.');
    }

    public function addExperience(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'id' => 'required|exists:user_infos,id'
        ]);

        Experience::create([
            'title' => $request->title,
            'date' => $request->date,
            'user_id' => $request->id
        ]);

        return back()->with('success', 'Work experience successfully added!');
    }

    public function deleteExperience($id)
    {
        $experience = Experience::findOrFail($id);
        $experience->delete();

        return back();
    }

    public function addSkill(Request $request)
    {
        $user = Auth::user()->id;

        $request->validate([
            'skill' => 'required|exists:skills,id',
        ]);

        SkillUser::create([
            'user_id' => $user,
            'skill_id' => $request->skill,
        ]);

        return back()->with('success', 'Skill successfully added!');
    }

    public function deleteSkill($id)
    {
        $skill = SkillUser::findOrFail($id);
        $skill->delete();

        return back()->with('success', 'Skill deleted successfully!');
    }

    public function showHomePage()
    {
        $images = [
            'images/18.png',
            'images/19.png',
            'images/20.png',
            'images/21.png',
        ];
        $pwdCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'PWD');
        })->count();

        $trainerCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Training Agency');
        })->count();

        $employerCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Employer');
        })->count();

        $sponsorCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Sponsor');
        })->count();

        return view('homepage', compact('images', 'pwdCount', 'trainerCount', 'employerCount', 'sponsorCount'));
    }

    public function showLanding()
    {
        $images = [
            'images/18.png',
            'images/19.png',
            'images/20.png',
            'images/21.png',
        ];
        $pwdCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'PWD');
        })->count();

        $trainerCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Training Agency');
        })->count();

        $employerCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Employer');
        })->count();

        $sponsorCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Sponsor');
        })->count();

        return view('landing', compact('images', 'pwdCount', 'trainerCount', 'employerCount', 'sponsorCount'));
    }

    public function showForgotPass()
    {
        return view('auth.forgotPass');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            
            $user = auth()->user()->load('userInfo');

            if($user->userInfo->registration_status == 'Activated'){
                $request->session()->regenerate();
                if (Auth::user()->hasRole('PWD')) {
                    return redirect()->intended(route('pwd-list-program'));
                } else {
                    return redirect()->intended(route('home'));
                }
            } 
            elseif($user->userInfo->registration_status == 'Pending'){
                Auth::logout();
                return redirect()->route('login-page')->withInput()->with('info', 'Your credentials are currently under review. Verification is still in progress. Thank you for your patience!');
            } 
            else{
                Auth::logout();
                return redirect()->route('login-page')->withInput()->with('info', 'Your account is currently deactivated. If you believe this is a mistake or would like to reactivate your account, please contact our support team. We are here to help!');
            }
            
        } 
        else {
            return back()->withInput()->with('error', 'The provided credentials do not match our records');
        }
    }

    public function showRegistration()
    {
        $roles = Role::all();
        $disabilities = Disability::all();
        $levels = EducationLevel::all();
        $skills = Skill::all();
        return view('auth.register', compact('roles', 'disabilities', 'levels', 'skills'));
    }

    public function register(Request $request)
    {
        if ($request->generate_email || ($request->email && $request->generate_email)) {
            $email = fake()->unique()->safeEmail();
        } else {
            $email = $request->email;
        }

        $validatedData = $request->validate([
            'password' => 'required|string|min:4|max:255|confirmed',
            'name' => 'required|string|max:255',
            'contactnumber' => 'required|string|size:11',
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
            'loc' => 'nullable|string|max:255',
            'pwd_id' => 'nullable|string|max:30',
            'age' => 'nullable|integer|min:1|max:99',
            'founder' => 'nullable|string|max:255',
            'year_established' => 'nullable|integer|min:1000|max:3000',
            'email' => 'required|email',
            'role' => 'required|exists:roles,id'            
        ]);

       if ($request->role == 2) {
            $pwdIdExists = Valid::where('valid_id_number', $request->pwd_id)->exists();
            $pwdIdUsed = UserInfo::where('pwd_id', $request->pwd_id)->exists();

            if(empty($request->pwd_id)){
                return redirect()->back()->withInput()->with('error', 'PWD ID Number is empty.');
            }

            if (!$pwdIdExists){
                return redirect()->back()->withInput()->with('error', 'The provided PWD ID Number is not valid.');
            }

            if ($pwdIdUsed) {
                return redirect()->back()->withInput()->with('error', 'The provided PWD ID Number is already registered.');
            }

            $validatedData['registration_status'] = 'Activated';

        } elseif($request->role == 3) {
            $IdUsed = UserInfo::where('pwd_id', $request->pwd_id)->exists();

            if(empty($request->pwd_id)){
                return redirect()->back()->withInput()->with('error', 'Training Provider Accreditation Number is empty.');
            }

            if ($IdUsed) {
                return redirect()->back()->withInput()->with('error', 'The provided Training Provider Accreditation Number is already registered.');
            }

            $validatedData['registration_status'] = 'Pending';

        } elseif($request->role == 4) {
            $IdUsed = UserInfo::where('pwd_id', $request->pwd_id)->exists();

            if(empty($request->pwd_id)){
                return redirect()->back()->withInput()->with('error', 'DTI Business Registration Number');
            }

            if ($IdUsed) {
                return redirect()->back()->withInput()->with('error', 'The provided DTI Business Registration Number is already registered.');
            }

            $validatedData['registration_status'] = 'Pending';
        }
        

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->role()->attach($request->role);

        UserInfo::create([
            'user_id' => $user->id,
            'disability_id' => $request->disability,
            'educational_id' => $request->education,
            'name' => $request->name,
            'contactnumber' => $request->contactnumber,
            'latitude' => $request->lat,
            'longitude' => $request->long,
            'location' => $request->loc,
            'pwd_id' => $request->pwd_id,
            'age' => $request->age ?? 0,
            'founder' => $request->founder ?? '',
            'year_established' => $request->year_established ?? 0,
            'registration_status' => $validatedData['registration_status']
        ]);

        return redirect()->route('login-page')->with('success', 'Your account has been successfully registered! Please allow up to 1 hour for the verification process. Thank you for your patience.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login-page');
    }

    public function downloadCertificate($enrolleeId)
    {
        $enrollee = Enrollee::find($enrolleeId);

        if (!$enrollee) {
            abort(404, 'Enrollee not found');
        }

        $trainingProgram = $enrollee->program;
        $user = User::find($enrollee->pwd_id);

        if (!$user) {
            abort(404, 'User not found');
        }

        $pdf = Pdf::loadView('slugs.certificate', [
            'user' => $user,
            'trainingProgram' => $trainingProgram,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('certificate-' . $user->userInfo->name . ' .pdf');
    }


    public function addPicture(Request $request)
    {
        $user = UserInfo::where('user_id', Auth::user()->id)->firstOrFail();

        $request->validate([
            'profilePic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('profilePic')) {
            $file = $request->file('profilePic');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'profile_photos/' . $fileName;

            Storage::disk('profile_photos')->put($fileName, file_get_contents($file));

            $user->profile_path = $filePath;
            $user->save();
        }

        return back()->with('success', 'Profile picture updated successfully.');
    }
}
