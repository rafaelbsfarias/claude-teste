<?php
/**
 * Plugin Name: Asaas Payment Integration
 * Description: Simple WordPress plugin for Asaas payment integration
 * Version: 1.0.0
 * Author: WordPress Developer
 * Text Domain: asaas-payment-integration
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ASAAS_API_URL', 'https://api-sandbox.asaas.com/v3');
define('ASAAS_API_TOKEN', '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OjM1MDQyZWVhLTUxNGMtNDVmYS04ZTNlLTI2NTQ3ZjMxZjQ4Mzo6JGFhY2hfZjc4OTYzNzktZjIzNi00ZGUzLWI1ZWEtYjY1YjQ4NjI1YzFj');

// Include files
require_once plugin_dir_path(__FILE__) . 'asaas-api.php';
require_once plugin_dir_path(__FILE__) . 'asaas-shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'tests/asaas-tests.php';

// Register activation hook
register_activation_hook(__FILE__, 'asaas_plugin_activate');

function asaas_plugin_activate() {
    // Activation logic here
    flush_rewrite_rules();
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'asaas_plugin_deactivate');

function asaas_plugin_deactivate() {
    // Deactivation logic here
    flush_rewrite_rules();
}

// Admin menu
add_action('admin_menu', 'asaas_add_admin_menu');

function asaas_add_admin_menu() {
    add_menu_page(
        'Asaas Settings',
        'Asaas Payments',
        'manage_options',
        'asaas-settings',
        'asaas_settings_page',
        'dashicons-money',
        30
    );
}

function asaas_settings_page() {
    ?>
    <div class="wrap">
        <h1>Asaas Payment Settings</h1>
        <p>Use these shortcodes on your pages:</p>
        <ul>
            <li><code>[asaas_payment]</code> - For one-time payments (PIX, Boleto, Credit Card)</li>
            <li><code>[asaas_subscription]</code> - For recurring payments (Credit Card only)</li>
        </ul>
    </div>
    <?php
}