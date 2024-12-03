<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TrainingApplication;
use App\Models\TrainingProgram;

class PwdApplicationNotification extends Notification
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
            'url' => url('/show-program/' . $this->trainingProgram->id),
        ];
    }
}
