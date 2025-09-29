<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatronymeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $patronyme;
    protected $type;

    public function __construct($patronyme, $type = 'created')
    {
        $this->patronyme = $patronyme;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'patronyme_id' => $this->patronyme->id,
            'patronyme_nom' => $this->patronyme->nom,
            'type' => $this->type,
            'message' => $this->getMessage(),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->line($this->getMessage())
            ->action('Voir le patronyme', route('patronymes.show', $this->patronyme));
    }

    private function getMessage()
    {
        switch ($this->type) {
            case 'created':
                return "Un nouveau patronyme '{$this->patronyme->nom}' a été ajouté.";
            case 'updated':
                return "Le patronyme '{$this->patronyme->nom}' a été modifié.";
            case 'approved':
                return "Votre contribution pour '{$this->patronyme->nom}' a été approuvée.";
            default:
                return "Une action a été effectuée sur le patronyme '{$this->patronyme->nom}'.";
        }
    }

    private function getSubject()
    {
        switch ($this->type) {
            case 'created':
                return 'Nouveau patronyme ajouté';
            case 'updated':
                return 'Patronyme modifié';
            case 'approved':
                return 'Contribution approuvée';
            default:
                return 'Notification patronyme';
        }
    }
}
