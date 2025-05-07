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
}
add_action('plugins_loaded', 'asaas_easy_subscription_plugin_init');

// Verifique se os hooks de AJAX estão registrados corretamente:
add_action('wp_ajax_process_donation_form', 'process_donation_form');
add_action('wp_ajax_nopriv_process_donation_form', 'process_donation_form'); // Importante para usuários deslogados

// Função de teste AJAX
function asaas_test_ajax() {
    wp_send_json_success(['message' => 'AJAX working for public users']);
}
add_action('wp_ajax_asaas_test_ajax', 'asaas_test_ajax');
add_action('wp_ajax_nopriv_asaas_test_ajax', 'asaas_test_ajax');

function ensure_ajax_hooks() {
    add_action('wp_ajax_process_donation_form', 'process_donation_form');
    add_action('wp_ajax_nopriv_process_donation_form', 'process_donation_form');
}
add_action('init', 'ensure_ajax_hooks');

/**
 * Registra os hooks AJAX diretamente no arquivo principal para garantir disponibilidade
 */
function asaas_register_direct_ajax_handlers() {
    add_action('wp_ajax_process_donation_form', 'process_donation_form');
    add_action('wp_ajax_nopriv_process_donation_form', 'process_donation_form');
    
    add_action('wp_ajax_process_donation', 'asaas_process_donation');
    add_action('wp_ajax_nopriv_process_donation', 'asaas_process_donation');
}
add_action('init', 'asaas_register_direct_ajax_handlers', 20);