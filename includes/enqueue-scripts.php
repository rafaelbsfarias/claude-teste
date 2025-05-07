<?php
if (!defined('ABSPATH')) {
    exit;
}

function asaas_enqueue_scripts() {
    // Registrar e enfileirar estilos
    wp_register_style('asaas-form-style', ASAAS_PLUGIN_URL . 'assets/frontend/css/form-style.css');
    wp_enqueue_style('asaas-form-style');
    
    // Registrar scripts - na ordem correta de dependência
    wp_register_script('asaas-form-utils', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-utils.js', [], false, true);
    wp_register_script('asaas-form-masks', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-masks.js', ['asaas-form-utils'], false, true);
    wp_register_script('asaas-form-ui', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-ui.js', ['asaas-form-utils'], false, true);
    wp_register_script('asaas-form-ajax', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-ajax.js', ['asaas-form-utils'], false, true);
    wp_register_script('asaas-form-script', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-script.js', ['asaas-form-utils', 'asaas-form-masks', 'asaas-form-ui', 'asaas-form-ajax'], false, true);
    
    // Enfileirar scripts
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-masks');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-ajax');
    wp_enqueue_script('asaas-form-script');
    
    // Localizar o script com as variáveis do WordPress
    wp_localize_script('asaas-form-ajax', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'site_url' => site_url(),
        'is_debug' => defined('WP_DEBUG') && WP_DEBUG
    ]);
}
add_action('wp_enqueue_scripts', 'asaas_enqueue_scripts');