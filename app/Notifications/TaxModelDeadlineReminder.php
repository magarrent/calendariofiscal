<?php

namespace App\Notifications;

use App\Models\Deadline;
use App\Models\TaxModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaxModelDeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TaxModel $taxModel,
        public Deadline $deadline,
        public int $daysUntilDeadline
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysText = $this->daysUntilDeadline === 1 ? '1 día' : "{$this->daysUntilDeadline} días";

        return (new MailMessage)
            ->subject("Recordatorio: {$this->taxModel->name} - Modelo {$this->taxModel->model_number}")
            ->greeting("Hola {$notifiable->name},")
            ->line("Este es un recordatorio de que la fecha límite para el **Modelo {$this->taxModel->model_number}** ({$this->taxModel->name}) se acerca.")
            ->line("**Días restantes:** {$daysText}")
            ->line("**Fecha límite:** {$this->deadline->deadline_date->format('d/m/Y')}")
            ->when($this->deadline->deadline_time, function ($mail) {
                return $mail->line("**Hora límite:** {$this->deadline->deadline_time}");
            })
            ->action('Ver Calendario', url('/'))
            ->line('No olvides completar este modelo a tiempo para evitar sanciones.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tax_model_id' => $this->taxModel->id,
            'tax_model_number' => $this->taxModel->model_number,
            'tax_model_name' => $this->taxModel->name,
            'deadline_id' => $this->deadline->id,
            'deadline_date' => $this->deadline->deadline_date->format('Y-m-d'),
            'deadline_time' => $this->deadline->deadline_time,
            'days_until_deadline' => $this->daysUntilDeadline,
        ];
    }
}
