<?php
/**
 * Plugin Name:       		Customer Group based taxes for WooCommerce
 * Description:       		Apply tax based on Customer Group.
 * Version:           		1.0.0
 * Author:            		Monster Infotech
 * Author URI:        		https://monsterinfotech.com/
 * License:           		GNU General Public License v3.0
 * License URI:       		http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       		customer-group-based-taxes-for-woocommerce
 * Domain Path:       		/languages
 * WC requires at least: 	0.4.9
 * WC tested up to: 			4.1.1
 *
 * @since             1.0.0
 * @author      			Parth Solanki
 * @package           Customer_Group_Based_Taxes_For_Woocommerce
 */

// Prevent direct file access
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
	* @since 1.0.0
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
		* @since 1.0.0
		* Define plugin version
		*/
	define( 'MICGBTFW_VERSION', '1.0.0' );

	/**
		* @since 1.0.0
		* Define plugin directory file URL
		*/ 
	define( 'MICGBTFW_PLUGIN_FILE_URL', __FILE__ );

	/**
		* @since 1.0.0
		* Define plugin directory path
		*/ 
	define( 'MICGBTFW_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );

	/**
		* @since 1.0.0
		* Include admin file
		*/ 
	require_once plugin_dir_path( MICGBTFW_PLUGIN_FILE_URL ) . 'includes/admin.php';

} else {
	// Show error message if WooCommerce plugin is not active
	function micgbtfw_error_notice() {
    ?>
    <div class="error notice">
      <p><?php _e( 'Please install and activate <strong>WooCommerce</strong> plugin for <strong>Customer Group based taxes for WooCommerce</strong> plugin.', 'customer-group-based-taxes-for-woocommerce' ); ?></p>
    </div>
    <?php
	}
	add_action( 'admin_notices', 'micgbtfw_error_notice' );
}
