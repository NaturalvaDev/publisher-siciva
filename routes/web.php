<?php

use App\Http\Controllers\WhatsappApiController;
use Illuminate\Support\Facades\Route;

Route::prefix("whatsapp-publisher")->group(function(){
    Route::post("queue",[WhatsappApiController::class,"setQueue"]);
    Route::get("progress",[WhatsappApiController::class,"getProgress"]);
});
