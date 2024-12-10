<?php

namespace App\Notifications;

use App\Models\TrainingProgram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewTrainingProgramNotification extends Notification
{
    use Queueable;

    protected $trainingProgram;

    /**
     * Create a new notification instance.
     */
    public function __construct(TrainingProgram $trainingProgram)
    {
        $this->trainingProgram = $trainingProgram;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('A new training program has been added.')
            ->action('View Program', url('/training-details/' . $this->trainingProgram->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        $creator = $this->trainingProgram->agency->hasRole('Employer') ? 'Employer' : 'Training Agency';

        return [
            'title' => $this->trainingProgram->title,
            'description' => $this->trainingProgram->description,
            'start_date' => $this->trainingProgram->schedule,
            'training_program_id' => $this->trainingProgram->id,
            'agency_name' => $this->trainingProgram->agency->userInfo->name,
            'creator_type' => $creator,
            'url' => url('training-details/' . $this->trainingProgram->id),
        ];
    }
}
