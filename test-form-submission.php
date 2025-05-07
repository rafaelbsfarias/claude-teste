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
    <title>Teste de Submissão de Formulário</title>
    <?php wp_head(); ?>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; }
        #form-debug { background: #f5f5f5; padding: 10px; margin-top: 20px; white-space: pre; }
    </style>
</head>
<body>
    <h1>Teste de Submissão de Formulário</h1>
    
    <div class="test-section">
        <h2>Formulário de Doação Única V2</h2>
        <?php echo do_shortcode('[asaas_single_donation_v2]'); ?>
    </div>
    
    <div id="form-debug">Aguardando submissão do formulário...</div>
    
    <script>
    jQuery(document).ready(function($) {
        // Interceptar o envio do formulário para debug
        $('#asaas-single-donation-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            $('#form-debug').html('Dados do formulário:<br>' + formData);
            
            // Mostrar qual seria o endpoint AJAX
            $('#form-debug').append('<br><br>URL AJAX: ' + ajax_object.ajax_url);
            
            // Exibir os valores que seriam enviados
            $('#form-debug').append('<br><br>Verificando dados críticos:');
            $('#form-debug').append('<br>- action: ' + $('input[name="action"]').val());
            $('#form-debug').append('<br>- form_type: ' + $('input[name="form_type"]').val());
            $('#form-debug').append('<br>- payment_method: ' + $('select[name="payment_method"]').val());
            
            // Verificar se existe o nonce
            var nonceField = $('input[name="asaas_nonce"]');
            $('#form-debug').append('<br>- nonce presente: ' + (nonceField.length > 0 ? 'Sim' : 'Não'));
            if (nonceField.length > 0) {
                $('#form-debug').append('<br>- valor do nonce: ' + nonceField.val());
            }
        });
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>