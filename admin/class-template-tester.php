<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once ASAAS_PLUGIN_DIR . 'includes/class-template-loader.php';

/**
 * Página de teste de templates
 */
class Asaas_Template_Tester {
    /**
     * Inicializa a classe
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_submenu_page']);
    }
    
    /**
     * Adiciona a página de submenu
     */
    public function add_submenu_page() {
        add_submenu_page(
            'asaas-settings',
            'Testar Templates',
            'Testar Templates',
            'manage_options',
            'asaas-test-templates',
            [$this, 'render_admin_page']
        );
    }
    
    /**
     * Renderiza a página de teste de templates
     */
    public function render_admin_page() {
        // Carrega o CSS do frontend
        wp_enqueue_style('asaas-form-style', ASAAS_PLUGIN_URL . 'assets/frontend/css/form-style.css');
        
        // Carrega scripts do frontend para teste
        wp_enqueue_script('jquery');
        wp_enqueue_script('asaas-form-utils', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-utils.js', ['jquery']);
        wp_enqueue_script('asaas-form-masks', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-masks.js', ['asaas-form-utils']);
        wp_enqueue_script('asaas-form-ui', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-ui.js', ['asaas-form-utils']);
        
        // Determina se usar V2 ou não com base no parâmetro URL
        $use_v2 = isset($_GET['v2']) && $_GET['v2'] === '1';
        
        if ($use_v2) {
            Asaas_Feature_Manager::enable_feature('use_v2_templates');
        } else {
            Asaas_Feature_Manager::disable_feature('use_v2_templates');
        }
        
        ?>
        <div class="wrap">
            <h1>Teste de Templates</h1>
            
            <div class="nav-tab-wrapper">
                <a href="?page=asaas-test-templates&v2=0" class="nav-tab <?php echo !$use_v2 ? 'nav-tab-active' : ''; ?>">
                    Templates Originais
                </a>
                <a href="?page=asaas-test-templates&v2=1" class="nav-tab <?php echo $use_v2 ? 'nav-tab-active' : ''; ?>">
                    Templates V2
                </a>
            </div>
            
            <div class="tab-content" style="background: #fff; padding: 20px; border: 1px solid #ccc; margin-top: 10px;">
                <h2>Doação Única</h2>
                <div style="max-width: 600px; margin: 0 auto;">
                    <?php echo Asaas_Template_Loader::load_template('form-single-donation'); ?>
                </div>
                
                <hr style="margin: 40px 0;">
                
                <h2>Doação Recorrente</h2>
                <div style="max-width: 600px; margin: 0 auto;">
                    <?php echo Asaas_Template_Loader::load_template('form-recurring-donation'); ?>
                </div>
            </div>
        </div>
        <?php
    }
}

// Inicializar o testador de templates
new Asaas_Template_Tester();