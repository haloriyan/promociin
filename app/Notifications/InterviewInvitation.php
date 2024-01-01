<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewInvitation extends Notification
{
    use Queueable;

    public $appointment;
    public $employer;
    public $employee;

    /**
     * Create a new notification instance.
     */
    public function __construct($props)
    {
        $this->appointment = $props['appointment'];
        $this->employee = $props['employee'];
        $this->employer = $props['employer'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Halo, ' . $this->employee->name . '!')
                    ->line('Anda baru saja mendapat undangan untuk interview dari ' . $this->employer->name . " pada " . Carbon::parse($this->appointment->dues)->isoFormat('DD MMM Y'))
                    ->line('Buka aplikasi Anda untuk menerima atau menolak undangan');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
