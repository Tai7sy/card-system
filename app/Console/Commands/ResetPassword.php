<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Reset the password of user\r\nUsage: php artisan reset:password user@email.com";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email');

        if (!$email) {
            $this->warn("please input the user's email\n");
            return false;
        }
        $user = \App\User::where('email', $email)->first();
        if (!$user) {
            $this->warn("can't find the user: $email \nplease input the user's email\n");
            return false;
        }

        $password = $this->argument('password');
        $user->password = bcrypt($password);
        $user->save();

        $this->info("the password of '$email' has been set to $password\n");
        return true;
    }
}
