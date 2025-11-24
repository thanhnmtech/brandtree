<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Messages Language Lines (English)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various messages throughout
    | the application. You are free to modify these language lines according
    | to your application's requirements.
    |
    */

    // OTP Verification Messages
    'otp' => [
        'session_expired' => 'Session expired. Please try again.',
        'user_not_found' => 'User not found.',
        'verified_success' => 'Email verified successfully!',
        'invalid_or_expired' => 'Invalid or expired OTP.',
        'resent_success' => 'OTP has been resent to your email.',
        'sent_success' => 'OTP has been sent to your email.',
        'no_otp_found' => 'No OTP found.',
        'expired' => 'OTP has expired.',
        'invalid' => 'Invalid OTP.',
        'verified_set_password' => 'OTP verified. Please set your new password.',
        'verify_first' => 'Please verify OTP first.',
    ],

    // Password Reset Messages
    'password_reset' => [
        'success' => 'Password has been reset successfully. Please login with your new password.',
        'user_not_found' => 'User not found.',
    ],

    // Google Auth Messages
    'google' => [
        'login_success' => 'Logged in successfully with Google!',
        'linked_success' => 'Google account linked successfully!',
        'created_success' => 'Account created and logged in successfully with Google!',
        'auth_failed' => 'Failed to authenticate with Google. Please try again.',
    ],

    // Registration Messages
    'registration' => [
        'success' => 'Registration successful! Please check your email for the OTP code.',
    ],

    // Ladipage Messages
    'ladipage' => [
        'secret_key_invalid' => 'secret_key invalid',
        'api_key_invalid' => 'api_key invalid',
        'ladi_id_empty' => 'ladiID empty',
        'content_empty' => 'content empty',
        'slug_invalid' => 'slug :slug invalid',
        'save_error' => 'Error saving data: :error',
    ],

];

