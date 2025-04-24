<?php

/**
 * @package         GDPR_Google_Maps_Embed_SF
 * @license         GPLv2 or later
 * @license URI     https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined('ABSPATH')) exit;

add_shortcode('dsgvo_map', function ($atts) {
    $atts = shortcode_atts(
        array('id' => '', 'class' => ''),
        $atts,
        'dsgvo_map'
    );
    $id = intval($atts['id']);


    $btn_text      = get_post_meta($id, '_dsgvo_gm_button_text', true) ?: __('Load Map', 'gdpr-dsgvo-compliant-google-maps-embeds');
    $btn_shape      = get_post_meta($id, '_dsgvo_gm_button_shape', true);
    $overlay_bg    = get_post_meta($id, '_dsgvo_gm_overlay_bg',  true);
    $button_bg     = get_post_meta($id, '_dsgvo_gm_button_bg',   true);
    $btn_color     = get_post_meta($id, '_dsgvo_gm_button_color', true);
    $privacy_color = get_post_meta($id, '_dsgvo_gm_privacy_color', true);

    $iframe          = get_post_meta($id, '_dsgvo_gm_iframe', true);
    $template        = get_post_meta($id, '_dsgvo_gm_template', true);
    $privacy_enabled = get_post_meta($id, '_dsgvo_gm_privacy_enabled', true);
    $privacy_link    = get_post_meta($id, '_dsgvo_gm_privacy_link', true);

    
    $width_input  = get_post_meta($id, '_dsgvo_gm_width', true);
    $height_input = get_post_meta($id, '_dsgvo_gm_height', true);

    
    $width_val  = $width_input  ? trim($width_input)  : '100%';
    $height_val = $height_input ? trim($height_input) : '100%';

    $privacy_text = get_post_meta($id, '_dsgvo_gm_privacy_text', true) ?: __('Please see our', 'gdpr-dsgvo-compliant-google-maps-embeds');
    $privacy_link_text = get_post_meta($id, '_dsgvo_gm_privacy_link_text', true) ?: __('Privacy Policy', 'gdpr-dsgvo-compliant-google-maps-embeds');

    // Append 'px' if numeric
    if (preg_match('/^\d+$/', $width_val)) {
        $width_val .= 'px';
    }
    if (preg_match('/^\d+$/', $height_val)) {
        $height_val .= 'px';
    }

    if (! $iframe) {
        return '';
    }

    // Build inline style
    if (substr($height_val, -1) === '%') {
        // Height is percentage - use padding-bottom for aspect ratio
        $style_attr = sprintf(
            'width:%s;position:relative;height:0;padding-bottom:%s;overflow:hidden;',
            esc_attr($width_val),
            esc_attr($height_val)
        );
    } else {
        // Fixed height in px or other unit
        $style_attr = sprintf(
            'width:%s;height:%s;position:relative;overflow:hidden;',
            esc_attr($width_val),
            esc_attr($height_val)
        );
    }

    // Classes & inline styles
    $class = 'dsgvo-gm-' . (in_array($template, ['light', 'dark']) ? $template : 'custom');
    $overlay_style = $template === 'custom'
        ? 'background-color:' . esc_attr($overlay_bg) . ';'
        : '';
    $btn_style = $template === 'custom'
        ? 'background-color:' . esc_attr($button_bg) . ';color:' . esc_attr($btn_color) . ';'
        : '';
    $privacy_style = $template === 'custom'
        ? 'color:' . esc_attr($privacy_color) . ';'
        : '';


    $btn_shape_style = $btn_shape === 'rounded'
        ? 'border-radius:15px;'
        : 'border-radius:0;';

    $b64 = base64_encode($iframe);

    // Build output
    $html  = '<div class="dsgvo-gm-container ' . esc_attr($class) . '" style="' . $style_attr . '">';
    $html .= '<div class="dsgvo-gm-overlay ' . esc_attr($class) . '" style="' . $overlay_style . '" data-iframe="' . esc_attr($b64) . '">';
    $html .= '<button class="dsgvo-gm-load-btn ' . esc_attr($class) . '" style="' . $btn_shape_style . $btn_style . '">'
        . esc_html($btn_text) .
        '</button>';
    if ($privacy_enabled && $privacy_link) {
        $html .= '<div class="dsgvo-gm-privacy-info ' . esc_attr($class) . '" style="' . $privacy_style . '">'
             /* translators: Privacy text, e.g. “Bitte schauen Sie in unsere...” */
            . esc_html( $privacy_text )
            . ' <a href="' . esc_url($privacy_link) . '" target="_blank" style="' . $privacy_style . '">'
            /* translators: Privacy link text, e.g. “Datenschutz” */
            . esc_html( $privacy_link_text )
            . '</a></div>';
    }
    $html .= '</div></div>';

    return $html;
});
