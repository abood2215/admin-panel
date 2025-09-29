<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Http\Livewire\AdminMenu;
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
class AppServiceProvider extends ServiceProvider {
    public function register() {}
}