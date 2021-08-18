<?php

/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.5
 */

defined('ABSPATH') || exit;

global $product;

$attribute_keys  = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars($variations_json, ENT_QUOTES, 'UTF-8', true);
$availableVariations = $available_variations;
do_action('woocommerce_before_add_to_cart_form'); ?>

<form class="variations_form cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. 
                                                                                                                                                                                                                                                                                        ?>">
    <?php do_action('woocommerce_before_variations_form'); ?>

    <?php if (empty($available_variations) && false !== $available_variations) : ?>
        <p class="stock out-of-stock"><?php echo esc_html(apply_filters('woocommerce_out_of_stock_message', __('This product is currently out of stock and unavailable.', 'woocommerce'))); ?></p>
    <?php else : ?>
        <?php
            $availableVariationsTrimmed = array();
            foreach ($available_variations as $availableVariation) {
                array_push($availableVariationsTrimmed, $availableVariation['attributes']);
            }
        ?>
        <table class="variations" cellspacing="0">
            <tbody>
                <?php foreach ($attributes as $attribute_name => $options) : ?>
                    <tr>
                        <td class="value">
                            <?php
                            if (!empty($options)) {
                                $selectedDefaultValue = isset($selected_attributes[sanitize_title($attribute_name)]) ? $selected_attributes[sanitize_title($attribute_name)] : '';
                                //echo $selectedDefaultValue;
                                if ($product && taxonomy_exists($attribute_name)) {
                                    // Get terms if this is a taxonomy - ordered. We need the names too.
                                    $terms = wc_get_product_terms(
                                        $product->get_id(),
                                        $attribute_name,
                                        array(
                                            'fields' => 'all',
                                        )
                                    );
                                    foreach ($terms as $term) {
                                        if (in_array($term->slug, $options, true)) {
                                            foreach ($availableVariationsTrimmed as $key => $availableVariation) {
                                                if (in_array($term->slug, $availableVariation)) {
                                                    echo $availableVariations[$key]["variation_description"];
                                                }
                                            }
                                            ?>
                                            <label class="custom-radio">
                                                <?php
                                                echo esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute_name, $product));
                                                ?>
                                                <input type="radio" name="<?php echo sanitize_title($attribute_name) ?>-cloned" data-attribute_name="<?php echo sanitize_title($attribute_name) ?>" value="<?php echo esc_attr($term->slug) ?>" <?php echo checked(sanitize_title($selectedDefaultValue), $term->slug, false) ?>" onchange="cloneVariableAttr(jQuery(this))">
                                                <span class="checkmark"></span>
                                            </label>
                                        <?php
                                        }
                                    }
                                } else {
                                    ?>
                                    <div class="wc-vrb-radio-container">
                                    <?php
                                        foreach ($options as $option) {
                                            // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                                            $selected = sanitize_title($selectedDefaultValue) === $selectedDefaultValue ? checked($selectedDefaultValue, sanitize_title($option), false) : checked($selectedDefaultValue, $option, false);
                                            foreach ($availableVariationsTrimmed as $key => $availableVariation) {
                                                if (in_array($option, $availableVariation)) {
                                                    $variationPriceHtml =  $availableVariations[$key]["price_html"];
                                                    $variationDescriptionHtml =  $availableVariations[$key]["variation_description"];
                                                    $variationLabelHtml =  $availableVariations[$key]["wc_vrb_variation_label"];
                                                }
                                            }
                                            ?>
                                            <div class="wc-vrb-custom-radio">
                                                    <input type="radio" class="wc-vrb-option-input" id="option-<?php echo $option?>" name="<?php echo sanitize_title($attribute_name) ?>-cloned" data-attribute_name="<?php echo sanitize_title($attribute_name) ?>" value="<?php echo esc_attr($option) ?>" <?php echo $selected; ?> onchange="cloneVariableAttr(jQuery(this))">
                                                    <label for="option-<?php echo $option?>" class="wc-vrb-option-label">
                                                        <div class="details">
                                                            <h4><?php echo esc_html(apply_filters('woocommerce_variation_option_name', $option, null, $attribute_name, $product));?></h4>
                                                            <?php echo $variationDescriptionHtml;?>
                                                            <?php if($variationLabelHtml):?>
                                                                <span class="recommended"><?php echo $variationLabelHtml?></span>
                                                            <?php endif;?>
                                                            
                                                        </div>
                                                            <?php echo $variationPriceHtml?>
                                                    </label>
                                            </div>
                                            <?php
                                        }
                                    ?>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <div style="display: none;">
                                <?php
                                wc_dropdown_variation_attribute_options(
                                    array(
                                        'options'   => $options,
                                        'attribute' => $attribute_name,
                                        'product'   => $product,
                                    )
                                );
                                //echo end($attribute_keys) === $attribute_name ? wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woocommerce') . '</a>')) : '';
                                ?>
                            </div>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="single_variation_wrap">
            <?php
            /**
             * Hook: woocommerce_before_single_variation.
             */
            do_action('woocommerce_before_single_variation');

            /**
             * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
             *
             * @since 2.4.0
             * @hooked woocommerce_single_variation - 10 Empty div for variation data.
             * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
             */
            do_action('woocommerce_single_variation');

            /**
             * Hook: woocommerce_after_single_variation.
             */
            do_action('woocommerce_after_single_variation');
            ?>
        </div>
    <?php endif; ?>

    <?php do_action('woocommerce_after_variations_form'); ?>
</form>

<?php
do_action('woocommerce_after_add_to_cart_form');
?>
<script type="text/javascript">
    function cloneVariableAttr(obj) {
        jQuery('#' + obj.data('attribute_name')).val(obj.val()).change();

        var addToCartHtml = jQuery('.single_add_to_cart_button').text();
        var variablePriceHtml = jQuery('.woocommerce-variation-price').find('.woocommerce-Price-amount').html();
        if (variablePriceHtml) {
            var priceHtml = variablePriceHtml + ' - ' + 'Add to cart';
            //jQuery('.single_add_to_cart_button').html(priceHtml);
        }
    }
</script>