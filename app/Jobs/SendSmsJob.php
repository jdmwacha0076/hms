<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phoneNumbers;
    protected $message;

    public function __construct($phoneNumbers, $message)
    {
        $this->phoneNumbers = $phoneNumbers;
        $this->message = $message;
    }

    public function handle()
    {
        $api_key = '388da7d45c2aa93e';
        $secret_key = 'OGQ2NzJjODhlM2EyNzA5MzRhNzE4ZWY4YTBhNWQ3MGNjNzU3MmRiYzM4M2E3MmE1NmFmYjE4YjhkNjNhMjQ2Ng==';

        $postData = array(
            'source_addr' => 'BOBTechWave',
            'encoding' => 0,
            'schedule_time' => '',
            'message' => $this->message,
            'recipients' => array_map(function ($phoneNumber, $index) {
                return [
                    'recipient_id' => (string) ($index + 1),
                    'dest_addr' => $phoneNumber,
                ];
            }, $this->phoneNumbers, array_keys($this->phoneNumbers)),
        );

        $url = 'https://apisms.beem.africa/v1/send';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($ch);

        if ($response === FALSE) {
            Log::error('SMS sending failed: ' . curl_error($ch));
        } else {
            Log::info('SMS Response: ' . $response);
        }

        curl_close($ch);
    }
}
