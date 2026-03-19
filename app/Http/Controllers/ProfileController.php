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
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

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

    /**
     * Cập nhật avatar người dùng từ popup.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        // Validate file avatar
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ]);

        $user = $request->user();

        // Xóa avatar cũ nếu tồn tại và là file local
        if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Lưu avatar mới vào thư mục 'users/avatars' trên disk 'public'
        $user->avatar = $request->file('avatar')->store('users/avatars', 'public');
        $user->save();

        return Redirect::back()->with('status', 'avatar-updated');
    }

    /**
     * Xóa avatar người dùng.
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Xóa file avatar nếu tồn tại và là file local
        if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Đặt avatar = null trong database
        $user->avatar = null;
        $user->save();

        return Redirect::back()->with('status', 'avatar-deleted');
    }

    /**
     * Cập nhật loại tài khoản của người dùng.
     */
    public function updateAccountType(Request $request)
    {
        $request->validate([
            'account_type' => ['required', 'in:student,business'],
        ], [
            'account_type.required' => 'Vui lòng chọn loại tài khoản.',
            'account_type.in' => 'Loại tài khoản không hợp lệ.',
        ]);

        $user = $request->user();
        $user->account_type = $request->account_type;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật loại tài khoản thành công.',
            'account_type' => $user->account_type
        ]);
    }
}
