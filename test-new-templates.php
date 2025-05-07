<?php
// Carregar o WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Definir constante de debug para capturar erros
define('ASAAS_TEST_DEBUG', true);

// Verificar se o usuário tem permissão
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado');
}

// Função para verificar e incluir arquivos com segurança
function asaas_safe_include($file_path) {
    if (!file_exists($file_path)) {
        echo "<div class='error-message' style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
        echo "Erro: O arquivo não foi encontrado: " . esc_html($file_path);
        echo "</div>";
        return false;
    }
    
    // Usar include com tratamento de erro
    try {
        include_once $file_path;
        return true;
    } catch (Exception $e) {
        echo "<div class='error-message' style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
        echo "Erro ao incluir arquivo: " . esc_html($e->getMessage());
        echo "</div>";
        return false;
    }
}

// Iniciar o HTML
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Novos Templates</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        h2 { color: #0073aa; }
        .asaas-form-group { margin-bottom: 15px; }
        .asaas-form-group label { display: block; margin-bottom: 5px; }
        .asaas-form-group input, .asaas-form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .asaas-payment-methods { display: flex; gap: 15px; }
        .asaas-payment-method { display: flex; align-items: center; }
        .asaas-payment-method input { width: auto; margin-right: 5px; }
        .asaas-form-row { display: flex; gap: 15px; }
        .asaas-form-row .asaas-form-group { flex: 1; }
        .asaas-submit-button { background: #0073aa; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .tab-buttons { display: flex; margin-bottom: 20px; }
        .tab-button { padding: 10px 20px; background: #f5f5f5; border: 1px solid #ddd; cursor: pointer; }
        .tab-button.active { background: #0073aa; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .debug-info { background: #f8f8f8; border: 1px solid #ddd; padding: 10px; margin: 20px 0; font-family: monospace; }
    </style>
</head>
<body>
    <h1>Teste de Novos Templates de Formulário</h1>
    
    <?php if (defined('ASAAS_TEST_DEBUG') && ASAAS_TEST_DEBUG): ?>
    <div class="debug-info">
        <h3>Informações de Debug:</h3>
        <p>Plugin Path: <?php echo esc_html(plugin_dir_path(__FILE__)); ?></p>
        <p>Components Path: <?php echo esc_html(plugin_dir_path(__FILE__) . 'includes/components/form-components.php'); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="tab-buttons">
        <div class="tab-button active" data-tab="single">Doação Única</div>
        <div class="tab-button" data-tab="recurring">Doação Recorrente</div>
    </div>
    
    <div id="single-tab" class="tab-content active">
        <h2>Template V2 - Doação Única</h2>
        <?php
        // Verificar e incluir componentes primeiro
        $components_path = plugin_dir_path(__FILE__) . 'includes/components/form-components.php';
        $components_loaded = asaas_safe_include($components_path);
        
        if ($components_loaded) {
            $single_template_path = plugin_dir_path(__FILE__) . 'templates/v2-form-single-donation.php';
            asaas_safe_include($single_template_path);
        } else {
            echo "<p>Não foi possível carregar os componentes necessários.</p>";
        }
        ?>
    </div>
    
    <div id="recurring-tab" class="tab-content">
        <h2>Template V2 - Doação Recorrente</h2>
        <?php
        if ($components_loaded) {
            $recurring_template_path = plugin_dir_path(__FILE__) . 'templates/v2-form-recurring-donation.php';
            asaas_safe_include($recurring_template_path);
        } else {
            echo "<p>Não foi possível carregar os componentes necessários.</p>";
        }
        ?>
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