<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->add([
                'text' => 'Dashboard',
                'url' => 'dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
            ]);
            $event->menu->add([
                'text' => 'Positions',
                'url' => 'positions',
                'icon' => 'fas fa-fw fa-briefcase',
            ]);
        });
    }
}
