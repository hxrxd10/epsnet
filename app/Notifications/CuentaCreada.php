<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso a quien DIGEU le creó una cuenta, con el enlace para definir su contraseña.
 */
class CuentaCreada extends Notification
{
    use Queueable;

    public function __construct(public string $token, public string $unidad) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $minutos = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Tu cuenta en '.config('app.name'))
            ->greeting("Hola, {$notifiable->name}")
            ->line('La Dirección General de Extensión Universitaria (DIGEU) te creó una cuenta en '.config('app.name')." para la {$this->unidad}.")
            ->line('Para entrar, define tu contraseña con el siguiente botón.')
            ->action('Definir mi contraseña', route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]))
            ->line("El enlace vence en {$minutos} minutos. Si vence, usa «¿Olvidaste tu contraseña?» en la página de inicio de sesión para recibir uno nuevo.");
    }
}
