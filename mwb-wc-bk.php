<?php
/**
 * Bookings Plugin.
 *
 * @link                 https://makewebbetter.com/
 * @since                1.0.0
 * @package              MWB_Bookings_For_WooCommerce
 *
 * @wordpress-plugin
 * Plugin Name:          MWB Bookings For WooCommerce
 * Plugin URI:           https://wordpress.org/plugins/mwb-bookings-for-woocommerce
 * Description:          MWB Bookings for WooCommerce helps you stay focused while offering a better online booking service for your business growth. Just stop speculating and opt for the best one out.
 * Version:              1.0.1
 * Author:               MakeWebBetter
 * Author URI:           https://makewebbetter.com/?utm_source=MWB-bookings-org&utm_medium=MWB-org-backend&utm_campaign=MWB-bookings-site
 * Requires at least:    4.0
 * Tested up to:         5.7.1
 * WC requires at least: 3.0.0
 * WC tested up to:      5.2.2
 * License:              GPL-3.0
 * License URI:          http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:          mwb-bookings-for-woocommerce
 * Domain Path:          /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// check if woocommerce is activated.
if ( ! mwb_wc_activated() ) {
	// wc not activated, show error and return.
	add_action( 'admin_init', 'mwb_wc_bk_plugin_deactivate' );
	return;
}

// All set activate the plugin.

// register activation.
register_activation_hook( __FILE__, 'activate_mwb_wc_bk' );
// register deactivation.
register_deactivation_hook( __FILE__, 'deactivate_mwb_wc_bk' );
// require plugin base class file.
require plugin_dir_path( __FILE__ ) . 'includes/class-mwb-wc-bk.php';
// define plugin constants.
define_mwb_wc_bk();
// begin plugin execution.
run_mwb_wc_bk();


/**
 * Deactivate plugin hook admin notice.
 */
function mwb_wc_bk_plugin_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
	add_action( 'admin_notices', 'mwb_wc_bk_plugin_error_notice' );
}

/**
 * Show admin notice on plugin deactivation
 */
function mwb_wc_bk_plugin_error_notice() {
	?>
	<div class="error notice is-dismissible">
		<p><?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to install Plugin.', 'mwb-bookings-for-woocommerce' ); ?></p>
	</div>
	<?php

	// To hide Plugin activated notice.
	unset( $_GET['activate'] );       // @codingStandardsIgnoreLine
}
/**
 * Check WC activated both on multisite and single site
 */
function mwb_wc_activated() {
	// multisite.
	$activated = false;
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$activated = true;
		}
	} elseif ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		$activated = true; // Single site.
	}
	return $activated;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mwb-wc-bk-activator.php
 */
function activate_mwb_wc_bk() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-wc-bk-activator.php';
	Mwb_Wc_Bk_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mwb-wc-bk-deactivator.php
 */
function deactivate_mwb_wc_bk() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-wc-bk-deactivator.php';
	Mwb_Wc_Bk_Deactivator::deactivate();
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mwb_wc_bk() {
	$plugin = new Mwb_Wc_Bk();
	$plugin->run();

}

/**
 * Define Plugin Contants
 */
function define_mwb_wc_bk() {
	mwb_wc_bk_constant( 'MWB_WC_BK_BASEPATH', plugin_dir_path( __FILE__ ) );
	mwb_wc_bk_constant( 'MWB_WC_BK_BASEURL', plugin_dir_url( __FILE__ ) );
	mwb_wc_bk_constant( 'MWB_WC_BK_VERSION', '1.0.1' );
	mwb_wc_bk_constant( 'MWB_WC_BK_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . 'public/templates/' );

	mwb_wc_bk_constant( 'ONBOARD_PLUGIN_NAME', 'MWB Bookings For WooCommerce' );

}
/**
 * Defining Constants
 *
 * @param string $name Name of constant.
 * @param string $value Value of contant.
 */
function mwb_wc_bk_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

/**
 * Adding settings link to the post action links.
 *
 * @param array $links array of the post action links.
 * @return array
 */
function mwb_booking_settings_link( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'edit.php?post_type=mwb_cpt_booking&page=global-settings&tab=settings' ) . '">' . esc_html__( 'Settings', 'mwb-bookings-for-woocommerce' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwb_booking_settings_link' );

/**
 * Plugin row meta links
 *
 * @param array  $links_array      Array of the plugin row links.
 * @param string $plugin_file_name Name of the Plugin.
 * @return array
 */
function mwb_booking_plugin_row_links( $links_array, $plugin_file_name ) {

	if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
		// you can still use array_unshift() to add links at the beginning.
		$links_array[] = '<img src="' . esc_url( MWB_WC_BK_BASEURL . 'admin/resources/images/Demo.svg' ) . '" style="width: 20px; padding-right: 5px;" ><a href="' . esc_url( 'https://demo.makewebbetter.com/mwb-bookings-for-woocommerce/?utm_source=MWB-bookings-org&utm_medium=MWB-org-backend&utm_campaign=MWB-bookings-demo' ) . '">Demo</a>';                                      // Mandatory Inline CSS.
		$links_array[] = '<img src="' . esc_url( MWB_WC_BK_BASEURL . 'admin/resources/images/Documentation.svg' ) . '" style="width: 20px; padding-right: 5px;" ><a href="' . esc_url( 'https://docs.makewebbetter.com/mwb-bookings-for-woocommerce/?utm_source=MWB-bookings-org&utm_medium=MWB-org-backend&utm_campaign=MWB-bookings-doc' ) . '">Documetation</a>';                      // Mandatory Inline CSS.
		$links_array[] = '<img src="' . esc_url( MWB_WC_BK_BASEURL . 'admin/resources/images/Support.svg' ) . '" style="width: 20px; padding-right: 5px;" ><a href="' . esc_url( 'https://makewebbetter.com/submit-query/?utm_source=MWB-bookings-org&utm_medium=MWB-org-backend&utm_campaign=MWB-bookings-support ' ) . '">Support</a>';                                                 // Mandatory Inline CSS.
	}

	return $links_array;
}

add_filter( 'plugin_row_meta', 'mwb_booking_plugin_row_links', 10, 2 );

