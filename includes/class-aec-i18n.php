<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/includes
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_i18n Class
 *
 * @since    1.0.0
 */
class AEC_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'another-events-calendar',
			false,
			AEC_PLUGIN_DIR . '/languages/'
		);

	}

}
