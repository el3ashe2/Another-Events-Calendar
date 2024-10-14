<?php

/**
 * Fired during plugin deactivation
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
 * AEC_Deactivator Class
 *
 * @since    1.0.0
 */
class AEC_Deactivator {

	/**
	 * Called when plugin deactivated.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
	
		delete_option( 'rewrite_rules' );

	}

}
