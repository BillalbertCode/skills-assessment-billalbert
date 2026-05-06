<?php

use Dustov\Quotes\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::get('/api/quotes', [QuoteController::class, 'index']);
Route::get('/api/quotes/{id}',[QuoteController::class, 'show']);