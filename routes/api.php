<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Controller::class)->group(function(){
    Route::POST('login','login');
    Route::POST('loginByFreeIPA','loginByFreeIPA');
    Route::GET('/','homePage');
});
