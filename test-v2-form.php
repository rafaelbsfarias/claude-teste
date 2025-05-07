<?php
// Carregar o WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Verificar se o usuário tem permissão
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste Formulário V2</title>
    <?php wp_head(); ?>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-container { border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; }
        .debug-panel { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; margin-top: 20px; }
        .debug-panel pre { white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Teste dos Formulários V2</h1>
    
    <div class="form-container">
        <h2>Formulário de Doação Única V2</h2>
        <?php 
        // Forçar o uso dos templates V2
        require_once ASAAS_PLUGIN_DIR . 'includes/class-feature-manager.php';
        Asaas_Feature_Manager::enable_feature('use_v2_templates');
        
        echo do_shortcode('[asaas_single_donation_v2]'); 
        ?>
    </div>
    
    <div class="debug-panel">
        <h3>Debug do Formulário</h3>
        <div id="form-debug"></div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Interceptar a submissão do formulário para debug
        $('#asaas-single-donation-form').on('submit', function(e) {
            e.preventDefault();
            
            // Coletar os dados do formulário
            const formData = $(this).serialize();
            
            // Exibir os dados no painel de debug
            $('#form-debug').html(`
                <p><strong>Dados do formulário:</strong></p>
                <pre>${formData}</pre>
            `);
            
            // Enviar os dados via AJAX para testar
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#form-debug').append(`
                        <p><strong>Resposta do servidor:</strong></p>
                        <pre>${JSON.stringify(response, null, 2)}</pre>
                    `);
                },
                error: function(xhr, status, error) {
                    $('#form-debug').append(`
                        <p><strong>Erro:</strong></p>
                        <pre>Status: ${status}
Error: ${error}
Response: ${xhr.responseText}</pre>
                    `);
                }
            });
        });
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>