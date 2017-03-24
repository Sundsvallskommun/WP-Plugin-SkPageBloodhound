<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              ""
 * @since             1.0.0
 * @package           Sk_Page_Bloodhound
 *
 * @wordpress-plugin
 * Plugin Name:       SK Bloodhound page tracker
 * Plugin URI:        ""
 * Description:       Bloodhound that helps track down pages in a autocompleter on "page parent" attribute. The bloodhound is added when creating or editing pages
 * Version:           1.0.0
 * Author:            Patrik Jansson
 * Author URI:        ""
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sk-page-bloodhound
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sk-page-bloodhound.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sk_page_bloodhound() {

	new Sk_Page_Bloodhound();


}
run_sk_page_bloodhound();
