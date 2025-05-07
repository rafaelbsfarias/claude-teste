<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once ASAAS_PLUGIN_DIR . 'includes/class-form-processor.php';

/**
 * Registra os handlers AJAX
 */
function asaas_register_ajax_handler() {
    // Registre ambas as ações para maior compatibilidade
    add_action('wp_ajax_process_donation', 'asaas_process_donation');
    add_action('wp_ajax_nopriv_process_donation', 'asaas_process_donation');
    
    add_action('wp_ajax_process_donation_form', 'asaas_process_donation');
    add_action('wp_ajax_nopriv_process_donation_form', 'asaas_process_donation');
    
    error_log('ASAAS: Handlers AJAX registrados');
}
add_action('init', 'asaas_register_ajax_handler');

/**
 * Processa o formulário de doação
 */
function asaas_process_donation() {
    error_log('ASAAS: Requisição AJAX recebida: ' . print_r($_POST, true));
    
    process_donation_form();
}

/**
 * Processa o formulário de doação via AJAX
 */
function process_donation_form() {
    // Ativar log de erros para depuração
    $log_file = dirname(__FILE__, 2) . '/debug-ajax.log';
    file_put_contents($log_file, "==== Nova requisição " . date('Y-m-d H:i:s') . " ====\n", FILE_APPEND);
    file_put_contents($log_file, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
    
    // REMOVIDA verificação de nonce para permitir uso público irrestrito
    
    // Verificação honeypot se existir (mantenha esta proteção)
    if (isset($_POST['website']) && !empty($_POST['website'])) {
        file_put_contents($log_file, "Proteção honeypot ativada - possível spam detectado\n", FILE_APPEND);
        wp_send_json_error(['message' => 'Invalid request.']);
        return;
    }

    // Verificação básica de referer como camada mínima de proteção
    $referer = wp_get_referer();
    if (!$referer || (strpos($referer, site_url()) !== 0 && !defined('WP_DEBUG'))) {
        file_put_contents($log_file, "Referer inválido: {$referer}\n", FILE_APPEND);
        wp_send_json_error(['message' => 'Invalid request origin.']);
        return;
    }

    // Continuar com o processamento do formulário
    $processor = new Asaas_Form_Processor();
    $result = $processor->process_form($_POST);
    
    file_put_contents($log_file, "Resultado do processamento: " . print_r($result, true) . "\n", FILE_APPEND);
    
    if ($result['success']) {
        wp_send_json_success([
            'message' => __('Donation processed successfully', 'asaas-easy-subscription-plugin'),
            'data' => $result['data']
        ]);
    } else {
        wp_send_json_error([
            'message' => __('There were errors in your submission', 'asaas-easy-subscription-plugin'),
            'errors' => $result['errors']
        ]);
    }
}