<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Xử lý xóa avatar nếu người dùng yêu cầu
        if ($request->input('remove_avatar') === '1' && !$request->hasFile('avatar')) {
            // Xóa file avatar cũ nếu tồn tại
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Đặt avatar = null trong validated để fill() cập nhật đúng vào database
            $validated['avatar'] = null;
        }
        // Xử lý upload avatar mới (theo mẫu BrandController)
        elseif ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu tồn tại và là file local (không phải URL bên ngoài)
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Lưu avatar mới vào thư mục 'users/avatars' trên disk 'public'
            $validated['avatar'] = $request->file('avatar')->store('users/avatars', 'public');
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    // public function destroy(Request $request): RedirectResponse
    // {
    //     $user = $request->user();

    //     // If user has Google ID and no password set, skip password validation
    //     if (!$user->google_id) {
    //         $request->validateWithBag('userDeletion', [
    //             'password' => ['required', 'current_password'],
    //         ]);
    //     }

    //     Auth::logout();

    //     $user->delete();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return Redirect::to('/');
    // }
}
