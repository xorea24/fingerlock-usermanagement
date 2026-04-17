<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Audit;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Hardware Status Check
            $isOnline = false;
            try {
                $lastPing = DB::table('settings')->where('key', 'last_hardware_ping')->value('value');
                $isOnline = $lastPing && Carbon::parse($lastPing)->greaterThan(Carbon::now()->subMinutes(2));
            } catch (\Exception $e) {
                // Table doesn't exist yet
            }

            // Failed Attempts Badge (last 60 min)
            $recentFailed = 0;
            try {
                $recentFailed = Audit::failed()->recent(60)->count();
            } catch (\Exception $e) {
                // Audits table may not exist yet
            }

            $event->menu->add([
                'text' => $isOnline ? 'Hardware: Online' : 'Hardware: Offline',
                'topnav_right' => true,
                'icon' => 'fas fa-microchip',
                'icon_color' => $isOnline ? 'success' : 'danger',
                'label_color' => $isOnline ? 'success' : 'danger',
            ]);

            $event->menu->add([
                'text' => 'Dashboard',
                'url' => 'dashboard',
                'icon' => 'fas fa-fw fa-chart-line',
                'active' => ['dashboard', 'dashboard/*'],
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
                'active' => ['log-audit', 'audit'],
                // Show red badge with count of recent failed attempts
                'label' => $recentFailed > 0 ? $recentFailed : null,
                'label_color' => 'danger',
            ]);
        });
    }
}