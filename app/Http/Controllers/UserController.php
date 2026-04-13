<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function toggleStatus($id) {
        $user = User::findOrfail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        return back()->with('success', 'User status toggled successfully.');
    }
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'fingerprint_id' => 'nullable|string|unique:users',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_admin'] = $request->boolean('is_admin');

        User::create($validated);

        Audit::log('CREATE_USER', "Created user: {$validated['email']}");

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'fingerprint_id' => ['nullable', 'string', Rule::unique('users')->ignore($user->id)],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Prevent users from removing their own admin status to avoid locking out the only admin
        if ($user->id === auth()->id() && !$request->has('is_admin')) {
            $validated['is_admin'] = true;
        } else {
            $validated['is_admin'] = $request->boolean('is_admin');
        }

        $user->update($validated);

        Audit::log('UPDATE_USER', "Updated user details for: {$user->email}");

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        Audit::log('DELETE_USER', "Deleted user account: {$user->email}");

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
