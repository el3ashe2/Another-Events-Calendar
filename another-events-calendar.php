<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/el3ashe2/Another-Events-Calendar
 * @since             2.0.0
 * @package           another-events-calendar
 *
 * @wordpress-plugin
 * Plugin Name:       Another Events Calendar
 * Plugin URI:        http://yendif.com/wordpress/another-events-calendar
 * Description:       Another Events Calendar is a top-of-the-line event management plugin helps you organize and manage any type of events such as Conferences, Seminars, Meetings, Team Building Events, Trade Shows, Business Dinners, etc... in a very simple way.
 * Version:           2.1.0
 * Author:            Yendif Technologies Pvt Ltd. Updated By el3ashe2 Compatible with Wordpress 6.6.2
 * Author URI:        http://yendif.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       another-events-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if( ! defined( 'WPINC' ) ) {
	die;
}

// Name of the plugin
if( ! defined( 'AEC_PLUGIN_NAME' ) ) {
    define( 'AEC_PLUGIN_NAME', 'Another Events Calendar' );
}

// Unique identifier for the plugin. Used as Text Domain
if( ! defined( 'AEC_PLUGIN_SLUG' ) ) {
    define( 'AEC_PLUGIN_SLUG', 'another-events-calendar' );
}

// Path to the plugin directory
if ( ! defined( 'AEC_PLUGIN_DIR' ) ) {
    define( 'AEC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// URL of the plugin
if( ! defined( 'AEC_PLUGIN_URL' ) ) {
    define( 'AEC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// The actuall plugin version
if( ! defined( 'AEC_PLUGIN_VERSION' ) ) {
    define( 'AEC_PLUGIN_VERSION', '1.1.0' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aec-activator.php
 */
function activate_aec() {
	require_once AEC_PLUGIN_DIR . 'includes/class-aec-activator.php';
	AEC_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aec-deactivator.php
 */
function deactivate_aec() {
	require_once AEC_PLUGIN_DIR . 'includes/class-aec-deactivator.php';
	AEC_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aec' );
register_deactivation_hook( __FILE__, 'deactivate_aec' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require AEC_PLUGIN_DIR . 'includes/class-aec.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */ 
function run_aec() {

	$plugin = new AEC();
	$plugin->run();	
	
	// Register AEC Widgets
	require_once AEC_PLUGIN_DIR . 'widgets/search/search.php';
	require_once AEC_PLUGIN_DIR . 'widgets/upcoming-events/upcoming-events.php';
	require_once AEC_PLUGIN_DIR . 'widgets/mini-calender/mini-calendar.php';
	
}
run_aec();
