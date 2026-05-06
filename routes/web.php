<?php

use Illuminate\Support\Facades\Route;

Route::get('quotes-app', function(){
    return view('quotes::index');
});