<?php
// Carregar o WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Verificar se o usuário tem permissão
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado');
}

// Carregar recursos necessários
require_once ASAAS_PLUGIN_DIR . 'includes/class-feature-manager.php';
require_once ASAAS_PLUGIN_DIR . 'includes/class-template-loader.php';

// Determinar se usar V2 com base no parâmetro URL
$use_v2 = isset($_GET['v2']) && $_GET['v2'] === '1';

if ($use_v2) {
    // Habilitar temporariamente os templates V2
    Asaas_Feature_Manager::enable_feature('use_v2_templates');
} else {
    // Desabilitar temporariamente os templates V2
    Asaas_Feature_Manager::disable_feature('use_v2_templates');
}

// Iniciar o HTML
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Integração - Asaas Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        .tab-buttons { display: flex; margin-bottom: 20px; }
        .tab-button { padding: 10px 20px; background: #f5f5f5; border: 1px solid #ddd; cursor: pointer; }
        .tab-button.active { background: #0073aa; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .version-toggle { margin-bottom: 20px; text-align: center; }
        .version-toggle a { display: inline-block; padding: 10px 20px; background: #f5f5f5; border: 1px solid #ddd; text-decoration: none; color: #333; }
        .version-toggle a.active { background: #0073aa; color: white; }
        h1, h2 { color: #0073aa; }
    </style>
    <?php
    // Enfileirar os estilos e scripts do formulário
    wp_enqueue_style('asaas-form-style');
    wp_enqueue_script('jquery');
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-masks');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-ajax');
    wp_enqueue_script('asaas-form-script');
    
    // Imprimir os estilos e scripts
    wp_print_styles();
    wp_print_scripts();
    ?>
</head>
<body>
    <h1>Teste de Integração - Asaas Plugin</h1>
    
    <div class="version-toggle">
        <p>Versão dos Templates:</p>
        <a href="?v2=0" class="<?php echo !$use_v2 ? 'active' : ''; ?>">Templates Originais</a>
        <a href="?v2=1" class="<?php echo $use_v2 ? 'active' : ''; ?>">Templates V2</a>
    </div>
    
    <div class="tab-buttons">
        <div class="tab-button active" data-tab="shortcode">Via Shortcode</div>
        <div class="tab-button" data-tab="direct">Via Template Loader</div>
    </div>
    
    <div id="shortcode-tab" class="tab-content active">
        <div class="test-section">
            <h2>Doação Única (Shortcode)</h2>
            <?php echo do_shortcode('[asaas_single_donation]'); ?>
        </div>
        
        <div class="test-section">
            <h2>Doação Recorrente (Shortcode)</h2>
            <?php echo do_shortcode('[asaas_recurring_donation]'); ?>
        </div>
    </div>
    
    <div id="direct-tab" class="tab-content">
        <div class="test-section">
            <h2>Doação Única (Template Loader)</h2>
            <?php echo Asaas_Template_Loader::load_template('form-single-donation'); ?>
        </div>
        
        <div class="test-section">
            <h2>Doação Recorrente (Template Loader)</h2>
            <?php echo Asaas_Template_Loader::load_template('form-recurring-donation'); ?>
        </div>
    </div>
    
    <script>
        // Funcionalidade de abas
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remover classe ativa de todos os botões e conteúdos
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Adicionar classe ativa ao botão e conteúdo correspondente
                    this.classList.add('active');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });
        });
    </script>
</body>
</html>