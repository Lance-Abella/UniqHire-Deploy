<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\RecommenderController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\PwdController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\PaymentController;
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

    Route::get('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
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
    Route::get('/skills/edit/{skill}', [AdminController::class, 'editSkill'])->middleware('role:Admin')->name('skill-edit');
    Route::put('/skills/edit/{skill}', [AdminController::class, 'updateSkill'])->middleware('role:Admin')->name('skill-update');
    Route::delete('/skills/{skill}', [AdminController::class, 'deleteSkill'])->middleware('role:Admin')->name('skill-delete');
    Route::delete('user/{id}', [AdminController::class, 'deleteUser'])->middleware('role:Admin')->name('user-delete');
    Route::patch('/users/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->middleware('role:Admin')->name('user-toggle-status');
    Route::patch('user/{id}/set-status/{status}', [AdminController::class, 'setStatus'])->middleware('role:Admin')->name('user-set-status');
    Route::get('/disability/all', [AdminController::class, 'showDisabilities'])->middleware('role:Admin')->name('disability-list');
    Route::post('/disability/add', [AdminController::class, 'addDisability'])->middleware('role:Admin')->name('disability-add');
    Route::get('/disability/edit/{disability}', [AdminController::class, 'editDisability'])->middleware('role:Admin')->name('disability-edit');
    Route::put('/disability/edit/{disability}', [AdminController::class, 'updateDisability'])->middleware('role:Admin')->name('disability-update');
    Route::delete('/disability/{disability}', [AdminController::class, 'deleteDisability'])->middleware('role:Admin')->name('disability-delete');
    Route::get('/social-media/all', [AdminController::class, 'showSocials'])->middleware('role:Admin')->name('social-list');
    Route::post('/social-media/add', [AdminController::class, 'addSocial'])->middleware('role:Admin')->name('social-add');
    Route::get('/social-media/edit/{social}', [AdminController::class, 'editSocial'])->middleware('role:Admin')->name('social-edit');
    Route::put('/social-media/edit/{social}', [AdminController::class, 'updateSocial'])->middleware('role:Admin')->name('social-update');
    Route::delete('/social-media/{social}', [AdminController::class, 'deleteSocial'])->middleware('role:Admin')->name('social-delete');
    Route::get('/criteria/all', [AdminController::class, 'showProgramCriteria'])->middleware('role:Admin')->name('criteria-list');
    Route::get('/criteria/edit/{id}', [AdminController::class, 'editProgramCriteria'])->middleware('role:Admin')->name('criteria-edit');
    Route::put('/criteria/update', [AdminController::class, 'updateProgramCriteria'])->middleware('role:Admin')->name('criteria-update');

    //Trainer Middleware
    Route::get('/manage-program', [AgencyController::class, 'showPrograms'])->middleware('role:Training Agency,Employer')->name('programs-manage');
    Route::get('/add-program', [AgencyController::class, 'showAddForm'])->middleware('role:Training Agency,Employer')->name('programs-add');
    Route::post('/add-program', [AgencyController::class, 'addProgram'])->middleware('role:Training Agency,Employer');
    Route::get('/show-program/{id}', [AgencyController::class, 'showProgramDetails'])->middleware('role:Training Agency,Employer')->name('programs-show');
    Route::delete('/delete-program/{id}', [AgencyController::class, 'deleteProgram'])->middleware('role:Training Agency,Employer')->name('programs-delete');
    Route::get('/edit-program/{id}', [AgencyController::class, 'editProgram'])->middleware('role:Training Agency,Employer')->name('programs-edit');
    Route::put('/edit-program/{id}', [AgencyController::class, 'updateProgram'])->middleware('role:Training Agency,Employer');
    Route::get('/agency/calendar', [AgencyController::class, 'showCalendar'])->middleware('role:Training Agency,Employer')->name('agency-calendar');
    // Route::post('/agency/action', [AgencyController::class, 'action'])->middleware('role:Training Agency')->name('agency-action');
    Route::post('/agency/accept', [AgencyController::class, 'accept'])->middleware('role:Training Agency,Employer')->name('agency-accept');
    Route::delete('/agency/deny/{trainid}', [AgencyController::class, 'deny'])->middleware('role:Training Agency,Employer')->name('agency-deny');
    Route::post('/agency/mark-complete', [AgencyController::class, 'markComplete'])->middleware('role:Training Agency,Employer')->name('mark-complete');
    Route::put('/programs/{id}/status/{status}', [AgencyController::class, 'updateProgramStatus'])->middleware('role:Training Agency,Employer')->name('programs.update-status');
    Route::post('/programs/{id}/cancel', [AgencyController::class, 'cancelProgram'])->middleware('role:Training Agency,Employer')->name('programs.cancel');


    // PWD Middleware
    // Route::post('/pwd/action', [PwdController::class, 'action'])->middleware('role:PWD')->name('pwd-action');
    Route::get('/browse/training-programs', [RecommenderController::class, 'showPrograms'])->middleware('role:PWD')->name('pwd-list-program');
    Route::get('/training-details/{id}', [PwdController::class, 'showDetails'])->middleware('role:PWD')->name('training-details');
    Route::post('/training-details/{id}', [PwdController::class, 'showDetails'])->middleware('role:PWD')->name('training-details');
    Route::get('/pwd/calendar', [PwdController::class, 'showCalendar'])->middleware('role:PWD')->name('pwd-calendar');
    Route::post('/training-program/apply', [PwdController::class, 'application'])->middleware('role:PWD')->name('pwd-application');
    Route::post('/job/apply', [PwdController::class, 'jobApplication'])->middleware('role:PWD')->name('pwd-jobApplication');
    Route::get('/track-trainings', [PwdController::class, 'showTrainings'])->middleware('role:PWD')->name('trainings');
    Route::get('/track-trainings/{id}', [PwdController::class, 'showDetails'])->middleware('role:PWD')->name('show-details');
    Route::post('/training-program/rate', [PwdController::class, 'rateProgram'])->middleware('role:PWD')->name('rate-program');
    Route::get('/browse/job-postings', [RecommenderController::class, 'showJobs'])->middleware('role:PWD')->name('pwd-list-job');
    Route::get('/job-details/{id}', [PwdController::class, 'showListingDetails'])->middleware('role:PWD')->name('job-details');
    Route::post('/job-details/{id}', [PwdController::class, 'showListingDetails'])->middleware('role:PWD')->name('job-details');
    Route::get('/events', [PwdController::class, 'showEvents'])->middleware('role:PWD')->name('events');
    Route::post('/events/apply', [PwdController::class, 'eventApplication'])->middleware('role:PWD')->name('event-application');
    Route::get('/track-jobs', [PwdController::class, 'showJobs'])->middleware('role:PWD')->name('jobs');
    Route::get('/track-jobs/{id}', [PwdController::class, 'showListingDetails'])->middleware('role:PWD')->name('show-job-details');


    //SPONSOR Middleware
    Route::get('/browse/list-of-programs', [SponsorController::class, 'showTrainingLists'])->middleware('role:Sponsor,Employer')->name('list-of-tp');
    Route::get('/trainingprogram-details/{id}', [SponsorController::class, 'showProgDetails'])->middleware('role:Sponsor,Employer')->name('trainingprog-details');
    Route::post('/trainingprogram-details/{id}', [SponsorController::class, 'showProgDetails'])->middleware('role:Sponsor,Employer')->name('trainingprog-details');
    Route::get('/training-program/{id}', [SponsorController::class, 'showProgDetails'])->middleware('role:Sponsor,Employer')->name('show-progdetails');
    Route::post('/training-program/payment', [SponsorController::class, 'payment'])->middleware('role:Sponsor,Employer')->name('payment');
    Route::post('/payment', [PaymentController::class, 'payment'])->middleware('role:Sponsor,Employer')->name('payment');
    Route::get('/payment/success', [PaymentController::class, 'success'])->middleware('role:Sponsor,Employer')->name('payment-success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->middleware('role:Sponsor,Employer')->name('payment-cancel');
    Route::get('/payment-history', [SponsorController::class, 'showTransactions'])->middleware('role:Sponsor,Employer')->name('payment-history');



    // Employer Middleware
    Route::get('/manage-jobs', [EmployerController::class, 'manageJobs'])->middleware('role:Employer')->name('manage-jobs');
    Route::get('/add-job', [EmployerController::class, 'showAddForm'])->middleware('role:Employer')->name('add-job');
    Route::post('/add-job', [EmployerController::class, 'addJob'])->middleware('role:Employer')->name('job-add');
    Route::get('/show-job/{id}', [EmployerController::class, 'showJobDetails'])->middleware('role:Employer')->name('jobs-show');
    Route::delete('/delete-job/{id}', [EmployerController::class, 'deleteJob'])->middleware('role:Employer')->name('jobs-delete');
    Route::get('/edit-job/{id}', [EmployerController::class, 'editJob'])->middleware('role:Employer')->name('jobs-edit');
    Route::put('/edit-job/{id}', [EmployerController::class, 'updateJob'])->middleware('role:Employer');
    Route::post('/employer/accept', [EmployerController::class, 'accept'])->middleware('role:Employer')->name('employer-accept');
    Route::delete('/employer/deny/{jobid}', [EmployerController::class, 'deny'])->middleware('role:Employer')->name('employer-deny');
    Route::get('/employer/calendar', [EmployerController::class, 'showCalendar'])->middleware('role:Employer')->name('employer-calendar');
    Route::post('/employer/mark-complete', [EmployerController::class, 'markHired'])->middleware('role:Employer')->name('mark-hired');
    Route::get('/post-events', [EmployerController::class, 'showEvents'])->middleware('role:Employer')->name('show-post-events');
    Route::post('/post-events', [EmployerController::class, 'postEvent'])->middleware('role:Employer')->name('post-events');
    Route::delete('/delete-event/{id}', [EmployerController::class, 'deleteEvent'])->middleware('role:Employer')->name('delete-event');
    Route::get('/employer/set-schedule/{id}', [EmployerController::class, 'setScheduleForm'])->middleware('role:Employer')->name('set-schedule');
    Route::post('/employer/set-schedule/{id}', [EmployerController::class, 'setSchedule'])->middleware('role:Employer')->name('set-schedule');
    Route::put('/jobs/{id}/status/{status}', [EmployerController::class, 'updateJobStatus'])->middleware('role:Employer')->name('jobs.update-status');
    Route::post('/jobs/{id}/cancel', [EmployerController::class, 'cancelJob'])->middleware('role:Employer')->name('jobs.cancel');
});
