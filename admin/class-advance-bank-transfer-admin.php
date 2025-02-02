<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.linkedin.com/in/ashutosh-verma-8609aa15b/
 * @since      1.0.0
 *
 * @package    Advance_Bank_Transfer
 * @subpackage Advance_Bank_Transfer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advance_Bank_Transfer
 * @subpackage Advance_Bank_Transfer/admin
 * @author     Ashutosh Verma <vermaa947@gmail.com>
 */
class Advance_Bank_Transfer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Advance_Bank_Transfer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Advance_Bank_Transfer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/advance-bank-transfer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Advance_Bank_Transfer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Advance_Bank_Transfer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name . 'scripts', plugin_dir_url( __FILE__ ) . 'js/advance-bank-transfer-admin.js', array( 'jquery' ), $this->version, false );
	}



	/**
	 * Register the Advance Bank Transfer gateway.
	 *
	 * @since    1.0.0
	 */
	public function include_gateway_class() {

		/**
		 * The class responsible for defining all actions that occur in the gateway configuration and functioning.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/gateway/class-advance-bank-transfer-gateway.php';
	}


	/**
	 * Register the Advance Bank Transfer gateway.
	 *
	 * @since    1.0.0
	 */
	public function register_payment_gateway(  $available_gateways  ) {

		if ( class_exists( 'WC_Gateway_Advance_BACS' ) ) {

			$available_gateways[] = 'WC_Gateway_Advance_BACS';
		}
		
		return $available_gateways;
	}

// End of class.
}
