<?php
if (!defined('ABSPATH')) {
    exit;
}

function asaas_enqueue_scripts() {
    // Use um caminho base consistente
    $plugin_url = plugin_dir_url(dirname(__FILE__)); // Vai para o diretório pai de includes
    
    // Registrar estilos
    wp_register_style('asaas-form-style', $plugin_url . 'assets/frontend/css/form-style.css', [], '1.0.0');
    
    // Registrar scripts na ordem correta
    wp_register_script('asaas-form-utils', $plugin_url . 'assets/frontend/js/form-utils.js', ['jquery'], '1.0.0', true);
    wp_register_script('asaas-form-masks', $plugin_url . 'assets/frontend/js/form-masks.js', ['jquery', 'asaas-form-utils'], '1.0.0', true);
    wp_register_script('asaas-form-ui', $plugin_url . 'assets/frontend/js/form-ui.js', ['jquery', 'asaas-form-utils'], '1.0.0', true);
    wp_register_script('asaas-form-ajax', $plugin_url . 'assets/frontend/js/form-ajax.js', ['jquery', 'asaas-form-utils', 'asaas-form-ui'], '1.0.0', true);
    wp_register_script('asaas-form-script', $plugin_url . 'assets/frontend/js/form-script.js', ['jquery', 'asaas-form-utils', 'asaas-form-masks', 'asaas-form-ui', 'asaas-form-ajax'], '1.0.0', true);
    
    // Enfileirar tudo
    wp_enqueue_style('asaas-form-style');
    wp_enqueue_script('jquery');
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-masks');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-ajax');
    wp_enqueue_script('asaas-form-script');
    
    // Localizar o script com as variáveis do WordPress
    wp_localize_script('asaas-form-ajax', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asaas_nonce')
    ]);
    
    // Verificar caminhos para debug
    if (WP_DEBUG) {
        error_log('Plugin URL base: ' . $plugin_url);
        error_log('Form Utils Path: ' . $plugin_url . 'assets/frontend/js/form-utils.js');
    }
}
add_action('wp_enqueue_scripts', 'asaas_enqueue_scripts');