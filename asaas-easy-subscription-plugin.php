<?php

/**
 * Plugin Name: Asaas Easy Subscription Plugin
 * Description: Integração com o Asaas para pagamentos únicos e recorrentes.
 * Version: 1.0.0
 * Author: Rafael
 * Text Domain: asaas-easy-subscription-plugin
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants.
define('ASAAS_PLUGIN_VERSION', '1.0.0');
define('ASAAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASAAS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once ASAAS_PLUGIN_DIR . 'includes/class-plugin-loader.php';

// Initialize the plugin.
function asaas_easy_subscription_plugin_init() {
    $plugin_loader = new Asaas_Plugin_Loader();
    $plugin_loader->init();
    
    // Load Elementor integration if Elementor is active
    if (class_exists('\Elementor\Plugin')) {
        require_once ASAAS_PLUGIN_DIR . 'includes/elementor-integration.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/elementor-widget.php';
    }
}
add_action('plugins_loaded', 'asaas_easy_subscription_plugin_init');

// Função de teste AJAX
function asaas_test_ajax() {
    wp_send_json_success(['message' => 'AJAX working for public users']);
}
add_action('wp_ajax_asaas_test_ajax', 'asaas_test_ajax');
add_action('wp_ajax_nopriv_asaas_test_ajax', 'asaas_test_ajax');