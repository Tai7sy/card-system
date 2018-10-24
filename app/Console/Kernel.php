<?php

namespace App\Console;

use App\Library\WeChatMessage;
use App\System;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        try {
            \DB::connection()->getPdo();
            if(\DB::table('migrations')->count() === 0){
                throw new \Exception('No migration table');
            }
            if(!\Schema::hasTable('systems')){
                throw new \Exception('No system table');
            }
        } catch (\Exception $e) {
            if (config('database.connections.mysql.host') === '数据库地址') {
                echo " * Please config your database info in '.env' \r\n";
            } else {
                echo " * Database connection error: " . $e->getMessage() . "\r\n";
            }
            return;
        }

        // 清理7天未支付订单
        if (System::_getInt('order_clean_unpay_open') === 1) {
            $day = System::_getInt('order_clean_unpay_day', 7);
            $schedule->call(function () use ($day) {
                \App\Order::where('status', \App\Order::STATUS_UNPAY)
                    ->where('created_at', '<', (new Carbon)->addDays(-$day))
                    ->delete();
            })->dailyAt('01:00');
        }
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
