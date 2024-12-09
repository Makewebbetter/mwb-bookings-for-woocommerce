<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://wpswings.com/
 * @since   1.0.0
 * @package Mwb_Bookings_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Bookings For WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/mwb-bookings-for-woocommerce/
 * Description:        <code><strong>Bookings for WooCommerce</strong></code> enable store owners to create an online booking system that allows them to turn their products into Booking Solutions.<a href="https://wpswings.com/woocommerce-plugins/?utm_source=wpswings-bookings&utm_medium=bookings-org-backend&utm_campaign=official" target="_blank"> Elevate your e-commerce store by exploring more on <strong> WP Swings </strong></a>.
 * Version:           3.3.0
 * Author:            WP Swings
 * Author URI:        https://wpswings.com/?utm_source=wpswings-bookings-official&utm_medium=bookings-org-page&utm_campaign=official
 * Text Domain:       mwb-bookings-for-woocommerce
 * Domain Path:       /languages
 *
 * Requires Plugins: woocommerce
 * Requires at least:    5.5.0
 * Tested up to:         6.7.1
 * WC requires at least: 6.5.0
 * WC tested up to:      9.4.3
 * Requires PHP:         7.2
 * Stable tag:           3.3.0
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
use Automattic\WooCommerce\Utilities\OrderUtil;

if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins', array() ), true ) || ( is_multisite() && array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins', array() ) ) ) ) {

	/**
	 * Define plugin constants.
	 *
	 * @since 2.0.0
	 */
	function define_mwb_bookings_for_woocommerce_constants() {
		mwb_bookings_for_woocommerce_constants( 'MWB_BOOKINGS_FOR_WOOCOMMERCE_VERSION', '3.3.0' );
		mwb_bookings_for_woocommerce_constants( 'MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		mwb_bookings_for_woocommerce_constants( 'MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL', plugin_dir_url( __FILE__ ) );
		mwb_bookings_for_woocommerce_constants( 'MWB_BOOKINGS_FOR_WOOCOMMERCE_SERVER_URL', 'https://wpswings.com' );
		mwb_bookings_for_woocommerce_constants( 'MWB_BOOKINGS_FOR_WOOCOMMERCE_ITEM_REFERENCE', 'Bookings For WooCommerce' );
	}

	/**
	 * Callable function for defining plugin constants.
	 *
	 * @param string $key   Key for contant.
	 * @param string $value value for contant.
	 * @since 2.0.0
	 */
	function mwb_bookings_for_woocommerce_constants( $key, $value ) {
		if ( ! defined( $key ) ) {
			define( $key, $value );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-mwb-bookings-for-woocommerce-activator.php
	 *
	 * @param boolean $network_wide either network activated or not.
	 * @since 1.0.0
	 * @return void
	 */
	function activate_mwb_bookings_for_woocommerce( $network_wide ) {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-bookings-for-woocommerce-activator.php';
		Mwb_Bookings_For_Woocommerce_Activator::mwb_bookings_for_woocommerce_activate( $network_wide );
		$mwb_mbfw_active_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_mbfw_active_plugin ) && ! empty( $mwb_mbfw_active_plugin ) ) {
			$mwb_mbfw_active_plugin['mwb-bookings-for-woocommerce'] = array(
				'plugin_name' => __( 'Bookings For WooCommerce', 'mwb-bookings-for-woocommerce' ),
				'active'      => '1',
			);
		} else {
			$mwb_mbfw_active_plugin                                 = array();
			$mwb_mbfw_active_plugin['mwb-bookings-for-woocommerce'] = array(
				'plugin_name' => __( 'Bookings For WooCommerce', 'mwb-bookings-for-woocommerce' ),
				'active'      => '1',
			);
		}
		update_option( 'mwb_all_plugins_active', $mwb_mbfw_active_plugin );
	}

	/**
	 * Will be used when new blog is created on multisite.
	 *
	 * @param object $new_site current blog object.
	 * @since 1.0.0
	 * @return void
	 */
	function mwb_bookings_for_woocommerce_on_new_blog_creation( $new_site ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		if ( is_plugin_active_for_network( 'mwb-bookings-for-woocommerce/mwb-bookings-for-woocommerce.php' ) ) {
			$blog_id = isset( $new_site->blog_id ) ? $new_site->blog_id : '';
			switch_to_blog( $blog_id );
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-bookings-for-woocommerce-activator.php';
			Mwb_Bookings_For_Woocommerce_Activator::mwb_bookings_for_woocommerce_update_default_value();
			restore_current_blog();
		}
	}
	add_action( 'wp_initialize_site', 'mwb_bookings_for_woocommerce_on_new_blog_creation' );

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-mwb-bookings-for-woocommerce-deactivator.php
	 */
	function deactivate_mwb_bookings_for_woocommerce() {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-bookings-for-woocommerce-deactivator.php';
		Mwb_Bookings_For_Woocommerce_Deactivator::mwb_bookings_for_woocommerce_deactivate();
		$mwb_mbfw_deactive_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_mbfw_deactive_plugin ) && ! empty( $mwb_mbfw_deactive_plugin ) ) {
			foreach ( $mwb_mbfw_deactive_plugin as $mwb_mbfw_deactive_key => $mwb_mbfw_deactive ) {
				if ( 'mwb-bookings-for-woocommerce' === $mwb_mbfw_deactive_key ) {
					$mwb_mbfw_deactive_plugin[ $mwb_mbfw_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'mwb_all_plugins_active', $mwb_mbfw_deactive_plugin );
	}
	register_activation_hook( __FILE__, 'activate_mwb_bookings_for_woocommerce' );
	register_deactivation_hook( __FILE__, 'deactivate_mwb_bookings_for_woocommerce' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-mwb-bookings-for-woocommerce.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since 2.0.0
	 */
	function run_mwb_bookings_for_woocommerce() {
		define_mwb_bookings_for_woocommerce_constants();
		$mbfw_plugin_standard = new Mwb_Bookings_For_Woocommerce();
		$mbfw_plugin_standard->mbfw_run();
		$GLOBALS['mbfw_mwb_mbfw_obj'] = $mbfw_plugin_standard;
	}
	run_mwb_bookings_for_woocommerce();
	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwb_bookings_for_woocommerce_settings_link' );

	/**
	 * Settings link.
	 *
	 * @since 2.0.0
	 * @param array $links Settings link array.
	 */
	function mwb_bookings_for_woocommerce_settings_link( $links ) {
		$my_link = array(
			'<a href="' . admin_url( 'admin.php?page=mwb_bookings_for_woocommerce_menu' ) . '">' . __( 'Settings', 'mwb-bookings-for-woocommerce' ) . '</a>',
		);
		if ( ! in_array( 'bookings-for-woocommerce-pro/bookings-for-woocommerce-pro.php', get_option( 'active_plugins', array() ), true ) ) {
			$my_link[] = '<a href="https://wpswings.com/product/bookings-for-woocommerce-pro/?utm_source=wpswings-bookings-pro&utm_medium=booikings-org-backend&utm_campaign=go-pro" target="_blank" id="mbfw-go-pro-link">' . __( 'Go Pro', 'mwb-bookings-for-woocommerce' ) . '</a>';
		}
		return array_merge( $my_link, $links );
	}


		/**
		 * Replace get_post_meta with wps_bookings_get_meta_data.
		 *
		 * @param integer $id is order id.
		 * @param string  $key is meta key.
		 * @param string  $v is meta value.
		 * @return string
		 */
	function wps_booking_get_meta_data( $id, $key, $v ) {
		if ( 'shop_order' === OrderUtil::get_order_type( $id ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$order    = wc_get_order( $id );
			if ( '_customer_user' == $key ) {
				$meta_val = $order->get_customer_id();
				return $meta_val;
			}
			$meta_val = $order->get_meta( $key );
			return $meta_val;
		} else {

			$meta_val = get_post_meta( $id, $key, $v );
			return $meta_val;
		}
	}



		/**
		 * Replace update_post_meta with wps_bookings_update_meta_data.
		 *
		 * @param integer $id is order id.
		 * @param string  $key is meta key.
		 * @param string  $value is meta value.
		 * @return void
		 */
	function wps_booking_update_meta_data( $id, $key, $value ) {
		if ( 'shop_order' === OrderUtil::get_order_type( $id ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$order = wc_get_order( $id );
			$order->update_meta_data( $key, $value );
			$order->save();
		} else {
			// Traditional CPT-based orders are in use.
			update_post_meta( $id, $key, $value );
		}
	}


	/**
	 * Adding custom setting links at the plugin activation list.
	 *
	 * @param  array  $links_array      array containing the links to plugin.
	 * @param  string $plugin_file_name plugin file name.
	 * @return array
	 */
	function mwb_bookings_for_woocommerce_custom_settings_at_plugin_tab( $links_array, $plugin_file_name ) {
		if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
			$links_array[] = '<a href="https://demo.wpswings.com/bookings-for-woocommerce-pro/?utm_source=wpswings-bookings-demo&utm_medium=booikngs-org-backend&utm_campaign=demo" target="_blank"><svg class="wps-info-img" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 169.34 169.34"><defs><style>.cls-1{fill:#1e1e1e;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g id="Layer_2-2" data-name="Layer 2"><g id="Layer_1-2-2" data-name="Layer 1-2"><g id="dEMO"><path class="cls-1" d="M84.67,169.34a84.67,84.67,0,1,1,84.67-84.67A84.76,84.76,0,0,1,84.67,169.34ZM84.67,12a72.67,72.67,0,1,0,72.67,72.67A72.75,72.75,0,0,0,84.67,12Z"/><path class="cls-1" d="M84.67,145.83a61.16,61.16,0,1,1,61.16-61.16A61.23,61.23,0,0,1,84.67,145.83Zm0-110.32a49.16,49.16,0,1,0,49.16,49.16A49.22,49.22,0,0,0,84.67,35.51Z"/><path class="cls-1" d="M74.18,107.67a4.81,4.81,0,0,1-4.81-4.81V66.49a4.8,4.8,0,0,1,7.21-4.17l31.5,18.18a4.82,4.82,0,0,1,0,8.34L76.58,107A4.82,4.82,0,0,1,74.18,107.67Z"/></g></g></g></g></g></svg>' . __( 'Demo', 'mwb-bookings-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://www.youtube.com/watch?v=QTIiZDAtWKA" target="_blank"><img src="' . MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/YouTube.png" class="wps-info-img" style="margin-right: 6px;margin-top: -3px;max-width: 15px;" alt="Video image">' . __( 'Video', 'mwb-bookings-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://docs.wpswings.com/bookings-for-woocommerce/?utm_source=wpswings-bookings-doc&utm_medium=bookings-org-backend&utm_campaign=documentation" target="_blank"><svg class="wps-info-img" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 124.99 169.34"><defs><style>.cls-1{fill:#1e1e1e;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g id="Layer_2-2" data-name="Layer 2"><g id="Layer_1-2-2" data-name="Layer 1-2"><g id="Documentation_filled" data-name="Documentation filled"><g id="Docs"><path class="cls-1" d="M19,169.34a19.05,19.05,0,0,1-19-19V19.06A19,19,0,0,1,19,0H79.87a6,6,0,0,1,6,6V36.42a2.72,2.72,0,0,0,2.72,2.69H119a6,6,0,0,1,6,6v105.2a19.06,19.06,0,0,1-19,19H19ZM19.06,12A7,7,0,0,0,12,19.05V150.31a7,7,0,0,0,7,7h86.9a7,7,0,0,0,7-7V51.11H88.57a14.76,14.76,0,0,1-14.7-14.67V12Z"/><path class="cls-1" d="M119,51.11H88.57a14.76,14.76,0,0,1-14.7-14.67V6a6,6,0,0,1,6-6h0a10.11,10.11,0,0,1,7.36,3.19l34.63,34.62a10.19,10.19,0,0,1,3.12,7.3,6,6,0,0,1-6,6Zm-5.4-4.61h0ZM85.87,18.79V36.42a2.72,2.72,0,0,0,2.72,2.69H106.2Z"/></g><path class="cls-1" d="M82.28,86.39H37.86a6.25,6.25,0,0,1-6.26-6.25h0a6.25,6.25,0,0,1,6.26-6.25H82.28a6.25,6.25,0,0,1,6.26,6.25h0A6.25,6.25,0,0,1,82.28,86.39Z"/><path class="cls-1" d="M82.28,116.06H37.86a6.25,6.25,0,0,1-6.26-6.25h0a6.25,6.25,0,0,1,6.26-6.25H82.28a6.25,6.25,0,0,1,6.26,6.25h0A6.25,6.25,0,0,1,82.28,116.06Z"/></g></g></g></g></g></svg>' . __( 'Documentation', 'mwb-bookings-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-bookings-support&utm_medium=bookings-org-backend&utm_campaign=support" target="_blank"><svg class="wps-info-img" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 177.04 169.03"><defs><style>.cls-1{fill:#1e1e1e;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g id="Layer_2-2" data-name="Layer 2"><g id="Layer_1-2-2" data-name="Layer 1-2"><g id="Support"><path class="cls-1" d="M106.85,158.44v-8.8h20.44c5.46,0,12.11-4.43,14.2-9.48l.52-1.24c2.64-6.33,4.31-10.53,4.79-11.81v-3.06l3.07,1h5.7v3c0,1,0,1.31-5.4,14.31l-.52,1.25c-3.48,8.36-13.29,14.9-22.34,14.9Z"/><path class="cls-1" d="M144.55,78.18V65.1A50.8,50.8,0,0,0,94,14.2H83.09A50.79,50.79,0,0,0,32.5,65.1V78.18H18.34V65.1C18.34,29.2,47.39,0,83.09,0H94c35.71,0,64.75,29.2,64.75,65.1V78.18Z"/><path class="cls-1" d="M154.58,141.69a16.13,16.13,0,0,1-4.42-.59c-6.82-1.89-11.24-8.11-11.24-15.85V83.76c0-7.74,4.4-14,11.24-15.85a16.51,16.51,0,0,1,4.42-.58,18.84,18.84,0,0,1,4.36.52c1.5.15,9.1,1.19,14,7.28,5.49,6.87,5.49,51.88,0,58.75-4.88,6.13-12.48,7.15-14,7.28A18.32,18.32,0,0,1,154.58,141.69Z"/><path class="cls-1" d="M22.46,141.69a18.32,18.32,0,0,1-4.36-.53C16.6,141,9,140,4.11,133.88-1.37,127-1.37,82,4.11,75.14,9,69,16.61,68,18.1,67.85a18.84,18.84,0,0,1,4.36-.52,16.51,16.51,0,0,1,4.42.58C33.7,69.81,38.12,76,38.12,83.76v41.49c0,7.74-4.41,13.95-11.24,15.85A16.52,16.52,0,0,1,22.46,141.69Z"/><path class="cls-1" d="M87.12,169a15.91,15.91,0,0,1,0-31.82H100A15.91,15.91,0,0,1,100,169H87.12Z"/></g></g></g></g></g></svg>' . __( 'Support', 'mwb-bookings-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/woocommerce-services/?utm_source=wpswings-bookings-services&utm_medium=bookings-org-backend&utm_campaign=woocommerce-services" target="_blank"><img src="' . MWB_BOOKINGS_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/services.svg" class="wps-info-img" style="margin-right: 6px;margin-top: -3px;max-width: 15px;" alt="Services image">' . __( 'Services', 'mwb-bookings-for-woocommerce' ) . '</a>';
		}
		return $links_array;
	}
	add_filter( 'plugin_row_meta', 'mwb_bookings_for_woocommerce_custom_settings_at_plugin_tab', 10, 2 );
	// Upgrade notice on plugin dashboard.

} else {
	mwb_mbfw_dependency_checkup();
}

/**
 * Checking dependency for woocommerce plugin.
 *
 * @return void
 */
function mwb_mbfw_dependency_checkup() {
	add_action( 'admin_init', 'mwb_mbfw_deactivate_child_plugin' );
	add_action( 'admin_notices', 'mwb_mbfw_show_admin_notices' );
}
/**
 * Deactivating child plugin.
 *
 * @return void
 */
function mwb_mbfw_deactivate_child_plugin() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}
/**
 * Showing admin notices.
 *
 * @return void
 */
function mwb_mbfw_show_admin_notices() {
	$mwb_mbfw_child_plugin  = __( 'Bookings For WooCommerce', 'mwb-bookings-for-woocommerce' );
	$mwb_mbfw_parent_plugin = __( 'WooCommerce', 'mwb-bookings-for-woocommerce' );
	echo '<div class="notice notice-error is-dismissible"><p>'
		/* translators: %s: dependency checks */
		. sprintf( esc_html__( '%1$s requires %2$s to function correctly. Please activate %2$s before activating %1$s. For now, the plugin has been deactivated.', 'mwb-bookings-for-woocommerce' ), '<strong>' . esc_html( $mwb_mbfw_child_plugin ) . '</strong>', '<strong>' . esc_html( $mwb_mbfw_parent_plugin ) . '</strong>' )
		. '</p></div>';
	if ( isset( $_GET['activate'] ) ) { // phpcs:ignore
		unset( $_GET['activate'] ); //phpcs:ignore
	}
}



// HPOS Compatibility and cart and checkout block.
	add_action(
		'before_woocommerce_init',
		function() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
			}
		}
	);
