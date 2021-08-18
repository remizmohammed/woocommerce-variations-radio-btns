<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wordpress.org/download/
 * @since      1.0.0
 *
 * @package    Wc_Variations_Radio_Buttons
 * @subpackage Wc_Variations_Radio_Buttons/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wc_Variations_Radio_Buttons
 * @subpackage Wc_Variations_Radio_Buttons/includes
 * @author     Amfotech <info@amfotech.com>
 */
class Wc_Variations_Radio_Buttons_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        // Require woocommerce plugin
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) and current_user_can( 'activate_plugins' ) ) {
            // Stop activation redirect and show error
            wp_die('Sorry, but this plugin requires the WooCommerce to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        }
    }
}
