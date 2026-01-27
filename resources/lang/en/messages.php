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

    // Brand Show Page Messages
    'brand_show' => [
        'update_brand' => 'Update Brand',
        'manage_plan' => 'Manage Plan',
        'manage_members' => 'Manage Members',
        'payment_history' => 'Payment History',
        'energy_stats' => 'Energy Statistics',
        'delete_brand' => 'Delete Brand',
        'founded_year' => 'Founded Year',
        'completed' => 'Completed',
        'industry' => 'Industry',
        'target_market' => 'Target Market',
        'energy' => 'Energy',
        'members' => 'Members',
        'brand_journey' => 'Brand Journey',
        'track_progress' => 'Track your brand development progress',
        'stage_1' => 'Stage 1',
        'stage_2' => 'Stage 2',
        'stage_3' => 'Stage 3',
        'root' => 'Root',
        'brand_foundation' => 'Brand Foundation',
        'trunk' => 'Trunk',
        'brand_identity' => 'Brand Identity',
        'canopy' => 'Canopy',
        'growth_spread' => 'Growth & Expansion',
        'progress' => 'Progress',
        'stage_completed' => 'Completed',
        'in_progress' => 'In Progress',
        'not_unlocked' => 'Not Unlocked',
        'brand_overview' => 'Brand Overview',
        'brand_overview_desc' => 'Analyze performance and brand building progress.',
        'positioning' => 'Positioning',
        'identity' => 'Identity',
        'overall_score' => 'Overall Score',
        'developing' => 'Developing',
        'completion' => 'Completion',
        'strategy_results' => 'Strategy Results',
        'authentic_foundation' => 'Authentic Foundation',
        'core_values_defined' => 'Core values have been defined',
        'quality' => 'Quality',
        'dedication' => 'Dedication',
        'innovation' => 'Innovation',
        'consistent_identity' => 'Consistent Identity',
        'trunk_not_completed' => 'Trunk stage not completed',
        'brand_health' => 'Brand Health',
        'canopy_not_started' => 'Canopy stage not started',
        'next_step' => 'Next Step',
        'next_step_desc' => 'Excellent! Now it\'s time to create a unique identity.',
        'next_step_detail' => 'Continue perfecting your Brand Identity. Create a unique positioning statement and design a consistent visual identity system.',
        'start_now' => 'Start Now',
        'tip' => 'Tip: You can ask AI for assistance anytime during the brand building process',
        'delete_brand_title' => 'Delete Brand',
        'action_cannot_undo' => 'This action cannot be undone',
        'delete_warning' => 'You are about to delete the brand <strong>:name</strong>. All related data will be permanently deleted.',
        'confirm_delete_label' => 'To confirm, please enter the brand name:',
        'enter_brand_name' => 'Enter brand name',
        'cancel' => 'Cancel',
        'ai_agents' => 'Specialized AI Agents',
        'analyst' => 'Analyst',
        'analyst_desc' => 'Analyze market and competitors',
        'strategist' => 'Strategist',
        'strategist_desc' => 'Build brand positioning strategy',
        'creator' => 'Creator',
        'creator_desc' => 'Create content and creative designs',
        'community' => 'Community Manager',
        'community_desc' => 'Manage and grow community',
        'interact' => 'Interact',
        'unlock_later' => 'Unlock Later',
    ],

    // Member Management Messages
    'members' => [
        'title' => 'Member Management',
        'add_member' => 'Add Member',
        'member_name' => 'Member Name',
        'email' => 'Email',
        'role' => 'Role',
        'status' => 'Status',
        'joined_date' => 'Joined Date',
        'actions' => 'Actions',
        'admin' => 'Administrator',
        'editor' => 'Executor / Marketing',
        'member' => 'Member',
        'active' => 'Active',
        'edit' => 'Edit',
        'remove' => 'Remove',
        'no_members' => 'No members yet',
        'invite_title' => 'Invite New Member',
        'invite_desc' => 'Choose an appropriate role to assign access and interaction with AI Agents',
        'member_email' => 'Member Email',
        'select_role' => 'Select Role',
        'admin_desc' => 'Full access to all stages and AI Agents',
        'editor_desc' => 'Full access to Canopy, view only Root and Trunk',
        'brand_tree_access' => 'Brand Tree Access',
        'root_foundation' => 'Root (Foundation)',
        'trunk_strategy' => 'Trunk (Strategy)',
        'canopy_execution' => 'Canopy (Execution)',
        'full_access' => 'Full Access',
        'view_only' => 'View Only',
        'cancel' => 'Cancel',
        'change_role' => 'Change Role',
        'change_role_desc' => 'Select a new role for the member',
        'admin_full_access' => 'Full access',
        'editor_full_trunk' => 'Full access to Canopy',
        'update' => 'Update',
        'remove_member' => 'Remove Member',
        'remove_confirm' => 'Are you sure you want to remove',
        'from_brand' => 'from the brand?',
    ],

    // Brand Form Messages
    'brand_form' => [
        'add_title' => 'Add New Brand',
        'edit_title' => 'Update Brand',
        'brand_name' => 'Brand Name',
        'brand_name_placeholder' => 'Enter brand name',
        'industry' => 'Industry',
        'industry_placeholder' => 'Enter industry',
        'target_market' => 'Target Market',
        'target_market_placeholder' => 'Enter target market',
        'founded_year' => 'Founded Year',
        'founded_year_placeholder' => 'Enter founded year',
        'description' => 'Description',
        'description_placeholder' => 'Enter description',
        'logo' => 'Brand Logo',
        'logo_upload_text' => 'Click to select or drag and drop',
        'logo_upload_hint' => 'PNG, JPG, GIF (max 2MB)',
        'submit_add' => 'Add Now',
        'submit_update' => 'Update',
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
