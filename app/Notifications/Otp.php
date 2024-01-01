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
            $this->subject = "Selamat Datang, ".$props['user']->name;
            $this->purpose = "melanjutkan pendaftaran";
        } else if ($purp == 'login') {
            $this->subject = "Halo Lagi, " . $props['user']->name;
            $this->purpose = "melanjutkan login ke aplikasi";
        } else if ($purp == 'reset_password') {
            $this->subject = "Atur Ulang Password";
            $this->purpose = "mengatur ulang password";
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
                    ->greeting('Halo, ' . $this->user->name)
                    ->line('Untuk ' . $this->purpose . ", mohon masukkan 4 angka berikut ini pada aplikasi")
                    ->line(
                        new HtmlString('<div style="font-size: 42px;font-weight: 700;margin-bottom: 40px;color: #2196f3;">' . $this->otp->code . '</div>')
                    )
                    ->line('Jika Anda merasa tidak melakukan ini, mohon untuk segera mengganti kata sandi Anda!');
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
