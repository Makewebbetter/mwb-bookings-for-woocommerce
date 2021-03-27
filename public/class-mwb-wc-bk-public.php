<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Mwb_Wc_Bk
 * @subpackage Mwb_Wc_Bk/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mwb_Wc_Bk
 * @subpackage Mwb_Wc_Bk/public
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Mwb_Wc_Bk_Public {

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
	 * Instance for the MWB_Woocommerce_Booking class
	 *
	 * @var obj
	 */
	public $mwb_booking;

	/**
	 * Slot for a particular booking product
	 *
	 * @var array
	 */
	public $mwb_product_slots = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name The name of the plugin.
	 * @param    string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// $this->mwb_booking = MWB_Woocommerce_Booking::get_booking_instance();
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
		 * defined in Mwb_Wc_Bk_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Wc_Bk_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mwb-wc-bk-public.css', array(), $this->version, 'all' );

		// wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), '1.12.0' );
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
		 * defined in Mwb_Wc_Bk_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Wc_Bk_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mwb-wc-bk-public.js', array( 'jquery' ), $this->version, false );

		// wp_enqueue_script( 'jquery-ui-dialog' );

		// Load the datepicker script (pre-registered in WordPress).
		wp_enqueue_script( 'jquery-ui-datepicker' );

		$args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'mwb_wc_bk_public' ),
		);

		if ( is_single() || is_singular() ) {
			$p_id    = get_the_id();
			$product = wc_get_product( $p_id );
			if ( $product && $product->is_type( 'mwb_booking' ) ) {
				$args['mwb_booking_product_page'] = 'true';
			}
		}

		if ( is_product() ) {
			$args['product_settings'] = get_post_meta( get_the_id() );
			$args['global_settings']  = get_option( 'mwb_booking_settings_options' );
			$args['current_date']     = gmdate( 'Y-m-d' );
			$args['not_allowed_days'] = maybe_unserialize( $args['product_settings']['mwb_booking_not_allowed_days'][0] );

			//$data = $this->mwb_booking_slot_management();
			// $args['slots'] = $data['slot_arr'];
			// $args['unavailable_dates'] = $data['unavail_dates'];

			//$args['slots']             = get_post_meta( get_the_id(), 'mwb_booking_product_slots', true );
			//$args['unavailable_dates'] = get_post_meta( get_the_id(), 'mwb_booking_unavailable_dates', true );
			// // echo '<pre>'; print_r( $args['product_settings'] ); echo '</pre>'; die("ok");
			// global $product;
			// echo '<pre>'; print_r( wc_get_product( $product ) ); echo '</pre>';die('first');
		}

		wp_localize_script(
			$this->plugin_name,
			'mwb_wc_bk_public',
			$args
		);
		// add_thickbox();

	}

	/**
	 * Add the form to the product type 'mwb_booking'.
	 *
	 * @return void
	 */
	public function mwb_include_booking_add_to_cart() {
		global $product;

		// $product_id = $product->get_id();
		if ( $product && $product->is_type( 'mwb_booking' ) ) {

			wc_get_template( 'single-product/add-to-cart/mwb-booking.php', array( 'slots' => $this->mwb_product_slots ), '', MWB_WC_BK_TEMPLATE_PATH );

			// $this->mwb_booking_slot_management( $product_id, $product );
		}
	}

	/**
	 * Slot management for the booking product
	 *
	 * @param [int] $product_id ID of the product.
	 * @param [obj] $product Product Object.
	 * @return void
	 */
	public function mwb_booking_slot_management() {

		global $product;
		$product_id   = $product->get_id();
		$product_meta = get_post_meta( $product_id );

		// $slots = get_post_meta( $product_id, 'mwb_booking_product_slots', true );

		// if ( empty( $slots ) ) {

		$slots = array();
		// }

		// $product_creation_ts = strtotime( get_the_date( 'Y-m-d', $product_id ) );
		// $product_modified_ts = strtotime( get_the_modified_date( 'Y-m-d', $product_id ) );

		// if ( $product_modified_ts ) {
		// 	$product_creation_ts = $product_modified_ts;
		// }

		$current_date = gmdate( 'Y-m-d', time() );
		$current_ts   = strtotime( $current_date );

		// if ( $current_ts > $product_modified_ts ) {
		// $product_creation_ts = $current_ts;
		// }

		// if ( empty( $slots ) ) {
		$start_date = gmdate( 'Y-m-d', $current_ts );
		// echo '<pre>'; print_r( $start_date ); echo '</pre>';

		$start_booking     = isset( $product_meta['mwb_start_booking_from'][0] ) ? $product_meta['mwb_start_booking_from'][0] : '';
		$daily_start_time  = isset( $product_meta['mwb_booking_start_time'][0] ) ? $product_meta['mwb_booking_start_time'][0] : '00:01';
		$daily_end_time    = isset( $product_meta['mwb_booking_end_time'][0] ) ? $product_meta['mwb_booking_end_time'][0] : '23:59';
		$min_advance_input = isset( $product_meta['mwb_advance_booking_min_input'][0] ) ? $product_meta['mwb_advance_booking_min_input'][0] : 1;
		$max_advance_input = isset( $product_meta['mwb_advance_booking_max_input'][0] ) ? $product_meta['mwb_advance_booking_max_input'][0] : 1;
		$min_advance_dura  = isset( $product_meta['mwb_advance_booking_min_duration'][0] ) ? $product_meta['mwb_advance_booking_min_duration'][0] : 'day';
		$max_advance_dura  = isset( $product_meta['mwb_advance_booking_max_duration'][0] ) ? $product_meta['mwb_advance_booking_max_duration'][0] : 'day';
		$unit_input        = ! empty( $product_meta['mwb_booking_unit_input'][0] ) ? $product_meta['mwb_booking_unit_input'][0] : '';
		$unit_duration     = ! empty( $product_meta['mwb_booking_unit_duration'][0] ) ? $product_meta['mwb_booking_unit_duration'][0] : '';

		if ( 'today' === $start_booking ) {
			$start_date = gmdate( 'Y-m-d', $current_ts );
			// echo 'today';
		} elseif ( 'tomorrow' === $start_booking ) {
			$start_date = gmdate( 'Y-m-d', strtotime( '+1 day', $current_ts ) );
			// echo 'tomorrow';
		} elseif ( 'custom_date' === $start_booking ) {
			$custom_date = isset( $product_meta['mwb_start_booking_custom_date'][0] ) ? $product_meta['mwb_start_booking_custom_date'][0] : '';
			if ( strtotime( $custom_date ) < strtotime( gmdate( 'Y-m-d', time() ) ) ) {
				$custom_date = gmdate( 'Y-m-d', time() );
			}
			$start_date = gmdate( 'Y-m-d', strtotime( $custom_date ) );
			if ( strtotime( $custom_date ) < $current_ts ) {
				$start_date = gmdate( 'Y-m-d', strtotime( $current_ts ) );
			}
			// echo 'custom';
		} elseif ( 'initially_available' === $start_booking ) {
			// $start_str  = '-' . $min_avail_input . ' ' . $min_avail_dura . '';
			$start_date = gmdate( 'Y-m-d', strtotime( '+' . $min_advance_input . ' ' . $min_advance_dura . '', $current_ts ) );
			// echo 'initial';
		}

		// echo '<pre>'; print_r( $start_date ); echo '</pre>';
		$end_date = gmdate( 'Y-m-d', strtotime( '+' . $max_advance_input . ' ' . $max_advance_dura . '', strtotime( $start_date ) ) );
		// $step     = '+' . $unit_input;
		$slots = $this->date_range( $start_date, $end_date, '+1 day', 'Y-m-d' );

		// echo '<pre>'; print_r( $slots ); echo '</pre>';die('lkl');

		if ( ! empty( $unit_duration ) && ! empty( $unit_input ) ) {
			if ( ! empty( $slots ) && is_array( $slots ) ) {
				foreach ( $slots as $date => $slot ) {
					$start_time = strtotime( $daily_start_time, strtotime( $date ) );
					$end_time   = strtotime( '+' . $unit_input . ' ' . $unit_duration, $start_time );
					$s          = array();

					if ( 'hour' === $unit_duration || 'minute' === $unit_duration ) {
						while ( $end_time <= strtotime( $daily_end_time, strtotime( $date ) ) ) {

							$s[ gmdate( 'H:i:s', $start_time ) . '-' . gmdate( 'H:i:s', $end_time ) ] = array(
								'book'          => 'bookable',
								'booking_count' => 0,
							);

							$start_time = $end_time;
							$end_time   = strtotime( '+' . $unit_input . ' ' . $unit_duration, $start_time );

						}
					} elseif ( 'day' === $unit_duration ) {

						$start_time = gmdate( 'H:i:s', strtotime( $daily_start_time, strtotime( $date ) ) );
						$end_time   = gmdate( 'H:i:s', strtotime( $daily_end_time, strtotime( $date ) ) );

						$s[ $start_time . '-' . $end_time ] = array(
							'book'          => 'bookable',
							'booking_count' => 0,
						);
					}
					$slots[ $date ] = $s;
				}
			}
		}
			// update_post_meta( $product_id, 'mwb_booking_product_slots', $slots );
		// }

		// $availability_rules = get_option( 'mwb_global_avialability_rules', array() );
		// $availabiltiy_count = get_option( 'mwb_global_availability_rules_count', 0 );
		// echo '<pre>'; print_r( $availabiltiy_count ); echo '</pre>';

		// echo '<pre>'; print_r( $slots ); echo '</pre>';die('klkl');

		$availability_instance = MWB_Woocommerce_Booking_Availability::get_availability_instance();

		$slot_arr = $availability_instance->check_product_global_availability( $product_id, $slots );
		$slot_arr = $availability_instance->check_product_setting_availability( $product_id, $slot_arr );
		$slot_arr = $availability_instance->manage_avaialability_acc_to_created_bookings( $product_id, $slot_arr );
		echo '<pre>'; print_r( $slot_arr ); echo '</pre>';

		$unavail_dates = $availability_instance->fetch_unavailable_dates( $slot_arr );
		// echo '<pre>'; print_r( $unavail_dates ); echo '</pre>';

		// return compact( 'slot_arr', 'unavail_dates' );

		echo '<div id="booking-slots-data" slots="' . esc_html( htmlspecialchars( wp_json_encode( $slot_arr ) ) ) . '" unavail_dates="' . esc_html( htmlspecialchars( wp_json_encode( $unavail_dates ) ) ) . '" ></div>';

		//update_post_meta( $product_id, 'mwb_booking_unavailable_dates', $unavail_dates );
		update_post_meta( $product_id, 'mwb_booking_product_slots', $slot_arr );
		// $this->mwb_product_slots = $slot_arr;
		// echo '<pre>'; print_r( $slot_arr ); echo '</pre>';
		// die('pl');
	}

	/**
	 * Calculate dates between start and end date.
	 *
	 * @param [date] $first Start Date.
	 * @param [date] $last Last Date.
	 * @param string $step Step to increment the date.
	 * @param string $output_format DAte format.
	 * @return array
	 */
	public function date_range( $first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {

		$dates   = array();
		$current = strtotime( $first );
		$last    = strtotime( $last );

		while ( $current <= $last ) {
			$dates[ gmdate( $output_format, $current ) ] = array();

			$current = strtotime( $step, $current );
		}

		return $dates;
	}

	/**
	 * Show time field on the booking form if the duration is in hours or minutes.
	 * Ajax hander
	 *
	 * @return void
	 */
	public function mwb_time_slots_in_booking_form() {

		check_ajax_referer( 'mwb_wc_bk_public', 'nonce' );

		$product_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$start_date = isset( $_POST['date'] ) ? $_POST['date'] : '';

		$start_date = gmdate( 'Y-m-d', strtotime( $start_date ) );

		$unit_duration = get_post_meta( $product_id, 'mwb_booking_unit_duration', true );

		$slots = isset( $_POST['slots'] ) ? $_POST['slots'] : array();
		// $slots = get_post_meta( $product_id, 'mwb_booking_product_slots', true );
		// echo '<pre>'; print_r( $slots ); echo '</pre>';die('slots');

		if ( ! empty( $product_id ) && ! empty( $start_date ) ) {
			if ( 'hour' === $unit_duration || 'minute' === $unit_duration ) {
				if ( array_key_exists( $start_date, $slots ) ) {
					?>
					<!-- <div id="mwb-wc-bk-time-section" class="mwb-wc-bk-form-section" > -->
						<div id="mwb-wc-bk-time-slot-field">
							<label for="mwb-wc-bk-time-slot-input"><?php esc_html_e( 'Time', 'mwb-wc-bk' ); ?></label>
							<select type="text" id="mwb-wc-bk-time-slot-input" class="mwb-wc-bk-form-input mwb-wc-bk-form-input-time" name="time_slot" required>
								<?php
								if ( ! empty( $slots ) ) {
									foreach ( $slots[ $start_date ] as $k => $v ) {
										if ( 'bookable' === $v['book'] ) {
											$s = explode( '-', $k );
											?>
											<option value="<?php echo strtotime( $s[0], strtotime( $start_date ) ); ?>">
											<?php
											echo gmdate( 'h:i:s a', strtotime( $s[0], strtotime( $start_date ) ) );
											?>
											</option>
											<?php
										} else {
											$s = explode( '-', $k );
											?>
											<option value="<?php strtotime( $s[0], strtotime( $start_date ) ); ?>" disabled ><?php echo gmdate( 'h:i:s a', strtotime( $s[0], strtotime( $start_date ) ) ); ?></option>
											<?php
										}
									}
								}
								?>
							</select>
						</div>
					<!-- </div> -->
					<?php
				}
			}
		}
		wp_die();
	}

	public function mwb_check_time_slot_availability() {

		check_ajax_referer( 'mwb_wc_bk_public', 'nonce' );

		$slots      = isset( $_POST['slots'] ) ? $_POST['slots'] : array();
		$time_slot  = isset( $_POST['time_slot'] ) ? $_POST['time_slot'] : '';
		$start_date = isset( $_POST['start_date'] ) ? $_POST['start_date'] : array();
		$duration   = isset( $_POST['duration'] ) ? $_POST['duration'] : '';

		echo '<pre>'; print_r( $slots ); echo '</pre>';
		echo '<pre>'; print_r( $time_slot ); echo '</pre>';
		echo '<pre>'; print_r( $start_date ); echo '</pre>';
		echo '<pre>'; print_r( $duration ); echo '</pre>';

		$time = gmdate( 'H:i:s', $time_slot );
		// echo '<pre>'; print_r( $time ); echo '</pre>';
		$arr    = $slots[ $start_date ];
		$result = array( 'status' => true );
		$count  = 0;
		foreach ( $arr as $k => $v ) {

			$time2 = explode( '-', $k );
			$time3 = $time2[0];
			if ( strtotime( $time, strtotime( $start_date ) ) <= strtotime( $time3, strtotime( $start_date ) ) ) {
				$count++;
				// echo 'working';
				if ( $count <= $duration ) {
					if ( 'non-bookable' === $k['book'] ) {
						$result['status'] = false;
					}
				} else {
					break;
				}
			}
		}
		echo wp_json_encode( $result );
		// wp_die();
	}

	/**
	 * Add the form fields to the Product of type 'mwb_booking'
	 *
	 * @return void
	 */
	public function mwb_booking_add_to_cart_form_fields() {
		global $product;
		$product_data = array(
			'product_id' => $product->get_id(),
		);
		// $this->mwb_booking = MWB_Woocommerce_Booking::get_booking_instance();
		if ( $product && $product->is_type( 'mwb_booking' ) ) {
			?>
			<div id="mwb-wc-bk-create-booking-form" product-data = "<?php echo esc_html( htmlspecialchars( wp_json_encode( $product_data ) ) ); ?>" >
				<?php
					wc_get_template( 'single-product/add-to-cart/form/duration-check.php', array(), '', MWB_WC_BK_TEMPLATE_PATH );
					wc_get_template( 'single-product/add-to-cart/form/dates-check.php', array(), '', MWB_WC_BK_TEMPLATE_PATH );
					wc_get_template( 'single-product/add-to-cart/form/people-check.php', array(), '', MWB_WC_BK_TEMPLATE_PATH );
					wc_get_template( 'single-product/add-to-cart/form/service-check.php', array(), '', MWB_WC_BK_TEMPLATE_PATH );
					wc_get_template( 'single-product/add-to-cart/form/show-total.php', array(), '', MWB_WC_BK_TEMPLATE_PATH );
				?>
			</div>
			<?php
		}
	}

	/**
	 * Ajax Handler for the Added form fields for the product.
	 *
	 * @return void
	 */
	// public function mwb_wc_bk_update_add_to_cart() {

	// 	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	// 	if ( ! wp_verify_nonce( $nonce, 'mwb_wc_bk_public' ) ) {
	// 		die( 'Nonce value cannot be verified' );
	// 	}
	// 	$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
	// 	$duration   = isset( $_POST['duration'] ) ? sanitize_text_field( wp_unslash( $_POST['duration'] ) ) : '';

	// 	$product_meta  = get_post_meta( $product_id );
	// 	$duration_cost = 0;

	// 	$price       = ! empty( $product_meta['mwb_booking_unit_cost_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_unit_cost_input'][0] ) ) : '';
	// 	$added_costs = ! empty( $product_meta['mwb_booking_added_cost_select'][0] ) ? maybe_unserialize( $product_meta['mwb_booking_added_cost_select'][0] ) : '';

	// 	$duration_cost += ( $price * $duration );
	// 	// echo '<pre>'; print_r( $duration_cost ); echo '</pre>';die( 'ok' );

	// 	if ( is_array( $added_costs ) && ! empty( $added_costs ) ) {
	// 		foreach ( $added_costs as $cost_id ) {
	// 			// $cost_term      = get_term( $cost_id );
	// 			$cost_term_meta = get_term_meta( $cost_id );
	// 			// echo '<pre>'; print_r( $cost_term_meta ); echo '</pre>';die("ok");
	// 			if ( ! empty( $cost_term_meta['mwb_booking_ct_costs_multiply_units'][0] ) && 'yes' === $cost_term_meta['mwb_booking_ct_costs_multiply_units'][0] ) {
	// 				// die("ok");
	// 				$cost_price     = ! empty( $cost_term_meta['mwb_booking_ct_costs_custom_price'][0] ) ? $cost_term_meta['mwb_booking_ct_costs_custom_price'][0] : 0;
	// 				$duration_cost += ( $cost_price * $duration );
	// 			}
	// 		}
	// 	}

	// 	$product    = wc_get_product( $product_id );
	// 	$price_html = wc_price( $duration_cost );
	// 	echo wp_json_encode(
	// 		array(
	// 			'price_html' => $price_html,
	// 			'success'    => true,
	// 		)
	// 	);
	// 	wp_die();
	// }

	/**
	 * Add the additional product data to the cart items
	 *
	 * @param array  $cart_item_data Array of any other cart item data for the product.
	 * @param number $product_id     ID of the product adding to the cart.
	 * @return array
	 */
	public function mwb_wc_bk_add_cart_item_data( $cart_item_data, $product_id ) {

		$product = wc_get_product( $product_id );
		if ( $product && $product->is_type( 'mwb_booking' ) ) {
			if ( ! isset( $cart_item_data['mwb_wc_bk_cart_data'] ) ) {
				$posted_data                      = $_REQUEST;
				$booking_data                     = $this->mwb_wc_bk_get_product_data( $posted_data, $product_id );
				$cart_item_data['mwb_wc_bk_data'] = $booking_data;
			}
		}
		// echo '<pre>';
		// print_r( $booking_data );
		// // print_r( $_REQUEST );
		// echo '</pre>';

		// die( "kjbh" );
		return $cart_item_data;
	}

	/**
	 * Function to get the Product's added fields Data.
	 *
	 * @param array $posted_data parameter.
	 * @return array
	 */
	public function mwb_wc_bk_get_product_data( $posted_data, $product_id ) {

		$booking_data = array();
		// $duration     = isset( $posted_data['duration'] ) ? $posted_data['duration'] : 1;
		// $booking_data['duration'] = $duration;

		$product_meta = get_post_meta( $product_id );

		$booking_unit_dur   = ! empty( $product_meta['mwb_booking_unit_duration'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_unit_duration'][0] ) ) : '';
		$booking_unit_input = ! empty( $product_meta['mwb_booking_unit_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_unit_input'][0] ) ) : 0;
		$booking_people     = ! empty( $product_meta['mwb_booking_people_select'][0] ) ? maybe_unserialize( $product_meta['mwb_booking_people_select'][0] ) : array();
		$booking_service    = ! empty( $product_meta['mwb_booking_services_select'][0] ) ? maybe_unserialize( $product_meta['mwb_booking_services_select'][0] ) : array();
		$booking_start_time = ! empty( $product_meta['mwb_booking_start_time'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_start_time'][0] ) ) : '00:01';
		$booking_end_time   = ! empty( $product_meta['mwb_booking_end_time'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_end_time'][0] ) ) : '23:59';

		// $booking_add_cost   = ! empty( $product_meta['mwb_booking_added_cost_select'][0] ) ? maybe_unserialize( $product_meta['mwb_booking_added_cost_select'][0] ) : array();

		if ( ! isset( $posted_data['end_date'] ) ) {
			if ( isset( $posted_data['duration'] ) && ! empty( $booking_unit_dur ) && ! empty( $booking_unit_input ) && isset( $posted_data['start_date'] ) ) {

				$time_slot        = isset( $posted_data['time_slot'] ) ? $posted_data['time_slot'] : strtotime( $booking_start_time, strtotime( $posted_data['start_date'] ) );
				// $booking_start_ts = strtotime( $time_slot, strtotime( $posted_data['start_date'] ) );
				$booking_start_ts = $time_slot;
				// $booking_end_ts   = strtotime( $booking_end_time, strtotime( $posted_data['start_date'] ) );

				$booking_data['duration']        = $posted_data['duration'];
				// $start_timestamp                 = strtotime( $posted_data['start_date'] )
				$start_timestamp                 = $booking_start_ts;
				// $booking_data['start_date']      = $posted_data['start_date'];
				$booking_data['start_date']      = gmdate( 'd-m-Y', $booking_start_ts );
				// $booking_data['start_timestamp'] = $start_timestamp;
				$booking_data['start_timestamp'] = $booking_start_ts;
				$total_dur                       = $posted_data['duration'] * $booking_unit_input;
				// $booking_data['duration']        = $total_dur;
				$booking_data['dura_param']      = $booking_unit_dur;

				if ( 'day' === $booking_unit_dur || 'month' === $booking_unit_dur ) {
					$end_date                      = gmdate( 'd-m-Y', strtotime( gmdate( 'd-m-Y', strtotime( $booking_data['start_date'] ) ) . ' +' . ( $total_dur - 1 ) . ' ' . $booking_unit_dur ) );
					$end_timestamp                 = strtotime( $booking_end_time, strtotime( $end_date ) );
					$booking_data['end_date']      = gmdate( 'd-m-Y', $end_timestamp );
					$booking_data['end_timestamp'] = $end_timestamp;
				} elseif ( 'hour' === $booking_unit_dur ) {
					// $end_timestamp                 = $start_timestamp + ( $booking_unit_input * 60 * 60 );
					$end_timestamp                 = $start_timestamp + ( $total_dur * 60 * 60 );
					$booking_data['end_timestamp'] = $end_timestamp;
					$booking_data['time_slot']     = $time_slot;
				} elseif ( 'minute' === $booking_unit_dur ) {
					// $end_timestamp                 = $start_timestamp + ( $booking_unit_input * 60 );
					$end_timestamp                 = $start_timestamp + ( $total_dur * 60 );
					$booking_data['end_timestamp'] = $end_timestamp;
					$booking_data['time_slot']     = $time_slot;
				}
			}
		} else {
			if ( ! empty( $booking_unit_dur ) && ! empty( $booking_unit_input ) && isset( $posted_data['start_date'] ) ) {

				$booking_start_ts = strtotime( $booking_start_time, strtotime( $posted_data['start_date'] ) );
				$booking_end_ts   = strtotime( $booking_end_time, strtotime( $posted_data['end_date'] ) );

				// $booking_data['start_date']      = $posted_data['start_date'];
				// $start_timestamp                 = strtotime( $posted_data['start_date'] );
				$booking_data['start_date']      = gmdate( 'd-m-Y', $booking_start_ts );
				$start_timestamp                 = $booking_start_ts;
				$booking_data['start_timestamp'] = $start_timestamp;
				// $booking_data['end_date']        = $posted_data['end_date'];
				// $booking_data['end_timestamp']   = strtotime( $posted_data['end_date'] );
				$booking_data['end_date']        = gmdate( 'd-m-Y', $booking_end_ts );
				$booking_data['end_timestamp']   = $booking_end_ts;
				$booking_data['dura_param']      = $booking_unit_dur;

				$duration_timestamp_diff = strtotime( $booking_data['end_date'] ) - strtotime( $booking_data['start_date'] );

				if ( 'day' === $booking_unit_dur ) {
					$booking_data['duration'] = $duration_timestamp_diff / ( 24 * 60 * 60 * $booking_unit_input );
				}
			}
		}
		if ( isset( $posted_data['people_total'] ) ) {
			$booking_data['people_total'] = ! empty( $posted_data['people_total'] ) ? $posted_data['people_total'] : 0;
			if ( ! empty( $booking_people ) && is_array( $booking_people ) ) {
				foreach ( $booking_people as $id ) {
					$people      = get_term( $id );
					$people_name = $people->name;

					$booking_data['people_count'][ $people_name ] = ! empty( $posted_data[ 'people-' . $id ] ) ? $posted_data[ 'people-' . $id ] : 0;
				}
			}
		}

		if ( isset( $posted_data['service_cost'] ) ) {
			if ( ! empty( $booking_service ) && is_array( $booking_service ) ) {
				foreach ( $booking_service as $id ) {
					$service_meta = get_term_meta( $id );
					$service      = get_term( $id );
					$service_name = $service->name;
					if ( 'no' === $service_meta['mwb_booking_ct_services_optional'][0] ) {
						$booking_data['inc_service'][ $service_name ] = ! empty( $posted_data[ 'inc-service-' . $id ] ) ? $posted_data[ 'inc-service-' . $id ] : 0;
					} else {
						if ( ! empty( $posted_data[ 'add-service-check-' . $id ] ) && 'on' === $posted_data[ 'add-service-check-' . $id ] ) {
							$booking_data['add_service'][ $service_name ] = ! empty( $posted_data[ 'add-service-' . $id ] ) ? $posted_data[ 'add-service-' . $id ] : 0;
						}
					}
				}
			}
		}

		// if ( ! empty( $booking_add_cost ) && is_array( $booking_add_cost ) ) {
		// 	foreach ( $booking_add_cost as $id ) {
		// 		$added_cost = get_term( $id );
		// 		$cost_name  = $added_cost->name;
		// 		if(  )
		// 	}
		// }

		if ( ! empty( $posted_data['total_cost'] ) ) {

			$booking_data['total_cost'] = $posted_data['total_cost'];
		}

		// $booking_data['posted_data']    = $posted_data;
		$booking_data['unit_dur']       = $booking_unit_dur;
		$booking_data['unit_dur_input'] = $booking_unit_input;
		// $booking_data['product_meta']    = $product_meta;
		// $booking_data['product_people']  = $booking_people;
		// $booking_data['product_service'] = $booking_service;

		return $booking_data;
	}

	/**
	 * Change Price for Product (while add to cart) according to the form fields.
	 *
	 * @param array  $cart_item_data Data for the cart item's products.
	 * @param string $cart_item_key  Key for the cart items array.
	 * @return array
	 */
	public function mwb_wc_bk_add_cart_item( $cart_item_data, $cart_item_key ) {

		// echo '<pre>'; print_r( $cart_item_data ); echo '</pre>';die('kal');
		$product_id = isset( $cart_item_data['product_id'] ) ? $cart_item_data['product_id'] : 0;
		$_product   = wc_get_product( $product_id );
		if ( $_product && $_product->is_type( 'mwb_booking' ) ) {
			$product      = $cart_item_data['data'];
			$booking_data = $cart_item_data['mwb_wc_bk_data'];
			$price        = (int) $cart_item_data['mwb_wc_bk_data']['total_cost'];
			// $price     = $this->get_booking_product_price( $product, $booking_data );
			$product->set_price( $price );
			$cart_item_data['data'] = $product;
		}
		return $cart_item_data;
	}

	/**
	 * Getting the updated product price according to the form fields
	 *
	 * @param object $product      Product object.
	 * @param array  $booking_data Cart item data for 'mwb_booking'.
	 * @return number
	 */
	// public function get_booking_product_price( $product, $booking_data ) {
	// 	$price    = $product->get_price();
	// 	$duration = $booking_data['duration'];
	// 	return $price * $duration;
	// }

	/**
	 * Change Price for Product (while add to cart) according to the form fields using session data.
	 *
	 * @param array  $session_data  Cart Item Product Data stored in the session.
	 * @param array  $cart_item     Items in the cart.
	 * @param string $cart_item_key Key for the cart items array.
	 * @return array
	 */
	public function mwb_wc_bk_get_cart_item_from_session( $session_data, $cart_item, $cart_item_key ) {
		$product_id = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;
		$_product   = wc_get_product( $product_id );
		if ( $_product && $_product->is_type( 'mwb_booking' ) ) {
			$product      = $session_data['data'];
			$booking_data = $session_data['mwb_wc_bk_data'];

			if ( array_key_exists( 'total_cost', $cart_item['mwb_wc_bk_data'] ) ) {
				$price = (int) $cart_item['mwb_wc_bk_data']['total_cost'];
			} else {
				$price = 0;
			}

			// $price     = $this->get_booking_product_price( $product, $booking_data );
			$product->set_price( $price );
			$session_data['data'] = $product;
		}
		return $session_data;
	}

	/**
	 * Change button text on product pages
	 *
	 * @param [string] $product Product Add to cart button text.
	 * @return string
	 */
	public function mwb_wc_bk_add_to_cart_text( $product ) {

		// echo '<pre>'; print_r( $product ); echo '</pre>';
		// if ( $product && $product->is_type( 'mwb_booking' ) ) {
		// 	return 'Book now';
		// }
		// return 'Add To Cart';

		return 'Book now';
	}

	/**
	 * Redirect add to cart to Checkout page.
	 *
	 * @param [string] $url for Cart page.
	 * @return string
	 */
	public function mwb_wc_bk_skip_cart_redirect_checkout( $url ) {

		if ( is_user_logged_in() ) {
			return wc_get_checkout_url();
		} else {
			$url = wc_get_page_permalink( 'myaccount' );
			return $url;
		}

	}

	/**
	 * Display the added product data in the cart.
	 *
	 * @param array $item_data      Array containing additional data for our cart item.
	 * @param array $cart_item_data Array of our cart item and its associated data.
	 * @return array
	 */
	public function mwb_wc_bk_get_item_data( $item_data, $cart_item_data ) {

		if ( empty( $cart_item_data['mwb_wc_bk_data'] ) ) {
			return $item_data;
		}

		$product_id = isset( $cart_item_data['product_id'] ) ? $cart_item_data['product_id'] : 0;
		$product    = wc_get_product( $product_id );
		if ( $product && $product->is_type( 'mwb_booking' ) ) {
			$booking_data               = $cart_item_data['mwb_wc_bk_data'];
			// $booking_data['product_id'] = $cart_item_data['product_id'];
			$booking_item_data          = array();

			// $booking_item_data['mwb_wc_bk_duration'] = array(
			// 	'key'     => 'duration',
			// 	'value'   => $booking_data['duration'],
			// 	'display' => '',
			// );
			$total_duration = $booking_data['duration'] * $booking_data['unit_dur_input'];
			$duration_str   = $total_duration . ' ' . $booking_data['unit_dur'];

			$booking_item_data = array(
				'mwb_wc_bk_duration' => array(
					'key'     => 'Duration',
					'value'   => $total_duration > 1 ? $duration_str . 's' : $duration_str,
					'display' => '',
				),
				'mwb_wc_bk_from'     => array(
					'key'     => 'From',
					'value'   => $booking_data['start_date'],
					'display' => '',
				),
				// 'mwb_wc_bk_to'       => array(
				// 	'key'     => 'To',
				// 	'value'   => $booking_data['end_date'],
				// 	'display' => '',
				// ),
			);

			if ( 'hour' !== $booking_data['unit_dur'] && 'minute' !== $booking_data['unit_dur'] ) {
				$booking_item_data['mwb_wc_bk_to'] = array(
					'key'     => 'To',
					'value'   => $booking_data['end_date'],
					'display' => '',
				);
			} else {
				$booking_item_data['mwb_wc_bk_from'] = array(
					'key'     => 'On',
					'value'   => $booking_data['start_date'],
					'display' => '',
				);
				$booking_item_data['mwb_wc_bk_time_slot'] = array(
					'key'     => 'Start Time',
					'value'   => $booking_data['time_slot'],
					// 'value'   => $booking_data['time_slot'],
					'display' => gmdate( 'h:i a', $booking_data['time_slot'] ),
				);
			}
			if ( ! empty( $booking_data['people_count'] ) && is_array( $booking_data['people_count'] ) ) {

				foreach ( $booking_data['people_count'] as $name => $count ) {
					$booking_item_data[ 'mwb_wc_bk_' . $name ] = array(
						'key'     => $name,
						'value'   => $count,
						'display' => '',
					);
				}
			}
			if ( ! empty( $booking_data['inc_service'] ) && is_array( $booking_data['inc_service'] ) ) {

				foreach ( $booking_data['inc_service'] as $name => $count ) {
					$str = $name . '-' . $count . ',';
				}
				$booking_item_data[ 'mwb_wc_bk_' . $name ] = array(
					'key'     => 'Included Booking services',
					'value'   => $str,
					'display' => '',
				);
			}
			if ( ! empty( $booking_data['add_service'] ) && is_array( $booking_data['add_service'] ) ) {
				$str = '';
				foreach ( $booking_data['add_service'] as $name => $count ) {
					$str .= $name . '-' . $count . ', ';
				}
				$booking_item_data[ 'mwb_wc_bk_' . $name ] = array(
					'key'     => 'Additional Booking services',
					'value'   => $str,
					'display' => '',
				);
			}

			$item_data = array_merge( $item_data, $booking_item_data );
		}
		// echo '<pre>';
		// // print_r( $cart_item_data );
		// print_r( $item_data );
		// echo '</pre>';
		// // die( "kjbh" );

		return $item_data;
	}

	/**
	 * Empty the cart before Add Booking.
	 *
	 * @param [type] $passed
	 * @param [int] $product_id ID of the product.
	 * @param [type] $quantity 
	 * @return void
	 */
	public function remove_cart_item_before_add_to_cart( $passed, $product_id, $quantity ) {

		if ( ! WC()->cart->is_empty() ) {
			WC()->cart->empty_cart();
		}
		return $passed;
	}

	public function mwb_wc_bk_single_cart_booking( $cart ) {

		// echo '<pre>'; print_r( $cart ); echo '</pre>';die("cart");
		// echo count( $cart );die("ok");

		// $count      = 0;
		// $cart_count = count( $cart );
		// if ( $cart_count > 1 ) {
		// 	// return $cart[ $cart_count - 1 ];
		// 	foreach ( $cart as $cart_hash => $cart_data ) {
		// 		$count ++;
		// 		if ( $cart_count > ( $count ) ) {
		// 			continue;
		// 		} else {
		// 			return array( $cart_hash => $cart_data );
		// 		}
		// 	}
		// } else {
		// 	return $cart;
		// }

		$cart_count = count( $cart );
		if ( $cart_count > 1 ) {

			$last_key = array_key_last( $cart );
			$new_cart = array( $last_key => $cart[ $last_key ] );

			return $new_cart;
		}
		return $cart;

	}

	public function mwb_change_booking_product_quantity( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		// if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		// 	return;
		// }

		$new_qty = 1;

		// Checking cart items.
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {

			$product_id = $cart_item['data']->get_id();
			$product    = wc_get_product( $product_id );

			if ( $product && $product->is_type( 'mwb_booking' ) && $cart_item['quantity'] != $new_qty ) {
				$cart->set_quantity( $cart_item_key, $new_qty ); // Change quantity.
			}
		}
	}

	/**
	 * Add custom added product data to the order.
	 *
	 * @param object $item          order line item object.
	 * @param string $cart_item_key the string containing our cart item key.
	 * @param array  $values        array containing all the data for our cart item, including our custom data.
	 * @param object $order         WC_Order instance containing the new order object.
	 * @return void
	 */
	public function mwb_wc_bk_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {

		if ( empty( $values['mwb_wc_bk_data'] ) ) {
			return;
		}
		// if ( ! is_user_logged_in() ) {
		// 	$url = wc_get_page_permalink( 'myaccount' );
		// 	wp_safe_redirect( $url );
		// }

		$booking_data = array();
		if ( isset( $values['mwb_wc_bk_data'] ) && is_array( $values['mwb_wc_bk_data'] ) ) {
			$booking_data = $values['mwb_wc_bk_data'];
		}
		// echo '<pre>'; print_r( $booking_data ); echo '</pre>';
		// echo '<pre>'; print_r( $order ); echo '</pre>';
		// die("ok");
		if ( ! empty( $booking_data ) ) {
			$item->add_meta_data( 'mwb_wc_bk_data', $booking_data, true );
			if ( isset( $booking_data['mwb_wc_bk_id'] ) ) {
				$item->add_meta_data( 'mwb_wc_bk_id', $booking_data['mwb_wc_bk_id'], true );
			}
		}
	}

	/**
	 * Avoid guest user to Book a mwb_booking product.
	 *
	 * @param [string] $value 'Yes' or 'No' for sign-up.
	 * @return string
	 */
	public function conditional_guest_checkout_based_on_product( $value ) {

		if ( WC()->cart ) {
			$cart = WC()->cart->get_cart();
			foreach ( $cart as $item ) {
				$prod_id  = $item['product_id'];
				$_product = wc_get_product( $prod_id );
				if ( $_product && $_product->is_type( 'mwb_booking' ) ) {
					$value = 'no';
					break;
				}
			}
		}
		return $value;
	}

	/**
	 * Preparing the Booking Order
	 *
	 * @param number $order_id   ID of the order created.
	 * @param array  $posted_data array for the posted data.
	 *
	 * @return void
	 */
	public function mwb_wc_bk_check_order_booking( $order_id, $posted_data = array() ) {

		// echo '<pre>'; print_r( $posted_data ); echo '</pre>';
		// die("order_processed");
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}
		$order_items = $order->get_items();
		if ( ! $order_items ) {
			return;
		}

		// echo '<pre>'; print_r( $order_items ); echo '</pre>';die("ook");
		// $order_type = '';

		foreach ( $order_items as $order_item_id => $order_item ) {
			if ( $order_item->is_type( 'line_item' ) ) {
				$product = $order_item->get_product();
				if ( ! $product || ! ( $product->is_type( 'mwb_booking' ) ) ) {
					continue;
				}

				// echo '<pre>'; print_r( $product ); echo '</pre>';die('kk');

				// echo '<pre>'; print_r( $order_item_id ); echo '</pre>';
				// echo '<pre>'; print_r( $order_item->get_meta( 'mwb_wc_bk_data' ) ); echo '</pre>';
				// $order_type = 'booking';

				$order_meta               = $order_item->get_meta( 'mwb_wc_bk_data' );
				$order_meta['product_id'] = $product->get_id();

				$args = array(
					'order_id'   => $order_id,
					'product_id' => $product->get_id(),
					'status'     => $order->get_status(),
					'user_id'    => $order->get_user_id(),
					'order_meta' => $order_meta,
				);

				$booking_data = $order_item->get_meta( 'mwb_wc_bk_data' );
				// $booking_id   = $order_item->get_meta( 'mwb_wc_bk_id' );
				// if ( ! $booking_id && ! empty( $booking_data ) ) {

				if ( ! empty( $booking_data ) ) {
					$booking_id = $this->mwb_wc_bk_create_booking( $args );
					if ( $booking_id ) {
						update_post_meta( $order_id, 'mwb_booking_id', $booking_id );
						$order_item->add_meta_data( 'mwb_booking_id', $booking_id, true );
						$order_item->save_meta_data();
						$order->add_order_note( sprintf( __( 'A new booking <a href="%1$1s">#%2$2s</a> has been created from this order', 'mwb-wc-bk' ), admin_url( 'post.php?post=' . $booking_id . '&action=edit' ), $booking_id ) );

						$this->save_booking_order_data( $order_id, $booking_id );
						$this->booking_status_acc_order_status( $order_id, $booking_id );
					}
				}
			}
		}

		// if ( 'booking' === $order_type ) {
		// 	update_post_meta( $order_id, 'booking_order', 'yes' );
		// }
	}

	/**
	 * Changing Booking Status according to order status, when a order is created
	 *
	 * @param [int] $order_id  ID of the order created.
	 * @param [int] $booking_id ID of the booking created.
	 * @return void
	 */
	public function booking_status_acc_order_status( $order_id, $booking_id ) {

		$order        = wc_get_order( $order_id );
		$order_status = $order->get_status();
		//echo '<pre>'; print_r( $order_status ); echo '</pre>';

		switch ( $order_status ) {
			case 'pending':
				$new_status = 'pending';
				break;
			case 'processing':
				$new_status = 'pending';
				break;
			case 'on-hold':
				$new_status = 'pending';
				break;
			case 'completed':
				$new_status = 'completed';
				break;
			case 'cancelled':
				$new_status = 'cancelled';
				break;
			case 'failed':
				$new_status = 'cancelled';
				break;
			case 'refunded':
				$new_status = 'refunded';
				break;
			default:
				$new_status = 'pending';
				break;
		}
		update_post_meta( $booking_id, 'mwb_booking_status', $new_status );
		do_action( 'mwb_booking_status_' . $new_status, $booking_id, $order_id );
		update_post_meta( $booking_id, 'trigger_admin_email', 'yes' );

		//do_action( 'mwb_booking_created', $booking_id, $order_id );
	}

	/** 
	 * Update Order meta to the booking post meta.
	 *
	 * @param [int]  $order_id Order ID.
	 * @param [type] $booking_id Booking ID after inserting post.
	 * @return void
	 */
	public function save_booking_order_data( $order_id, $booking_id ) {

		$meta_data = get_post_meta( $order_id );
		// echo '<pre>'; print_r( $meta_data ); echo '</pre>';die("save_booking");
		foreach ( $meta_data as $key => $value ) {
			update_post_meta( $booking_id, $key, $value[0] );
		}
	}

	/**
	 * Fetching the Order(Booking Order) ID after inserting the post.
	 *
	 * @param array $args Arguments.
	 *
	 * @return number $booking_id
	 */
	public function mwb_wc_bk_create_booking( $args ) {

		// echo '<pre>'; print_r( $args ); echo '</pre>';die("ok");
		$product      = get_post( $args['product_id'] );
		$order_id     = $args['order_id'];
		$product_name = $product->post_name;
		$title        = 'Booking for ' . $product_name;
		$booking_id   = wp_insert_post(
			array(
				'post_type'   => 'mwb_cpt_booking',
				'post_title'  => $title,
				'post_status' => 'publish',
			)
		);

		$args['order_meta']['order_id'] = $args['order_id'];
		update_post_meta( $booking_id, '_customer_user', $args['user_id'] );
		update_post_meta( $booking_id, 'mwb_meta_data', $args['order_meta'] );

		// do_action( 'mwb_booking_created', $booking_id, $order_id );

		return $booking_id;
	}

	/**
	 * Calculation for the booking price
	 *
	 * @return void
	 */
	public function booking_price_cal() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'mwb_wc_bk_public' ) ) {
			die( 'Nonce value cannot be verified' );
		}

		// echo '<pre>'; print_r( $_POST ); echo '</pre>';die("ok");
		$product_id   = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		$people_total = ! empty( $_POST['people_total'] ) ? sanitize_text_field( wp_unslash( $_POST['people_total'] ) ) : 0;
		$duration     = isset( $_POST['duration'] ) ? sanitize_text_field( wp_unslash( $_POST['duration'] ) ) : '';

		$product_meta = get_post_meta( $product_id );

		$people_select       = ! empty( $product_meta['mwb_booking_people_select'][0] ) ? maybe_unserialize( $product_meta['mwb_booking_people_select'][0] ) : '';
		$people_enable_check = ! empty( $product_meta['mwb_people_enable_checkbox'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_people_enable_checkbox'][0] ) ) : '';
		$enable_people_type  = ! empty( $product_meta['mwb_enable_people_types'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_enable_people_types'][0] ) ) : '';
		$added_costs         = ! empty( $product_meta['mwb_booking_added_cost_select'][0] ) ? maybe_unserialize( $product_meta['mwb_booking_added_cost_select'][0] ) : '';

		$people          = array();
		$people_data     = array();
		$added_cost_data = array();
		$added_cost_arr  = array();

		$booking_people_cost  = 0;
		$booking_added_cost   = 0;
		$booking_cost         = 0;
		$booking_service_cost = 0;
		$total_added_cost     = 0;
		$total_service_cost   = 0;

		if ( 'yes' === $people_enable_check ) {
			if ( is_array( $people_select ) && ! empty( $people_select ) ) {
				foreach ( $people_select as $k => $v ) {
					$people_term      = get_term( $v );
					$people_term_meta = get_term_meta( $v );
					$people_name      = $people_term->name;
					$people[ $v ]     = ! empty( $_POST['people_count'][ $v ] ) ? sanitize_text_field( wp_unslash( $_POST['people_count'][ $v ] ) ) : '';

					$people_data[ $v ] = array(
						'name'         => $people_term->name,
						'term_id'      => $v,
						'people_count' => ! empty( $people[ $v ] ) ? $people[ $v ] : 0,
					);
					foreach ( $people_term_meta as $key => $value ) {
						$people_data[ $v ]['people_meta'][ $key ] = ! empty( $value[0] ) ? $value[0] : '';
					}
				}
			}
		}

		// echo '<pre>'; print_r( $people_data ); echo '</pre>';
		$unit_cost          = ! empty( $product_meta['mwb_booking_unit_cost_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_unit_cost_input'][0] ) ) : '';
		$unit_cost_multiply = ! empty( $product_meta['mwb_booking_unit_cost_multiply'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_unit_cost_multiply'][0] ) ) : '';
		$base_cost          = ! empty( $product_meta['mwb_booking_base_cost_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_base_cost_input'][0] ) ) : '';
		$base_cost_multiply = ! empty( $product_meta['mwb_booking_base_cost_multiply'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_base_cost_multiply'][0] ) ) : '';
		$extra_cost         = ! empty( $product_meta['mwb_booking_extra_cost_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_extra_cost_input'][0] ) ) : '';
		$extra_cost_people  = ! empty( $product_meta['mwb_booking_extra_cost_people_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_extra_cost_people_input'][0] ) ) : '1';

		$enabled_services = ! empty( $product_meta['mwb_booking_services_select'][0] ) ? maybe_unserialize( sanitize_text_field( wp_unslash( $product_meta['mwb_booking_services_select'][0] ) ) ) : '';

		$discount_type = ! empty( $product_meta['mwb_booking_cost_discount_type'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_cost_discount_type'][0] ) ) : '';

		$monthly_discount = ! empty( $product_meta['mwb_booking_monthly_discount_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_monthly_discount_input'][0] ) ) : '';
		$weekly_discount  = ! empty( $product_meta['mwb_booking_weekly_discount_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_weekly_discount_input'][0] ) ) : '';
		$custom_discount  = ! empty( $product_meta['mwb_booking_custom_days_discount_input'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_custom_days_discount_input'][0] ) ) : '';
		$custom_disc_days = ! empty( $product_meta['mwb_booking_custom_discount_days'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_booking_custom_discount_days'][0] ) ) : '';

		if ( 'yes' === $people_enable_check ) {
			if ( 'yes' === $enable_people_type ) {
				if ( is_array( $people_select ) && ! empty( $people_select ) ) {
					foreach ( $people_select as $id ) {
						if ( ! empty( $unit_cost_multiply ) && 'yes' === $unit_cost_multiply ) {
							if ( ! empty( $people_data[ $id ]['people_count'] ) ) {
								if ( isset( $people_data[ $id ]['people_meta'] ) ) {
									$booking_people_cost += ( ! empty( $people_data[ $id ]['people_meta']['mwb_ct_booking_people_unit_cost'] ) ? $people_data[ $id ]['people_meta']['mwb_ct_booking_people_unit_cost'] : $unit_cost ) * $people_data[ $id ]['people_count'];
								} else {
									$booking_people_cost += $unit_cost * $people_data[ $id ]['people_count'];
								}
							}
						}
						if ( ! empty( $base_cost_multiply ) && 'yes' === $base_cost_multiply ) {
							if ( $people_data[ $id ]['people_count'] ) {
								if ( isset( $people_data[ $id ]['people_meta'] ) ) {
									$booking_people_cost += ( ! empty( $people_data[ $id ]['people_meta']['mwb_ct_booking_people_base_cost'] ) ? $people_data[ $id ]['people_meta']['mwb_ct_booking_people_base_cost'] : $base_cost ) * $people_data[ $id ]['people_count'];
								} else {
									$booking_people_cost += $base_cost * $people_data[ $id ]['people_count'];
								}
							}
						}
					}
				}
			} else {
				if ( ! empty( $unit_cost_multiply ) && 'yes' === $unit_cost_multiply ) {
					$booking_people_cost = $people_total * $unit_cost;
				}
				if ( ! empty( $base_cost_multiply ) && 'yes' === $base_cost_multiply ) {
					$booking_people_cost += $people_total * $base_cost;
				}
			}
		}

		// echo "people cost -" . $booking_people_cost . "<br>";

		if ( empty( $unit_cost_multiply ) || 'no' === $unit_cost_multiply ) {

			$booking_people_cost += $unit_cost;

			if ( ! empty( $extra_cost ) ) {
				if ( ! empty( $people_total ) ) {
					if ( ! empty( $extra_cost_people ) ) {
						$booking_people_cost += $extra_cost * floor( $people_total / $extra_cost_people );
					} else {
						$booking_people_cost += $extra_cost;
					}
				}
			}
		}
		// echo "people cost -" . $booking_people_cost . "<br>";

		if ( empty( $base_cost_multiply ) || 'no' === $base_cost_multiply ) {
			$booking_people_cost += $base_cost;
		}

		// echo "people cost -" . $booking_people_cost . "<br>";

		if ( is_array( $added_costs ) && ! empty( $added_costs ) ) {
			foreach ( $added_costs as $cost_id ) {
				$cost_term      = get_term( $cost_id );
				$cost_term_meta = get_term_meta( $cost_id );

				$added_cost_data[ $cost_id ] = array(
					'name'    => $cost_term->name,
					'term_id' => $cost_id,
				);
				foreach ( $cost_term_meta as $k => $v ) {

					$added_cost_data[ $cost_id ]['cost_meta'][ $k ] = ! empty( $v[0] ) ? $v[0] : '';
				}
			}
		}

		// echo '<pre>'; print_r( $added_cost_data ); echo '</pre>';die('ok');
		if ( is_array( $added_costs ) && ! empty( $added_costs ) ) {
			foreach ( $added_costs as $cost_id ) {
				if ( ! empty( $people_total ) ) {
					// echo '<pre>'; print_r( $added_cost_data[ $cost_id ] ); echo '</pre>';
					if ( isset( $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_multiply_people'] ) && 'yes' === $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_multiply_people'] ) {
						$booking_added_cost = ( ( isset( $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_custom_price'] ) ? $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_custom_price'] : 0 ) * $people_total );
						// $total_added_cost  += $booking_added_cost;
						// $added_cost_arr[ $added_cost_data[ $cost_id ]['name'] ] = $booking_added_cost;
					} elseif ( empty( $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_multiply_people'] ) || 'no' === $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_multiply_people'] ) {
						// die('ok');
						$booking_added_cost = ( isset( $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_custom_price'] ) ? $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_custom_price'] : 0 );
						// $total_added_cost  += $booking_added_cost;
						// $added_cost_arr[ $added_cost_data[ $cost_id ]['name'] ] = $booking_added_cost;
					}
					if ( ! empty( $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_multiply_units'] ) && 'yes' === $added_cost_data[ $cost_id ]['cost_meta']['mwb_booking_ct_costs_multiply_units'] ) {
						$booking_added_cost = $booking_added_cost * $duration;
					}
					$added_cost_arr[ $added_cost_data[ $cost_id ]['name'] ] = $booking_added_cost;
					$total_added_cost                                      += $booking_added_cost;
				}
			}
		}
		// echo '<pre>'; print_r( $added_cost_arr ); echo '</pre>';
		// echo "people cost -" . $booking_people_cost . "  added cost-" . $total_added_cost;die;

		// $enable_people_type = ! empty( $product_meta['mwb_enable_people_types'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_enable_people_types'][0] ) ) : '';
		// echo '<pre>'; print_r( $enabled_services ); echo '</pre>'; die('services');
		if ( ! empty( $enabled_services ) && is_array( $enabled_services ) ) {
			foreach ( $enabled_services as $service_id ) {

				$service_term_meta = get_term_meta( $service_id );
				$service_cost      = ! empty( $service_term_meta['mwb_ct_booking_service_cost'][0] ) ? $service_term_meta['mwb_ct_booking_service_cost'][0] : 0;
				$if_optional       = ! empty( $service_term_meta['mwb_booking_ct_services_optional'][0] ) ? $service_term_meta['mwb_booking_ct_services_optional'][0] : 'no';
				$has_quantity      = ! empty( $service_term_meta['mwb_booking_ct_services_has_quantity'][0] ) ? $service_term_meta['mwb_booking_ct_services_has_quantity'][0] : 'no';
				// echo '<pre>'; print_r( $service_cost ); echo '</pre>';
				if ( 'yes' === $has_quantity ) {
					// echo "has_quantity";
					if ( 'yes' === $if_optional ) {
						$service_count = ! empty( $_POST['add_service_count'][ $service_id ] ) ? sanitize_text_field( wp_unslash( $_POST['add_service_count'][ $service_id ] ) ) : 0;
					} else {
						$service_count = ! empty( $_POST['inc_service_count'][ $service_id ] ) ? sanitize_text_field( wp_unslash( $_POST['inc_service_count'][ $service_id ] ) ) : 1;
					}
				} else {
					// echo "no quantity";
					if ( ! empty( $_POST['inc_service_count'] ) && is_array( $_POST['inc_service_count'] ) ) {
						$service_count = array_key_exists( $service_id, $_POST['inc_service_count'] ) ? 1 : 0;
					} else {
						$service_count = 0;
					}
				}
				// echo '<pre>'; print_r( $service_cost ); echo '</pre>';
				if ( 'yes' === $enable_people_type ) {
					if ( isset( $service_term_meta['mwb_booking_ct_services_multiply_people'][0] ) && ( 'yes' === $service_term_meta['mwb_booking_ct_services_multiply_people'][0] ) ) {
						if ( ! empty( $people_select ) && is_array( $people_select ) ) {
							foreach ( $people_select as $people_id ) {
								$people_term           = get_term( $people_id );
								$service_people_cost   = ! empty( $service_term_meta[ 'mwb_ct_booking_service_cost_' . $people_term->slug ][0] ) ? $service_term_meta[ 'mwb_ct_booking_service_cost_' . $people_term->slug ][0] : $service_cost;
								$booking_service_cost += ( $service_count * $service_people_cost * $people_data[ $people_id ]['people_count'] );
								// $booking_service_cost += $people_data[ $people_id ]['people_count'] * $service_people_cost;
								if ( ! empty( $service_term_meta['mwb_booking_ct_services_multiply_units'][0] ) && 'yes' === $service_term_meta['mwb_booking_ct_services_multiply_units'][0] ) {
									$booking_service_cost = $booking_service_cost * $duration;
								}
							}
						}
						// $service_people_cost = ! empty( $service_term_meta[''] );
					} else {
						$booking_service_cost = $service_count * $service_cost;
						if ( ! empty( $service_term_meta['mwb_booking_ct_services_multiply_units'][0] ) && 'yes' === $service_term_meta['mwb_booking_ct_services_multiply_units'][0] ) {
							$booking_service_cost = $booking_service_cost * $duration;
						}
					}
				} else {
					$booking_service_cost = $service_count * $service_cost;
					if ( ! empty( $service_term_meta['mwb_booking_ct_services_multiply_units'][0] ) && 'yes' === $service_term_meta['mwb_booking_ct_services_multiply_units'][0] ) {
						$booking_service_cost = $booking_service_cost * $duration;
					}
				}
				$total_service_cost  += $booking_service_cost;
				$booking_service_cost = 0;
			}
		}
		// echo "service cost - " . $booking_service_cost . "<br>";

		// if ( ! empty( $duration ) && $duration > 1 ) {

			// $booking_people_cost  = $booking_people_cost * $duration;
			// // $total_added_cost     = $total_added_cost * $duration;
			// $booking_service_cost = $booking_service_cost * $duration;

			// echo '<pre>'; print_r( $added_cost_arr ); echo '</pre>';
			// if ( is_array( $added_costs ) && ! empty( $added_costs ) ) {
			// 	foreach ( $added_costs as $cost_id ) {
			// 		// $added_cost_arr[ $added_cost_data[ $cost_id ]['name'] ] = $added_cost_arr[ $added_cost_data[ $cost_id ]['name'] ] * $duration;

			// 		$total_added_cost += $added_cost_arr[ $added_cost_data[ $cost_id ]['name'] ];
			// 	}
			// }
		// }
		// echo '<pre>'; print_r( $added_cost_arr ); echo '</pre>';
		// echo '<pre>'; print_r( $total_added_cost ); echo '</pre>';

		$booking_cost = $booking_people_cost + $total_added_cost + $total_service_cost;

		// echo "people cost-" . $booking_people_cost . "<br>";
		// echo "added cost- " . $booking_added_cost . "<br>";
		// echo 'service cost-' . $booking_service_cost . "<br>";

		// foreach( $added_cost_arr as  )

		// echo '<pre>'; print_r( $added_cost_data ); echo '</pre>';die("ok");
		// $id = $this->mwb_booking->get_booking_product_id();

		// $formdata = ! empty( $_POST[ 'formdata' ] ) ? $_POST[ 'formdata' ] : array();

		// $formatted = array();
		// parse_str( $formdata, $formatted );

		// $id = ! empty( $product->get_id() ) ? $product->get_id() : '1';
		// $product_meta  = get_post_meta( $product->get_id() );
		// $people_select = ! empty( $product_meta['mwb_booking_people_select'][0] ) ? $product_meta['mwb_booking_people_select'][0] : '';

		// $people_total = ! empty( $_POST['people_total'] ) ? sanitize_text_field( wp_unslash( $_POST['people_total'] ) ) : array();
		// $arr          = array();
		// foreach ( $people_select as $id ) {
		// 	$arr[ $id ] = ! empty( $_POST[ $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ $id ] ) ) : '';
		// }
		// $arr['people_total'] = $people_total;

		$product    = wc_get_product( $product_id );
		$price_html = wc_price( $booking_cost );
		echo wp_json_encode(
			array(
				'price_html'           => $price_html,
				'success'              => true,
				'booking_total_cost'   => $booking_cost,
				'booking_people_cost'  => $booking_people_cost,
				'booking_service_cost' => $total_service_cost,
				'booking_added_cost'   => $total_added_cost,
				'indiv_added_cost_arr' => $added_cost_arr,
				'posted_data'          => $_POST,
			)
		);
		wp_die();
	}

	/**
	 * Calculation of Booking Service Cost.
	 *
	 * @return void
	 */
	public function show_booking_total() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'mwb_wc_bk_public' ) ) {
			die( 'Nonce value cannot be verified' );
		}

		// $product_id           = ! empty( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		// $booking_service_cost = 0;

		// $product_meta = get_post_meta( $product_id );

		// $enabled_services = ! empty( $product_meta['mwb_booking_services_select'][0] ) ? maybe_unserialize( sanitize_text_field( wp_unslash( $product_meta['mwb_booking_services_select'][0] ) ) ) : '';
		// $people_select    = ! empty( $product_meta['mwb_booking_people_select'][0] ) ? maybe_unserialize( $product_meta['mwb_booking_people_select'][0] ) : '';

		// // $people_enable_check = ! empty( $product_meta['mwb_people_enable_checkbox'][0] ) ? $product_meta['mwb_people_enable_checkbox'][0] : '';
		// $enable_people_type  = ! empty( $product_meta['mwb_enable_people_types'][0] ) ? sanitize_text_field( wp_unslash( $product_meta['mwb_enable_people_types'][0] ) ) : '';
		// if ( ! empty( $enabled_services ) && is_array( $enabled_services ) ) {
		// 	foreach ( $enabled_services as $service_id ) {
		// 		$service_term_meta = get_term_meta( $service_id );
		// 		$service_cost      = ! empty( $service_term_meta['mwb_ct_booking_service_cost'] ) ? $service_term_meta['mwb_ct_booking_service_cost'] : 0;
		// 		// echo '<pre>'; print_r( $service_term_meta ); echo '</pre>';die("ok");
		// 		if ( 'yes' === $enable_people_type ) {
		// 			if ( isset( $service_term_meta['mwb_booking_ct_services_multiply_people'][0] ) && ( 'yes' === $service_term_meta['mwb_booking_ct_services_multiply_people'][0] ) ) {
		// 				if ( ! empty( $people_select ) ) {
		// 					foreach ( $people_select as $people_id ) {
		// 						$people_term         = get_term( $people_id );
		// 						$service_people_cost = ! empty( $service_term_meta[ 'mwb_ct_booking_service_cost_' . $people_term->slug ][0] ) ? $service_term_meta[ 'mwb_ct_booking_service_cost_' . $people_term->slug ][0] : $service_cost;
		// 					}
		// 				}
		// 				// $service_people_cost = ! empty( $service_term_meta[''] );
		// 			}
		// 		}
		// 	}
		// }
		// echo wp_json_encode( $enabled_services );
		// wp_die();

		$total_cost     = isset( $_POST['total_cost'] ) ? sanitize_text_field( wp_unslash( $_POST['total_cost'] ) ) : 0;
		$base_cost      = isset( $_POST['base_cost'] ) ? sanitize_text_field( wp_unslash( $_POST['base_cost'] ) ) : 0;
		$service_cost   = isset( $_POST['service_cost'] ) ? sanitize_text_field( wp_unslash( $_POST['service_cost'] ) ) : 0;
		$added_cost_arr = isset( $_POST['added_cost_arr'] ) ? array_map( 'sanitize_text_field', $_POST['added_cost_arr'] ) : array();
		?>

		<label for="mwb-wc-bk-total-fields"><b><?php esc_html_e( 'Totals', '' ); ?></b></label>
		<ul style="list-style-type:none;">
			<?php
			if ( ! empty( $base_cost ) ) {
				?>
				<li>
					<label for=""><?php esc_html_e( 'Base Cost', '' ); ?></label>
					<span>&emsp;&#8377;<?php echo esc_html( $base_cost ); ?></span>
					<input type="hidden" name="base_cost" value="<?php echo esc_html( $base_cost ); ?>" >
				</li>
				<?php
			}
			if ( ! empty( $service_cost ) ) {
				?>
				<li>
					<label for=""><?php esc_html_e( 'Service Cost', '' ); ?></label>
					<span>&emsp;&#8377;<?php echo esc_html( $service_cost ); ?></span>
					<input type="hidden" name="service_cost" value="<?php echo esc_html( $service_cost ); ?>" >
				</li>
				<?php
			}
			if ( ! empty( $added_cost_arr ) && is_array( $added_cost_arr ) ) {
				foreach ( $added_cost_arr as $name => $cost ) {
					if ( ! empty( $cost ) ) {
						?>
				<li>
					<label for=""><?php echo esc_html( $name ); ?><?php esc_html_e( '-cost' ); ?></label>
					<span>&emsp;&#8377;<?php echo esc_html( $cost ); ?></span>
					<input type="hidden" name="added_cost-<?php echo esc_html( strtolower( $name ) ); ?>" value="<?php echo esc_html( $cost ); ?>" >
				</li>
						<?php
					}
				}
			}
			if ( ! empty( $total_cost ) ) {
				?>
				<li>
					<label for=""><b><?php esc_html_e( 'Total Cost', '' ); ?></b></label>
					<span><b>&emsp;&#8377;<?php echo esc_html( $total_cost ); ?></b></span>
					<input type="hidden" name="total_cost" value="<?php echo esc_html( $total_cost ); ?>">
				</li>
		<?php } ?>
		</ul>
		<div>
			<?php
				// $availabilit_func = MWB_Woocommerce_Booking_Availability::get_availability_instance();

				// $availabilit_func->
			?>
		</div>
		<?php
		wp_die();
	}

	/**
	 * Add a tab to the menu linkjs array to show All Booking on my account page.
	 *
	 * @param [array] $menu_links Array of the tabs.
	 * @return array
	 */
	public function mwb_booking_list_user_bookings( $menu_links ) {

		$menu_links['all_bookings'] = __( 'All Bookings', '' );

		return $menu_links;
	}

	/**
	 * Register the endpoint for the new tab.
	 *
	 * @return void
	 */
	public function mwb_booking_add_endpoint() {

		add_rewrite_endpoint( 'all_bookings', EP_PAGES );
	}

	/**
	 * Content for the abaove end point created.
	 *
	 * @return void
	 */
	public function mwb_booking_endpoint_content() {

		require_once MWB_WC_BK_BASEPATH . 'public/partials/mwb-booking-list-user-bookings.php';
	}

}
