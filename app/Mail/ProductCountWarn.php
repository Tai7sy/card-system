<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductCountWarn extends Mailable
{
    use Queueable, SerializesModels;

    public $product = null;
    public $count = null;

    /**
     * Create a new message instance.
     *
     * @param \App\Product $product
     * @param int $count
     */
    public function __construct($product, $count)
    {
        $this->product = $product;
        $this->count = $count;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('app.name') . '-库存预警 #' . $this->product->name)
            ->view('emails.product_count_warn');
    }
}
