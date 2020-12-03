<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.linkedin.com/in/ashutosh-verma-8609aa15b/
 * @since      1.0.0
 *
 * @package    Advance_Bank_Transfer
 * @subpackage Advance_Bank_Transfer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Advance_Bank_Transfer
 * @subpackage Advance_Bank_Transfer/includes
 * @author     Ashutosh Verma <vermaa947@gmail.com>
 */
class Advance_Bank_Transfer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'advance-bank-transfer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
