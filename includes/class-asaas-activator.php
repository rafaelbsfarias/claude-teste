<?php
/**
 * Executa na ativação do plugin.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

/**
 * Classe contendo funcionalidades executadas na ativação do plugin.
 */
class Asaas_Activator {

    /**
     * Método executado na ativação do plugin.
     *
     * - Cria diretórios necessários
     * - Inicializa opções
     *
     * @since 1.0.0
     */
    public static function activate() {
        // Configurações iniciais
        if (!get_option('asaas_api_key')) {
            add_option('asaas_api_key', '');
        }
        
        if (!get_option('asaas_environment')) {
            add_option('asaas_environment', 'sandbox');
        }
        
        // Criar diretórios necessários, se não existirem
        self::create_directories();
        
        // Limpar cache de reescrita
        flush_rewrite_rules();
    }
    
    /**
     * Cria os diretórios necessários para o plugin.
     *
     * @since 1.0.0
     */
    private static function create_directories() {
        // Diretórios de assets públicos
        $public_css_dir = ASAAS_PLUGIN_DIR . 'public/css';
        $public_js_dir = ASAAS_PLUGIN_DIR . 'public/js';
        
        // Cria diretórios se não existirem
        if (!file_exists($public_css_dir)) {
            wp_mkdir_p($public_css_dir);
        }
        
        if (!file_exists($public_js_dir)) {
            wp_mkdir_p($public_js_dir);
        }
        
        // Diretórios de assets admin
        $admin_css_dir = ASAAS_PLUGIN_DIR . 'admin/css';
        $admin_js_dir = ASAAS_PLUGIN_DIR . 'admin/js';
        
        // Cria diretórios se não existirem
        if (!file_exists($admin_css_dir)) {
            wp_mkdir_p($admin_css_dir);
        }
        
        if (!file_exists($admin_js_dir)) {
            wp_mkdir_p($admin_js_dir);
        }
    }
}