<?php

namespace App\Jobs;

use App\Library\CurlRequest;
use App\System;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \App\Order|null $order */
    private $order = null;


    /**
     * Create a new job instance.
     *
     * @param \App\Order $order
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CurlRequest::post('https://api.his.cat/api/sms/send', http_build_query([
            'id' => System::_get('sms_api_id'),
            'key' => System::_get('sms_api_key'),
            'to' => @json_decode($this->order->contact_ext, true)['_sms'],
            'msg' => config('app.url') . '/pay/result/' . $this->order->order_no
        ]));
    }
}
