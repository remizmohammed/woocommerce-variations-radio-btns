<?php
/**
 * WC_VRB Integrations Class
 *
 * @author   WooThemes
 * @since    2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Wc_Variations_Radio_Buttons')) :

    class Wc_Variations_Radio_Buttons
    {

        /**
         * Setup class.
         *
         * @since 1.0
         */
        public function __construct()
        {
            add_action( 'admin_init', array($this, 'wc_vrb_has_woocommerce_plugin'));
            add_action('plugins_loaded', array($this, 'init'));
        }

        public function wc_vrb_has_woocommerce_plugin()
        {
            if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                add_action( 'admin_notices', array($this, 'wc_vrb_notice' ));
        
                deactivate_plugins( WC_VARIATIONS_RADIO_BUTTONS_BASENAME ); 
        
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        }

        public function wc_vrb_notice()
        {
            ?>
                <div class="error"><p>Sorry, WooCommerce Variation Radio Buttons plugin requires the Parent plugin to be installed and active.</p></div>
            <?php
        }

        public function init()
        {
            wp_register_style('wc-vrb-variation-radio-btns', WC_VARIATIONS_RADIO_BUTTONS_URL.'assets/css/wc-vrb-styles.css');
            remove_action('woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30);
            add_action('woocommerce_variable_add_to_cart', array($this, 'woocommerce_variable_add_to_cart_wc_vrb'), 30);
            
            /* Modify Price html */
            add_filter('woocommerce_variable_price_html', array($this, 'wc_vrb_variation_price'), 10, 2);
            add_action( 'woocommerce_variation_options_pricing', array($this, 'wc_vrb_add_custom_field_to_variations'), 10, 3 );
            add_action( 'woocommerce_save_product_variation', array($this, 'wc_vrb_save_custom_field_variations'), 10, 2 );
            add_filter( 'woocommerce_available_variation', array($this, 'wc_vrb_add_custom_field_variation_data') );
        }

        public function woocommerce_variable_add_to_cart_wc_vrb()
        {
            global $product;
            // Enqueue variation scripts.
            wp_enqueue_style('wc-vrb-variation-radio-btns');
            wp_enqueue_script('wc-add-to-cart-variation');

            // Get Available variations?
            $get_variations = count($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);

            // Load the template.
            wc_vrb_get_template(
                'content-product-variations.php',
                array(
                    'available_variations' => $get_variations ? $product->get_available_variations() : false,
                    'attributes'           => $product->get_variation_attributes(),
                    'selected_attributes'  => $product->get_default_attributes(),
                )
            );
        }

        public function wc_vrb_variation_price($price, $product)
        {
            if( !is_product() ) {
                return $price;
            }
            foreach ($product->get_available_variations() as $pav) {
                $def = true;
                foreach ($product->get_variation_default_attributes() as $defkey => $defval) {
                    if ($pav['attributes']['attribute_' . $defkey] != $defval) {
                        $def = false;
                    }
                }
                if ($def) {
                    $price = $pav['display_price'];
                }
            }

            return wc_price($price);
        }

        public function wc_vrb_add_custom_field_to_variations($loop, $variation_data, $variation)
        {
            woocommerce_wp_text_input( array(
                'id' => 'wc_vrb_variation_label[' . $loop . ']',
                'class' => 'short',
                'label' => __( 'Label ', 'woocommerce' ),
                'value' => get_post_meta( $variation->ID, 'wc_vrb_variation_label', true ),
                'desc_tip' => 'true',
                'description' => __('Add Label as highlighted.', 'woocommerce')
            ) );
        }

        public function wc_vrb_save_custom_field_variations( $variation_id, $i ) {
            $wc_vrb_variation_label = $_POST['wc_vrb_variation_label'][$i];
            if ( isset( $wc_vrb_variation_label ) ) update_post_meta( $variation_id, 'wc_vrb_variation_label', esc_attr( $wc_vrb_variation_label ) );
        }

        public function wc_vrb_add_custom_field_variation_data( $variations ) {
            $variations['wc_vrb_variation_label'] = get_post_meta( $variations[ 'variation_id' ], 'wc_vrb_variation_label', true );
            return $variations;
        }
    }

endif;
