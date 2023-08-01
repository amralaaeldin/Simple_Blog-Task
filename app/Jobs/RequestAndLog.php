<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class RequestAndLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = new Client();

        try {
            $response = $client->get('https://randomuser.me/api');

            if ($response->getStatusCode() === 200) {
                Log::info(json_decode($response->getBody(), true));
                print_r(json_decode($response->getBody(), true));
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error($e->getMessage());
            echo ($e->getMessage());
        }
    }
}
