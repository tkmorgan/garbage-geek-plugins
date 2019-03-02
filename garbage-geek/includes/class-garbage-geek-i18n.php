<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://34.73.97.67/
 * @since      1.0.0
 *
 * @package    Garbage_Geek
 * @subpackage Garbage_Geek/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Garbage_Geek
 * @subpackage Garbage_Geek/includes
 * @author     Knox Hacks 2019 Garbage Geeks Team <tkmorgan@gmail.com>
 */
class Garbage_Geek_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'garbage-geek',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
