<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Update extends Command
{
    protected $signature = 'update';

    protected $description = 'Show the source installation update steps';

    public function handle(): int
    {
        $this->info('源码版请通过 Git 更新。此命令不会覆盖文件或修改数据库。');
        $this->warn('请先备份数据库和 .env，并处理工作区中的本地修改。');
        foreach ([
            'git pull --ff-only',
            'git submodule update --init --recursive',
            'composer install --no-dev --optimize-autoloader',
            'npm ci',
            'npm run build',
            'php artisan migrate --force',
            'php artisan optimize:clear',
        ] as $command) {
            $this->line($command);
        }

        return self::SUCCESS;
    }
}
