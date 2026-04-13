<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            // Hardware Status Check — guard against missing table during fresh installs
            $isOnline = false;
            try {
                $lastPing = DB::table('settings')->where('key', 'last_hardware_ping')->value('value');
                $isOnline = $lastPing && Carbon::parse($lastPing)->greaterThan(Carbon::now()->subMinutes(2));
            } catch (\Exception $e) {
                // Table doesn't exist yet (migrations not run) — default to offline
            }

            $event->menu->add([
                'text' => $isOnline ? 'Hardware: Online' : 'Hardware: Offline',
                'topnav_right' => true,
                'icon' => 'fas fa-microchip',
                'icon_color' => $isOnline ? 'success' : 'danger',
                'label_color' => $isOnline ? 'success' : 'danger',
            ]);

            $event->menu->add([
                'text' => 'User',
                'url' => 'User', // Adjust URL as needed
                'icon' => 'fas fa-fw fa-images',
            ]);
            $event->menu->add([
                'text' => 'Manage User',
                'url' => 'users',
                'icon' => 'fas fa-fw fa-users',
                'active' => ['users', 'users/*'],
            ]);
            $event->menu->add([
                'text' => 'Log Audit',
                'url' => 'log-audit',
                'icon' => 'fas fa-fw fa-history',
                'active' => ['log-audit'],
            ]);
        });
    }
}