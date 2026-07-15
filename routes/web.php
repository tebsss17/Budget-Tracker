<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');

    Route::livewire('/transactions', 'pages::transactions.index')->name('transactions');

    Route::livewire('/budget', 'pages::budget.index')->name('budgets');

    Route::livewire('/analytics', 'pages::analytics')->name('analytics');

});

require __DIR__.'/settings.php';
