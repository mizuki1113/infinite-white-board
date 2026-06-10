<?php

use App\Http\Controllers\BoardController;
use Illuminate\Support\Facades\Route;

Route::apiResource('boards', BoardController::class)->names('api.boards');
