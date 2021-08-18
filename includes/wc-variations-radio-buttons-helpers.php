<?php
function wc_vrb_locate_template($template_name, $template_path = '', $default_path = '')
{
    // Set variable to search in woocommerce-plugin-templates folder of theme.
    if (!$template_path) :
        $template_path = 'woocommerce-plugin-templates/';
    endif;

    // Set default plugin templates path.
    if (!$default_path) :
        $default_path = WC_VARIATIONS_RADIO_BUTTONS_PATH . 'templates/'; // Path to the template folder
    endif;

    // Search template file in theme folder.
    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
    ));

    // Get plugins template file.
    if (!$template) :
        $template = $default_path . $template_name;
    endif;
    return apply_filters('wcpt_locate_template', $template, $template_name, $template_path, $default_path);
}

function wc_vrb_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '')
{
    if (is_array($args) && isset($args)) :
        extract($args);
    endif;

    $template_file = wc_vrb_locate_template($template_name, $tempate_path, $default_path);

    if (!file_exists($template_file)) :
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
        return;
    endif;

    include $template_file;
}