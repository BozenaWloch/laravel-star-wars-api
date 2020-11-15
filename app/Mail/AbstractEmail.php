<?php
declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $pageUrl;

    /**
     * AbstractEmail constructor.
     */
    public function __construct()
    {
        $this->pageUrl = \rtrim(config('app.main_page_url'), '/');
    }
}
