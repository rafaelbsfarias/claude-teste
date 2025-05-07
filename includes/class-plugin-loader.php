<?php

if (!defined('ABSPATH')) {
    exit;
}

class Asaas_Plugin_Loader {
    public function init() {
        // Carregar arquivos do painel administrativo       
        if (is_admin()) {
            require_once ASAAS_PLUGIN_DIR . 'admin/class-admin-settings.php';
            require_once ASAAS_PLUGIN_DIR . 'admin/class-admin-menu.php';
            
            // Carregar recursos experimentais na administração
            require_once ASAAS_PLUGIN_DIR . 'admin/class-feature-admin.php';
            require_once ASAAS_PLUGIN_DIR . 'admin/class-template-tester.php';
        } else {
            // Carregar configurações mesmo quando não estiver no admin
            require_once ASAAS_PLUGIN_DIR . 'admin/class-admin-settings.php';
        }

        // Carregar sistema de componentes
        require_once ASAAS_PLUGIN_DIR . 'includes/components/form-components.php';
        
        // Carregar gerenciador de recursos
        require_once ASAAS_PLUGIN_DIR . 'includes/class-feature-manager.php';
        
        // Carregar carregador de templates
        require_once ASAAS_PLUGIN_DIR . 'includes/class-template-loader.php';

        // Carregar scripts e estilos
        require_once ASAAS_PLUGIN_DIR . 'includes/enqueue-scripts.php';
        
        // Carregar shortcodes (originais e novos)
        require_once ASAAS_PLUGIN_DIR . 'includes/shortcodes.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/shortcodes-v2.php';
        
        // Carregar API e manipulador AJAX
        require_once ASAAS_PLUGIN_DIR . 'includes/class-asaas-api.php';
        require_once ASAAS_PLUGIN_DIR . 'includes/ajax-handler.php';
    }
}