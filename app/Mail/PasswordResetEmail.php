<?php
declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends AbstractEmail
{
    use Queueable, SerializesModels;

    private string $receiver;

    private string $token;

    /**
     * PasswordResetEmail constructor.
     *
     * @param string $receiver
     * @param string $token
     */
    public function __construct(string $receiver, string $token)
    {
        $this->token = $token;
        $this->receiver = $receiver;

        parent::__construct();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.password_reset')
            ->with([
                'redirectUrl' => \sprintf('%s?%s', $this->pageUrl, $this->token),
                'receiver'    => $this->receiver,
                'pageUrl'     => $this->pageUrl,
            ]);
    }
}
