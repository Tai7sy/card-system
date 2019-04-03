<?php

namespace App\Console;

use App\System;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
            DB::connection()->getPdo();
            if (DB::table('migrations')->count() === 0) {
                die(" * No migration table, please install your website first.\r\n");
            }
            if (!Schema::hasTable('systems')) {
                die(" * No system table, please install your website first.\r\n");
            }
        } catch (\Exception $e) {
            if (config('database.connections.mysql.host') === '数据库地址') {
                die(" * Please change your config of database in '.env' file \r\n");
            } else {
                die(" * Database connection error: " . $e->getMessage() . "\r\n");
            }
        }

        // 清理7天未支付订单
        if (System::_getInt('order_clean_unpay_open') === 1) {
            $day = System::_getInt('order_clean_unpay_day', 7);
            $schedule->call(function () use ($day) {
                echo '[' . date('Y-m-d H:i:s') . "] cleaning unpaid orders(${$day}days ago)...\n";
                \App\Order::where('status', \App\Order::STATUS_UNPAY)
                    ->where('created_at', '<', (new Carbon)->addDays(-$day))
                    ->delete();

                echo '[' . date('Y-m-d H:i:s') . "] unpaid orders cleaned \n";
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
