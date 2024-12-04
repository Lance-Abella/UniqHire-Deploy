<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications;
use App\Models\TrainingProgram;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $user = auth()->user();
        $notificationsQuery = $user->unreadNotifications;

        if ($user->hasRole('PWD')) {
            $notifications = $notificationsQuery->filter(function ($notifications) {
                return in_array($notifications->type, [
                    'App\\Notifications\\NewTrainingProgramNotification',
                    'App\\Notifications\\ApplicationAcceptedNotification',
                    'App\\Notifications\\TrainingCompletedNotification',
                    'App\\Notifications\\NewJobListingNotification',
                    'App\\Notifications\\JobApplicationAcceptedNotification',
                ]);
            });
        } else if ($user->hasRole('Training Agency')) {
            $notifications = $notificationsQuery->filter(function ($notifications) {
                return in_array($notifications->type, [
                    'App\\Notifications\\PwdApplicationNotification',
                    'App\\Notifications\\SponsorDonationNotification',
                ]);
            });
        } else if ($user->hasRole('Employer')) {
            $notifications = $notificationsQuery->filter(function ($notifications) {
                return in_array($notifications->type, [
                    'App\\Notifications\\PwdJobApplicationNotification',
                ]);
            });
        }


        return response()->json($notifications->toArray());
    }

    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');
        $notification = Auth::user()->unreadNotifications->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $unreadCount = Auth::user()->unreadNotifications->count();
            return response()->json(['status' => 'success', 'unread_count' => $unreadCount]);
        }

        return response()->json(['status' => 'error'], 404);
    }
}
