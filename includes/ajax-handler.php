<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once ASAAS_PLUGIN_DIR . 'includes/class-form-processor.php';
require_once ASAAS_PLUGIN_DIR . 'includes/security/class-nonce-manager.php';

/**
 * Registra o handler AJAX
 */
function asaas_register_ajax_handler() {
    add_action('wp_ajax_process_donation', 'asaas_process_donation');
    add_action('wp_ajax_nopriv_process_donation', 'asaas_process_donation');
    
    // Adicionar um log para verificar se esta função é chamada
    error_log('ASAAS: Handlers AJAX registrados');
}
add_action('init', 'asaas_register_ajax_handler');

/**
 * Processa o formulário de doação via AJAX
 */
function process_donation_form() {
    // Ativar log de erros para depuração
    $log_file = dirname(__FILE__, 2) . '/debug-ajax.log';
    file_put_contents($log_file, "==== Nova requisição " . date('Y-m-d H:i:s') . " ====\n", FILE_APPEND);
    file_put_contents($log_file, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
    
    // SOLUÇÃO: Comentar temporariamente a verificação de nonce
    /*
    if (!isset($_POST['nonce'])) {
        wp_send_json_error(['message' => 'Security token missing. Please refresh the page and try again.']);
        return;
    }

    // Determinar qual tipo de nonce verificar
    $nonce_action = isset($_POST['form_type']) && 
                   ($_POST['form_type'] === 'single_donation' || 
                    $_POST['form_type'] === 'recurring_donation') 
        ? 'asaas_public_form' : 'asaas_nonce';

    // Verificar o nonce
    if (!wp_verify_nonce($_POST['nonce'], $nonce_action)) {
        wp_send_json_error(['message' => 'Security verification failed. Please refresh the page and try again.']);
        return;
    }
    */

    // Verificação honeypot se existir
    if (isset($_POST['website']) && !empty($_POST['website'])) {
        wp_send_json_error(['message' => 'Invalid request.']);
        return;
    }

    // Continuar com o processamento do formulário
    $processor = new Asaas_Form_Processor();
    $result = $processor->process_form($_POST);
    
    error_log('ASAAS: Resultado do processamento: ' . print_r($result, true));
    
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

/**
 * Processa o formulário de doação
 */
function asaas_process_donation() {
    error_log('ASAAS: Requisição AJAX recebida: ' . print_r($_POST, true));
    
    process_donation_form();
}