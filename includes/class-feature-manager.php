<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gerencia recursos e configurações do plugin
 */
class Asaas_Feature_Manager {
    /**
     * Verifica se um recurso específico está habilitado
     */
    public static function is_feature_enabled($feature_name) {
        $defaults = [
            'use_v2_templates' => false,  // Por padrão, usa templates antigos
            'improved_form_validation' => false,
            'enhanced_error_logging' => false,
        ];
        
        // Primeiro verifica constantes
        if (defined('ASAAS_ENABLE_' . strtoupper($feature_name)) && constant('ASAAS_ENABLE_' . strtoupper($feature_name))) {
            return true;
        }
        
        // Depois verifica as configurações salvas no banco de dados
        $settings = get_option('asaas_feature_toggles', []);
        $settings = wp_parse_args($settings, $defaults);
        
        return isset($settings[$feature_name]) ? (bool) $settings[$feature_name] : false;
    }
    
    /**
     * Habilita um recurso específico
     */
    public static function enable_feature($feature_name) {
        $settings = get_option('asaas_feature_toggles', []);
        $settings[$feature_name] = true;
        update_option('asaas_feature_toggles', $settings);
    }
    
    /**
     * Desabilita um recurso específico
     */
    public static function disable_feature($feature_name) {
        $settings = get_option('asaas_feature_toggles', []);
        $settings[$feature_name] = false;
        update_option('asaas_feature_toggles', $settings);
    }
}