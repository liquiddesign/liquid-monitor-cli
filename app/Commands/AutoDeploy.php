<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;

class AutoDeploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-deploy';

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
        $response = Http::post(env('API_HOST') . '/deploy/is-deploy', [
            'apiKey' => env('API_KEY'),
        ]);

        if ($response->status() !== 200) {
            return;
        }

        $deploy = $response->json();

        $response = Http::acceptJson()->post(env('API_HOST') . '/deploy/start-deploy', [
            'apiKey' => env('API_KEY'),
            'deployId' => $deploy['data']['id'],
        ]);

        if ($response->status() !== 200) {
            Log::error('Deploy failed to start:' . $response);
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->everyMinute();
    }
}
