<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'calculadora')->name('calculadora.home');
Volt::route('/calculadora', 'calculadora')->name('calculadora');

Route::view('/politicas-de-privacidad', 'politicas-privacidad')->name('politicas.privacidad');
Route::view('/privacidad', 'politicas-privacidad')->name('privacidad');
