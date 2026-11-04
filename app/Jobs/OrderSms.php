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

    /** @var string|null $order */
    private $mobile = null;

    /** @var \App\Order|null $order */
    private $order = null;

    /**
     * Create a new job instance.
     *
     * @param string $mobile
     * @param \App\Order $order
     */
    public function __construct($mobile, $order)
    {
        $this->mobile = $mobile;
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
            'to' => $this->mobile,
            'msg' => config('app.url') . '/pay/result/' . $this->order->order_no
        ]));
    }
}
