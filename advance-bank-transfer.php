<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.linkedin.com/in/ashutosh-verma-8609aa15b/
 * @since             1.0.0
 * @package           Advance_Bank_Transfer
 *
 * @wordpress-plugin
 * Plugin Name:       Advance Bank Transfer
 * Plugin URI:        Wisetr.com/advance-bank-transfer
 * Description:       Take payments in person via BACS. More commonly known as direct bank/wire transfer.
 * Version:           1.0.0
 * Author:            Ashutosh Verma
 * Author URI:        https://www.linkedin.com/in/ashutosh-verma-8609aa15b/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advance-bank-transfer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin Active Detection.
 */
function is_dependent_plugin_active( $plugin_slug ) {

	if ( empty( $plugin_slug ) ) {
		return false;
	}

	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	return in_array( $plugin_slug, $active_plugins ) || array_key_exists( $plugin_slug, $active_plugins );
}

/**
 * The code that runs during plugin activation.
 * This action is for woocommerce dependency check.
 */
function check_dependent_plugins_status() {

	// Default we are good to go.
	$activation = array(
		'status'	=>	true,
		'message'	=>	null,
	);

	// Dependant plugin.
	if ( ! is_dependent_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		$activation['status'] = false;
		$activation['message'] = 'woo_inactive';
	}

	return $activation;
}

$dependent_plugins_status = check_dependent_plugins_status();

if( empty( $dependent_plugins_status[ 'status' ] ) && 'woo_inactive' == $dependent_plugins_status[ 'message' ] ) {

	// Deactivation of plugin at dependency failed.
	add_action( 'admin_init', 'abt_plugin_activation_failure' );

	/**
	 * Deactivate this plugin.
	 */
	function abt_plugin_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'abt_plugin_activation_admin_notice' );

	/**
	 * This function is used to display plugin activation error notice.
	 */
	function abt_plugin_activation_admin_notice() {

		global $dependent_plugins_status;

		// To hide Plugin activated notice.
		unset( $_GET['activate'] );

		?>

		<?php if ( 'woo_inactive' == $dependent_plugins_status['message'] ) : ?>

			<div class="notice notice-error is-dismissible">
				<p><strong><?php esc_html_e( 'WooCommerce', 'advance-bank-transfer' ); ?></strong><?php esc_html_e( ' is not activated, Please activate WooCommerce first to activate ', 'advance-bank-transfer' ); ?><strong><?php esc_html_e( 'Advance Bank Transfer', 'advance-bank-transfer' ); ?></strong><?php esc_html_e( '.', 'advance-bank-transfer' ); ?></p>
			</div>

		<?php endif;
	}
} else {

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'ADVANCE_BANK_TRANSFER_VERSION', '1.0.0' );

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-advance-bank-transfer-activator.php
	 */
	function activate_advance_bank_transfer() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-advance-bank-transfer-activator.php';
		Advance_Bank_Transfer_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-advance-bank-transfer-deactivator.php
	 */
	function deactivate_advance_bank_transfer() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-advance-bank-transfer-deactivator.php';
		Advance_Bank_Transfer_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_advance_bank_transfer' );
	register_deactivation_hook( __FILE__, 'deactivate_advance_bank_transfer' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-advance-bank-transfer.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_advance_bank_transfer() {

		$plugin = new Advance_Bank_Transfer();
		$plugin->run();

	}
	run_advance_bank_transfer();
}