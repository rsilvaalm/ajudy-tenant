<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SetPasswordNotification extends Notification
{
    public function __construct(
        private string $token,
        private string $tenantName
    ) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/cadastrar-senha?token={$this->token}&email=" . urlencode($notifiable->email));

        return (new MailMessage)
            ->subject("Bem-vindo ao {$this->tenantName} — Cadastre sua senha")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("Uma conta foi criada para você no sistema **{$this->tenantName}**.")
            ->line("Clique no botão abaixo para cadastrar sua senha e ativar seu acesso.")
            ->action('Cadastrar minha senha', $url)
            ->line("Este link expira em 24 horas.")
            ->line("Se você não solicitou este acesso, ignore este e-mail.")
            ->salutation("Equipe {$this->tenantName}");
    }
}
