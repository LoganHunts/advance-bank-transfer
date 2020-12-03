<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.linkedin.com/in/ashutosh-verma-8609aa15b/
 * @since      1.0.0
 *
 * @package    Advance_Bank_Transfer
 * @subpackage Advance_Bank_Transfer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Advance_Bank_Transfer
 * @subpackage Advance_Bank_Transfer/public
 * @author     Ashutosh Verma <vermaa947@gmail.com>
 */
class Advance_Bank_Transfer_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/advance-bank-transfer-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/advance-bank-transfer-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'adv_bank_transfer',
			array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'auth_nonce'    => wp_create_nonce( 'auth_adv_nonce' ),
			)
		);
	}
	

	/**
	 * Register the AJAX Callback for file upload.
	 *
	 * @since    1.0.0
	 */
	public function perform_upload(){

		// Nonce verification.
		check_ajax_referer( 'auth_adv_nonce', 'auth_nonce' );

		if ( ! empty( $_FILES['receipt']['name'] ) ) {

			$errors = array();
			$file_name = $_FILES['receipt']['name'];
			$file_tmp = $_FILES['receipt']['tmp_name'];
			$file_type = $_FILES['receipt']['type'];
		
			$file_data = explode( '.', $file_name );
			$file_ext = end( $file_data );
			$file_ext = strtolower( $file_ext );

			$gateway_options = new WC_Gateway_Advance_BACS();
			$settings = ! empty( $gateway_options->settings ) ? $gateway_options->settings : array();
			$extensions = ! empty( $settings['support_formats'] ) ? $settings['support_formats'] : array();
		
			if ( ! empty( $file_ext ) ) {
				if( ! in_array( $file_ext, $extensions ) ) {
					$errors[] = "Extension not supported.";
				}
			}
				
			if( empty( $errors ) ) {

				$receipt_dir = '/wc-receipt-submissions/';
				$upload_dir_data = wp_upload_dir();
				$base_dir = ! empty( $upload_dir_data[ 'basedir' ] ) ? $upload_dir_data[ 'basedir' ] : '';
				$base_url = ! empty( $upload_dir_data[ 'baseurl' ] ) ? $upload_dir_data[ 'baseurl' ] : '';
			
				if ( ! is_dir( $base_dir . $receipt_dir ) ) {
					mkdir( $base_dir . $receipt_dir, 0755, true );
				}

				move_uploaded_file( $file_tmp, $base_dir . $receipt_dir . $file_name );
				echo json_encode(
					array(
						'result'    => 'success',
						'path'    => $base_dir . $receipt_dir . $file_name,
						'url'    => $base_url . $receipt_dir . $file_name,
					)
				);
			} else {
				echo json_encode(
					array(
						'result'    => 'failure',
						'errors'    =>  $errors
					)
				);
			}
			
			wp_die();
		}
	}

	/**
	 * Register the AJAX Callback for file delete.
	 *
	 * @since    1.0.0
	 */
	public function remove_current_upload(){

		// Nonce verification.
		check_ajax_referer( 'auth_adv_nonce', 'auth_nonce' );

		$file_path = ! empty( $_POST[ 'path' ] ) ? $_POST[ 'path' ] : '';
		if ( ! empty( $file_path ) ) {

			// Check file exist or not 
			if( file_exists( $file_path ) ){ 
				// Remove file 
				unlink( $file_path ); 
		  
				echo json_encode(
					array(
						'result'    => 'success',
					)
				);
			} else {
				echo json_encode(
					array(
						'result'    => 'failure',
					)
				);
			}
			wp_die();
		}
	}

// End of class.
}
