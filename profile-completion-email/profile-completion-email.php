<?php

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}

/**
 * Get subscribers with less than 100% completion of specified user meta fields.
 *
 * @return array List of users with their email and completion percentage.
 */
function gw_get_incomplete_job_seekers() {
    $table_prefix = 'wp_';
    $user_role = 'subscriber';
    $meta_fields = '';
    
    global $wpdb;
    $query = "SELECT u.display_name, u.user_email, COUNT(um.meta_key) AS filled_fields, 
       (COUNT(um.meta_key) / 40.0) * 100 AS completion_percentage
    FROM {$table_prefix}users u
    INNER JOIN {$table_prefix}usermeta um ON u.ID = um.user_id
    WHERE um.meta_key IN (
    'job-seeker-full-name', 'job-seeker-email', 'profile-photo', 
    'current-designation', 'employer-name', 'date-of-birth', 
    'job-seeker-gender', 'job-seeker-nationality', 'residence-city', 
    'residence-country', 'marital-status', 'driving-license-issued-country', 
    'known-languages', 'visa-status-for-current-location', 'religion', 
    'job-seeker-mobile-number', 'cv-headline', 'job-seeker-passport-number', 
    'total-years-of-experience', 'current-industry', 'functional-area', 
    'current-work-level', 'job-seeker-salary-currency-type', 
    'current-monthly-salary', 'availability-to-join', 'profile-summary', 
    'job-seeker-work-experience', 'job-seeker-education', 
    'preferred-designations', 'preferred-locations', 'preferred-industries', 
    'upload-cv', 'documents', 'job-seeker-photo', 'job-seeker-passport', 
    'job-seeker-education-certificates', 'job-seeker-extra-skill-certificate', 
    'job-seeker-work-experience-certificate', 'job-seeker-no-objection-certificate', 
    'job-seeker-cv'
        ) AND um.meta_value IS NOT NULL AND um.meta_value != ''
        AND u.ID IN (
    SELECT u2.ID
    FROM {$table_prefix}users u2
    INNER JOIN {$table_prefix}usermeta um2 ON u2.ID = um2.user_id
    WHERE um2.meta_key = '{$table_prefix}capabilities'
    AND um2.meta_value LIKE '%{$user_role}%'
    )
    GROUP BY u.ID
    HAVING completion_percentage < 100";

    $results = $wpdb->get_results($query, ARRAY_A);

    // Format the result
    $users = [];
    foreach ($results as $result) {
        $users[] = [
            'name'           => $result['display_name'],
            'email'           => $result['user_email'],
            'percentage' => round($result['completion_percentage'], 2),
        ];
    }

    return $users;
}

function gw_get_email_template() {
    ob_start();
    require_once ABEN_GW_PATH . 'profile-completion-email/template.php';
    return ob_get_clean();
}

add_filter( 'cron_schedules', 'gw_custom_schedules' );
function gw_custom_schedules($schedules) {
    $schedules['gw_every_week'] = [
        'interval' => WEEK_IN_SECONDS,
        'display'  => __('GW Every Week'),
];

        return $schedules;
}

add_action('gw_profile_cron_hook', 'gw_profile_cron_exec',20);
function gw_profile_cron_exec() {
    $users = gw_get_incomplete_job_seekers();
    $template = gw_get_email_template();
    $subject = 'Complete Your Profile Today!';
    $headers[] = 'From: GulfWorking.com <notifications@gulfworking.com>';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    
    foreach($users as $user) {
        $login_token = aben_generate_login_token($user['email']);
        $placeholders = [
            '{{NAME}}' => $user['name'],
            '{{USER_EMAIL}}' => $user['email'],
            '{{TOKEN}}' => $login_token,
        ];
        
        // Send email to user
        $message = str_replace(array_keys($placeholders), array_values($placeholders), $template);
        wp_mail($user['email'], $subject, $message, $headers );
        error_log('Profile completion mail sent to' . $user['email']);
    }
}