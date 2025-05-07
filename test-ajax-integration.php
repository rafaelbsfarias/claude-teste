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
    <title>Teste de Integração AJAX</title>
    <?php wp_head(); ?>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; }
        .ajax-log { background: #f5f5f5; padding: 10px; margin-top: 20px; white-space: pre; height: 200px; overflow: auto; }
        .ajax-log-entry { margin-bottom: 5px; }
        .ajax-log-entry.success { color: green; }
        .ajax-log-entry.error { color: red; }
        .ajax-log-entry.info { color: blue; }
    </style>
</head>
<body>
    <h1>Teste de Integração AJAX</h1>
    
    <div class="test-section">
        <h2>Formulário de Doação Única V2</h2>
        <?php echo do_shortcode('[asaas_single_donation_v2]'); ?>
    </div>
    
    <div class="ajax-log" id="ajax-log">
        <div class="ajax-log-entry info">Aguardando ações...</div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Override do método $.ajax para logar todas as requisições
        var originalAjax = $.ajax;
        $.ajax = function(options) {
            logMessage('info', 'Iniciando requisição AJAX para: ' + options.url);
            logMessage('info', 'Dados enviados: ' + JSON.stringify(options.data));
            
            // Interceptar o callback de sucesso
            var originalSuccess = options.success;
            options.success = function(response) {
                logMessage('success', 'Resposta recebida: ' + JSON.stringify(response));
                if (originalSuccess) originalSuccess.apply(this, arguments);
            };
            
            // Interceptar o callback de erro
            var originalError = options.error;
            options.error = function(xhr, status, error) {
                logMessage('error', 'Erro na requisição: ' + status + ' - ' + error);
                if (originalError) originalError.apply(this, arguments);
            };
            
            return originalAjax.apply(this, arguments);
        };
        
        // Função para logar mensagens
        function logMessage(type, message) {
            var $log = $('#ajax-log');
            var timestamp = new Date().toLocaleTimeString();
            $log.append('<div class="ajax-log-entry ' + type + '">[' + timestamp + '] ' + message + '</div>');
            $log.scrollTop($log[0].scrollHeight);
        }
        
        // Log quando o documento estiver pronto
        logMessage('info', 'Página carregada. Scripts AJAX configurados.');
        logMessage('info', 'URL do AJAX: ' + ajax_object.ajax_url);
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>