<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WhatsappBroadcaster implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $session_id;
    protected $token;
    protected $data;
    protected $delay;
    /**
     * Create a new job instance.
     *
     * @param string $session_id
     * @param string $token
     * @param array $data
     * @return void
     */
    public function __construct($session_id, $token, $data,$delay)
    {
        $this->session_id = $session_id;
        $this->token = $token;
        $this->data = $data;
        $this->delay = $delay;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Lakukan logika pengiriman pesan WhatsApp di sini
        foreach ($this->data as $message) {
            Http::withHeaders([
                [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept'        => 'application/json',
                ]
            ])->post(env("WHATSAPP_SERVER_ENDPOINT")."/api/".$this->session_id."/send-message");
            sleep($this->delay);
        }
    }
}
