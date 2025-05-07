<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gerenciador de nonce para operações de segurança
 */
class Nonce_Manager {

    /**
     * Prefixo para todas as ações de nonce do plugin
     */
    const NONCE_PREFIX = 'asaas_easy_subscription_';
    
    /**
     * Ação para doação única
     */
    const ACTION_SINGLE_DONATION = 'single_donation';
    
    /**
     * Ação para doação recorrente
     */
    const ACTION_RECURRING_DONATION = 'recurring_donation';
    
    /**
     * Cria um nonce de doação pública que pode ser usado sem autenticação
     * 
     * @return string O nonce gerado
     */
    public static function create_public_nonce() {
        $token = wp_create_nonce('asaas_public_donation');
        
        // Armazenar em opção temporária para validação
        $stored_tokens = get_option('asaas_public_tokens', []);
        $stored_tokens[$token] = time() + 24 * HOUR_IN_SECONDS; // 24 horas de validade
        update_option('asaas_public_tokens', $stored_tokens);
        
        return $token;
    }
    
    /**
     * Verifica um nonce público de doação
     * 
     * @param string $nonce O nonce a ser verificado
     * @return bool True se o nonce for válido, False caso contrário
     */
    public static function verify_public_nonce($nonce) {
        $stored_tokens = get_option('asaas_public_tokens', []);
        
        // Se o token existe e não expirou
        if (isset($stored_tokens[$nonce]) && $stored_tokens[$nonce] > time()) {
            return true;
        }
        
        // Verificação padrão do WP como fallback
        return wp_verify_nonce($nonce, 'asaas_public_donation') !== false;
    }

    /**
     * Gera um campo de nonce para um formulário
     *
     * @param string $action Ação do formulário
     * @param bool $echo Se deve imprimir ou retornar
     * @return string|void HTML do campo nonce
     */
    public static function generate_nonce_field($action, $echo = true) {
        return wp_nonce_field(self::NONCE_PREFIX . $action, 'asaas_nonce', false, $echo);
    }
    
    /**
     * Verifica se um nonce é válido
     * 
     * @param string $nonce O nonce a ser verificado
     * @param string $action A ação associada ao nonce
     * @return bool True se o nonce for válido, False caso contrário
     */
    public static function verify_nonce($nonce, $action) {
        // Para doações públicas, usar nossa verificação personalizada
        if ($action === 'asaas_public_form') {
            return self::verify_public_nonce($nonce);
        }
        
        // Verificação padrão para admin/usuários logados
        return wp_verify_nonce($nonce, $action) !== false;
    }
}