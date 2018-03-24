<?php
/*
Plugin Name: Astahub - Welcome Dashboard
Plugin URI: 
Description: Astahub default welcome widget on admin dashboard.
Author: harisrozak
Author URI: 
Version: 0.1
Text Domain: astahub-welcome-dashboard
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * load custom css on dashboard
 */
function awd_enqueue_scripts() {
    $screen = get_current_screen();
    if(isset($screen->base) && $screen->base != 'dashboard') return;
    wp_enqueue_style('awd-admin-style', plugins_url('style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'awd_enqueue_scripts');

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function awd_init_widgets() {
    $user = wp_get_current_user();
    $display_name = ucfirst($user->data->display_name);

    wp_add_dashboard_widget(
        "astahub_welcome_dashboard", // Widget slug.
        "Welcome Back $display_name", // Title.
        "awd_display" // Display function.
    );  
}
add_action( 'wp_dashboard_setup', 'awd_init_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function awd_display() {
    // Display whatever it is you want to show.
    awd_string_last_login();
    awd_user_actions();
}

/**
 * Capture user login and add it as timestamp in user meta data
 *
 */ 
function awd_record_last_login( $user_login, $user ) {
    $last_login = get_the_author_meta('last_login');
    
    if($last_login) {
        update_user_meta( $user->ID, 'last_login', $last_login );
    }
    else {
        update_user_meta( $user->ID, 'last_login', time() );    
    }

    update_user_meta( $user->ID, 'last_login_current', time() );
}
add_action( 'wp_login', 'awd_record_last_login', 10, 2 );
 
/**
 * Display last login time
 */  
function awd_string_last_login() { 
    $user = wp_get_current_user();
    $last_login = get_user_meta($user->ID, 'last_login', true);
    $icon = '<span class="dashicons dashicons-backup"></span>';

    if($last_login) {
        printf('<p>%s Your last login: %s</p>', $icon, date('d F Y', $last_login) );    
    }
    else {
        echo "<p>This is your first login!</p>";   
    }
} 

/**
 * Dashboard welcome widget actions
 */
function awd_user_actions() {
?>
    <p class="awd-actions">
        <a href="<?php echo admin_url( 'profile.php' ); ?>">
            <span class="dashicons dashicons-edit"></span> Change Password
        </a>
        <a href="<?php echo wp_logout_url(); ?>">
            <span class="dashicons dashicons-lock"></span> Log Out
        </a>
    </p>
<?php
}