<?php

namespace App\Console;

use App\System;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];


    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (!app()->runningInConsole()) {
            return;
        }

        // 清理7天未支付订单
        if (System::_getInt('order_clean_unpay_open') === 1) {
            $day = System::_getInt('order_clean_unpay_day', 7);
            $schedule->call(function () use ($day) {
                echo '[' . date('Y-m-d H:i:s') . "] cleaning unpaid orders($day days ago)...\n";
                \App\Order::where('status', \App\Order::STATUS_UNPAY)
                    ->where('created_at', '<', (new Carbon)->addDays(-$day))
                    ->delete();

                $message = '[' . date('Y-m-d H:i:s') . "] unpaid-orders cleaned \n";
                // Log::alert($message);
                echo $message;
            })->dailyAt('01:00');
        }

        // 清理回收站卡密
        $schedule->call(function () {
            $day = 7;
            echo '[' . date('Y-m-d H:i:s') . "] cleaning deleted cards($day days ago)...\n";
            \App\Card::onlyTrashed()
                ->where('deleted_at', '<', (new Carbon)->addDays(-$day))
                ->forceDelete();
            $message = '[' . date('Y-m-d H:i:s') . "] deleted-cards cleaned\n";
            // Log::alert($message);
            echo $message;
        })->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        /** @noinspection PhpIncludeInspection */
        require base_path('routes/console.php');
    }
}
