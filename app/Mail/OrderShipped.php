<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    /** 重试 3 次. */
    public $tries = 3;

    /** 超时时间。 */
    public $timeout = 20;


    public $order_no;
    public $order_url;
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
        $this->order_no = $order->order_no;
        $this->order_url = config('app.url') . route('pay.result', [$order->order_no], false);
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
        return $this->subject('订单提醒(#' . $this->order_no . ')-' . config('app.name'))
            ->view('emails.order');
    }
}
