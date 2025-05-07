<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once ASAAS_PLUGIN_DIR . 'includes/class-feature-manager.php';

/**
 * Responsável por carregar os templates do plugin
 */
class Asaas_Template_Loader {
    /**
     * Carrega um template específico
     */
    public static function load_template($template_name, $args = []) {
        // Extrair argumentos para usar no template
        if (!empty($args)) {
            extract($args);
        }
        
        // Determinar qual versão do template usar
        $use_v2 = Asaas_Feature_Manager::is_feature_enabled('use_v2_templates');
        
        // Construir caminho do template
        $template_path = ASAAS_PLUGIN_DIR . 'templates/';
        
        if ($use_v2) {
            $template_file = 'v2-' . $template_name . '.php';
            
            // Verificar se o template V2 existe
            if (!file_exists($template_path . $template_file)) {
                // Fallback para template original se V2 não existir
                $template_file = $template_name . '.php';
            }
        } else {
            $template_file = $template_name . '.php';
        }
        
        // Verificar se o template existe
        if (!file_exists($template_path . $template_file)) {
            return '<!-- Template não encontrado: ' . esc_html($template_file) . ' -->';
        }
        
        // Capturar o output do template
        ob_start();
        include $template_path . $template_file;
        return ob_get_clean();
    }
}