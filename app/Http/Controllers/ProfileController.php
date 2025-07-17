<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display profile edit form
     */
    public function edit(): Response
    {
        return Inertia::render('app/profile/edit');
    }

    /**
     * Handle an incoming request for update user
     */
    public function update(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
                'avatar' => 'nullable|image|max:1024',
            ]);

            if ($validated) {
                $default_disk = config('filesystems.default');
                if ($request->hasFile('avatar')) {
                    if ($user->avatar) {
                        Storage::disk($default_disk)->delete($user->avatar);
                    }

                    $validated['avatar'] = $request
                        ->file('avatar')
                        ->store('avatars', $default_disk);
                }

                $user->update($validated);
                DB::commit();

                return back()->with('success', 'Profile updated successfully.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('update-profile', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }
}
