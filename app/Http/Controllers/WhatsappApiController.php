<?php

namespace App\Http\Controllers;

use Log;
use Throwable;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use App\Jobs\WhatsappBroadcaster;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;

class WhatsappApiController extends Controller
{
    public function setQueue(Request $request)
    {
        // Validasi permintaan yang masuk
        $validated = $request->validate([
            "session_id" => "required",
            "token" => "required",
            "data" => "required|array",
            "delay" => "required|integer" // Pastikan delay adalah nilai integer
        ]);

        // Ekstraksi data dari permintaan yang sudah divalidasi
        $session_id = $validated['session_id'];
        $token = $validated['token'];
        $data = $validated['data']; // Data dalam bentuk array
        $delay = $validated['delay'];

        // Pisahkan data menjadi bagian-bagian kecil (chunk)
        $chunks = array_chunk($data, 10); // Pisahkan setiap 10 data menjadi satu chunk

        // Persiapkan job untuk setiap chunk
        $jobs = [];
        foreach ($chunks as $chunk) {
            $jobs[] = (new WhatsappBroadcaster($session_id, $token, $chunk))->delay(now()->addSeconds($delay));
        }

        // Kirim batch job
        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) {
                // Semua job berhasil diselesaikan
                Log::info('Batch berhasil diselesaikan', ['batch_id' => $batch->id]);
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // Terdeteksi kegagalan pertama pada job
                Log::error('Batch gagal', ['batch_id' => $batch->id, 'error' => $e->getMessage()]);
            })
            ->finally(function (Batch $batch) {
                // Batch selesai dieksekusi
                Log::info('Batch selesai dieksekusi', ['batch_id' => $batch->id]);
            })
            ->dispatch();

        return response()->json(['message' => 'Batch telah diantrekan dengan sukses', 'batch_id' => $batch->id], 200);
    }
    public function getProgress(Request $request)
    {
        // Validasi permintaan yang masuk
        $validated = $request->validate([
            "batch_id" => "required|string"
        ]);

        // Dapatkan status batch
        $batch = Bus::findBatch($validated['batch_id']);

        if (!$batch) {
            return response()->json(['message' => 'Batch tidak ditemukan'], 404);
        }

        return response()->json([
            'batch_id' => $batch->id,
            'total_jobs' => $batch->totalJobs,
            'pending_jobs' => $batch->pendingJobs,
            'failed_jobs' => $batch->failedJobs,
            'processed_jobs' => $batch->processedJobs(),
            'progress' => $batch->progress(),
            'created_at' => $batch->createdAt,
            'finished_at' => $batch->finishedAt
        ]);
    }
}
