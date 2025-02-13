<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $option = $this->menu('Liquid Monitor CLI - Deploy', [
            'Deploy all',
            'Deploy only frontend',
            'Deploy only backend',
        ])->open();

        $this->info("You have chosen the option number #$option");

        switch ($option) {
            case 0:
                $this->task('Deploying all', function () {
                    $resultCode = null;
                    $result = \passthru(\env('DEPLOY_ALL_SCRIPT'), $resultCode);

                    dump($result);

                    return $resultCode === 0;
                });

                break;
            case 1:
                $this->task('Deploying frontend', function () {
                    $resultCode = null;
                    $result = \passthru(\env('DEPLOY_FRONT_SCRIPT'), $resultCode);

                    dump($result);

                    return $resultCode === 0;
                });

                break;
            case 2:
                $this->task('Deploying backend', function () {
                    $resultCode = null;
                    $result = \passthru(\env('DEPLOY_BACK_SCRIPT'), $resultCode);

                    dump($result);

                    return $resultCode === 0;
                });

                break;
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
