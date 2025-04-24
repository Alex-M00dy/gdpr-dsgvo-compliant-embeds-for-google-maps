<?php

/**
 * @package         GDPR_Google_Maps_Embed_SF
 * @license         GPLv2 or later
 * @license URI     https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

/**
 * Define Public Key for professional license validation
 */
define( 'DSGVO_GM_LICENSE_PUBKEY', trim(
    '-----BEGIN PUBLIC KEY-----' . "\n" .
    'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvROWzZeV0afupykJ11Dp' . "\n" .
    'ToVsqTwwQn93AO5xj/KaKJhmoAb7Ufn8Crj3MJkHDDQy8fZgQ5gi8B82D6igr9ss' . "\n" .
    'TmbDLcUKSlKjsM1f+/qdwYwnTIBL/0N0AfeG1gSQ0J+l6eA15aO94Aqk3L23AowK' . "\n" .
    'QdtNRzLrk8MfnCaW3f56DuWnsPfpJdsJujYtyOPLb0hjSH9FyufYbp7rk7YMlQEu' . "\n" .
    'Io6vADutAjiCmCg+K3OwkYK2Quql2hTHJIxB2V6DfKKHbGt7aaGrqgWIlQgt2MEh' . "\n" .
    'WCjJ/iYJxNFGA+kzQCD5Mrm3d1/c3SVvWK7LpIXdtHF2AU6VxZppjbPSxxIICGQF' . "\n" .
    '+QIDAQAB' . "\n" .
    '-----END PUBLIC KEY-----'
) );


/**
 * Register the license key setting.
 */
add_action('admin_init', function () {
    register_setting(
        'dsgvo_gm_license',
        'dsgvo_gm_license_key',
        ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field']
    );
});

/**
 * Add "License" submenu under the DSGVO Maps Custom Post Type menu.
 */
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=dsgvo_map',
        __('Professional License', 'gdpr-dsgvo-compliant-google-maps-embeds'),
        __('License', 'gdpr-dsgvo-compliant-google-maps-embeds'),
        'manage_options',
        'dsgvo-gm-license',
        'dsgvo_gm_license_page'
    );
});

/**
 * AJAX handler: verify license via signed response.
 */
add_action('wp_ajax_dsgvo_gm_check_license', function () {
    check_ajax_referer('dsgvo_gm_check_nonce', 'nonce');

    // Read license key and current domain
    $key    = isset($_POST['license_key']) ? sanitize_text_field(wp_unslash($_POST['license_key'])) : '';
    $domain = $_SERVER['HTTP_HOST'];

    if (empty($key)) {
        wp_send_json_error(['message' => __('No license key provided.', 'gdpr-dsgvo-compliant-google-maps-embeds')]);
    }

    // Clear any cached transient
    $transient_key = 'dsgvo_gm_license_' . md5($key);
    delete_transient($transient_key);

    // Request to license server
    $response = wp_remote_post('https://lg.m00dy.org/license/verify-license.php', [
        'body'    => [
            'license' => $key,
            'domain'  => $domain,
        ],
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Validate response
    if (
        $code !== 200
        || empty($data['status'])
        || empty($data['expires'])
        || empty($data['signature'])
        || empty($data['domain'])
    ) {
        wp_send_json_error(['message' => __('Invalid server response.', 'gdpr-dsgvo-compliant-google-maps-embeds')]);
    }

    // Verify signature
    $payload = $data['status'] . '|' . $data['expires'] . '|' . $data['domain'];
    $pubkey  = openssl_pkey_get_public(DSGVO_GM_LICENSE_PUBKEY);
    $sig     = base64_decode($data['signature']);
    $sig_ok  = openssl_verify($payload, $sig, $pubkey, OPENSSL_ALGO_SHA256) === 1;

    $valid = $sig_ok && $data['status'] === 'valid';

    // Cache result
    set_transient($transient_key, $valid, 12 * HOUR_IN_SECONDS);

    // Return status and expiry
    wp_send_json_success([
        'valid'   => $valid,
        'expires' => $data['expires'],
    ]);
});

/**
 * Render the License settings page.
 */
function dsgvo_gm_license_page()
{
    $key = get_option('dsgvo_gm_license_key', '');
?>
    <div class="wrap">
        <h1><?php esc_html_e('Professional License', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></h1>

        <div class="dsgvo-gm-license-benefits">
            <h2><?php esc_html_e('Why upgrade to the Professional License?', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></h2>
            <ul>
                <li><strong><?php esc_html_e('Unlimited Maps:', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></strong> <?php esc_html_e('Remove the 3–map limit and add as many locations as you need.', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></li>
                <li><strong><?php esc_html_e('Priority Support & Updates:', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></strong> <?php esc_html_e('Get fast, dedicated assistance and regular feature and security updates.', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></li>
                <li><strong><?php esc_html_e('Exclusive Pro Features COMING SOON:', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></strong> <?php esc_html_e('Unlock advanced templates and more.', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></li>
                <li><strong><?php esc_html_e('Ongoing GDPR Compliance:', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></strong> <?php esc_html_e('Stay up-to-date with the latest data-protection standards.', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></li>
            </ul>
            <p><?php esc_html_e('Learn more about the Professional version at', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?>
                <a href="https://lg.m00dy.org/gmaps-wordpress-plugin" target="_blank" rel="noopener"><?php echo esc_html('https://lg.m00dy.org/gmaps-wordpress-plugin'); ?></a>
            </p>
        </div>

        <h2><?php esc_html_e('License Key', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?></h2>
        <div id="dsgvo_gm_check_status"></div>

        <form method="post" action="options.php" style="margin-top:20px; display:inline-block;">
            <?php settings_fields('dsgvo_gm_license'); ?>
            <input id="dsgvo_gm_license_key" type="text" name="dsgvo_gm_license_key" value="<?php echo esc_attr($key); ?>" class="regular-text" />
            <input type="submit" class="button-primary" value="<?php esc_attr_e('Save License Key', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?>" />
        </form>

        <br><br>
        <?php wp_nonce_field('dsgvo_gm_check_nonce', 'nonce'); ?>
        <input type="button" id="dsgvo_gm_check_btn" class="button-secondary" value="<?php esc_attr_e('Verify License Key', 'gdpr-dsgvo-compliant-google-maps-embeds'); ?>" />

        <script>
            jQuery(function($) {
                $('#dsgvo_gm_check_btn').on('click', function() {
                    var status = $('#dsgvo_gm_check_status');
                    /* translators: Verifying o, e.g. Verifizieren */
                    status.html('<span>' + <?php echo json_encode(__('💪 Verifying...', 'gdpr-dsgvo-compliant-google-maps-embeds')); ?> + '</span>');
                    $.post(ajaxurl, {
                        action: 'dsgvo_gm_check_license',
                        nonce: $('#nonce').val(),
                        license_key: $('#dsgvo_gm_license_key').val()
                    }, function(resp) {
                        if (resp.success) {
                            if (resp.data.valid) {
                                /* translators: License is valid, expires o, e.g. Lizenzschlüssel gültig */
                                status.html('<span style="color:green;"><strong>' + <?php echo json_encode(__('✅ License is valid, expires on %s.', 'gdpr-dsgvo-compliant-google-maps-embeds')); ?>.replace('%s', resp.data.expires) + '</strong></span>');
                            } else {
                                /* translators: License is invalid, e.g. Lizenzschlüssel ungültig */
                                status.html('<span style="color:red;"><strong>' + <?php echo json_encode(__('❌ License is invalid.', 'gdpr-dsgvo-compliant-google-maps-embeds')); ?> + '</strong></span>');
                            }
                        } else {
                            status.html('<span style="color:red;"><strong>' +
                                resp.data.message +
                                '</strong></span>');
                        }
                    });
                });
            });
        </script>
    </div>
<?php
}

/**
 * Verify license key via transient cache and signed response.
 */
function dsgvo_gm_verify_license($key)
{
    if (empty($key)) {
        return false;
    }
    $transient_key = 'dsgvo_gm_license_' . md5($key);
    $cached = get_transient($transient_key);
    if ($cached !== false) {
        return (bool) $cached;
    }
    $response = wp_remote_post('https://lg.m00dy.org/license/verify-license.php', [
        'body'    => [
            'license' => $key,
            'domain'  => $_SERVER['HTTP_HOST'],
        ],
        'timeout' => 15,
    ]);
    if (is_wp_error($response)) {
        set_transient($transient_key, false, 12 * HOUR_IN_SECONDS);
        return false;
    }
    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    if (
        $code !== 200
        || empty($data['status'])
        || empty($data['expires'])
        || empty($data['signature'])
        || empty($data['domain'])
    ) {
        set_transient($transient_key, false, 12 * HOUR_IN_SECONDS);
        return false;
    }
    $payload = $data['status'] . '|' . $data['expires'] . '|' . $data['domain'];
    $pubkey  = openssl_pkey_get_public(DSGVO_GM_LICENSE_PUBKEY);
    $sig     = base64_decode($data['signature']);
    $sig_ok  = openssl_verify($payload, $sig, $pubkey, OPENSSL_ALGO_SHA256) === 1;
    $valid   = $sig_ok && $data['status'] === 'valid';
    set_transient($transient_key, $valid, 12 * HOUR_IN_SECONDS);
    return $valid;
}

/**
 * Returns true if license is valid.
 */
function dsgvo_gm_is_license_valid()
{
    $key = get_option('dsgvo_gm_license_key', '');
    return dsgvo_gm_verify_license($key);
}

// Remove extra maps when license changes
add_action('update_option_dsgvo_gm_license_key', 'dsgvo_gm_handle_license_change', 10, 2);
function dsgvo_gm_handle_license_change($old, $new)
{
    if (! dsgvo_gm_is_license_valid()) {
        $limit = DSGVO_GM_MAX_MAPS;
        $maps  = get_posts([
            'post_type'   => 'dsgvo_map',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby'     => 'date',
            'order'       => 'ASC',
        ]);
        if (count($maps) > $limit) {
            for ($i = $limit; $i < count($maps); $i++) {
                wp_delete_post($maps[$i]->ID, true);
            }
        }
    }
}
