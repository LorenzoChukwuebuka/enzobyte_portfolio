<?php

use App\Http\Controllers\SitePageController;
use Illuminate\Support\Facades\Route;

//Route::get('/', [SitePageController::class, 'home'])->name('home');

Route::controller(SitePageController::class)->group(function () {
    Route::get('', "home")->name('home');
    Route::get('projects', 'portfolio')->name('projects');
    Route::get('contact', 'contact')->name('contact');
    Route::get('about', 'about')->name('about');
});

Route::get('/{any}', fn() => view('admin'))->where('any', '.*');