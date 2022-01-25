<?php
namespace App\Service\Mail;

use App\CommandBus\Auth\Register\Command as RegisterCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(RegisterCommand $registerCommand)
    {

    }

    public function build()
    {
        return $this->markdown('emails.auth.registration')
            ->subject(config('app.name') . ": Registration Notification")
            ->from(config('mail.from.address'));
    }
}
