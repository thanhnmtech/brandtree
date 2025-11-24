<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class OtpPasswordResetController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password-otp');
    }

    /**
     * Send OTP for password reset
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->generateOtp('reset');

        session(['reset_email' => $request->email]);

        return redirect()->route('password.verify-otp')->with('success', __('messages.otp.sent_success'));
    }

    /**
     * Show the OTP verification form for password reset
     */
    public function showVerifyOtpForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => __('messages.otp.session_expired')]);
        }
        return view('auth.verify-password-otp');
    }

    /**
     * Verify OTP and show reset password form
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return back()->withErrors(['otp' => __('messages.otp.session_expired')]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['otp' => __('messages.otp.user_not_found')]);
        }

        // Verify OTP without clearing it yet
        if (!$user->otp || !$user->otp_expires_at) {
            return back()->withErrors(['otp' => __('messages.otp.no_otp_found')]);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => __('messages.otp.expired')]);
        }

        if ($user->otp !== $request->otp) {
            return back()->withErrors(['otp' => __('messages.otp.invalid')]);
        }

        session(['otp_verified' => true]);

        return redirect()->route('password.reset')->with('success', __('messages.otp.verified_set_password'));
    }

    /**
     * Show the reset password form
     */
    public function showResetForm()
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => __('messages.otp.verify_first')]);
        }
        return view('auth.reset-password-otp');
    }

    /**
     * Reset the password
     */
    public function reset(Request $request)
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => __('messages.otp.verify_first')]);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => __('messages.password_reset.user_not_found')]);
        }

        $user->password = Hash::make($request->password);
        $user->clearOtp();

        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('success', __('messages.password_reset.success'));
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        $email = session('reset_email');
        if (!$email) {
            return back()->withErrors(['otp' => __('messages.otp.session_expired')]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['otp' => __('messages.otp.user_not_found')]);
        }

        $user->generateOtp('reset');

        return back()->with('success', __('messages.otp.resent_success'));
    }
}
