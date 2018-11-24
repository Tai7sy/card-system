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
    public $card_msg;
    public $cards_txt;

    /**
     * Create a new message instance.
     *
     * @param \App\Order $order
     * @param $card_msg
     * @param $cards_txt
     */
    public function __construct($order, $card_msg, $cards_txt)
    {
        $this->order = $order;
        $this->card_msg = $card_msg;
        $this->cards_txt = $cards_txt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('app.name') . '-订单提醒 #' . $this->order->order_no)
            ->view('emails.order');
    }
}
