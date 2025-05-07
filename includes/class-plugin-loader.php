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
        }

        require_once ASAAS_PLUGIN_DIR . 'includes/enqueue-scripts.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/shortcodes.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/class-asaas-api.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/ajax-handler.php'; 
    }

    /**
     * Registra todos os hooks AJAX
     */
    private function register_ajax_hooks() {
        // Adicione esta linha para garantir que o hook está registrado para usuários não logados
        add_action('wp_ajax_process_donation_form', 'process_donation_form');
        add_action('wp_ajax_nopriv_process_donation_form', 'process_donation_form');
        
        // Certifique-se de que a função process_donation_form está definida e acessível
        if (!function_exists('process_donation_form')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ajax-handler.php';
        }
    }
}