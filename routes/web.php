<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\PwdController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\NotificationController;
use App\Models\UserInfo;
use App\Models\TrainingProgram;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login-page');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/forgot-password', [AuthController::class, 'showForgotPass'])->name('forgot-password');


Route::get('/register', [AuthController::class, 'showRegistration'])->name('register-form');
Route::post('/register', [AuthController::class, 'register'])->name('register');


Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [AuthController::class, 'showLanding'])->name('landing');

Route::middleware('auth')->group(function () {
    Route::get('/home', [AuthController::class, 'showHomePage'])->name('home');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::delete('/profile/delete-experiences/{id}', [AuthController::class, 'deleteExperience'])->name('delete-experience');
    Route::post('/profile/add-experiences', [AuthController::class, 'addExperience'])->name('add-experience');
    Route::delete('/profile/delete-skill/{id}', [AuthController::class, 'deleteSkill'])->name('delete-skill');
    Route::post('/profile/add-skill', [AuthController::class, 'addSkill'])->name('add-skill');
    Route::put('/profile', [AuthController::class, 'editProfile'])->name('edit-profile');
    Route::post('/profile/remove-pic', [AuthController::class, 'removePicture'])->name('remove-pic');

    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.getNotifications');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    Route::get('/show-program/{id}', [AgencyController::class, 'showProgramDetails'])->name('programs-show');
    Route::get('/download-certificate/{enrolleeId}', [AuthController::class, 'downloadCertificate'])->name('download-certificate');


    Route::get('/user/{id}', [AgencyController::class, 'showEnrolleeProfile'])->name('show-profile');


    //Admin Middleware
    Route::get('/pwd/all', [AdminController::class, 'showPwds'])->middleware('role:Admin')->name('pwd-list');
    Route::get('/training-agency/all', [AdminController::class, 'showTrainers'])->middleware('role:Admin')->name('trainer-list');
    Route::get('/employee/all', [AdminController::class, 'showEmployers'])->middleware('role:Admin')->name('employee-list');
    Route::get('/sponsor/all', [AdminController::class, 'showSponsors'])->middleware('role:Admin')->name('sponsor-list');
    Route::get('/skill/all', [AdminController::class, 'showSkills'])->middleware('role:Admin')->name('skill-list');
    Route::post('/skill/add', [AdminController::class, 'addSkill'])->middleware('role:Admin')->name('skill-add');
    Route::get('skills/edit/{skill}', [AdminController::class, 'editSkill'])->middleware('role:Admin')->name('skill-edit');
    Route::put('skills/edit/{skill}', [AdminController::class, 'updateSkill'])->middleware('role:Admin')->name('skill-update');
    Route::delete('skills/{skill}', [AdminController::class, 'deleteSkill'])->middleware('role:Admin')->name('skill-delete');
    Route::delete('user/{id}', [AdminController::class, 'deleteUser'])->middleware('role:Admin')->name('user-delete');


    //Trainer Middleware
    Route::get('/manage-program', [AgencyController::class, 'showPrograms'])->middleware('role:Training Agency')->name('programs-manage');
    Route::get('/add-program', [AgencyController::class, 'showAddForm'])->middleware('role:Training Agency')->name('programs-add');
    Route::post('/add-program', [AgencyController::class, 'addProgram'])->middleware('role:Training Agency');
    Route::get('/show-program/{id}', [AgencyController::class, 'showProgramDetails'])->middleware('role:Training Agency')->name('programs-show');
    Route::delete('/delete-program/{id}', [AgencyController::class, 'deleteProgram'])->middleware('role:Training Agency')->name('programs-delete');
    Route::get('/edit-program/{id}', [AgencyController::class, 'editProgram'])->middleware('role:Training Agency')->name('programs-edit');
    Route::put('/edit-program/{id}', [AgencyController::class, 'updateProgram'])->middleware('role:Training Agency');
    Route::get('/agency/calendar', [AgencyController::class, 'showCalendar'])->middleware('role:Training Agency')->name('agency-calendar');
    // Route::post('/agency/action', [AgencyController::class, 'action'])->middleware('role:Training Agency')->name('agency-action');
    Route::post('/agency/accept', [AgencyController::class, 'accept'])->middleware('role:Training Agency')->name('agency-accept');
    Route::post('/agency/mark-complete', [AgencyController::class, 'markComplete'])->middleware('role:Training Agency')->name('mark-complete');


    // PWD Middleware
    // Route::post('/pwd/action', [PwdController::class, 'action'])->middleware('role:PWD')->name('pwd-action');
    Route::get('/browse/training-programs', [PwdController::class, 'showPrograms'])->middleware('role:PWD')->name('pwd-list-program');
    Route::get('/training-details/{id}', [PwdController::class, 'showDetails'])->middleware('role:PWD')->name('training-details');
    Route::post('/training-details/{id}', [PwdController::class, 'showDetails'])->middleware('role:PWD')->name('training-details');
    Route::get('/pwd/calendar', [PwdController::class, 'showCalendar'])->middleware('role:PWD')->name('pwd-calendar');
    Route::post('/training-program/apply', [PwdController::class, 'application'])->middleware('role:PWD')->name('pwd-application');
    Route::get('/training-programs', [PwdController::class, 'showTrainings'])->middleware('role:PWD')->name('trainings');
    Route::get('/training-program/details/{id}', [PwdController::class, 'showDetails'])->middleware('role:PWD')->name('show-details');
    Route::post('/training-program/rate', [PwdController::class, 'rateProgram'])->middleware('role:PWD')->name('rate-program');
    Route::get('/browse/job-postings', [PwdController::class, 'showJobs'])->middleware('role:PWD')->name('pwd-list-job');
    Route::get('/job-details/{id}', [PwdController::class, 'showListingDetails'])->middleware('role:PWD')->name('job-details');
    Route::post('/job-details/{id}', [PwdController::class, 'showListingDetails'])->middleware('role:PWD')->name('job-details');


    //SPONSOR Middleware
    Route::get('/browse/list-of-programs', [SponsorController::class, 'showTrainingLists'])->middleware('role:Sponsor')->name('list-of-tp');
    Route::get('/trainingprogram-details/{id}', [SponsorController::class, 'showProgDetails'])->middleware('role:Sponsor')->name('trainingprog-details');
    Route::post('/trainingprogram-details/{id}', [SponsorController::class, 'showProgDetails'])->middleware('role:Sponsor')->name('trainingprog-details');
    Route::get('/training-program/{id}', [SponsorController::class, 'showProgDetails'])->middleware('role:Sponsor')->name('show-progdetails');
    Route::post('/training-program/payment', [SponsorController::class, 'payment'])->middleware('role:Sponsor')->name('payment');


    // Employer Middleware
    Route::get('/manage-jobs', [EmployerController::class, 'manageJobs'])->middleware('role:Employer')->name('manage-jobs');
    Route::get('/add-job', [EmployerController::class, 'showAddForm'])->middleware('role:Employer')->name('add-job');
    Route::post('/add-job', [EmployerController::class, 'addJob'])->middleware('role:Employer')->name('job-add');
    Route::get('/show-job/{id}', [EmployerController::class, 'showJobDetails'])->name('jobs-show');
});
