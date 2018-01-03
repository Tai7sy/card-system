<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;


    public $order;
    public $msg;

    /**
     * Create a new message instance.
     *
     * @param $order
     */
    public function __construct($order, $msg)
    {
        $this->order = $order;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('app.name') . ' - 订单提醒')
            ->view('emails.order');
    }
}
