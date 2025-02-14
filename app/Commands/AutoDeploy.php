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
    protected $description = 'Auto deploy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->task('Auto deploy', function () {
            $response = Http::post(env('API_HOST') . '/deploy/is-deploy', [
                'apiKey' => env('API_KEY'),
            ]);

            Log::debug($response->status());

            if ($response->status() !== 200) {
                return;
            }

            $deploy = $response->json();
            Log::debug($deploy);

            $response = Http::acceptJson()->post(env('API_HOST') . '/deploy/start-deploy', [
                'apiKey' => env('API_KEY'),
                'deployId' => $deploy['data']['id'],
            ]);

            if ($response->status() !== 200) {
                Log::error('Deploy failed to start:' . $response);
            }

            $resultCode = null;
            \ob_start();
            \passthru(env('DEPLOY_BACK_SCRIPT'), $resultCode);
            $result = \ob_get_contents();
            \ob_end_clean();

            $response = Http::acceptJson()->post(env('API_HOST') . '/deploy/deploy-done', [
                'apiKey' => env('API_KEY'),
                'deployId' => $deploy['data']['id'],
                'resultCode' => $resultCode,
                'result' => $result,
            ]);

            Log::debug($response->status());
            Log::debug($response->json());
        });
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->everyMinute();
    }
}
