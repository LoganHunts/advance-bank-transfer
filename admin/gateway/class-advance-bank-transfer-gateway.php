<?php
/**
 * Provide a Class WC_Gateway_Advance_BACS file.
 *
 * This file is used to markup the configuration aspects of the plugin.
 *
 * @link       https://www.linkedin.com/in/ashutosh-verma-8609aa15b/
 * @since      1.0.0
 *
 * @package    Advance_Bank_Transfer
 * @subpackage Advance_Bank_Transfer/admin/gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bank Transfer Payment Gateway.
 *
 * Provides a Bank Transfer Payment Gateway. Based on code by Mike Pepper.
 *
 * @class       WC_Gateway_BACS
 * @extends     WC_Payment_Gateway
 * @version     2.1.0
 * @package     WooCommerce\Classes\Payment
 */
class WC_Gateway_Advance_BACS extends WC_Payment_Gateway {

	/**
	 * Array of locales
	 *
	 * @var array
	 */
	public $locale;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		$this->id                 = 'adv_bacs';
		$this->icon               = apply_filters( 'woocommerce_bacs_icon', '' );
		$this->has_fields         = false;
		$this->method_title       = __( 'Advance Direct bank transfer', 'advance-bank-transfer' );
		$this->method_description = __( 'Take payments in person via BACS. More commonly known as direct bank/wire transfer', 'advance-bank-transfer' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions' );

		// BACS account fields shown on the thanks page and in emails.
		$this->account_details = get_option(
			'woocommerce_bacs_accounts',
			array(
				array(
					'account_name'   => $this->get_option( 'account_name' ),
					'account_number' => $this->get_option( 'account_number' ),
					'sort_code'      => $this->get_option( 'sort_code' ),
					'bank_name'      => $this->get_option( 'bank_name' ),
					'iban'           => $this->get_option( 'iban' ),
					'bic'            => $this->get_option( 'bic' ),
				),
			)
		);

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'save_account_details' ) );
		add_action( 'woocommerce_thankyou_bacs', array( $this, 'thankyou_page' ) );

		add_filter( 'woocommerce_gateway_description', array( $this, 'add_receipt_fields_html' ), 10, 2 );
		
		// Customer Emails.
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		global $woocommerce;
		$countries_obj   = new WC_Countries();
		$countries   = $countries_obj->__get('countries');
		$this->form_fields = array(
			'enabled'         => array(
				'title'   => __( 'Enable/Disable', 'advance-bank-transfer' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable bank transfer', 'advance-bank-transfer' ),
				'default' => 'no',
			),
			'title'           => array(
				'title'       => __( 'Title', 'advance-bank-transfer' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'advance-bank-transfer' ),
				'default'     => __( 'Advance Direct bank transfer', 'advance-bank-transfer' ),
				'desc_tip'    => true,
			),
			'description'     => array(
				'title'       => __( 'Description', 'advance-bank-transfer' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'advance-bank-transfer' ),
				'default'     => __( 'Make your payment directly into our bank account and upload receipt below. Your order will not be shipped until the funds have cleared in our account.', 'advance-bank-transfer' ),
				'desc_tip'    => true,
			),
			'instructions'    => array(
				'title'       => __( 'Instructions', 'advance-bank-transfer' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page and emails.', 'advance-bank-transfer' ),
				'default'     => 'Thank you for your Order. We will review your payment receipt and process your Order accordingly.',
				'desc_tip'    => true,
			),
			'account_details' => array(
				'type' => 'account_details',
			),
			'xxclude_countries'    => array(
				'title'       => __( 'Exclude Countries', 'advance-bank-transfer' ),
				'type'        => 'multiselect',
				'options'        => $countries,
				'description' => __( 'The payment method won\'t be shown if billing country is one of these.', 'advance-bank-transfer' ),
				'default'     => '',
				'desc_tip'    => true,
			),
		);
		
	}

	/**
	 * Generate account details html.
	 *
	 * @return string
	 */
	public function generate_account_details_html() {

		ob_start();

		$country = WC()->countries->get_base_country();
		$locale  = $this->get_country_locale();

		// Get sortcode label in the $locale array and use appropriate one.
		$sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort code', 'advance-bank-transfer' );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html_e( 'Account details:', 'advance-bank-transfer' ); ?></th>
			<td class="forminp" id="bacs_accounts">
				<div class="wc_input_table_wrapper">
					<table class="widefat wc_input_table sortable" cellspacing="0">
						<thead>
							<tr>
								<th class="sort">&nbsp;</th>
								<th><?php esc_html_e( 'Account name', 'advance-bank-transfer' ); ?></th>
								<th><?php esc_html_e( 'Account number', 'advance-bank-transfer' ); ?></th>
								<th><?php esc_html_e( 'Bank name', 'advance-bank-transfer' ); ?></th>
								<th><?php echo esc_html( $sortcode ); ?></th>
								<th><?php esc_html_e( 'IBAN', 'advance-bank-transfer' ); ?></th>
								<th><?php esc_html_e( 'BIC / Swift', 'advance-bank-transfer' ); ?></th>
							</tr>
						</thead>
						<tbody class="accounts">
							<?php
							$i = -1;
							if ( $this->account_details ) {
								foreach ( $this->account_details as $account ) {
									$i++;

									echo '<tr class="account">
										<td class="sort"></td>
										<td><input type="text" value="' . esc_attr( wp_unslash( $account['account_name'] ) ) . '" name="bacs_account_name[' . esc_attr( $i ) . ']" /></td>
										<td><input type="text" value="' . esc_attr( $account['account_number'] ) . '" name="bacs_account_number[' . esc_attr( $i ) . ']" /></td>
										<td><input type="text" value="' . esc_attr( wp_unslash( $account['bank_name'] ) ) . '" name="bacs_bank_name[' . esc_attr( $i ) . ']" /></td>
										<td><input type="text" value="' . esc_attr( $account['sort_code'] ) . '" name="bacs_sort_code[' . esc_attr( $i ) . ']" /></td>
										<td><input type="text" value="' . esc_attr( $account['iban'] ) . '" name="bacs_iban[' . esc_attr( $i ) . ']" /></td>
										<td><input type="text" value="' . esc_attr( $account['bic'] ) . '" name="bacs_bic[' . esc_attr( $i ) . ']" /></td>
									</tr>';
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="7"><a href="#" class="add button"><?php esc_html_e( '+ Add account', 'advance-bank-transfer' ); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e( 'Remove selected account(s)', 'advance-bank-transfer' ); ?></a></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<script type="text/javascript">
					jQuery(function() {
						jQuery('#bacs_accounts').on( 'click', 'a.add', function(){

							var size = jQuery('#bacs_accounts').find('tbody .account').length;

							jQuery('<tr class="account">\
									<td class="sort"></td>\
									<td><input type="text" name="bacs_account_name[' + size + ']" /></td>\
									<td><input type="text" name="bacs_account_number[' + size + ']" /></td>\
									<td><input type="text" name="bacs_bank_name[' + size + ']" /></td>\
									<td><input type="text" name="bacs_sort_code[' + size + ']" /></td>\
									<td><input type="text" name="bacs_iban[' + size + ']" /></td>\
									<td><input type="text" name="bacs_bic[' + size + ']" /></td>\
								</tr>').appendTo('#bacs_accounts table tbody');

							return false;
						});
					});
				</script>
			</td>
		</tr>
		<?php
		return ob_get_clean();

	}

	/**
	 * Save account details table.
	 */
	public function save_account_details() {

		$accounts = array();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verification already handled in WC_Admin_Settings::save()
		if ( isset( $_POST['bacs_account_name'] ) && isset( $_POST['bacs_account_number'] ) && isset( $_POST['bacs_bank_name'] )
			 && isset( $_POST['bacs_sort_code'] ) && isset( $_POST['bacs_iban'] ) && isset( $_POST['bacs_bic'] ) ) {

			$account_names   = wc_clean( wp_unslash( $_POST['bacs_account_name'] ) );
			$account_numbers = wc_clean( wp_unslash( $_POST['bacs_account_number'] ) );
			$bank_names      = wc_clean( wp_unslash( $_POST['bacs_bank_name'] ) );
			$sort_codes      = wc_clean( wp_unslash( $_POST['bacs_sort_code'] ) );
			$ibans           = wc_clean( wp_unslash( $_POST['bacs_iban'] ) );
			$bics            = wc_clean( wp_unslash( $_POST['bacs_bic'] ) );

			foreach ( $account_names as $i => $name ) {
				if ( ! isset( $account_names[ $i ] ) ) {
					continue;
				}

				$accounts[] = array(
					'account_name'   => $account_names[ $i ],
					'account_number' => $account_numbers[ $i ],
					'bank_name'      => $bank_names[ $i ],
					'sort_code'      => $sort_codes[ $i ],
					'iban'           => $ibans[ $i ],
					'bic'            => $bics[ $i ],
				);
			}
		}
		// phpcs:enable

		update_option( 'woocommerce_bacs_accounts', $accounts );
	}


	/**
	 * Output for the order received page.
	 *
	 * @param int $order_id Order ID.
	 */
	public function payment_fields() {

		if ( $description = $this->get_description() ) {
			echo wpautop( wptexturize( $description ) );
		}
		$this->bank_details( $billing_country );
	}

	/**
	 * Output for the receipt fields.
	 *
	 * @param int $desc 	Description.
	 * @param int $id	 	Gateway ID.
	 */
	public function add_receipt_fields_html( $desc = '', $id='' ) {

		if( ! empty( $id ) && $id == $this->id ) {
			
			return $desc . $this->return_receipt_fields_html();
		}

		return $desc;
	}

	/**
	 * Output for the receipt fields html.
	 */
	public function return_receipt_fields_html() {

		ob_start(); ?>

		<div class="adv_receipt_wrapper">
			<div class="adv_receipt_field">
				<input type="file" name="adv_receipt_attachment" class="adv_receipt_attachment"/>
			</div>
			<div id="progress-wrp" class="is_hidden">
				<div class="progress-bar"></div>
				<div class="status">0%</div>
			</div>
			<div class="adv_receipt_field is_hidden">
				<a href="javascript:void(0);" class="adv_receipt_remove_attachment"><?php esc_html_e( 'Remove File', 'advance-bank-transfer' ); ?></a>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Output for the order received page.
	 *
	 * @param int $order_id Order ID.
	 */
	public function thankyou_page( $order_id ) {

		if ( $this->instructions ) {
			echo wp_kses_post( wpautop( wptexturize( wp_kses_post( $this->instructions ) ) ) );
		}
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param WC_Order $order Order object.
	 * @param bool     $sent_to_admin Sent to admin.
	 * @param bool     $plain_text Email format: plain text or HTML.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

		if ( ! $sent_to_admin && $this->id === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
			if ( $this->instructions ) {
				echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
			}
		}
	}

   /**
	 * Get bank details and place into a list format.
	 *
	 * @param int $billing_country Id.
	 */
	private function bank_details( $billing_country = '' ) {

		if ( empty( $this->account_details ) ) {
			return;
		}

		// Get the country $locale.
		$locale  = $this->get_country_locale();

		// Get sortcode label in the $locale array and use appropriate one.
		$sortcode = isset( $locale[ $billing_country ]['sortcode']['label'] ) ? $locale[ $billing_country ]['sortcode']['label'] : __( 'Sort code', 'advance-bank-transfer' );

		$bacs_accounts = apply_filters( 'woocommerce_adv_bacs_accounts', $this->account_details, $billing_country );

		if ( ! empty( $bacs_accounts ) ) {
			$account_html = '';
			$has_details  = false;

			foreach ( $bacs_accounts as $bacs_account ) {
				$bacs_account = (object) $bacs_account;

				if ( $bacs_account->account_name ) {
					$account_html .= '<h3 class="wc-bacs-bank-details-account-name">' . wp_kses_post( wp_unslash( $bacs_account->account_name ) ) . ':</h3>' . PHP_EOL;
				}

				$account_html .= '<ul class="wc-bacs-bank-details order_details bacs_details">' . PHP_EOL;

				// BACS account fields shown on the thanks page and in emails.
				$account_fields = apply_filters(
					'woocommerce_bacs_account_fields',
					array(
						'bank_name'      => array(
							'label' => __( 'Bank', 'advance-bank-transfer' ),
							'value' => $bacs_account->bank_name,
						),
						'account_number' => array(
							'label' => __( 'Account number', 'advance-bank-transfer' ),
							'value' => $bacs_account->account_number,
						),
						'sort_code'      => array(
							'label' => $sortcode,
							'value' => $bacs_account->sort_code,
						),
						'iban'           => array(
							'label' => __( 'IBAN', 'advance-bank-transfer' ),
							'value' => $bacs_account->iban,
						),
						'bic'            => array(
							'label' => __( 'BIC', 'advance-bank-transfer' ),
							'value' => $bacs_account->bic,
						),
					),
					$billing_country
				);

				foreach ( $account_fields as $field_key => $field ) {
					if ( ! empty( $field['value'] ) ) {
						$account_html .= '<li class="' . esc_attr( $field_key ) . '">' . wp_kses_post( $field['label'] ) . ': <strong>' . wp_kses_post( wptexturize( $field['value'] ) ) . '</strong></li>' . PHP_EOL;
						$has_details   = true;
					}
				}

				$account_html .= '</ul>';
			}

			if ( $has_details ) {
				echo '<section class="woocommerce-bacs-bank-details"><h2 class="wc-bacs-bank-details-heading">' . esc_html__( 'Our bank details', 'advance-bank-transfer' ) . '</h2>' . wp_kses_post( PHP_EOL . $account_html ) . '</section>';
			}
		}

	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( $order->get_total() > 0 ) {
			// Mark as on-hold (we're awaiting the payment).
			$order->update_status( apply_filters( 'woocommerce_bacs_process_payment_order_status', 'on-hold', $order ), __( 'Awaiting BACS payment', 'advance-bank-transfer' ) );
		} else {
			$order->payment_complete();
		}

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);

	}

	/**
	 * Get country locale if localized.
	 *
	 * @return array
	 */
	public function get_country_locale() {

		if ( empty( $this->locale ) ) {

			// Locale information to be used - only those that are not 'Sort Code'.
			$this->locale = apply_filters(
				'woocommerce_get_bacs_locale',
				array(
					'AU' => array(
						'sortcode' => array(
							'label' => __( 'BSB', 'advance-bank-transfer' ),
						),
					),
					'CA' => array(
						'sortcode' => array(
							'label' => __( 'Bank transit number', 'advance-bank-transfer' ),
						),
					),
					'IN' => array(
						'sortcode' => array(
							'label' => __( 'IFSC', 'advance-bank-transfer' ),
						),
					),
					'IT' => array(
						'sortcode' => array(
							'label' => __( 'Branch sort', 'advance-bank-transfer' ),
						),
					),
					'NZ' => array(
						'sortcode' => array(
							'label' => __( 'Bank code', 'advance-bank-transfer' ),
						),
					),
					'SE' => array(
						'sortcode' => array(
							'label' => __( 'Bank code', 'advance-bank-transfer' ),
						),
					),
					'US' => array(
						'sortcode' => array(
							'label' => __( 'Routing number', 'advance-bank-transfer' ),
						),
					),
					'ZA' => array(
						'sortcode' => array(
							'label' => __( 'Branch code', 'advance-bank-transfer' ),
						),
					),
				)
			);

		}

		return $this->locale;

	}
}
