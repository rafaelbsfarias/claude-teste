<?php

if (!defined('ABSPATH')) {
    exit;
}

class Asaas_Plugin_Loader {
    public function init() {
        // Carregar arquivos do painel administrativo.       
        
        if (is_admin()) {
            require_once ASAAS_PLUGIN_DIR . 'admin/class-admin-settings.php';
            require_once ASAAS_PLUGIN_DIR . 'admin/class-admin-menu.php';                        
        } else {
            // Carregar configurações mesmo quando não estiver no admin
            require_once ASAAS_PLUGIN_DIR . 'admin/class-admin-settings.php';
        }

        require_once ASAAS_PLUGIN_DIR . 'includes/enqueue-scripts.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/shortcodes.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/class-asaas-api.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/ajax-handler.php';
        
        // Garantir que os hooks AJAX estejam registrados
        $this->register_ajax_hooks();
    }

    /**
     * Registra todos os hooks AJAX
     */
    private function register_ajax_hooks() {
        // Adicionar hooks para ambas as ações
        add_action('wp_ajax_process_donation_form', 'process_donation_form');
        add_action('wp_ajax_nopriv_process_donation_form', 'process_donation_form');
        
        add_action('wp_ajax_process_donation', 'asaas_process_donation');
        add_action('wp_ajax_nopriv_process_donation', 'asaas_process_donation');
    }
}