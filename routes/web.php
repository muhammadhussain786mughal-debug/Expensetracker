<?php

use App\Http\Controllers\webcontroller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/reciept",[webcontroller::class,"reciept_get"]);
Route::post("/reciept",[webcontroller::class,"reciept_post"]);