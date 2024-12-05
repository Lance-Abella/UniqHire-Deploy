<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use App\Models\TrainingProgram;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $user = auth()->user();
        $notificationsQuery = $user->notifications->where('read', false);

        if ($user->hasRole('PWD')) {
            $notifications = $notificationsQuery->filter(function ($notifications) {
                return in_array($notifications->type, [
                    'App\\Notifications\\NewTrainingProgramNotification',
                    'App\\Notifications\\ApplicationAcceptedNotification',
                    'App\\Notifications\\TrainingCompletedNotification',
                    'App\\Notifications\\NewJobListingNotification',
                    'App\\Notifications\\JobApplicationAcceptedNotification',
                    'App\\Notifications\\JobHiredNotification',
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
        } else {
            $notifications = $notificationsQuery;
        }


        return response()->json($notifications->toArray());
    }

    public function markAsRead(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:notifications,id',
        ]);

        $notification = auth()->user()->notifications->find($validated['id']);

        if ($notification) {
            // Mark the notification as read
            $notification->markAsRead();

            // Get the updated unread notifications count
            $unreadCount = auth()->user()->unreadNotifications->count();

            return response()->json([
                'status' => 'success',
                'unread_count' => $unreadCount, // Send the updated unread count
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Notification not found',
        ]);
    }
}
