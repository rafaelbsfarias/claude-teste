<?php
if (!defined('ABSPATH')) {
    exit;
}

function asaas_enqueue_scripts() {
    // Registrar e enfileirar estilos
    wp_register_style('asaas-form-style', ASAAS_PLUGIN_URL . 'assets/frontend/css/form-style.css', [], ASAAS_PLUGIN_VERSION);
    wp_enqueue_style('asaas-form-style');
    
    // Registrar scripts
    wp_register_script('asaas-form-utils', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-utils.js', ['jquery'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-masks', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-masks.js', ['asaas-form-utils'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-ui', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-ui.js', ['asaas-form-utils'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-payment-method', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-payment-method.js', ['jquery'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-payment-response', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-payment-response.js', ['jquery'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-ajax', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-ajax.js', ['jquery', 'asaas-form-payment-response'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-script', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-script.js', ['asaas-form-utils', 'asaas-form-masks', 'asaas-form-ui', 'asaas-form-payment-method', 'asaas-form-ajax'], ASAAS_PLUGIN_VERSION, true);
    
    // Enfileirar scripts
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-masks');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-payment-method');
    wp_enqueue_script('asaas-form-payment-response');
    wp_enqueue_script('asaas-form-ajax');
    wp_enqueue_script('asaas-form-script');
    
    // Localizar script com dados da API
    wp_localize_script('asaas-form-ajax', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asaas_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'asaas_enqueue_scripts');