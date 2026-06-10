<?php

use App\Http\Controllers\BoardController;
use Illuminate\Support\Facades\Route;

Route::get('/boards/{board}', [BoardController::class, 'apiShow'])->name('api.boards.show');
Route::apiResource('boards', BoardController::class)->except('show')->names('api.boards');
