<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmationsExpiringToday extends Notification
{
    use Queueable;

    /**
     * The confirmations which expired
     * @var Collection
     */
    public $confirmations;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Collection $confirmations)
    {
        $this->confirmations = $confirmations;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $today = Carbon::parse('today')->format('D, M j, Y');

        return (new MailMessage)
                ->subject('Confirmations Expiring Today, '.$today)
                ->markdown('mail.confirmations.expiring', [
                    'today' => $today,
                    'confirmations' => $this->confirmations,
                ]);
    }
}
