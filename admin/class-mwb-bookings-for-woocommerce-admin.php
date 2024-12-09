<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    Mwb_Bookings_For_Woocommerce
 * @subpackage Mwb_Bookings_For_Woocommerce/admin
 */

 use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mwb_Bookings_For_Woocommerce
 * @subpackage Mwb_Bookings_For_Woocommerce/admin
 */
class Mwb_Bookings_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since 2.0.0
	 * @var   string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 2.0.0
	 * @var   string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * List of tabs and sub tabs.
	 *
	 * @var array
	 */
	public $mwb_settings = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 2.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 2.0.0
	 * @param string $hook The plugin page slug.
	 */
	public function mbfw_admin_enqueue_styles( $hook ) {
		$screen = get_current_screen();

		$mwb_bfw_taxonomy_array = $this->mwb_get_taxonomy_array();

		if ( ( isset( $screen->id ) && 'wp-swings_page_mwb_bookings_for_woocommerce_menu' === $screen->id || 'wp-swings_page_home' === $screen->id ) || ( in_array( get_current_screen()->taxonomy, $mwb_bfw_taxonomy_array ) ) ) {

			wp_enqueue_style( 'mwb-mbfw-select2-css', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/mwb-bookings-for-woocommerce-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-mbfw-meterial-css', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-mbfw-meterial-css2', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-mbfw-meterial-lite', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-mbfw-meterial-icons-css', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-mbfw-admin-min-css', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/mwb-admin.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'mwb-datatable-css', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables/media/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'mwb-admin-full-calendar-css', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/full-calendar/main.css', array(), '5.8.0', 'all' );
			wp_enqueue_style( 'wps-admin-boo-home-css', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/wps-admin-boo-home.min.css', array(), '5.8.0', 'all' );

		}

		wp_enqueue_style( 'mwb-mbfw-global-custom-css', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/mwb-admin-global-custom.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 2.0.0
	 * @param string $hook The plugin page slug.
	 */
	public function mbfw_admin_enqueue_scripts( $hook ) {

		$screen                 = get_current_screen();
		$mwb_bfw_taxonomy_array = $this->mwb_get_taxonomy_array();
		if ( ( isset( $screen->id ) && 'wp-swings_page_mwb_bookings_for_woocommerce_menu' === $screen->id || 'wp-swings_page_home' === $screen->id ) || ( in_array( get_current_screen()->taxonomy, $mwb_bfw_taxonomy_array ) ) ) {
			wp_enqueue_script( 'mwb-mbfw-select2', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/mwb-bookings-for-woocommerce-select2.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( 'mwb-mbfw-metarial-js', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-mbfw-metarial-js2', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-mbfw-metarial-lite', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-mbfw-datatable', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables.net/js/jquery.dataTables.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-mbfw-datatable-btn', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables.net/buttons/dataTables.buttons.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-mbfw-datatable-btn-2', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables.net/buttons/buttons.html5.min.js', array(), time(), false );
			wp_register_script( $this->plugin_name . 'admin-js', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/js/mwb-bookings-for-woocommerce-admin.js', array( 'jquery', 'mwb-mbfw-select2', 'mwb-mbfw-metarial-js', 'mwb-mbfw-metarial-js2', 'mwb-mbfw-metarial-lite' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name . 'admin-js',
				'mbfw_admin_param',
				array(
					'todays_date'               => current_time( 'Y-m-d' ),
					'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
					'nonce'                     => wp_create_nonce( 'mwb_mbfw_admin_nonce' ),
					'reloadurl'                 => admin_url( 'admin.php?page=mwb_bookings_for_woocommerce_menu' ),
					'mbfw_gen_tab_enable'       => get_option( 'mbfw_radio_switch_demo' ),
					'mbfw_admin_param_location' => ( admin_url( 'admin.php' ) . '?page=mwb_bookings_for_woocommerce_menu&mbfw_tab=mwb-bookings-for-woocommerce-general' ),
					'full_cal_button_text'      => array(
						'today' => __( 'Today', 'mwb-bookings-for-woocommerce' ),
						'month' => __( 'Month', 'mwb-bookings-for-woocommerce' ),
						'week'  => __( 'Week', 'mwb-bookings-for-woocommerce' ),
						'day'   => __( 'Day', 'mwb-bookings-for-woocommerce' ),
						'list'  => __( 'List', 'mwb-bookings-for-woocommerce' ),
					),
				)
			);
			wp_enqueue_script( $this->plugin_name . 'admin-js' );
			wp_enqueue_script( 'mwb-mbfw-admin-min-js', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/js/mwb-admin.js', array( 'jquery' ), time(), true );
			wp_enqueue_script( 'mwb-admin-full-calendar-js', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/full-calendar/main.js', array( 'jquery' ), time(), true );

		}

		wp_register_script( 'mwb-mbfw-admin-custom-global-js', MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/js/mwb-admin-global-custom.js', array( 'jquery' ), time(), false );
			wp_localize_script(
				'mwb-mbfw-admin-custom-global-js',
				'mbfw_product_ajax',
				array(
					'alert_booking'               => __( 'Booking cost should not be less than 0  !', 'mwb-bookings-for-woocommerce' ),
					'end_date_validate_booking'   => __( 'End time should be greater than start time', 'mwb-bookings-for-woocommerce' ),
					'start_date_validate_booking' => __( 'Start time should be less than end time', 'mwb-bookings-for-woocommerce' ),

				)
			);

			wp_enqueue_script( 'mwb-mbfw-admin-custom-global-js' );

	}

	/**
	 * Adding settings menu for Mwb Bookings For WooCommerce.
	 *
	 * @since 2.0.0
	 */
	public function mbfw_options_page() {
		global $submenu;
		if ( empty( $GLOBALS['admin_page_hooks']['wps-plugins'] ) ) {
			add_menu_page( 'WP Swings', 'WP Swings', 'manage_options', 'wps-plugins', array( $this, 'mwb_plugins_listing_page' ), MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/wpswings_logo.png', 15 );
			add_submenu_page( 'wps-plugins', 'Home', 'Home', 'manage_options', 'home', array( $this, 'wpswings_welcome_callback_function' ), 1 );
			$mbfw_menus =
			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			apply_filters( 'wps_add_plugins_menus_array', array() );
			if ( is_array( $mbfw_menus ) && ! empty( $mbfw_menus ) ) {
				foreach ( $mbfw_menus as $mbfw_key => $mbfw_value ) {
					add_submenu_page( 'wps-plugins', $mbfw_value['name'], $mbfw_value['name'], 'manage_options', $mbfw_value['menu_link'], array( $mbfw_value['instance'], $mbfw_value['function'] ) );
				}
			}
		}
	}


	/**
	 *
	 * Adding the default menu into the WordPress menu.
	 *
	 * @name wpswings_callback_function
	 * @since 1.0.0
	 */
	public function wpswings_welcome_callback_function() {
		include MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/mwb-bookings-for-woocommerce-welcome.php';
	}


	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since 2.0.0
	 */
	public function mwb_mbfw_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'wps-plugins', $submenu ) ) {
			if ( isset( $submenu['wps-plugins'][0] ) ) {
				unset( $submenu['wps-plugins'][0] );
			}
		}
	}


	/**
	 * Mwb Bookings For WooCommerce mbfw_admin_submenu_page.
	 *
	 * @since 2.0.0
	 * @param array $menus Marketplace menus.
	 */
	public function mbfw_admin_submenu_page( $menus = array() ) {
		$menus[] = array(
			'name'      => __( 'Bookings For WooCommerce', 'mwb-bookings-for-woocommerce' ),
			'slug'      => 'mwb_bookings_for_woocommerce_menu',
			'menu_link' => 'mwb_bookings_for_woocommerce_menu',
			'instance'  => $this,
			'function'  => 'mbfw_options_menu_html',
		);
		return $menus;
	}

	/**
	 * Mwb Bookings For WooCommerce mwb_plugins_listing_page.
	 *
	 * @since 2.0.0
	 */
	public function mwb_plugins_listing_page() {
		$active_marketplaces =
		/**
		 * Filter is for returning something.
		 *
		 * @since 1.0.0
		 */
		apply_filters( 'wps_add_plugins_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			include MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/welcome.php';
		}
	}

	/**
	 * Mwb Bookings For WooCommerce admin menu page.
	 *
	 * @since 2.0.0
	 */
	public function mbfw_options_menu_html() {
		include_once MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/mwb-bookings-for-woocommerce-admin-dashboard.php';
	}

	/**
	 * Mwb developer admin hooks listing.
	 *
	 * @return array
	 */
	public function mwb_developer_admin_hooks_listing() {
		$admin_hooks = array();
		$val         = self::mwb_developer_hooks_function( MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . 'admin/' );
		if ( ! empty( $val['hooks'] ) ) {
			$admin_hooks[] = $val['hooks'];
			unset( $val['hooks'] );
		}
		$data = array();
		foreach ( $val['files'] as $v ) {
			if ( 'css' !== $v && 'js' !== $v && 'images' !== $v ) {
				$helo = self::mwb_developer_hooks_function( MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . 'admin/' . $v . '/' );
				if ( ! empty( $helo['hooks'] ) ) {
					$admin_hooks[] = $helo['hooks'];
					unset( $helo['hooks'] );
				}
				if ( ! empty( $helo ) ) {
					$data[] = $helo;
				}
			}
		}
		return $admin_hooks;
	}

	/**
	 * Hooks for listing public hooks.
	 *
	 * @return array
	 */
	public function mwb_developer_public_hooks_listing() {
		$public_hooks = array();
		$val          = self::mwb_developer_hooks_function( MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . 'public/' );
		if ( ! empty( $val['hooks'] ) ) {
			$public_hooks[] = $val['hooks'];
			unset( $val['hooks'] );
		}
		$data = array();
		foreach ( $val['files'] as $v ) {
			if ( 'css' !== $v && 'js' !== $v && 'images' !== $v ) {
				$helo = self::mwb_developer_hooks_function( MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . 'public/' . $v . '/' );
				if ( ! empty( $helo['hooks'] ) ) {
					$public_hooks[] = $helo['hooks'];
					unset( $helo['hooks'] );
				}
				if ( ! empty( $helo ) ) {
					$data[] = $helo;
				}
			}
		}
		return $public_hooks;
	}

	/**
	 * Method to list coustom hooks.
	 *
	 * @param string $path path to the file to load custom Hooks.
	 * @return array
	 */
	public static function mwb_developer_hooks_function( $path ) {
		$all_hooks = array();
		$scan      = scandir( $path );
		$response  = array();
		foreach ( $scan as $file ) {
			if ( strpos( $file, '.php' ) ) {
				$myfile = file( $path . $file );
				foreach ( $myfile as $key => $lines ) {
					if ( preg_match( '/do_action/i', $lines ) && ! strpos( $lines, 'str_replace' ) && ! strpos( $lines, 'preg_match' ) ) {
						$all_hooks[ $key ]['action_hook'] = $lines;
						$all_hooks[ $key ]['desc']        = $myfile[ $key - 1 ];
					}
					if ( preg_match( '/apply_filters/i', $lines ) && ! strpos( $lines, 'str_replace' ) && ! strpos( $lines, 'preg_match' ) ) {
						$all_hooks[ $key ]['filter_hook'] = $lines;
						$all_hooks[ $key ]['desc']        = $myfile[ $key - 1 ];
					}
				}
			} elseif ( strpos( $file, '.' ) == '' && strpos( $file, '.' ) !== 0 ) { // phpcs:ignore
				$response['files'][] = $file;
			}
		}
		if ( ! empty( $all_hooks ) ) {
			$response['hooks'] = $all_hooks;
		}
		return $response;
	}

	/**
	 * Mwb Bookings For WooCommerce admin menu page.
	 *
	 * @since 2.0.0
	 * @param array $mbfw_settings_general Settings fields.
	 */
	public function mbfw_admin_general_settings_page( $mbfw_settings_general ) {
		$mbfw_settings_general = array(
			array(
				'title'       => __( 'Enable Plugin', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable plugin.', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_plugin_enable',
				'value'       => get_option( 'mwb_mbfw_is_plugin_enable' ),
				'class'       => 'mwb_mbfw_is_plugin_enable',
				'name'        => 'mwb_mbfw_is_plugin_enable',
			),
			array(
				'title'       => __( 'Enable Bookings', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable to start the bookings for customers else customers can not do the bookings.', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_booking_enable',
				'value'       => get_option( 'mwb_mbfw_is_booking_enable' ),
				'class'       => 'mwb_mbfw_is_booking_enable',
				'name'        => 'mwb_mbfw_is_booking_enable',
			),
			array(
				'title'       => __( 'Disable book now button on empty form', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Disable book now button if dates are not choosen on calendar .', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_disable_book_now',
				'value'       => get_option( 'mwb_mbfw_disable_book_now' ),
				'class'       => 'mwb_mbfw_disable_book_now',
				'name'        => 'mwb_mbfw_disable_book_now',
			),

		);
		$mbfw_settings_general =
		/**
		 * Filter is for returning something.
		 *
		 * @since 1.0.0
		 */
		apply_filters( 'mbfw_general_settings_array_filter', $mbfw_settings_general );
		$mbfw_settings_general[] = array(
			'type'        => 'button',
			'id'          => 'mwb_mbfw_general_settings_save',
			'button_text' => __( 'Save Settings', 'mwb-bookings-for-woocommerce' ),
			'class'       => 'mwb_mbfw_general_settings_save',
			'name'        => 'mwb_mbfw_general_settings_save',
		);
		return $mbfw_settings_general;
	}

	/**
	 * Booking form settings.
	 *
	 * @param array $mbfw_booking_form_array array containing booking form settings.
	 * @return array
	 */
	public function mbfw_booking_form_settings_page( $mbfw_booking_form_array ) {
		$mbfw_booking_form_array = array(
			array(
				'title'       => __( 'Booking Form Settings', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'heading',
				'description' => __( 'Booking Form Settings Heading', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_form_settings_heading',
				'class'       => 'mwb_mbfw_is_show_included_service',
			),
			array(
				'title'       => __( 'Display Included Services', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => sprintf(
					/* translators:%s booking services link */
					__( 'Enable this to show %s on the booking form.', 'mwb-bookings-for-woocommerce' ),
					'<a href="' . admin_url( 'edit-tags.php?taxonomy=mwb_booking_service&post_type=product' ) . '" class="mwb-bfwp-helper-link__admin" target="_blank">' . __( 'booking services', 'mwb-bookings-for-woocommerce' ) . '</a>'
				),
				'id'          => 'mwb_mbfw_is_show_included_service',
				'value'       => get_option( 'mwb_mbfw_is_show_included_service' ),
				'class'       => 'mwb_mbfw_is_show_included_service',
				'name'        => 'mwb_mbfw_is_show_included_service',
			),
			array(
				'title'       => __( 'Display Totals', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable this to display the booking total for different services and quantities while booking.', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_show_totals',
				'value'       => get_option( 'mwb_mbfw_is_show_totals' ),
				'class'       => 'mwb_mbfw_is_show_totals',
				'name'        => 'mwb_mbfw_is_show_totals',
			),
		);
		$mbfw_booking_form_array =
		/**
		 * Filter is for returning something.
		 *
		 * @since 1.0.0
		 */
		apply_filters( 'mbfw_booking_form_setting_array_filter', $mbfw_booking_form_array );
		$mbfw_booking_form_array[] = array(
			'type'        => 'button',
			'id'          => 'mwb_mbfw_booking_form_settings_save',
			'button_text' => __( 'Save Settings', 'mwb-bookings-for-woocommerce' ),
			'class'       => 'mwb_mbfw_booking_form_settings_save',
			'name'        => 'mwb_mbfw_booking_form_settings_save',
		);
		return $mbfw_booking_form_array;
	}

	/**
	 * Aavilability setting page.
	 *
	 * @param array $mbfw_availability_settings array containing available settings.
	 * @return array
	 */
	public function mbfw_add_availability_settings_page( $mbfw_availability_settings ) {
		$mbfw_availability_settings = array(

			array(
				'title'       => __( 'Enable availability setting', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_enable_availibility_setting',
				'name'        => 'mwb_mbfw_enable_availibility_setting',
				'value'       => get_option( 'mwb_mbfw_enable_availibility_setting' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable this to apply availability setting for your product, Your Product will appear only in available time set up by you.', 'mwb-bookings-for-woocommerce' ),
			),

			array(
				'title'       => __( 'Daily Start Time', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_daily_start_time',
				'name'        => 'mwb_mbfw_daily_start_time',
				'class'       => 'mwb_mbfw_daily_start_time mbfw_time_picker',
				'value'       => get_option( 'mwb_mbfw_daily_start_time' ),
				'type'        => 'time',
				'description' => __( 'Please choose daily start time, users will be able to book from this time.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'title'       => __( 'Daily End Time', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_daily_end_time',
				'name'        => 'mwb_mbfw_daily_end_time',
				'class'       => 'mwb_mbfw_daily_end_time mbfw_time_picker',
				'value'       => get_option( 'mwb_mbfw_daily_end_time' ),
				'type'        => 'time',
				'description' => __( 'Please choose daily end time, bookings will be closed for users after this time.', 'mwb-bookings-for-woocommerce' ),
			),
		);
		$mbfw_availability_settings =
		/**
		 * Filter is for returning something.
		 *
		 * @since 1.0.0
		 */
		apply_filters( 'mwb_mbfw_availability_setting_fields_array', $mbfw_availability_settings );
		$mbfw_availability_settings[] = array(
			'type'        => 'button',
			'id'          => 'mwb_mbfw_availability_settings_save',
			'button_text' => __( 'Save Settings', 'mwb-bookings-for-woocommerce' ),
			'class'       => 'mwb_mbfw_availability_settings_save',
			'name'        => 'mwb_mbfw_availability_settings_save',
		);
		return $mbfw_availability_settings;
	}


	/**
	 * Mwb Bookings For WooCommerce save tab settings.
	 *
	 * @since 2.0.0
	 */
	public function mbfw_admin_save_tab_settings() {
		global $mbfw_mwb_mbfw_obj;
		$mwb_settings_save_progress = false;
		if ( wp_doing_ajax() ) {
			return;
		}
		if ( ! isset( $_POST['mwb_tabs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mwb_tabs_nonce'] ) ), 'admin_save_data' ) ) {
			return;
		}

		if ( isset( $_POST['mbfw_button_demo'] ) ) {

			$screen = get_current_screen();
			if ( isset( $screen->id ) && 'wp-swings_page_home' === $screen->id ) {
				$enable_tracking = ! empty( $_POST['mbfw_enable_tracking'] ) ? sanitize_text_field( wp_unslash( $_POST['mbfw_enable_tracking'] ) ) : '';
				update_option( 'mbfw_enable_tracking', $enable_tracking );
				return;
			}
		}
		if ( isset( $_POST['mwb_mbfw_general_settings_save'] ) ) {
			$mwb_mbfw_gen_flag     = false;
			$mbfw_genaral_settings =
			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			apply_filters( 'mbfw_general_settings_array', array() );
			$mwb_settings_save_progress = true;
		}
		if ( isset( $_POST['mwb_mbfw_booking_form_settings_save'] ) ) {
			$mwb_mbfw_gen_flag     = false;
			$mbfw_genaral_settings =
			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			apply_filters( 'mbfw_booking_form_settings_array', array() );
			$mwb_settings_save_progress = true;
		}
		if ( isset( $_POST['mwb_mbfw_availability_settings_save'] ) ) {
			$mwb_mbfw_gen_flag     = false;
			$mbfw_genaral_settings =
			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			apply_filters( 'mbfw_availability_settings_array', array() );
			$mwb_settings_save_progress = true;
		}
		if ( $mwb_settings_save_progress ) {
			$mbfw_button_index = array_search( 'submit', array_column( $mbfw_genaral_settings, 'type' ), true );
			if ( empty( $mbfw_button_index ) ) {
				$mbfw_button_index = array_search( 'button', array_column( $mbfw_genaral_settings, 'type' ), true );
			}
			if ( isset( $mbfw_button_index ) && ( '' !== $mbfw_button_index || ! $mbfw_button_index ) ) {
				unset( $mbfw_genaral_settings[ $mbfw_button_index ] );
				if ( is_array( $mbfw_genaral_settings ) && ! empty( $mbfw_genaral_settings ) ) {
					foreach ( $mbfw_genaral_settings as $mbfw_genaral_setting ) {
						if ( isset( $mbfw_genaral_setting['id'] ) && '' !== $mbfw_genaral_setting['id'] ) {
							if ( 'availability_select' === $mbfw_genaral_setting['type'] ) {
								$sub_tabs = $mbfw_genaral_setting['sub_tabs'];
								foreach ( $sub_tabs as $mbfw_sub_components ) {
									foreach ( $mbfw_sub_components as $sub_components ) {
										if ( isset( $_POST[ $sub_components['id'] ] ) ) {
											update_option( $sub_components['id'], is_array( $_POST[ $sub_components['id'] ] ) ? map_deep( wp_unslash( $_POST[ $sub_components['id'] ] ), 'sanitize_text_field' ) : sanitize_text_field( wp_unslash( $_POST[ $sub_components['id'] ] ) ) );
										} else {
											update_option( $sub_components['id'], '' );
										}
									}
								}
							} else {
								if ( isset( $_POST[ $mbfw_genaral_setting['id'] ] ) ) {
									update_option( $mbfw_genaral_setting['id'], is_array( $_POST[ $mbfw_genaral_setting['id'] ] ) ? map_deep( wp_unslash( $_POST[ $mbfw_genaral_setting['id'] ] ), 'sanitize_text_field' ) : sanitize_text_field( wp_unslash( $_POST[ $mbfw_genaral_setting['id'] ] ) ) );
								} else {
									update_option( $mbfw_genaral_setting['id'], '' );
								}
							}
						} else {
							$mwb_mbfw_gen_flag = true;
						}
					}
				}
				if ( $mwb_mbfw_gen_flag ) {
					$mwb_mbfw_error_text = esc_html__( 'Id of some field is missing', 'mwb-bookings-for-woocommerce' );
					$mbfw_mwb_mbfw_obj->mwb_mbfw_plug_admin_notice( $mwb_mbfw_error_text, 'error' );
				} else {
					$mwb_mbfw_error_text = esc_html__( 'Settings saved Successfully', 'mwb-bookings-for-woocommerce' );
					$mbfw_mwb_mbfw_obj->mwb_mbfw_plug_admin_notice( $mwb_mbfw_error_text, 'success' );
				}
			}
		}
	}

	/**
	 * Add product type in dropdown
	 *
	 * @param array $types array containing product types.
	 * @return array
	 */
	public function mbfw_add_product_type_in_dropdown( $types ) {
		$types['mwb_booking'] = __( 'Booking product', 'mwb-bookings-for-woocommerce' );
		return $types;
	}

	/**
	 * Adding product data tabs at product editing page.
	 *
	 * @param array $tabs array containing tabs.
	 * @return array
	 */
	public function mbfw_add_product_data_tabs( $tabs ) {
		$tabs['shipping']['class'][]       = 'hide_if_mwb_booking';
		$tabs['attribute']['class'][]      = 'hide_if_mwb_booking';
		$tabs['linked_product']['class'][] = 'hide_if_mwb_booking';
		$tabs['advanced']['class'][]       = 'hide_if_mwb_booking';
		$tabs['inventory']['class'][]      = 'show_if_mwb_booking';

		$tabs = array_merge(
			$tabs,
			array(
				'general_settings' => array(
					'label'    => __( 'General Settings', 'mwb-bookings-for-woocommerce' ),
					'target'   => 'mwb_booking_general_data',
					'class'    => array( 'show_if_mwb_booking' ),
					'priority' => 10,
				),
				'cost'             => array(
					'label'    => __( 'Costs', 'mwb-bookings-for-woocommerce' ),
					'target'   => 'mwb_booking_cost_data',
					'class'    => array( 'show_if_mwb_booking' ),
					'priority' => 20,
				),
				'people'           => array(
					'label'    => __( 'People', 'mwb-bookings-for-woocommerce' ),
					'target'   => 'mwb_booking_people_data',
					'class'    => array( 'show_if_mwb_booking' ),
					'priority' => 30,
				),
				'services'         => array(
					'label'    => __( 'Services', 'mwb-bookings-for-woocommerce' ),
					'target'   => 'mwb_booking_services_data',
					'class'    => array( 'show_if_mwb_booking' ),
					'priority' => 40,
				),
				'availability'     => array(
					'label'    => __( 'Availability', 'mwb-bookings-for-woocommerce' ),
					'target'   => 'mwb_booking_availability_data',
					'class'    => array( 'show_if_mwb_booking' ),
					'priority' => 50,
				),
			)
		);

		return $tabs;
	}

	/**
	 * Adding custom Product data tabs.
	 *
	 * @return void
	 */
	public function mbfw_product_data_tabs_html() {
		$booking_criteria = wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_booking_criteria', true );
		$booking_type     = wps_booking_get_meta_data( get_the_ID(), 'wps_mbfw_booking_type', true );
		wp_nonce_field( 'mwb_booking_product_meta', '_mwb_nonce' );
		?>
		<div id="mwb_booking_general_data" class="panel woocommerce_options_panel show_if_mwb_booking">
			<p class="form-field mwb_mbfw_booking_type_field">
				<label for="wps_mbfw_booking_type"><?php esc_html_e( 'Booking type', 'mwb-bookings-for-woocommerce' ); ?></label>
				<select name="wps_mbfw_booking_type" id="wps_mbfw_booking_type">
					<option value="dual_cal" <?php selected( 'dual_cal', $booking_type ); ?>><?php esc_html_e( 'Dual Calendar', 'mwb-bookings-for-woocommerce' ); ?></option>
					<option value="single_cal" <?php selected( 'single_cal', $booking_type ); ?>><?php esc_html_e( 'Single Calendar', 'mwb-bookings-for-woocommerce' ); ?></option>
				</select>
			<p class="mbfw_notice"><?php esc_html_e( 'Notice : Please go to availability setting and set available days and time slot !', 'mwb-bookings-for-woocommerce' ); ?></p>
			</p>
			<p class="form-field mwb_mbfw_booking_criteria_field">
				<label for="mwb_mbfw_booking_criteria"><?php esc_html_e( 'Quantity', 'mwb-bookings-for-woocommerce' ); ?></label>
				<select name="mwb_mbfw_booking_criteria" id="mwb_mbfw_booking_criteria">
					<option value="customer_selected_unit" <?php selected( 'customer_selected_unit', $booking_criteria ); ?>><?php esc_html_e( 'Customers can choose', 'mwb-bookings-for-woocommerce' ); ?></option>
					<option value="fixed_unit" <?php selected( 'fixed_unit', $booking_criteria ); ?>><?php esc_html_e( 'Fixed unit', 'mwb-bookings-for-woocommerce' ); ?></option>
				</select>
				<input type="number" step="1" min="1" max="" style="width: 4em;" id="mwb_mbfw_booking_count" name="mwb_mbfw_booking_count" value=<?php echo esc_attr( wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_booking_count', true ) ); ?>>
				<span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Select the booking criteria. If it is Set Daily time slots predetermined, enter the fixed number; otherwise, customer will opt for any number for booking.', 'mwb-bookings-for-woocommerce' ); ?>"></span>
			</p>
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'          => 'mwb_mbfw_admin_confirmation',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_admin_confirmation', true ),
					'label'       => __( 'Booking Confirmation', 'mwb-bookings-for-woocommerce' ),
					'description' => __( 'Booking confirmation required by admin.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => 'mwb_mbfw_cancellation_allowed',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_cancellation_allowed', true ),
					'label'       => __( 'Cancellation Allowed', 'mwb-bookings-for-woocommerce' ),
					'description' => __( 'When you enable this option, your users will be able to effortlessly cancel any booking requests they previously placed.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);

			$order_statuses = wc_get_order_statuses();
			unset( $order_statuses['wc-completed'] );
			unset( $order_statuses['wc-cancelled'] );
			unset( $order_statuses['wc-refunded'] );
			unset( $order_statuses['wc-failed'] );
			$saved_statuses = wps_booking_get_meta_data( get_the_ID(), 'mwb_bfwp_order_statuses_to_cancel', true );
			$this->mbfw_multi_select_field_html(
				array(
					'label'       => __( 'Order statuses', 'mwb-bookings-for-woocommerce' ),
					'class'       => 'select short',
					'id'          => 'mwb_bfwp_order_statuses_to_cancel',
					'name'        => 'mwb_bfwp_order_statuses_to_cancel[]',
					'description' => __( 'Please select the desired order statuses at which the orders can be cancelled by users.', 'mwb-bookings-for-woocommerce' ),
					'value'       => is_array( $saved_statuses ) ? $saved_statuses : array(),
					'desc_tip'    => true,
					'options'     => $order_statuses,
					'custom_attr' => ( 'yes' !== wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_cancellation_allowed', true ) ) ? array( 'disabled' => 'disabled' ) : array(),
				)
			);

			woocommerce_wp_select(
				array(
					'label'       => __( 'Booking Unit', 'mwb-bookings-for-woocommerce' ),
					'id'          => 'mwb_mbfw_booking_unit',
					'name'        => 'mwb_mbfw_booking_unit',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_booking_unit', true ),
					'desc_tip'    => true,
					'description' => __( 'Please select booking unit to consider while booking.', 'mwb-bookings-for-woocommerce' ),
					'options'     => array(
						'day'  => __( 'Day(s)', 'mwb-bookings-for-woocommerce' ),
						'hour' => __( 'Hour(s)', 'mwb-bookings-for-woocommerce' ),
					),
					'style'       => 'width:10em',
				)
			);

			woocommerce_wp_checkbox(
				array(
					'id'          => 'mwb_mbfw_show_date_with_time',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_show_date_with_time', true ),
					'label'       => __( 'Enable to show check-in and check-out time with date on calender', 'mwb-bookings-for-woocommerce' ),
					'description' => __( 'This option would enable to show check-in and check-out time with dates on calendar on the site ( a calendar will be shown while booking ).', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			woocommerce_wp_text_input(
				array(
					'label'       => __( 'Daily booking start time on calendar', 'mwb-bookings-for-woocommerce' ),
					'id'          => 'mwb_mbfw_daily_calendar_start_time',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_daily_calendar_start_time', true ),
					'type'        => 'text',
					'description' => __( 'Set daily booking start time on frontend', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
					'style'       => 'width:10em;',

				)
			);
			woocommerce_wp_text_input(
				array(
					'label'       => __( 'Daily booking end time on calendar', 'mwb-bookings-for-woocommerce' ),
					'id'          => 'mwb_mbfw_daily_calendar_end_time',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_daily_calendar_end_time', true ),
					'type'        => 'text',
					'description' => __( 'Set daily booking end time on frontend', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
					'style'       => 'width:10em;',

				)
			);

			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			do_action( 'mwb_mbfw_add_extra_field_product_gen_setting', get_the_ID() );
			?>
		</div>
		<div id="mwb_booking_cost_data" class="panel woocommerce_options_panel show_if_mwb_booking">
			<?php
			woocommerce_wp_text_input(
				array(
					'id'                => 'mwb_mbfw_booking_unit_cost',
					'value'             => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_booking_unit_cost', true ),
					'label'             => __( 'Booking Unit Cost', 'mwb-bookings-for-woocommerce' ),
					'description'       => __( 'Enter unit cost i.e. the booking unit cost for the particular service that you’re opting to book for.', 'mwb-bookings-for-woocommerce' ),
					'type'              => 'number',
					'desc_tip'          => true,
					'style'             => 'width:10em;',
					'custom_attributes' => array(
						'step'  => '0.01',
						'min'   => '0',
						'style' => 'width:100%;',
					),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => 'mwb_mbfw_is_booking_unit_cost_per_people',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_is_booking_unit_cost_per_people', true ),
					'label'       => __( 'Booking Unit Cost Per People', 'mwb-bookings-for-woocommerce' ),
					'description' => __( 'Enabling this would determine your booking unit cost multiplied by number of people.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => 'mwb_mbfw_booking_base_cost_hide',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_booking_base_cost_hide', true ),
					'label'       => __( 'Hide Base Cost from Product Page', 'mwb-bookings-for-woocommerce' ),
					'description' => __( 'Enable to hide Base cost from Product Page.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => 'mwb_mbfw_booking_base_cost',
					'value'             => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_booking_base_cost', true ),
					'label'             => __( 'Base Cost', 'mwb-bookings-for-woocommerce' ),
					'description'       => __( 'Enter base cost i.e. the base rental cost for the service that you’re opting to book for. ', 'mwb-bookings-for-woocommerce' ),
					'type'              => 'number',
					'desc_tip'          => true,
					'style'             => 'width:10em;',
					'custom_attributes' => array(
						'step'  => '0.01',
						'min'   => '0',
						'style' => 'width:100%;',
					),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => 'mwb_mbfw_is_booking_base_cost_per_people',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_is_booking_base_cost_per_people', true ),
					'label'       => __( 'Base Cost Per People', 'mwb-bookings-for-woocommerce' ),
					'description' => __( 'Enabling this would determine your booking base cost multiplied by number of people.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			?>
			<p class="mwb-mbfw-additional-notice">
				<?php
				printf(
					/* translators: %s taxonomy edit link */
					esc_html__( 'To Add Additional cost Please add Booking costs in the Booking costs taxonomy %s , and select Booking costs tags.', 'mwb-bookings-for-woocommerce' ),
					wp_kses_post( '<a href="' . admin_url( 'edit-tags.php?taxonomy=mwb_booking_cost&post_type=product' ) . '" target="_blank">here</a>' )
				);
				?>
			</p>
			<?php
			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			do_action( 'mwb_mbfw_booking_costs_meta_section_add_fields', get_the_ID() );
			?>
		</div>
		<div id="mwb_booking_people_data" class="panel woocommerce_options_panel show_if_mwb_booking">
			<?php
			woocommerce_wp_checkbox(
				array(
					'label'       => __( 'Enable People Option', 'mwb-bookings-for-woocommerce' ),
					'id'          => 'mwb_mbfw_is_people_option',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_is_people_option', true ),
					'description' => __( 'People Option will be Visible While Booking.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);

			woocommerce_wp_text_input(
				array(
					'id'                => 'mwb_mbfw_minimum_people_per_booking',
					'type'              => 'number',
					'value'             => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_minimum_people_per_booking', true ),
					'label'             => __( 'Minimum No. of People', 'mwb-bookings-for-woocommerce' ),
					'description'       => __( 'Minimum Number of People Per Booking', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'          => true,
					'style'             => 'width:10em;',
					'custom_attributes' => array(
						'min'   => '0',
						'style' => 'width:100%;',
						'class' => 'hide_if_bfwp_active',
					),

				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => 'mwb_mbfw_maximum_people_per_booking',
					'type'              => 'number',
					'value'             => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_maximum_people_per_booking', true ),
					'label'             => __( 'Maximum No. of People', 'mwb-bookings-for-woocommerce' ),
					'description'       => __( 'Maximum Number of People Per Booking', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'          => true,
					'style'             => 'width:10em;',
					'custom_attributes' => array( 'min' => 0 ),
				)
			);
			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			do_action( 'mwb_mbfw_people_meta_section_add_fields', get_the_ID() );
			?>
		</div>
		<div id="mwb_booking_services_data" class="panel woocommerce_options_panel show_if_mwb_booking">
			<?php
			woocommerce_wp_checkbox(
				array(
					'label'       => __( 'Add Extra Services', 'mwb-bookings-for-woocommerce' ),
					'id'          => 'mwb_mbfw_is_add_extra_services',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_is_add_extra_services', true ),
					'description' => __( 'Add Extra Services, will be chosen by Customer while Booking.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			?>
			<p class="mwb-mbfw-additional-notice">
				<?php
				printf(
					/* translators: %s taxonomy edit link */
					esc_html__( 'To Add Additional Services Please add Booking Services in the Booking Services taxonomy %s and select Services taxonomy from tag.', 'mwb-bookings-for-woocommerce' ),
					wp_kses_post( '<a href="' . admin_url( 'edit-tags.php?taxonomy=mwb_booking_service&post_type=product' ) . '" target="_blank">here</a>' )
				);
				?>
			</p>
		</div>
		<div id="mwb_booking_availability_data" class="panel woocommerce_options_panel show_if_mwb_booking">
			<?php

			$active_plugins = get_option( 'active_plugins' );
			if ( ! in_array( 'bookings-for-woocommerce-pro/bookings-for-woocommerce-pro.php', $active_plugins ) ) {

				woocommerce_wp_text_input(
					array(
						'label'             => __( 'Choose Upcoming Holiday to disable booking on that day', 'mwb-bookings-for-woocommerce' ),
						'id'                => 'mwb_mbfw_choose_holiday',
						'value'             => wps_booking_get_meta_data( get_the_ID(), 'mwb_mbfw_choose_holiday', true ),
						'description'       => __( 'Bookings will be unavailable on that day.', 'mwb-bookings-for-woocommerce' ),
						'type'              => 'text',
						'desc_tip'          => true,
						'style'             => 'width:10em;',
						'custom_attributes' => array( 'autocomplete' => 'off' ),
					)
				);
			}

			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			do_action( 'mwb_mbfw_booking_availability_meta_tab_fields', get_the_ID() );
			?>
			<p class="mwb-mbfw-additional-notice">
				<?php
				printf(
					/* translators:%s admin setting page link. */
					esc_html__( 'To Choose daily start time and end time please %s.', 'mwb-bookings-for-woocommerce' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=mwb_bookings_for_woocommerce_menu&mbfw_tab=mwb-bookings-for-woocommerce-configuration&bfw_sub_nav=mwb-bookings-for-woocommerce-booking-availability-settings' ) ) . '" target="_blank">' . esc_html__( 'visit here', 'mwb-bookings-for-woocommerce' ) . '</a>'
				);
				?>
			</p>
			<?php
			woocommerce_wp_text_input(
				array(
					'label'             => __( 'Set days availability', 'mwb-bookings-for-woocommerce' ),
					'id'                => 'wps_mbfw_set_availability',
					'value'             => wps_booking_get_meta_data( get_the_ID(), 'wps_mbfw_set_availability', true ),
					'description'       => __( 'Bookings will be available on these days.', 'mwb-bookings-for-woocommerce' ),
					'type'              => 'text',
					'desc_tip'          => true,
					'style'             => 'width:10em;',
					'custom_attributes' => array( 'autocomplete' => 'off' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'label'             => __( 'Set days availability upto', 'mwb-bookings-for-woocommerce' ),
					'id'                => 'wps_mbfw_set_availability_upto',
					'value'             => wps_booking_get_meta_data( get_the_ID(), 'wps_mbfw_set_availability_upto', true ),
					'description'       => __( 'Bookings will be available till the day selected these days.', 'mwb-bookings-for-woocommerce' ),
					'type'              => 'text',
					'desc_tip'          => true,
					'style'             => 'width:10em;',
					'custom_attributes' => array( 'autocomplete' => 'off' ),
				)
			);

			woocommerce_wp_checkbox(
				array(
					'id'          => 'wps_mbfw_day_and_days_upto_togather_enabled',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'wps_mbfw_day_and_days_upto_togather_enabled', true ),
					'label'       => __( 'Enable Days availabilty and days availability upto Togather', 'mwb-bookings-for-woocommerce' ),
					'description' => __( 'Enable if you want days availabilty and days availability upto worked togather.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);

			woocommerce_wp_checkbox(
				array(
					'id'          => 'wps_mbfw_night_slots_enabled',
					'value'       => wps_booking_get_meta_data( get_the_ID(), 'wps_mbfw_night_slots_enabled', true ),
					'label'       => __( 'Enable if create slots for night hours', 'mwb-bookings-for-woocommerce' ),
					'description' => __( 'Bookings can be done from 23:00 - 01:00.', 'mwb-bookings-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			require_once MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . '/admin/partials/mwb-bookings-for-woocommerce-time-slot.php';
			?>
		</div>
		

		<?php
	}

	/**
	 * Multiselect field html.
	 *
	 * @param array $field array containing fields for html input fields.
	 * @return void
	 */
	public function mbfw_multi_select_field_html( $field ) {
		global $thepostid, $post, $woocommerce;
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		?>
		<p class="form-field <?php echo esc_attr( $field['id'] . '_field ' . $field['wrapper_class'] ); ?>">
			<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
			<select id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" class="<?php echo esc_attr( $field['class'] ); ?>" multiple="multiple"
			<?php
			if ( isset( $field['custom_attr'] ) && is_array( $field['custom_attr'] ) ) {
				foreach ( $field['custom_attr'] as $attr_key => $attr_val ) {
					echo esc_attr( $attr_key . '=' . $attr_val );
				}
			}
			?>
			>
				<?php
				foreach ( $field['options'] as $key => $value ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ); //phpcs:ignore WordPress ?>><?php echo esc_html( $value ); ?></option>
					<?php
				}
				?>
			</select>
			<?php
			if ( ! empty( $field['description'] ) ) {
				if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
					?>
					<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr( $field['description'] ); ?>"></span>
					<?php
				} else {
					?>
					<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
					<?php
				}
			}
			?>
		</p>
		<?php
	}

	/**
	 * Save custom product meta boxes data.
	 *
	 * @param int    $id post id.
	 * @param object $post post object.
	 * @return void
	 */
	public function mbfw_save_custom_product_meta_boxes_data( $id, $post ) {
		$product = wc_get_product( $id );
		if ( $product && 'mwb_booking' === $product->get_type() ) {
			if ( ! isset( $_POST['_mwb_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_mwb_nonce'] ) ), 'mwb_booking_product_meta' ) ) {
				return;
			}

			$product_meta_data = array(
				'mwb_mbfw_booking_criteria'                => array_key_exists( 'mwb_mbfw_booking_criteria', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_criteria'] ) ) : '',
				'wps_mbfw_booking_type'                    => array_key_exists( 'wps_mbfw_booking_type', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['wps_mbfw_booking_type'] ) ) : '',
				'mwb_mbfw_booking_count'                   => array_key_exists( 'mwb_mbfw_booking_count', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_count'] ) ) : '',
				'mwb_mbfw_booking_unit'                    => array_key_exists( 'mwb_mbfw_booking_unit', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_unit'] ) ) : '',
				'mwb_mbfw_enable_calendar'                 => array_key_exists( 'mwb_mbfw_enable_calendar', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_enable_calendar'] ) ) : '',
				'mwb_mbfw_enable_time_picker'              => array_key_exists( 'mwb_mbfw_enable_time_picker', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_enable_time_picker'] ) ) : '',
				'mwb_mbfw_max_bookings'                    => array_key_exists( 'mwb_mbfw_max_bookings', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_max_bookings'] ) ) : '',
				'mwb_mbfw_show_date_with_time'             => array_key_exists( 'mwb_mbfw_show_date_with_time', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_show_date_with_time'] ) ) : '',
				'mwb_mbfw_admin_confirmation'              => array_key_exists( 'mwb_mbfw_admin_confirmation', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_admin_confirmation'] ) ) : '',
				'mwb_mbfw_cancellation_allowed'            => array_key_exists( 'mwb_mbfw_cancellation_allowed', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_cancellation_allowed'] ) ) : '',
				'mwb_mbfw_booking_unit_cost'               => array_key_exists( 'mwb_mbfw_booking_unit_cost', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_unit_cost'] ) ) : '',
				'_price'                                   => array_key_exists( 'mwb_mbfw_booking_unit_cost', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_unit_cost'] ) ) : '',
				'mwb_mbfw_is_booking_unit_cost_per_people' => array_key_exists( 'mwb_mbfw_is_booking_unit_cost_per_people', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_booking_unit_cost_per_people'] ) ) : '',
				'mwb_mbfw_booking_base_cost'               => array_key_exists( 'mwb_mbfw_booking_base_cost', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_base_cost'] ) ) : '',
				'mwb_mbfw_booking_base_cost_hide'          => array_key_exists( 'mwb_mbfw_booking_base_cost_hide', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_base_cost_hide'] ) ) : '',
				'mwb_mbfw_is_booking_base_cost_per_people' => array_key_exists( 'mwb_mbfw_is_booking_base_cost_per_people', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_booking_base_cost_per_people'] ) ) : '',
				'mwb_mbfw_is_people_option'                => array_key_exists( 'mwb_mbfw_is_people_option', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_people_option'] ) ) : '',
				'mwb_mbfw_minimum_people_per_booking'      => array_key_exists( 'mwb_mbfw_minimum_people_per_booking', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_minimum_people_per_booking'] ) ) : '',
				'mwb_mbfw_minimum_no_days_booking'         => array_key_exists( 'mwb_mbfw_minimum_no_days_booking', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_minimum_no_days_booking'] ) ) : '',
				'mwb_mbfw_maximum_people_per_booking'      => array_key_exists( 'mwb_mbfw_maximum_people_per_booking', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_maximum_people_per_booking'] ) ) : '',
				'mwb_mbfw_is_add_extra_services'           => array_key_exists( 'mwb_mbfw_is_add_extra_services', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_add_extra_services'] ) ) : '',
				'mwb_mbfw_maximum_booking_per_unit'        => array_key_exists( 'mwb_mbfw_maximum_booking_per_unit', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_maximum_booking_per_unit'] ) ) : '',
				'mwb_bfwp_order_statuses_to_cancel'        => array_key_exists( 'mwb_bfwp_order_statuses_to_cancel', $_POST ) ? ( is_array( $_POST['mwb_bfwp_order_statuses_to_cancel'] ) ? map_deep( wp_unslash( $_POST['mwb_bfwp_order_statuses_to_cancel'] ), 'sanitize_text_field' ) : sanitize_text_field( wp_unslash( $_POST['mwb_bfwp_order_statuses_to_cancel'] ) ) ) : array(),
				'mwb_mbfw_daily_calendar_start_time'       => array_key_exists( 'mwb_mbfw_daily_calendar_start_time', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_daily_calendar_start_time'] ) ) : '',
				'mwb_mbfw_daily_calendar_end_time'         => array_key_exists( 'mwb_mbfw_daily_calendar_end_time', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_daily_calendar_end_time'] ) ) : '',
				'mwb_mbfw_choose_holiday'                  => array_key_exists( 'mwb_mbfw_choose_holiday', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_choose_holiday'] ) ) : '',
				'wps_mbfw_set_availability'                => array_key_exists( 'wps_mbfw_set_availability', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['wps_mbfw_set_availability'] ) ) : '',
				'wps_mbfw_set_availability_upto'           => array_key_exists( 'wps_mbfw_set_availability_upto', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['wps_mbfw_set_availability_upto'] ) ) : '',
				'wps_mbfw_time_slots'                      => array_key_exists( 'mbfw_fields', $_POST ) ? ( is_array( $_POST['mbfw_fields'] ) ? map_deep( wp_unslash( $_POST['mbfw_fields'] ), 'sanitize_text_field' ) : sanitize_text_field( wp_unslash( $_POST['mbfw_fields'] ) ) ) : array(),
				'_stock'                                   => array_key_exists( '_stock', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['_stock'] ) ) : '',
				'_original_stock'                          => array_key_exists( '_original_stock', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['_original_stock'] ) ) : '',
				'_backorders'                              => array_key_exists( '_backorders', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['_backorders'] ) ) : '',
				'_low_stock_amount'                        => array_key_exists( '_low_stock_amount', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['_low_stock_amount'] ) ) : '',
				'_stock_status'                            => array_key_exists( '_stock_status', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['_stock_status'] ) ) : '',
				'_sku'                                     => array_key_exists( '_sku', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['_sku'] ) ) : '',
				'_manage_stock'                            => array_key_exists( '_manage_stock', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['_manage_stock'] ) ) : '',
				'wps_mbfw_night_slots_enabled'             => array_key_exists( 'wps_mbfw_night_slots_enabled', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['wps_mbfw_night_slots_enabled'] ) ) : '',
				'wps_mbfw_day_and_days_upto_togather_enabled'             => array_key_exists( 'wps_mbfw_day_and_days_upto_togather_enabled', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['wps_mbfw_day_and_days_upto_togather_enabled'] ) ) : '',
			);

			$product_meta_data =
			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			apply_filters( 'mwb_mbfw_save_product_meta_data', $product_meta_data, $id );

			foreach ( $product_meta_data as $meta_key => $meta_value ) {

				if ( strpos( $meta_key, 'wps_mbfw_unit_' ) !== false ) {

					if ( ! empty( $meta_value ) || ! empty( wps_booking_get_meta_data( $id, $meta_key, true ) ) ) {

						 wps_booking_update_meta_data( $id, $meta_key, $meta_value );
					}
				} else {

					wps_booking_update_meta_data( $id, $meta_key, $meta_value );
				}
			}

			$active_plugins = get_option( 'active_plugins' );

			if ( in_array( 'bookings-for-woocommerce-pro/bookings-for-woocommerce-pro.php', $active_plugins ) ) {

				$wps_wgm_categ_enable = get_option( 'wps_bfwp_general_setting_categ_enable',true );
				if ( '' == $wps_wgm_categ_enable || 'yes' !== $wps_wgm_categ_enable ) {

					wp_set_object_terms( $id, array( 'booking' ), 'product_cat', true );
				} 
			} else {
				wp_set_object_terms( $id, array( 'booking' ), 'product_cat', true );

			}
			wp_remove_object_terms( $id, 'uncategorized', 'product_cat' );
			/**
			 * Filter is for returning something.
			 *
			 * @since 1.0.0
			 */
			do_action( 'mwb_mbfw_share_product_on_fb', $id );
		}
	}

	/**
	 * Adding extra field at custom mwb_booking_cost taxonomy page.
	 *
	 * @return void
	 */
	public function mbfw_adding_custom_fields_at_booking_cost_taxonomy_page() {
		wp_nonce_field( 'mwb_edit_taxonomy_page', '_mwb_nonce' );
		$fields = array(
			array(
				'id'          => 'mwb_mbfw_booking_cost',
				'type'        => 'number',
				'value'       => '',
				'label'       => __( 'Booking Cost', 'mwb-bookings-for-woocommerce' ),
				'description' => __( 'Please Add Booking cost here.', 'mwb-bookings-for-woocommerce' ),
				'style'       => 'width:10em;',
				'min'         => 0,
				'step'        => '0.01',
			),
			array(
				'label'       => __( 'Multiply by No. of People', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_booking_cost_multiply_people',
				'type'        => 'checkbox',
				'value'       => 'yes',
				'description' => __( 'Either to multiply by number of people.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'label'       => __( 'Multiply by Duration', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_booking_cost_multiply_duration',
				'type'        => 'checkbox',
				'value'       => 'yes',
				'description' => __( 'Either to multiply by Duration of Booking.', 'mwb-bookings-for-woocommerce' ),
			),
		);
		$this->mbfw_taxonomy_adding_fields_html( $fields );
	}

	/**
	 * Html fields for taxonomy edit page.
	 *
	 * @param array $fields array containing attributes of html fields.
	 * @return void
	 */
	public function mbfw_taxonomy_adding_fields_html( $fields ) {
		foreach ( $fields as $field ) {
			?>
			<div class="wps-bfw-form-field">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_attr( isset( $field['label'] ) ? $field['label'] : '' ); ?></label>
				<div class="wps-form-field-wrap">
					<input
					type="<?php echo esc_attr( $field['type'] ); ?>"
					class="<?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : 'short' ); ?>"
					style="<?php echo esc_attr( isset( $field['style'] ) ? $field['style'] : '' ); ?>"
					name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $field['id'] ); ?>"
					id="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>"
					<?php echo ( isset( $field['min'] ) ? 'min="' . esc_attr( $field['min'] ) . '"' : '' ); ?>
					<?php echo ( isset( $field['step'] ) ? 'step="' . esc_attr( $field['step'] ) . '"' : '' ); ?>						
					placeholder=""
					<?php
					if ( isset( $field['custom_attribute'] ) && is_array( $field['custom_attribute'] ) ) {
						$custom_attributes = $field['custom_attribute'];
						foreach ( $custom_attributes as $attr_name => $attr_val ) {
							echo esc_html( $attr_name . '=' . $attr_val );
						}
					}
					?>
					/>
					<span class="description"><?php echo wp_kses_post( isset( $field['description'] ) ? $field['description'] : '' ); ?></span>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Html fields for custom field at taxonomy edit page.
	 *
	 * @param array $term_fields_arr array containing various attributes of HTML input tag.
	 * @return void
	 */
	public function mbfw_taxonomy_custom_fields_html( $term_fields_arr ) {
		foreach ( $term_fields_arr as $tag_arr ) {
			if ( isset( $tag_arr['term_id'] ) && isset( $tag_arr['id'] ) ) {
				?>
				<tr class="form-field">
					<th>
						<label for="<?php echo esc_attr( $tag_arr['id'] ); ?>"><?php echo wp_kses_post( isset( $tag_arr['label'] ) ? $tag_arr['label'] : '' ); ?></label>
					</th>
					<td>
						<input
						name="<?php echo esc_attr( isset( $tag_arr['name'] ) ? $tag_arr['name'] : '' ); ?>"
						id="<?php echo esc_attr( $tag_arr['id'] ); ?>"
						type="<?php echo esc_attr( $tag_arr['type'] ); ?>"
						value="<?php echo esc_attr( ( 'checkbox' === $tag_arr['type'] ) ? 'yes' : get_term_meta( $tag_arr['term_id'], $tag_arr['id'], true ) ); ?>"
						<?php ( 'checkbox' === $tag_arr['type'] ) ? checked( get_term_meta( $tag_arr['term_id'], $tag_arr['id'], true ), 'yes' ) : ''; ?>
						<?php
						if ( isset( $tag_arr['custom_attribute'] ) && is_array( $tag_arr['custom_attribute'] ) ) {
							$custom_attributes = $tag_arr['custom_attribute'];
							foreach ( $custom_attributes as $attr_name => $attr_val ) {
								echo esc_html( $attr_name . '=' . $attr_val );
							}
						}
						?>
						style="<?php echo esc_attr( isset( $tag_arr['style'] ) ? $tag_arr['style'] : '' ); ?>"
						/>
						<p class="description"><?php echo wp_kses_post( isset( $tag_arr['description'] ) ? $tag_arr['description'] : '' ); ?></p>
					</td>
				</tr>
				<?php
			}
		}
	}

	/**
	 * Adding custom fields at edit page of mwb_booking_cost taxonomy.
	 *
	 * @param object $term object of current term.
	 * @param string $taxonomy current taxonomy.
	 * @return void
	 */
	public function mbfw_adding_custom_fields_at_booking_cost_taxonomy_edit_page( $term, $taxonomy ) {
		wp_nonce_field( 'mwb_edit_taxonomy_page', '_mwb_nonce' );
		$term_fields_arr = array(
			array(
				'id'          => 'mwb_mbfw_booking_cost',
				'name'        => 'mwb_mbfw_booking_cost',
				'label'       => __( 'Booking Cost', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'text',
				'term_id'     => $term->term_id,
				'description' => __( 'Please Add booking cost here.', 'mwb-bookings-for-woocommerce' ),
				'style'       => 'width:10em;',
				'min'         => 0,
				'step'        => '0.01',

			),
			array(
				'id'          => 'mwb_mbfw_is_booking_cost_multiply_people',
				'name'        => 'mwb_mbfw_is_booking_cost_multiply_people',
				'label'       => __( 'Multiply by No. of People', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'checkbox',
				'term_id'     => $term->term_id,
				'description' => __( 'Either to multiply by number of people.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'id'          => 'mwb_mbfw_is_booking_cost_multiply_duration',
				'name'        => 'mwb_mbfw_is_booking_cost_multiply_duration',
				'label'       => __( 'Multiply by Duration of Booking', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'checkbox',
				'term_id'     => $term->term_id,
				'description' => __( 'Either to multiply by Duration of Booking.', 'mwb-bookings-for-woocommerce' ),
			),
		);
		$this->mbfw_taxonomy_custom_fields_html( $term_fields_arr );
	}

	/**
	 * Saving custom field at mwb_booking_cost taxonomy.
	 *
	 * @param int $term_id current term id for custom taxonomy.
	 * @return void
	 */
	public function mbfw_saving_custom_fields_at_booking_cost_taxonomy_page( $term_id ) {
		if ( ! isset( $_POST['_mwb_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_mwb_nonce'] ) ), 'mwb_edit_taxonomy_page' ) ) {
			return;
		}
		$b_cost = array_key_exists( 'mwb_mbfw_booking_cost', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_cost'] ) ) : '';
		if ( 0 > $b_cost ) {
			return;
		}
		$term_meta_data = array(
			'mwb_mbfw_booking_cost'                      => array_key_exists( 'mwb_mbfw_booking_cost', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_booking_cost'] ) ) : '',
			'mwb_mbfw_is_booking_cost_multiply_people'   => array_key_exists( 'mwb_mbfw_is_booking_cost_multiply_people', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_booking_cost_multiply_people'] ) ) : '',
			'mwb_mbfw_is_booking_cost_multiply_duration' => array_key_exists( 'mwb_mbfw_is_booking_cost_multiply_duration', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_booking_cost_multiply_duration'] ) ) : '',
		);
		foreach ( $term_meta_data as $term_meta_key => $term_meta_value ) {
			update_term_meta( $term_id, $term_meta_key, $term_meta_value );
		}
	}

	/**
	 * Adding custom column to mwb_booking_cost taxonomy table.
	 *
	 * @param array $columns array containing columns.
	 * @return array
	 */
	public function mbfw_adding_custom_column_booking_costs_taxonomy_table( $columns ) {
		unset( $columns['description'] );
		unset( $columns['slug'] );
		unset( $columns['posts'] );
		$columns['cost']     = __( 'Cost', 'mwb-bookings-for-woocommerce' );
		$columns['people']   = __( 'People', 'mwb-bookings-for-woocommerce' );
		$columns['duration'] = __( 'Duration', 'mwb-bookings-for-woocommerce' );
		return $columns;
	}

	/**
	 * Add custom column data to mwb_booking_cost taxonomy.
	 *
	 * @param string $content content of the column.
	 * @param string $column_name column name of the table.
	 * @param int    $term_id current term id.
	 * @return string
	 */
	public function mbfw_adding_custom_column_data_booking_costs_taxonomy_table( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'cost':
				$content = get_term_meta( $term_id, 'mwb_mbfw_booking_cost', true );
				break;
			case 'people':
				$content = $this->mwb_mbfw_load_icon_for_yesno_custom_taxonomy_listing( get_term_meta( $term_id, 'mwb_mbfw_is_booking_cost_multiply_people', true ) );
				break;
			case 'duration':
				$content = $this->mwb_mbfw_load_icon_for_yesno_custom_taxonomy_listing( get_term_meta( $term_id, 'mwb_mbfw_is_booking_cost_multiply_duration', true ) );
				break;
			default:
				break;
		}
		return $content;
	}

	/**
	 * Adding extra field at custom mwb_booking_service taxonomy page.
	 *
	 * @return void
	 */
	public function mbfw_adding_custom_fields_at_booking_service_taxonomy_page() {
		wp_nonce_field( 'mwb_edit_taxonomy_page', '_mwb_nonce' );
		$fields = array(
			array(
				'id'          => 'mwb_mbfw_service_cost',
				'type'        => 'number',
				'value'       => '',
				'label'       => __( 'Service Cost', 'mwb-bookings-for-woocommerce' ),
				'description' => __( 'Please Add service cost here.', 'mwb-bookings-for-woocommerce' ),
				'style'       => 'width:10em;',
				'min'         => 0,
				'step'        => '0.01',
			),
			array(
				'label'       => __( 'Multiply by Number of People', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_service_cost_multiply_people',
				'type'        => 'checkbox',
				'value'       => 'yes',
				'description' => __( 'Either to multiply by number of people.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'label'       => __( 'Multiply by Booking Duration', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_service_cost_multiply_duration',
				'value'       => 'yes',
				'type'        => 'checkbox',
				'description' => __( 'Either to multiply by Booking Duration.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'label'       => __( 'If Optional', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_service_optional',
				'value'       => 'yes',
				'type'        => 'checkbox',
				'description' => __( 'Either the Service is Optional.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'label'       => __( 'If Hidden', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_service_hidden',
				'value'       => 'yes',
				'type'        => 'checkbox',
				'description' => __( 'Either the Service is Hidden.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'label'       => __( 'If has Quantity', 'mwb-bookings-for-woocommerce' ),
				'id'          => 'mwb_mbfw_is_service_has_quantity',
				'value'       => 'yes',
				'type'        => 'checkbox',
				'description' => __( 'Either the service has quantity.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'id'               => 'mwb_mbfw_service_minimum_quantity',
				'type'             => 'number',
				'value'            => '',
				'label'            => __( 'Minimum Quantity', 'mwb-bookings-for-woocommerce' ),
				'description'      => __( 'Please Add Minimum Quantity of the Service Bookable.', 'mwb-bookings-for-woocommerce' ),
				'custom_attribute' => array( 'disabled' => 'disabled' ),
				'style'            => 'width:10em;',
			),
			array(
				'id'               => 'mwb_mbfw_service_maximum_quantity',
				'type'             => 'number',
				'value'            => '',
				'label'            => __( 'Maximum Quantity', 'mwb-bookings-for-woocommerce' ),
				'description'      => __( 'Please Add Maximum Quantity of the Service Bookable.', 'mwb-bookings-for-woocommerce' ),
				'custom_attribute' => array( 'disabled' => 'disabled' ),
				'style'            => 'width:10em;',
			),
		);
		$this->mbfw_taxonomy_adding_fields_html( $fields );
	}

	/**
	 * Adding custom fields at edit page of mwb_booking_service taxonomy.
	 *
	 * @param object $term object of current term.
	 * @param string $taxonomy current taxonomy.
	 * @return void
	 */
	public function mbfw_adding_custom_fields_at_booking_service_taxonomy_edit_page( $term, $taxonomy ) {
		wp_nonce_field( 'mwb_edit_taxonomy_page', '_mwb_nonce' );
		$term_fields_arr = array(
			array(
				'id'          => 'mwb_mbfw_service_cost',
				'name'        => 'mwb_mbfw_service_cost',
				'label'       => __( 'Service Cost', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'number',
				'term_id'     => $term->term_id,
				'description' => __( 'Please Add service cost here.', 'mwb-bookings-for-woocommerce' ),
				'style'       => 'width:10em;',
				'min'         => 0,
				'step'        => '0.01',
			),
			array(
				'id'          => 'mwb_mbfw_is_service_cost_multiply_people',
				'name'        => 'mwb_mbfw_is_service_cost_multiply_people',
				'label'       => __( 'Multiply by No. of People', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'checkbox',
				'term_id'     => $term->term_id,
				'description' => __( 'Either to multiply by number of people.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'id'          => 'mwb_mbfw_is_service_cost_multiply_duration',
				'name'        => 'mwb_mbfw_is_service_cost_multiply_duration',
				'label'       => __( 'Multiply by Booking Duration', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'checkbox',
				'term_id'     => $term->term_id,
				'description' => __( 'Either to multiply by Duration of Booking.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'id'          => 'mwb_mbfw_is_service_optional',
				'name'        => 'mwb_mbfw_is_service_optional',
				'label'       => __( 'If Optional', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'checkbox',
				'term_id'     => $term->term_id,
				'description' => __( 'Either the Service is Optional.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'id'          => 'mwb_mbfw_is_service_hidden',
				'name'        => 'mwb_mbfw_is_service_hidden',
				'label'       => __( 'If Hidden', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'checkbox',
				'term_id'     => $term->term_id,
				'description' => __( 'Either the Service is Hidden.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'id'          => 'mwb_mbfw_is_service_has_quantity',
				'name'        => 'mwb_mbfw_is_service_has_quantity',
				'label'       => __( 'If has Quantity', 'mwb-bookings-for-woocommerce' ),
				'type'        => 'checkbox',
				'term_id'     => $term->term_id,
				'description' => __( 'Either the Service has Quantity.', 'mwb-bookings-for-woocommerce' ),
			),
			array(
				'id'               => 'mwb_mbfw_service_minimum_quantity',
				'name'             => 'mwb_mbfw_service_minimum_quantity',
				'label'            => __( 'Minimum Quantity', 'mwb-bookings-for-woocommerce' ),
				'type'             => 'number',
				'term_id'          => $term->term_id,
				'description'      => __( 'Please Add Minimum Quantity of the Service Bookable.', 'mwb-bookings-for-woocommerce' ),
				'custom_attribute' => ( 'yes' !== get_term_meta( $term->term_id, 'mwb_mbfw_is_service_has_quantity', true ) ) ? array( 'disabled' => 'disabled' ) : array(),
				'style'            => 'width:10em;',
			),
			array(
				'id'               => 'mwb_mbfw_service_maximum_quantity',
				'name'             => 'mwb_mbfw_service_maximum_quantity',
				'label'            => __( 'Maximum Quantity', 'mwb-bookings-for-woocommerce' ),
				'type'             => 'number',
				'term_id'          => $term->term_id,
				'description'      => __( 'Please Add Maximum Quantity.', 'mwb-bookings-for-woocommerce' ),
				'custom_attribute' => ( 'yes' !== get_term_meta( $term->term_id, 'mwb_mbfw_is_service_has_quantity', true ) ) ? array( 'disabled' => 'disabled' ) : array(),
				'style'            => 'width:10em;',
			),
		);
		$this->mbfw_taxonomy_custom_fields_html( $term_fields_arr );
	}

	/**
	 * Saving custom field at mwb_booking_cost taxonomy.
	 *
	 * @param int $term_id current term id for custom taxonomy.
	 * @return void
	 */
	public function mbfw_saving_custom_fields_at_booking_service_taxonomy_page( $term_id ) {
		if ( ! isset( $_POST['_mwb_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_mwb_nonce'] ) ), 'mwb_edit_taxonomy_page' ) ) {
			return;
		}
		$b_service = array_key_exists( 'mwb_mbfw_service_cost', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_service_cost'] ) ) : '';
		if ( 0 > $b_service ) {
			return;
		}
		$term_meta_data = array(
			'mwb_mbfw_service_cost'                      => array_key_exists( 'mwb_mbfw_service_cost', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_service_cost'] ) ) : '',
			'mwb_mbfw_is_service_cost_multiply_people'   => array_key_exists( 'mwb_mbfw_is_service_cost_multiply_people', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_service_cost_multiply_people'] ) ) : '',
			'mwb_mbfw_is_service_cost_multiply_duration' => array_key_exists( 'mwb_mbfw_is_service_cost_multiply_duration', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_service_cost_multiply_duration'] ) ) : '',
			'mwb_mbfw_is_service_optional'               => array_key_exists( 'mwb_mbfw_is_service_optional', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_service_optional'] ) ) : '',
			'mwb_mbfw_is_service_hidden'                 => array_key_exists( 'mwb_mbfw_is_service_hidden', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_service_hidden'] ) ) : '',
			'mwb_mbfw_is_service_has_quantity'           => array_key_exists( 'mwb_mbfw_is_service_has_quantity', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_is_service_has_quantity'] ) ) : '',
			'mwb_mbfw_service_minimum_quantity'          => array_key_exists( 'mwb_mbfw_service_minimum_quantity', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_service_minimum_quantity'] ) ) : '',
			'mwb_mbfw_service_maximum_quantity'          => array_key_exists( 'mwb_mbfw_service_maximum_quantity', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['mwb_mbfw_service_maximum_quantity'] ) ) : '',
		);

		foreach ( $term_meta_data as $term_meta_key => $term_meta_value ) {
			update_term_meta( $term_id, $term_meta_key, $term_meta_value );
		}
	}

	/**
	 * Adding custom column to mwb_booking_service taxonomy table.
	 *
	 * @param array $columns array containing columns.
	 * @return array
	 */
	public function mbfw_adding_custom_column_booking_services_taxonomy_table( $columns ) {
		unset( $columns['description'] );
		unset( $columns['slug'] );
		unset( $columns['posts'] );
		$columns['cost']         = __( 'Cost', 'mwb-bookings-for-woocommerce' );
		$columns['people']       = __( 'People', 'mwb-bookings-for-woocommerce' );
		$columns['duration']     = __( 'Duration', 'mwb-bookings-for-woocommerce' );
		$columns['optional']     = __( 'Optional', 'mwb-bookings-for-woocommerce' );
		$columns['is_hidden']    = __( 'Hidden', 'mwb-bookings-for-woocommerce' );
		$columns['has_quantity'] = __( 'Quantity', 'mwb-bookings-for-woocommerce' );
		return $columns;
	}

	/**
	 * Add custom column data to mwb_booking_service taxonomy.
	 *
	 * @param string $content content of the column.
	 * @param string $column_name column name of the table.
	 * @param int    $term_id current term id.
	 * @return string
	 */
	public function mbfw_adding_custom_column_data_booking_services_taxonomy_table( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'cost':
				$content = get_term_meta( $term_id, 'mwb_mbfw_service_cost', true );
				break;
			case 'people':
				$content = $this->mwb_mbfw_load_icon_for_yesno_custom_taxonomy_listing( get_term_meta( $term_id, 'mwb_mbfw_is_service_cost_multiply_people', true ) );
				break;
			case 'duration':
				$content = $this->mwb_mbfw_load_icon_for_yesno_custom_taxonomy_listing( get_term_meta( $term_id, 'mwb_mbfw_is_service_cost_multiply_duration', true ) );
				break;
			case 'optional':
				$content = $this->mwb_mbfw_load_icon_for_yesno_custom_taxonomy_listing( get_term_meta( $term_id, 'mwb_mbfw_is_service_optional', true ) );
				break;
			case 'is_hidden':
				$content = $this->mwb_mbfw_load_icon_for_yesno_custom_taxonomy_listing( get_term_meta( $term_id, 'mwb_mbfw_is_service_hidden', true ) );
				break;
			case 'has_quantity':
				$content = $this->mwb_mbfw_load_icon_for_yesno_custom_taxonomy_listing( get_term_meta( $term_id, 'mwb_mbfw_is_service_has_quantity', true ) );
				break;
			default:
				break;
		}
		return $content;
	}

	/**
	 * Returm icons in custom taxonomy listing page.
	 *
	 * @param string $value string containing yes or no.
	 * @return string
	 */
	public function mwb_mbfw_load_icon_for_yesno_custom_taxonomy_listing( $value ) {
		if ( 'yes' === $value ) {
			return '<span class="dashicons dashicons-yes" title="' . esc_attr__( 'yes', 'mwb-bookings-for-woocommerce' ) . '"></span>';
		}
		return '<span class="dashicons dashicons-no-alt" title="' . esc_attr__( 'no', 'mwb-bookings-for-woocommerce' ) . '"></span>';
	}

	/**
	 * Add custom badge of booking at order listing page.
	 *
	 * @param string $column_name current table columnname.
	 * @param int    $order_id current order id.
	 * @return void
	 */
	public function mbfw_add_label_for_booking_type( $column_name, $order_id ) {
		if ( ! OrderUtil::custom_orders_table_usage_is_enabled() ) {

			if ( 'order_number' === $column_name ) {
				$order = wc_get_order( $order_id );
				if ( 'booking' === $order->get_meta( 'mwb_order_type', true ) ) {
					?>
					<span class="mwb-mbfw-booking-product-order-listing" title="<?php esc_html_e( 'This order contains Booking Service/Product.', 'mwb-bookings-for-woocommerce' ); ?>">
						<?php esc_html_e( 'Booking Order', 'mwb-bookings-for-woocommerce' ); ?>
					</span>
					<?php
				}
			}
		}
	}
	/**
	 * Add custom badge of booking at order listing page.
	 *
	 * @param string $column_name current table columnname.
	 * @param int    $order_id current order id.
	 * @return void
	 */
	public function mbfw_add_label_for_booking_type_temp( $column_name, $order_id ) {
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {

			if ( 'order_number' === $column_name ) {
				$order = wc_get_order( $order_id );
				if ( 'booking' === $order->get_meta( 'mwb_order_type', true ) ) {
					?>
					<span class="mwb-mbfw-booking-product-order-listing" title="<?php esc_html_e( 'This order contains Booking Service/Product.', 'mwb-bookings-for-woocommerce' ); ?>">
						<?php esc_html_e( 'Booking Order', 'mwb-bookings-for-woocommerce' ); ?>
					</span>
					<?php
				}
			}
		}
	}

	/**
	 * Adding filter on the order listing page.
	 *
	 * @return void
	 */
	public function mbfw_add_filter_on_order_listing_page() {
		global $pagenow, $post_type;
		if ( 'shop_order' !== $post_type && 'edit.php' !== $pagenow ) {
			return;
		}
		$current = isset( $_GET['filter_booking'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_booking'] ) ) : ''; // phpcs:ignore WordPress.Security
		?>
		<select name="filter_booking">
			<option value="">
				<?php esc_html_e( 'choose filter..', 'mwb-bookings-for-woocommerce' ); ?>
			</option>
			<option value="booking" <?php selected( $current, 'booking' ); ?>>
				<?php esc_html_e( 'Filter by Booking', 'mwb-bookings-for-woocommerce' ); ?>
			</option>
		</select>
		<?php
	}

	/**
	 * Altering query to show the results from custom filter.
	 *
	 * @param object $query query for showing posts.
	 * @return void
	 */
	public function mbfw_vary_query_to_list_only_booking_types( $query ) {
		global $pagenow;
		// phpcs:disable WordPress
		if ( $query->is_admin && 'edit.php' === $pagenow && isset( $_GET['filter_booking'] ) && '' !== sanitize_text_field( wp_unslash( $_GET['filter_booking'] ) ) && isset( $_GET['post_type'] ) && ( 'shop_order' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) ) ) {
			$meta_query = array(
				array(
					'meta_key' => 'mwb_order_type',
					'value'    => sanitize_text_field( wp_unslash( $_GET['filter_booking'] ) ),
				),
			);
			$query->set( 'meta_query', $meta_query );
			$query->set( 'posts_per_page', 10 );
			$query->set( 'paged', ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ) );
		}
		// phpcs:enable WordPress
	}

	/**
	 * Hide custom line item key from order edit page.
	 *
	 * @param array $hidden_keys array containing keys to hide.
	 * @return array
	 */
	public function mbfw_hide_order_item_meta_data( $hidden_keys ) {
		$custom_line_item_key_name = array(
			'_mwb_mbfw_people_number',
			'_mwb_mbfw_enable_calendar',
			'_mwb_mbfw_enable_time_picker',
			'_mwb_mbfw_service_and_count',
			'_mwb_mbfw_booking_extra_costs',
			'_wps_booking_slot',
			'_wps_single_cal_date_time_from',
			'_wps_single_cal_date_time_to',
		);
		return /**
		* Filter is for returning something.
		*
		* @since 1.0.0
		*/
		apply_filters( 'mwb_mbfw_hide_custom_line_item_meta_keys', array_merge( $hidden_keys, $custom_line_item_key_name ) );
	}

	/**
	 * Change the line item meta key.
	 *
	 * @param string $display_key key name to display.
	 * @param object $meta meta key object.
	 * @param object $item current item object.
	 * @return string
	 */
	public function mbfw_change_line_item_meta_key_order_edit_page( $display_key, $meta, $item ) {

		switch ( $display_key ) {
			case '_mwb_bfwp_date_time_from':
				return __( 'From', 'mwb-bookings-for-woocommerce' );
			case '_mwb_bfwp_date_time_to':
				return __( 'To', 'mwb-bookings-for-woocommerce' );
			case '_wps_single_cal_booking_dates':
				return __( 'Booking Dates', 'mwb-bookings-for-woocommerce' );
			default:
				break;
		}

		return $display_key;
	}

	/**
	 * Get all booking dates.
	 *
	 * @return void
	 */
	public function mwb_mbfw_get_all_events_date() {

		check_ajax_referer( 'mwb_mbfw_admin_nonce', 'nonce' );

		$status = ! empty( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
		$orders = '';
		if ( ! empty( $status ) ) {
			$orders = wc_get_orders(
				array(
					'status'   => $status,
					'limit'    => -1,
					'meta_key' => 'mwb_order_type', // phpcs:ignore WordPress_wps_single_cal_booking_dates.
					'meta_val' => 'booking',
				)
			);
		} else {
			$orders = wc_get_orders(
				array(
					'status'   => array( 'wc-processing', 'wc-on-hold', 'wc-pending', 'wc-completed' ),
					'limit'    => -1,
					'meta_key' => 'mwb_order_type', // phpcs:ignore WordPress_wps_single_cal_booking_dates.
					'meta_val' => 'booking',
				)
			);
		}

		$all_events = array();
		foreach ( $orders as $order ) {
			$status = $order->get_status();
			$items  = $order->get_items();
			foreach ( $items as $item ) {

				$booking_type = wps_booking_get_meta_data( $item['product_id'], 'wps_mbfw_booking_type', true );
				// for single calender.
				if ( ! empty( $item->get_meta( '_wps_single_cal_booking_dates', true ) ) ) {
					$date_time_from = $item->get_meta( '_wps_single_cal_booking_dates', true );
					$date_time_to   = $item->get_meta( '_wps_single_cal_booking_dates', true );

					$date_time_from = ( ! empty( $date_time_from ) ? $date_time_from : gmdate( 'd-m-Y H:i', $order->get_date_created()->getTimestamp() ) );
					$date_time_to   = ( ! empty( $date_time_to ) ? $date_time_to : gmdate( 'd-m-Y H:i', $order->get_date_created()->getTimestamp() ) );

					$date_array_from = explode( ' | ', $date_time_from );
					$date_array_to   = explode( ' | ', $date_time_to );
					if ( ! empty( $date_array_from ) && is_array( $date_array_from ) ) {

						foreach ( $date_array_from as $key => $value ) {

							$all_events[] = array(
								'title' => '#Order Id: ' . $order->get_id() . ' ' . $item['name'],
								'start' => gmdate( 'Y-m-d', strtotime( $value ) ),
								'end'   => gmdate( 'Y-m-d', strtotime( $date_array_to[ $key ] ) ),
								'url'   => admin_url( 'admin.php?page=wc-orders&action=edit&id=' . $order->get_id() ),
								'class' => $status,
							);
						}
					} else {
						$all_events[] = array(
							'title' => '#Order Id: ' . $order->get_id() . ' ' . $item['name'],
							'start' => gmdate( 'Y-m-d', strtotime( $date_time_from ) ) . 'T' . gmdate( 'H:i', strtotime( $date_time_from ) ),
							'end'   => gmdate( 'Y-m-d', strtotime( $date_time_to ) ) . 'T' . gmdate( 'H:i', strtotime( $date_time_to ) ),
							'url'   => admin_url( 'admin.php?page=wc-orders&action=edit&id=' . $order->get_id() ),
							'class' => $status,
						);
					}
				}

				// for dual calender.
				if ( ! empty( $item->get_meta( '_mwb_bfwp_date_time_from', true ) && ! empty( $item->get_meta( '_mwb_bfwp_date_time_to', true ) ) ) ) {

					$date_time_from = $item->get_meta( '_mwb_bfwp_date_time_from', true );
					$date_time_to   = $item->get_meta( '_mwb_bfwp_date_time_to', true );

					$date_time_from = ( ! empty( $date_time_from ) ? $date_time_from : gmdate( 'd-m-Y H:i', $order->get_date_created()->getTimestamp() ) );
					$date_time_to   = ( ! empty( $date_time_to ) ? $date_time_to : gmdate( 'd-m-Y H:i', $order->get_date_created()->getTimestamp() ) );
					$all_events[]   = array(
						'title' => '#Order Id: ' . $order->get_id() . ' ' . $item['name'],
						'start' => gmdate( 'Y-m-d', strtotime( $date_time_from ) ) . 'T' . gmdate( 'H:i', strtotime( $date_time_from ) ),
						// 'start' =>DateTime::createFromFormat('d \d\e F \d\e Y H:i', $date_time_from)->format('Y-m-d\TH:i:sP'),
						'end'   => gmdate( 'Y-m-d', strtotime( $date_time_to ) ) . 'T' . gmdate( 'H:i', strtotime( $date_time_to ) ),
						'url'   => admin_url( 'admin.php?page=wc-orders&action=edit&id=' . $order->get_id() ),
						'class' => $status,
					);
				}
			}
		}

		echo wp_json_encode( $all_events );
		wp_die();
	}

	/**
	 * Migrate old plugin settings.
	 *
	 * @return void
	 * @version 1.0.0
	 */
	public function mwb_mbfw_migrate_settings_from_older_plugin() {
		if ( ! get_option( 'mwb_mbfw_plugin_setting_migrated' ) ) {
			include_once MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH . 'includes/class-mwb-bookings-for-woocommerce-activator.php';
			Mwb_Bookings_For_Woocommerce_Activator::mwb_migrate_old_plugin_settings();
		}
	}

		// new feature plugin simplification start.
		/**
		 * Return mwb-plugin menu at customtaxonomy page.
		 *
		 * @param mixed $parent_file parent_file.
		 * @return mixed
		 */
	public function prefix_highlight_taxonomy_parent_menu( $parent_file ) {
		global $submenu_file, $current_screen, $pagenow;
		$mwb_bfw_taxonomy_array = $this->mwb_get_taxonomy_array();
		if ( in_array( get_current_screen()->taxonomy, $mwb_bfw_taxonomy_array ) ) {
			$parent_file = 'wps-plugins';
		}
		return $parent_file;
	}

		/**
		 * Return booking sub-menu at custom taxonomy page.
		 *
		 * @param mixed $submenu_file submenu_file.
		 * @param mixed $parent_file parent_file.
		 * @return mixed
		 */
	public function mwb_bfw_set_submenu_file_to_handle_menu_for_wp_pages( $submenu_file, $parent_file ) {

		$mwb_bfw_taxonomy_array = $this->mwb_get_taxonomy_array();
		if ( in_array( get_current_screen()->taxonomy, $mwb_bfw_taxonomy_array, true ) ) {
			$submenu_file = 'mwb_bookings_for_woocommerce_menu';
		}

		return $submenu_file;
	}

		/**
		 * Display booking html at custom taxonomy page.
		 *
		 * @return void
		 */
	public function mwb_bfw_taxonomy_page_display_html() {

		global $current_screen;
		$mwb_bfw_taxonomy_array = $this->mwb_get_taxonomy_array();
		if ( in_array( get_current_screen()->taxonomy, $mwb_bfw_taxonomy_array ) ) {
			$this->mbfw_options_menu_html();
			echo '<section class="mwb-section mwb-taxonomy-section_wrap"><div>';
		}

	}

		/**
		 * Function returning array of custom taxonomy.
		 *
		 * @return array
		 */
	public static function mwb_get_taxonomy_array() {
		$taxonomy_array = array( 'mwb_booking_cost', 'mwb_booking_service' );
							/**
							 * Filter is for returning something.
							 *
							 * @since 1.0.0
							 */
		$taxonomy_array = apply_filters( 'mwb_bfw_booking_taxonomy_array', $taxonomy_array );
		return $taxonomy_array;
	}

		/**
		 * Custom taxonomy footer.
		 *
		 * @return void
		 */
	public function mwb_bfw_footer_custom_taxonomy_edit_page_callback() {
		$mwb_bfw_taxonomy_array = $this->mwb_get_taxonomy_array();

		if ( in_array( get_current_screen()->taxonomy, $mwb_bfw_taxonomy_array ) ) {
			echo '</div></section>';
		}
	}

	/**
	 * Function to set quantity
	 *
	 * @param object $cart is object.
	 * @return void
	 */
	public function wps_mbfw_change_cart_item_quantities( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['data']->get_id();
			if ( ! empty( $product_id ) ) {
				$_product = wc_get_product( $product_id );

				if ( 'mwb_booking' == $_product->get_type() ) {
					$max_booking = wps_booking_get_meta_data( $product_id, 'mwb_mbfw_maximum_booking_per_unit', true );

					if ( ! empty( $max_booking ) && $cart_item['quantity'] > $max_booking ) {
						$cart->set_quantity( $cart_item_key, $max_booking );
					}
				}
			}
		}
	}

}
