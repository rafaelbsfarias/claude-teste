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
 * Processa o formulário de doação
 */
function asaas_process_donation() {
    error_log('ASAAS: Requisição AJAX recebida: ' . print_r($_POST, true));
    
    // Determinar o tipo de doação (única ou recorrente)
    $donation_type = isset($_POST['donation_type']) && $_POST['donation_type'] === 'recurring' 
        ? Asaas_Nonce_Manager::ACTION_RECURRING_DONATION 
        : Asaas_Nonce_Manager::ACTION_SINGLE_DONATION;
    
    // Verificar nonce para segurança
    if (!Asaas_Nonce_Manager::verify_nonce($_POST, $donation_type)) {
        error_log('ASAAS: Falha na verificação do nonce');
        wp_send_json_error([
            'message' => __('Security verification failed. Please refresh the page and try again.', 'asaas-easy-subscription-plugin')
        ]);
        wp_die(); // Importante: finalizar a execução aqui
    }
    
    try {
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
    } catch (Exception $e) {
        error_log('ASAAS: Exceção no processamento: ' . $e->getMessage());
        wp_send_json_error([
            'message' => $e->getMessage()
        ]);
    }
    
    wp_die(); // IMPORTANTE: Sempre finalize a execução AJAX com wp_die()
}