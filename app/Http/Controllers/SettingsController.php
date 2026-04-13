<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Album;

class SettingsController extends Controller
{
    public function indexPage()
    {
        $albums = Album::with('photos')->orderBy('name')->get();
        
        // FIX: Fetch existing settings so the Blade can show current values
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        return view('admin.settings.list', [
            'title'    => 'System Configuration',
            'albums'   => $albums,
            'settings' => $settings, // Pass settings to the view
        ]);
    }

    public function getLatestData() 
    {
        // Fetch everything in one go for better performance
        $settings = DB::table('settings')->whereIn('key', ['slide_duration', 'transition_effect'])->pluck('value', 'key');

        return response()->json([
            'seconds' => $settings['slide_duration'] ?? 5,
            'effect' => $settings['transition_effect'] ?? 'fade',
            // If updated_at is null, we return the current time so the slideshow knows to refresh
            'last_update' => DB::table('settings')->max('updated_at') ?? Carbon::now()->toDateTimeString(),
        ]);
    }

    public function update(Request $request)
    {
        // FIX: Changed display_album_ids to 'nullable|string' to match your JS input
        $request->validate([
            'slide_duration'    => 'required|integer|min:1|max:60',
            'transition_effect' => 'required|string',
            'display_album_ids' => 'nullable|string', 
        ]);

        $data = [
            'slide_duration'    => $request->slide_duration,
            'transition_effect' => $request->transition_effect,
            'display_album_ids' => $request->display_album_ids ?? '', 
        ];

        foreach ($data as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $value,
                    'updated_at' => Carbon::now() 
                ]
            );
        }

        session(['last_tab' => 'settings']);
        
        // Return JSON if it's an AJAX request (for your JS), otherwise redirect back
        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Settings updated!']);
        }

        return back()->with('success', 'Settings updated successfully!');
    }

    public function heartbeat(Request $request)
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'last_hardware_ping'],
            [
                'value' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()
            ]
        );

        return response()->json(['status' => 'online', 'timestamp' => Carbon::now()]);
    }
}