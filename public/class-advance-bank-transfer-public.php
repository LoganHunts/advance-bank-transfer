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
				'ajaxurl'       => plugin_dir_url( __FILE__ ) . 'uploads-helper.php',
				'base_dir'    => defined( 'ABSPATH' ) ? ABSPATH : '',
			)
		);
	}
	

	/**
	 * Register the AJAX Callback for file upload.
	 *
	 * @since    1.0.0
	 */
	public function perform_upload(){
		echo '<pre>'; print_r( $_POST ); echo '</pre>'; 
		echo '<pre>'; print_r( $_FILE ); echo '</pre>'; 
		die();
		if (!empty($_FILES['fqfiles']['name'])) {
			// echo '<pre>';
			// print_r($_FILES);
			// echo '</pre>';
			// echo '<pre>'; print_r( $_POST['fqfile'] ); echo '</pre>';
			$errors = array();
			$file_name = $_FILES['fqfiles']['name'];
			// $file_size   = $_FILES['fqfile']['size'];
			$file_tmp = $_FILES['fqfiles']['tmp_name'];
			$file_type = $_FILES['fqfiles']['type'];
			$file_ext = strtolower(end(explode('.', $_FILES['fqfiles']['name'])));

			$extensions = array("pdf", "docx", "txt", "png");
			//echo '<pre>'; print_r( $FILE ); echo '</pre>';
			// die();
			if (!empty($file_ext)) {
				if (in_array($file_ext, $extensions) === false) {
					$errors[] = "extension not allowed, please choose a pdf or docx file.";
				}
			}
			$log_dir = ABSPATH . "wp-content/uploads/quote-submission";
			if (!is_dir($log_dir)) {

				mkdir($log_dir, 0755, true);
			}

			$mwb_gaq_form_data['fqfilename'] = '';

			if (empty($errors) == true) {
				$mwb_gaq_form_data['fqfilename'] = "quote_" . $post_id . "." . $file_ext;
				move_uploaded_file($file_tmp, $log_dir . "/" . $mwb_gaq_form_data['fqfilename']);
				echo "Success";

			} else {
				echo "\t";
				print_r($errors);
				echo "\t";
			}
		}

	}

// End of class.
}
