<?php
/*
Plugin Name: WooCommerce Variations Radio Btns
Plugin URI: https://github.com/remizmohammed/woocommerce-variations-radio-btns
Description: WooCommerce Add-on to change the product variations dropdown to radio buttons.
Version: 1.0
Author: Remiz mohammed
Author URI: https://github.com/remizmohammed
License: GPLv2 or later
Text Domain: wc_vrb
*/

define('WC_VARIATIONS_RADIO_BUTTONS_PATH', plugin_dir_path(__FILE__));
define('WC_VARIATIONS_RADIO_BUTTONS_URL', plugin_dir_url(__FILE__));
define('WC_VARIATIONS_RADIO_BUTTONS_BASENAME', plugin_basename( __FILE__ ));

function activate_woocommerce_Variations_radio_btns()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-variations-radio-buttons-activator.php';
    Wc_Variations_Radio_Buttons_Activator::activate();
}

function deactivate_woocommerce_Variations_radio_btns()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-variations-radio-buttons-deactivator.php';
    Wc_Variations_Radio_Buttons_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_woocommerce_Variations_radio_btns');
register_deactivation_hook(__FILE__, 'deactivate_woocommerce_Variations_radio_btns');

require plugin_dir_path(__FILE__) . 'includes/class-wc-variations-radio-buttons.php';
require plugin_dir_path(__FILE__) . 'includes/wc-variations-radio-buttons-helpers.php';

function run_wc_variations_radio_buttons()
{
    new Wc_Variations_Radio_Buttons();
}
run_wc_variations_radio_buttons();
