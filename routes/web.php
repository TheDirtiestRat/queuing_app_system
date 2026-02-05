<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueuingController;

Route::get('/', [QueuingController::class, "menu_page"]);

Route::get('/display-monitor', [QueuingController::class, "monitor_page"])->middleware('check');;

Route::get('/get-ticket-page', function () {
    // a page or form that gets tickets
});

// admin side
Route::get('/admin/menu', [QueuingController::class, "menu_page"])->middleware('check');;
Route::post('/admin/add_window', [QueuingController::class, 'add_window'])->middleware('check');;
Route::delete('/admin/remove_window/{id}', [QueuingController::class, 'remove_window'])->middleware('check');;
Route::post('/admin/reset_window', [QueuingController::class, 'reset_window'])->middleware('check');;
Route::post('/admin/reset_tickets', [QueuingController::class, 'reset_tickets'])->middleware('check');;
Route::post('/admin/genereate-tickets', [QueuingController::class, 'generate_tickets'])->middleware('check');;

Route::get('/admin/window/{id}', [QueuingController::class, "window_page"])->middleware('check');;
Route::post('/admin/next_queue/{window_id}', [QueuingController::class, 'next_queue'])->middleware('check');;
Route::post('/admin/select_queue/{window_id}-{queue_id}', [QueuingController::class, 'select_queue'])->middleware('check');;
Route::post('/admin/done_queue/{window_id}-{queue_id}', [QueuingController::class, 'done_queue'])->middleware('check');
Route::post('/admin/reserved_queue/{window_id}-{queue_id}', [QueuingController::class, 'reserved_queue'])->middleware('check');

// apis
Route::prefix('api')->group(function () {
    Route::get('/monitor-update', [QueuingController::class, 'monitor_update']);
    Route::post('/window_call_send', [QueuingController::class, 'window_calling_sending']);
    Route::post('/window_call_recieved', [QueuingController::class, 'window_calling_recieved']);
    // Route::post('/ticket/call', [QueuingController::class, 'call_ticket']);
})->middleware('check');