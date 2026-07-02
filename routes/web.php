<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');

    Route::livewire('/transactions', 'pages::transactions.index')->name('transactions');
    Route::livewire('/transactions/{transanction}', 'pages::transactions.show')->name('transactions.show');

    Route::livewire('/budget', 'pages::budget.index')->name('budget');

});

require __DIR__.'/settings.php';
