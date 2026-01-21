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
        'invalid_or_expired' => 'Invalid or expired OTP code.',
        'resent_success' => 'OTP code has been resent to your email.',
        'sent_success' => 'OTP code has been sent to your email.',
        'no_otp_found' => 'No OTP code found.',
        'expired' => 'OTP code has expired.',
        'invalid' => 'Invalid OTP code.',
        'verified_set_password' => 'OTP verified. Please set your new password.',
        'verify_first' => 'Please verify OTP first.',
    ],

    // Password Reset Messages
    'password_reset' => [
        'success' => 'Password reset successfully. Please login with your new password.',
        'user_not_found' => 'User not found.',
    ],

    // Google Auth Messages
    'google' => [
        'login_success' => 'Successfully logged in with Google!',
        'linked_success' => 'Google account linked successfully!',
        'created_success' => 'Account created and logged in with Google!',
        'auth_failed' => 'Google authentication failed. Please try again.',
    ],

    // Registration Messages
    'registration' => [
        'success' => 'Registration successful! Please check your email for OTP code.',
    ],

    // Brand Messages
    'brand' => [
        'created' => 'Brand created successfully!',
        'updated' => 'Brand updated successfully!',
        'deleted' => 'Brand deleted successfully!',
        'member_added' => 'Member added successfully!',
        'member_removed' => 'Member removed successfully!',
        'member_updated' => 'Member permissions updated!',
        'cannot_remove_self' => 'You cannot remove yourself from the brand.',
        'cannot_remove_owner' => 'You cannot remove the brand owner.',
    ],

    // Subscription Messages
    'subscription' => [
        'activated' => 'Successfully activated :plan plan!',
        'cancelled' => 'Plan cancelled successfully.',
        'trial_used' => 'This brand has already used the trial plan.',
        'no_active' => 'No active subscription.',
        'not_found' => 'Subscription not found.',
    ],

    // Payment Messages
    'payment' => [
        'transfer_info' => 'Please transfer according to the information below.',
        'already_processed' => 'This transaction has already been processed.',
        'success' => 'Payment successful! Your plan has been activated.',
        'not_received' => 'Payment not received. Please check again later.',
    ],

    // Ladipage Messages
    'ladipage' => [
        'secret_key_invalid' => 'Invalid secret key',
        'api_key_invalid' => 'Invalid API key',
        'ladi_id_empty' => 'Ladipage ID is empty',
        'content_empty' => 'Content is empty',
        'slug_invalid' => 'Slug :slug is invalid',
        'save_error' => 'Error saving data: :error',
        'created' => 'Ladipage created successfully!',
        'updated' => 'Ladipage updated successfully!',
        'deleted' => 'Ladipage deleted successfully!',
    ],

    // Dashboard Messages
    'dashboard' => [
        'active_brands' => 'Active Brands',
        'total_brands_desc' => 'Total brands you are managing',
        'need_care' => 'Need Care',
        'need_care_desc' => 'Brands waiting for your attention',
        'growing' => 'Growing',
        'growing_desc' => 'Brands in development',
        'completed' => 'Completed',
        'completed_desc' => 'Ready to leverage',
        'search_placeholder' => 'Search brands...',
        'all_status' => 'All Status',
        'status_seedling' => 'Need Care',
        'status_growing' => 'Growing',
        'status_completed' => 'Completed',
        'sort_updated' => 'Recently Updated',
        'sort_newest' => 'Newest',
        'add_brand' => 'Add Brand',
        'no_brands_found' => 'No brands found',
        'progress' => 'Development Progress',
        'root' => 'Root',
        'trunk' => 'Trunk',
        'next_step' => 'Next Step',
        'manage_brand' => 'Manage Brand',
        'updated_at' => 'Updated',
    ],

];
