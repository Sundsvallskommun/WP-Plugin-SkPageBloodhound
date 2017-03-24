<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       ""
 * @since      1.0.0
 *
 * @package    Sk_Page_Bloodhound
 * @subpackage Sk_Page_Bloodhound/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sk_Page_Bloodhound
 * @subpackage Sk_Page_Bloodhound/includes
 * @author     Patrik Jansson <patrik.jansson@cybercom.com>
 */
class Sk_Page_Bloodhound_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sk-page-bloodhound',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
