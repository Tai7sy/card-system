<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductCountWarn extends Mailable
{
    use Queueable, SerializesModels;

    /** 重试 3 次. */
    public $tries = 3;

    /** 超时时间。 */
    public $timeout = 20;


    public $product = null;
    public $product_count = null;

    /**
     * Create a new message instance.
     *
     * @param \App\Product $product
     * @param int $product_count
     */
    public function __construct($product, $product_count)
    {
        $this->product = $product;
        $this->product_count = $product_count;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('您的商品库存不足-' . config('app.name'))
            ->view('emails.product_count_warn');
    }
}
