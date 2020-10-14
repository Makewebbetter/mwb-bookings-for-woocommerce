<?php
/**
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           Mwb_Wc_Bk
 *
 * @wordpress-plugin
 * Plugin Name:       Booking For WooCommerce
 * Plugin URI:        https://makewebbetter.com/
 * Description:       Booking For WooCommerce.
 * Version:           1.0.0
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mwb-wc-bk
 * Domain Path:       /languages
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
		<p><?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to install Plugin.', 'mwb-wc-bk' ); ?></p>
	</div>
	<style>
		#message{display:none;}
	</style>
	<?php
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
	mwb_wc_bk_constant( 'MWB_WC_BK_VERSION', '1.0.0' );
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