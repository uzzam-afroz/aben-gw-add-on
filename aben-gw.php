<?php

/**
 * Plugin Name:       Aben GW Add-On
 * Description:       Adds features to the Aben plugin specific to GW.
 * Version:           1.0.0
 * Author:            Zamy
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aben
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    // Exit if accessed directly.
    exit;
}

// Include the necessary function to check plugin activation.
if (!function_exists('is_plugin_active')) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Check if the Aben plugin is active.
 *
 * @return bool True if the Aben plugin is active, false otherwise.
 */
function is_aben_active()
{
    $active_plugins = get_option('active_plugins');
    return in_array('aben/aben.php', $active_plugins);
}

// Check if Aben plugin is active before adding the custom action.
if (is_aben_active()) {
    add_action('aben_post_button_hook', 'aben_gw_apply_button');
}

/**
 * Outputs a custom Apply button with a magic login link.
 *
 * @param string $link The URL to which the apply button should redirect.
 */
function aben_gw_apply_button($link)
{
    ?>
<div style="width:20%; display:inline-block; align-self:center;">
    <a href="<?php echo esc_url($link); ?>/?email={{USER_EMAIL}}&token={{TOKEN}}" target="_blank"
        rel="noopener noreferrer"
        style=" display: inline-block; padding: 10px 20px; color: #fff; text-decoration: none; background: #2271b1;">
        Apply
    </a>
</div>
<?php
}

/**
 * Generates a magic login token for a user based on their email.
 *
 * @return string|bool The generated token or false if user not found.
 */

function aben_generate_login_token($user_email)
{
    // Get the user by their email.
    $user = get_user_by('email', $user_email);

    if (!$user) {
        return false;
    }

    // Check if a token already exists.
    $is_token = get_transient('aben_login_token_' . md5($user->user_email));

    if (!empty($is_token)) {
        $token = $is_token;
    } else {
        // Generate a new token.
        $token = wp_generate_password(40, false);
        $expiry = DAY_IN_SECONDS * 30;

        // Log the generated token for debugging.
        error_log('Generated Token: ' . $token);

        // Store the token as a transient.
        set_transient('aben_login_token_' . md5($user->user_email), $token, $expiry);
    }

    return $token;
}

// Hook the auto login function into the 'init' action.
add_action('init', 'aben_handle_auto_login');

/**
 * Handles auto login by validating the email and token passed via URL parameters.
 */
function aben_handle_auto_login()
{
    if (isset($_GET['email']) && isset($_GET['token'])) {

        // Retrieve the token and email from the query parameters.
        $token = sanitize_text_field($_GET['token']);
        $user_email = sanitize_email($_GET['email']);

        // Log the token from the URL for debugging.
        error_log('Token from URL: ' . $token);

        // Retrieve the stored token from the transient.
        $stored_token = get_transient('aben_login_token_' . md5($user_email));

        // Log the stored token for debugging.
        error_log('Stored Token: ' . $stored_token);

        // Retrieve the user by email.
        $user = get_user_by('email', $user_email);

        // Ensure the user exists and the tokens match.
        if ($user && $stored_token && hash_equals($stored_token, $token)) {

            // Log the user in.
            wp_set_auth_cookie($user->ID);

            // Capture the current page URL
            $current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            // Remove the query args (email and token) from the current URL
            $redirect_url = remove_query_arg(array('email', 'token'), $current_url);

            // Redirect the user back to the same page without the query args
            wp_redirect($redirect_url);

            exit;
        } else {

            // Invalid or expired token, show an error.
            wp_die('The link has expired or is invalid.');

        }

    } else {
        // No query parameters, do nothing.
        return;
    }
}