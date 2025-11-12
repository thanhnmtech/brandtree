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

        return redirect()->route('password.verify-otp')->with('success', 'OTP has been sent to your email.');
    }

    /**
     * Show the OTP verification form for password reset
     */
    public function showVerifyOtpForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expired. Please try again.']);
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
            return back()->withErrors(['otp' => 'Session expired. Please try again.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'User not found.']);
        }

        // Verify OTP without clearing it yet
        if (!$user->otp || !$user->otp_expires_at) {
            return back()->withErrors(['otp' => 'No OTP found.']);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired.']);
        }

        if ($user->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        session(['otp_verified' => true]);

        return redirect()->route('password.reset')->with('success', 'OTP verified. Please set your new password.');
    }

    /**
     * Show the reset password form
     */
    public function showResetForm()
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Please verify OTP first.']);
        }
        return view('auth.reset-password-otp');
    }

    /**
     * Reset the password
     */
    public function reset(Request $request)
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Please verify OTP first.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->password = Hash::make($request->password);
        $user->clearOtp();

        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password has been reset successfully. Please login with your new password.');
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        $email = session('reset_email');
        if (!$email) {
            return back()->withErrors(['otp' => 'Session expired. Please try again.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'User not found.']);
        }

        $user->generateOtp('reset');

        return back()->with('success', 'OTP has been resent to your email.');
    }
}
