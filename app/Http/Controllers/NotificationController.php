<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use App\Models\User;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $user = User::with('userInfo')->find(auth()->id());
        $notificationsQuery = $user->notifications;

        if ($user->hasRole('PWD')) {
            $notifications = $notificationsQuery->filter(function ($notification) {
                return in_array($notification->type, [
                    'App\\Notifications\\NewTrainingProgramNotification',
                    'App\\Notifications\\ApplicationAcceptedNotification',
                    'App\\Notifications\\TrainingCompletedNotification',
                    'App\\Notifications\\NewJobListingNotification',
                    'App\\Notifications\\JobApplicationAcceptedNotification',
                    'App\\Notifications\\JobHiredNotification',
                    'App\\Notifications\\SetScheduleNotification',
                    'App\\Notifications\\SetEventsNotification',
                ]);
            });
        } else if ($user->hasRole('Training Agency')) {
            $notifications = $notificationsQuery->filter(function ($notification) {
                return in_array($notification->type, [
                    'App\\Notifications\\PwdApplicationNotification',
                    'App\\Notifications\\SponsorDonationNotification',
                ]);
            });
        } else if ($user->hasRole('Employer')) {
            $notifications = $notificationsQuery->filter(function ($notification) {
                return in_array($notification->type, [
                    'App\\Notifications\\PwdApplicationNotification',
                    'App\\Notifications\\PwdJobApplicationNotification',
                ]);
            });
        } else {
            $notifications = $notificationsQuery;
        }

        $formattedNotifications = $notifications->map(function ($notification) {
            $notificationArray = $notification->toArray();
            $notificationArray['read'] = !is_null($notification->read_at);
            return $notificationArray;
        });

        return response()->json($formattedNotifications);
    }

    public function markAsRead(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:notifications,id',
        ]);
        $user = User::with('userInfo')->find(auth()->id());
        $notification = $user->notifications()
            ->where('id', $validated['id'])
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $unreadCount = auth()->user()->unreadNotifications->count();

            return response()->json([
                'status' => 'success',
                'unread_count' => $unreadCount,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Notification not found',
        ], 404);
    }
}
