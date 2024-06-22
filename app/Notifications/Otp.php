<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class Otp extends Notification
{
    use Queueable;

    public $otp;
    public $user;
    public $purpose;
    public $subject;

    /**
     * Create a new notification instance.
     */
    public function __construct($props)
    {
        $this->user = $props['user'];
        $this->otp = $props['otp'];
        $purp = $props['otp']->purpose;

        if ($purp == 'register') {
            $this->subject = "Welcome, ".$props['user']->name;
            $this->purpose = "continuing registration";
        } else if ($purp == 'login') {
            $this->subject = "Hello (again), " . $props['user']->name;
            $this->purpose = "logging into Promociin app";
        } else if ($purp == 'reset_password') {
            $this->subject = "Reset Password";
            $this->purpose = "resetting password";
        }
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
                    ->subject($this->subject)
                    ->greeting('Hi, ' . $this->user->name)
                    ->line('For ' . $this->purpose . ", please insert this 4 digits number on your screen")
                    ->line(
                        new HtmlString('<div style="font-size: 42px;font-weight: 700;margin-bottom: 40px;color: #2196f3;">' . $this->otp->code . '</div>')
                    )
                    ->line('If you feel did not doing this, please change your password soon!');
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
