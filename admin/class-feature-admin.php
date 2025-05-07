<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once ASAAS_PLUGIN_DIR . 'includes/class-feature-manager.php';

/**
 * Administração de recursos experimentais
 */
class Asaas_Feature_Admin {
    /**
     * Inicializa a classe
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_submenu_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_post_asaas_toggle_features', [$this, 'handle_form_submit']);
    }
    
    /**
     * Adiciona a página de submenu
     */
    public function add_submenu_page() {
        add_submenu_page(
            'asaas-settings',
            'Recursos Experimentais',
            'Recursos Experimentais',
            'manage_options',
            'asaas-features',
            [$this, 'render_admin_page']
        );
    }
    
    /**
     * Registra as configurações
     */
    public function register_settings() {
        register_setting('asaas_features', 'asaas_feature_toggles');
    }
    
    /**
     * Processa o formulário de alteração de recursos
     */
    public function handle_form_submit() {
        // Verificar nonce
        if (!isset($_POST['asaas_features_nonce']) || !wp_verify_nonce($_POST['asaas_features_nonce'], 'asaas_toggle_features')) {
            wp_die('Ação não autorizada');
        }
        
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_die('Você não tem permissão para alterar estas configurações');
        }
        
        // Recuperar valores atuais
        $settings = get_option('asaas_feature_toggles', []);
        
        // Lista de recursos disponíveis
        $available_features = [
            'use_v2_templates',
            'improved_form_validation',
            'enhanced_error_logging',
        ];
        
        // Atualizar valores com base no formulário
        foreach ($available_features as $feature) {
            $settings[$feature] = isset($_POST['features'][$feature]) ? true : false;
        }
        
        // Salvar configurações
        update_option('asaas_feature_toggles', $settings);
        
        // Redirecionar com mensagem de sucesso
        wp_redirect(add_query_arg('updated', 'true', admin_url('admin.php?page=asaas-features')));
        exit;
    }
    
    /**
     * Renderiza a página de administração
     */
    public function render_admin_page() {
        // Recuperar configurações atuais
        $settings = get_option('asaas_feature_toggles', []);
        
        ?>
        <div class="wrap">
            <h1>Recursos Experimentais do Asaas</h1>
            
            <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
                <div class="notice notice-success is-dismissible">
                    <p>Configurações atualizadas com sucesso!</p>
                </div>
            <?php endif; ?>
            
            <p>Habilite ou desabilite recursos experimentais do plugin.</p>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="asaas_toggle_features">
                <?php wp_nonce_field('asaas_toggle_features', 'asaas_features_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Usar Templates V2</th>
                        <td>
                            <label>
                                <input type="checkbox" name="features[use_v2_templates]" value="1" <?php checked(isset($settings['use_v2_templates']) && $settings['use_v2_templates']); ?>>
                                Habilitar novos templates de formulário (versão beta)
                            </label>
                            <p class="description">Usa os novos templates de formulário com componentes reutilizáveis.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Validação de Formulário Aprimorada</th>
                        <td>
                            <label>
                                <input type="checkbox" name="features[improved_form_validation]" value="1" <?php checked(isset($settings['improved_form_validation']) && $settings['improved_form_validation']); ?>>
                                Habilitar validação de formulário aprimorada
                            </label>
                            <p class="description">Usa validação de formulário mais robusta com mensagens de erro mais específicas.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Registro de Erros Avançado</th>
                        <td>
                            <label>
                                <input type="checkbox" name="features[enhanced_error_logging]" value="1" <?php checked(isset($settings['enhanced_error_logging']) && $settings['enhanced_error_logging']); ?>>
                                Habilitar registro de erros avançado
                            </label>
                            <p class="description">Registra informações detalhadas sobre erros e requisições (apenas para depuração).</p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Salvar Alterações">
                </p>
            </form>
            
            <h2>Testar Templates</h2>
            <p>Use os botões abaixo para visualizar e testar os templates disponíveis:</p>
            
            <a href="<?php echo admin_url('admin.php?page=asaas-test-templates'); ?>" class="button">Testar Templates</a>
        </div>
        <?php
    }
}

// Inicializar a administração de recursos
new Asaas_Feature_Admin();