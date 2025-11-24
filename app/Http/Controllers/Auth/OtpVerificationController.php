<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    /**
     * Show the OTP verification form
     */
    public function show()
    {
        return view('auth.verify-otp');
    }

    /**
     * Verify the OTP
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user) {
            // If not logged in, try to find user by email in session
            $email = session('otp_email');
            if (!$email) {
                return back()->withErrors(['otp' => __('messages.otp.session_expired')]);
            }
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            return back()->withErrors(['otp' => __('messages.otp.user_not_found')]);
        }

        if ($user->verifyOtp($request->otp)) {
            Auth::login($user);
            session()->forget('otp_email');
            return redirect()->route('dashboard')->with('success', __('messages.otp.verified_success'));
        }

        return back()->withErrors(['otp' => __('messages.otp.invalid_or_expired')]);
    }

    /**
     * Resend OTP
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            $email = session('otp_email');
            if (!$email) {
                return back()->withErrors(['otp' => __('messages.otp.session_expired')]);
            }
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            return back()->withErrors(['otp' => __('messages.otp.user_not_found')]);
        }

        $user->generateOtp('verification');

        return back()->with('success', __('messages.otp.resent_success'));
    }
}
