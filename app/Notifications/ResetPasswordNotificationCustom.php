<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ResetPasswordNotificationCustom extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('🔐 Atur Ulang Kata Sandi - SysGRA')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun SysGRA Anda.')
            ->line(new HtmlString('Silakan klik tombol di bawah ini untuk melanjutkan proses pemulihan akun:'))
            ->action('Atur Ulang Password', $url)
            ->line('Tautan ini hanya berlaku selama **60 menit** demi keamanan akun Anda.')
            ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini dan pastikan akun Anda tetap aman.')
            ->salutation(new HtmlString('Salam Hangat,<br><strong>Tim IT Griya Rias Asmara</strong>'));
    }
}