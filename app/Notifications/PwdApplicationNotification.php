<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\TrainingProgram;

class PwdApplicationNotification extends Notification
{
    use Queueable;

    protected $trainingProgram;
    protected $applicant;

    /**
     * Create a new notification instance.
     */
    public function __construct(TrainingProgram $trainingProgram, User $applicant)
    {
        $this->trainingProgram = $trainingProgram;
        $this->applicant = $applicant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable)
    {

        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('A PWD user has applied for your training program.')
            ->action('View Application', url('/show-program/' . $this->trainingProgram->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->trainingProgram->title,
            'training_program_id' => $this->trainingProgram->id,
            'applicant_name' => $this->applicant->userInfo->name,
            'applicant_id' => $this->applicant->id,
            'url' => url('/show-program/' . $this->trainingProgram->id),
        ];
    }
}
