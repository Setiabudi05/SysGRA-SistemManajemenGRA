<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SistemNotifikasi extends Notification
{
    use Queueable;

    private $dataNotif;

    public function __construct($dataNotif)
    {
        $this->dataNotif = $dataNotif;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'judul' => $this->dataNotif['judul'],
            'pesan' => $this->dataNotif['pesan'],
            'icon'  => $this->dataNotif['icon'] ?? 'bi-bell',
            'link'  => $this->dataNotif['link'] ?? '#',
        ];
    }
}