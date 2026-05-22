<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Interface visual para testar a API — disponível apenas em ambiente local/dev
if (app()->isLocal()) {
    Route::get('/tester', function () {
        return view('api-tester');
    });
}
