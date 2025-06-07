<?php

/**
 * @package         GDPR_Google_Maps_Embed_SF
 * @license         GPLv2 or later
 * @license URI     https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly

define('DSGVO_GM_VERSION', '1.0.2');

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
        'dsgvo-gm-color-picker',
        DSGVO_GM_PLUGIN_URL . 'assets/js/dsgvo-gm-color-picker.js',
        ['wp-color-picker', 'jquery'],
        DSGVO_GM_VERSION, 
        true
    );
});

// Register Custom Post Type
add_action('init', 'dsgvo_gm_register_post_type');
function dsgvo_gm_register_post_type()
{
    register_post_type('dsgvo_map', array(
        'labels' => array(
            'name'               => __('Maps', 'gdpr-dsgvo-compliant-embeds-for-google-maps'),
            'singular_name'      => __('Map', 'gdpr-dsgvo-compliant-embeds-for-google-maps'),
            'add_new_item'       => __('Add New Map', 'gdpr-dsgvo-compliant-embeds-for-google-maps'),
            'edit_item'          => __('Edit Map', 'gdpr-dsgvo-compliant-embeds-for-google-maps'),
            'all_items'          => __('All Maps', 'gdpr-dsgvo-compliant-embeds-for-google-maps'),
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'supports'      => array('title'),
        'menu_icon'     => 'dashicons-location-alt',
    ));
}

// Add meta box for DSGVO Maps
add_action('add_meta_boxes', function () {
    add_meta_box(
        'dsgvo_gm_map_settings',
        __('Map Settings', 'gdpr-dsgvo-compliant-embeds-for-google-maps'),
        'dsgvo_gm_map_settings_callback',
        'dsgvo_map',
        'normal',
        'high'
    );
});

// Render settings fields
function dsgvo_gm_map_settings_callback($post)
{
    wp_nonce_field('dsgvo_gm_save', 'dsgvo_gm_nonce');

    // Retrieve existing values or defaults
    $iframe     = get_post_meta($post->ID, '_dsgvo_gm_iframe', true);
    $template   = get_post_meta($post->ID, '_dsgvo_gm_template',  true) ?: 'light';
    $btn_text   = get_post_meta($post->ID, '_dsgvo_gm_button_text', true) ?: __('Load Google Maps', 'gdpr-dsgvo-compliant-embeds-for-google-maps');
    $btn_shape = get_post_meta($post->ID, '_dsgvo_gm_button_shape', true);
    if (! in_array($btn_shape, ['rounded', 'square'], true)) {
        $btn_shape = 'rounded'; // Default
    }

    $overlay_bg = get_post_meta($post->ID, '_dsgvo_gm_overlay_bg',  true) ?: '#ffffff';
    $button_bg  = get_post_meta($post->ID, '_dsgvo_gm_button_bg',   true) ?: '#0073aa';
    $btn_color  = get_post_meta($post->ID, '_dsgvo_gm_button_color',   true) ?: '#ffffff';
    $privacy_color = get_post_meta($post->ID, '_dsgvo_gm_privacy_color', true) ?: '#666666';
    $privacy_enabled = get_post_meta($post->ID, '_dsgvo_gm_privacy_enabled', true) ?: 0;
    $privacy_link = get_post_meta($post->ID, '_dsgvo_gm_privacy_link', true) ?: '';

    $privacy_text = get_post_meta($post->ID, '_dsgvo_gm_privacy_text', true) ?: '';
    $privacy_link_text = get_post_meta($post->ID, '_dsgvo_gm_privacy_link_text', true) ?: '';

    $width = get_post_meta($post->ID, '_dsgvo_gm_width', true) ?: '100%';
    $height = get_post_meta($post->ID, '_dsgvo_gm_height', true) ?: '100%';


    // Set default if empty
    if ('' === $width) {
        $width = '100%';
    }
    if ('' === $height) {
        $height = '100%';
    }
    ?>
    <p>
        <strong><?php esc_html_e('Shortcode:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></strong><br>
        <input type="text" readonly style="width:100%;" value="<?php echo esc_attr("[dsgvo_map id=\"{$post->ID}\"]"); ?>" onclick="this.select();">
    </p>

    <br>
    <hr>
    <br>

    <p>
        <label for="dsgvo_gm_iframe"><?php esc_html_e('iframe Code:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
        <textarea id="dsgvo_gm_iframe" name="dsgvo_gm_iframe" style="width:100%;height:100px;"><?php printf('%s', esc_textarea($iframe)); ?></textarea>

    </p>

    <h4><?php esc_html_e('Button Settings', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></h4>

    <p>
        <label><?php esc_html_e('Button Text:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
        <input
            type="text"
            name="dsgvo_gm_button_text"
            value="<?php printf('%s', esc_attr($btn_text)); ?>"
            style="width:100%;" />
    </p>

    <p>
        <label><?php esc_html_e('Button Type:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
        <label style="margin-right:1em;">
            <input
                type="radio"
                name="dsgvo_gm_button_shape"
                value="rounded"
                <?php checked($btn_shape, 'rounded'); ?> />
            <?php esc_html_e('Rounded', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?>
        </label>
        <label>
            <input
                type="radio"
                name="dsgvo_gm_button_shape"
                value="square"
                <?php checked($btn_shape, 'square'); ?> />
            <?php esc_html_e('Squared', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?>
        </label>
    </p>

    <h4><?php esc_html_e('Style Settings', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></h4>

    <p>
        <label><?php esc_html_e('Design:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
        <select id="dsgvo_gm_template" name="dsgvo_gm_template">
            <option value="light" <?php selected($template, 'light'); ?>><?php esc_html_e('Light', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></option>
            <option value="dark" <?php selected($template, 'dark');  ?>><?php esc_html_e('Dark',  'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></option>
            <option value="custom" <?php selected($template, 'custom'); ?>><?php esc_html_e('Custom', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></option>
        </select>
    </p>

    <div id="dsgvo_gm_custom_colors" style="display:<?php printf('%s', esc_attr($template === 'custom' ? 'block' : 'none')); ?>;">
        <p>
            <label><?php esc_html_e('Overlay Background Color:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
            <input
                type="text"
                name="dsgvo_gm_overlay_bg"
                value="<?php printf('%s', esc_attr($overlay_bg)); ?>"
                class="wp-color-picker-field"
                data-default-color="#ffffff" />
        </p>

        <p>
            <label><?php esc_html_e('Button Background Color:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
            <input
                type="text"
                name="dsgvo_gm_button_bg"
                value="<?php printf('%s', esc_attr($button_bg)); ?>"
                class="wp-color-picker-field"
                data-default-color="#0073aa" />
        </p>

        <p>
            <label><?php esc_html_e('Button Text Color:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
            <input
                type="text"
                name="dsgvo_gm_button_color"
                value="<?php printf('%s', esc_attr($btn_color)); ?>"
                class="wp-color-picker-field"
                data-default-color="#ffffff" />
        </p>

        <p>
            <label><?php esc_html_e('Privacy Info Text Color:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
            <input
                type="text"
                name="dsgvo_gm_privacy_color"
                value="<?php printf('%s', esc_attr($privacy_color)); ?>"
                class="wp-color-picker-field"
                data-default-color="#666666" />
        </p>
    </div>

    <h4><?php esc_html_e('Size Settings (% or px)', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></h4>

    <p>
        <label for="dsgvo_gm_width"><?php esc_html_e('Width:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label>
        <input
            id="dsgvo_gm_width"
            name="dsgvo_gm_width"
            type="text"
            value="<?php printf('%s', esc_attr($width)); ?>"
            style="width:100px;"
            placeholder="<?php esc_attr_e('100% or 600px', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?>">
    </p>
    <p>
        <label for="dsgvo_gm_height"><?php esc_html_e('Height:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label>
        <input
            id="dsgvo_gm_height"
            name="dsgvo_gm_height"
            type="text"
            value="<?php printf('%s', esc_attr($height)); ?>"
            style="width:100px;"
            placeholder="<?php esc_attr_e('100% or 450px', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?>">
    </p>

    <h4><?php esc_html_e('Privacy Settings', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></h4>

    <p>
        <label>
            <input
                type="checkbox"
                name="dsgvo_gm_privacy_enabled"
                value="1"
                <?php checked($privacy_enabled, 1); ?>>
            <?php esc_html_e('Enable privacy notice', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?>
        </label>
    </p>

    <p>
        <label for="dsgvo_gm_privacy_text"><?php esc_html_e('Privacy Policy Text:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
        <input
            id="dsgvo_gm_privacy_text"
            name="dsgvo_gm_privacy_text"
            type="text"
            value="<?php printf('%s', esc_attr($privacy_text)); ?>"
            style="width:100%;">
    </p>

    <p>
        <label for="dsgvo_gm_privacy_link_text"><?php esc_html_e('Privacy Policy URL Text:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
        <input
            id="dsgvo_gm_privacy_link_text"
            name="dsgvo_gm_privacy_link_text"
            type="text"
            value="<?php printf('%s', esc_attr($privacy_link_text)); ?>"
            style="width:100%;">
    </p>

    <p>
        <label for="dsgvo_gm_privacy_link"><?php esc_html_e('Privacy Policy URL:', 'gdpr-dsgvo-compliant-embeds-for-google-maps'); ?></label><br>
        <input
            id="dsgvo_gm_privacy_link"
            name="dsgvo_gm_privacy_link"
            type="url"
            value="<?php printf('%s', esc_attr($privacy_link)); ?>"
            style="width:100%;">
    </p>

    <?php
}

// Save meta box data
add_action('save_post', 'dsgvo_gm_save_meta');
function dsgvo_gm_save_meta($post_id)
{
    if (! isset($_POST['dsgvo_gm_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dsgvo_gm_nonce'])), 'dsgvo_gm_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (get_post_type($post_id) !== 'dsgvo_map') {
        return;
    }

    // Iframe
    if (isset($_POST['dsgvo_gm_iframe'])) {
        $iframe = wp_kses(wp_unslash($_POST['dsgvo_gm_iframe']), [
            'iframe' => [
                'src'            => [],
                'width'          => [],
                'height'         => [],
                'style'          => [],
                'allowfullscreen' => [],
                'loading'        => [],
                'referrerpolicy' => [],
            ]
        ]);
        update_post_meta($post_id, '_dsgvo_gm_iframe', $iframe);
    }

    // Button Text
    $btn_text = isset($_POST['dsgvo_gm_button_text'])
        ? sanitize_text_field(wp_unslash($_POST['dsgvo_gm_button_text']))
        : '';
    update_post_meta($post_id, '_dsgvo_gm_button_text', $btn_text);

    // Button Shape
    if (isset($_POST['dsgvo_gm_button_shape'])) {
        $btn_shape = sanitize_text_field(wp_unslash($_POST['dsgvo_gm_button_shape']));
        // only permitted values (rounded & square)
        if (in_array($btn_shape, ['rounded', 'square'], true)) {
            update_post_meta($post_id, '_dsgvo_gm_button_shape', $btn_shape);
        }
    }

    // Template (light|dark|custom)
    $tmpl = isset($_POST['dsgvo_gm_template']) && in_array($_POST['dsgvo_gm_template'], ['light', 'dark', 'custom'], true)
        ? sanitize_text_field(wp_unslash($_POST['dsgvo_gm_template']))
        : 'light';
    update_post_meta($post_id, '_dsgvo_gm_template', $tmpl);


    // Custom‑Colors if template custom
    $custom_fields = [
        'dsgvo_gm_overlay_bg'  => '_dsgvo_gm_overlay_bg',
        'dsgvo_gm_button_bg'   => '_dsgvo_gm_button_bg',
        'dsgvo_gm_button_color' => '_dsgvo_gm_button_color',
        'dsgvo_gm_privacy_color' => '_dsgvo_gm_privacy_color',
    ];

    if ($tmpl === 'custom') {
        foreach ($custom_fields as $field_name => $meta_key) {
            if (isset($_POST[$field_name])) {
                $color = sanitize_hex_color(wp_unslash($_POST[$field_name]));
                update_post_meta($post_id, $meta_key, $color);
            }
        }
    } else {
        // if light/dark remove custom‑Metas
        foreach ($custom_fields as $meta_key) {
            delete_post_meta($post_id, $meta_key);
        }
    }

    // Size: width & height
    if (isset($_POST['dsgvo_gm_width'])) {
        update_post_meta($post_id, '_dsgvo_gm_width', sanitize_text_field(wp_unslash($_POST['dsgvo_gm_width'])));
    }
    if (isset($_POST['dsgvo_gm_height'])) {
        update_post_meta($post_id, '_dsgvo_gm_height', sanitize_text_field(wp_unslash($_POST['dsgvo_gm_height'])));
    }

    // Privacy-Fields
    $enabled = isset($_POST['dsgvo_gm_privacy_enabled']) ? 1 : 0;
    update_post_meta($post_id, '_dsgvo_gm_privacy_enabled', $enabled);
    if (isset($_POST['dsgvo_gm_privacy_link'])) {
        update_post_meta(
            $post_id,
            '_dsgvo_gm_privacy_link',
            esc_url_raw(wp_unslash($_POST['dsgvo_gm_privacy_link']))
        );
    }

    if (isset($_POST['dsgvo_gm_privacy_text'])) {
        update_post_meta($post_id, '_dsgvo_gm_privacy_text', sanitize_text_field(wp_unslash($_POST['dsgvo_gm_privacy_text'])));
    }

    if (isset($_POST['dsgvo_gm_privacy_link_text'])) {
        update_post_meta($post_id, '_dsgvo_gm_privacy_link_text', sanitize_text_field(wp_unslash($_POST['dsgvo_gm_privacy_link_text'])));
    }
}
