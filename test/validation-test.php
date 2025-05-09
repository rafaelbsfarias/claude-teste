<?php
// Caminho correto para wp-load.php
$wp_load_path = realpath(dirname(__FILE__) . '/../../../../wp-load.php');

if (!file_exists($wp_load_path)) {
    die('Não foi possível localizar wp-load.php. Caminho verificado: ' . $wp_load_path);
}

// Carregar o WordPress
require_once $wp_load_path;

// Configuração básica
$plugin_dir = dirname(dirname(__FILE__));
$plugin_url = plugins_url('', dirname(__FILE__));

// Função para registrar scripts
function enqueue_test_scripts() {
    global $plugin_url;
    
    // Registrar scripts com as dependências corretas
    wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), [], false, true);
    wp_register_script('asaas-form-utils', $plugin_url . '/assets/frontend/js/form-utils.js', ['jquery'], '1.0', true);
    wp_register_script('asaas-form-ui', $plugin_url . '/assets/frontend/js/form-ui.js', ['jquery', 'asaas-form-utils'], '1.0', true);
    wp_register_script('asaas-form-ajax', $plugin_url . '/assets/frontend/js/form-ajax.js', ['jquery', 'asaas-form-ui'], '1.0', true);
    
    // Enfileirar os scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-ajax');
    
    // Adicionar objeto ajax
    wp_localize_script('asaas-form-ajax', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asaas_nonce')
    ]);
    
    // Também incluir CSS
    wp_enqueue_style('asaas-form-style', $plugin_url . '/assets/frontend/css/form-style.css');
}

// Registrar os scripts
add_action('wp_enqueue_scripts', 'enqueue_test_scripts');
do_action('wp_enqueue_scripts');

// Dados de teste PIX
$test_data = [
    'payment_method' => 'pix',
    'pix_code' => 'iVBORw0KGgoAAAANSUhEUgAAAMgAAADIAQAAAACFI5MzAAAA8klEQVR4Xu2WwW7DMAxD9dH/f7n7UkCNbMnNMHQnGAZiPhCiKIlaW758+Tj5QFtdXTu3xLrYvqpqQDvzCacQjXhHvJnvOmJkk01yidJJ3onoJEYi6iR9Et1JfxKnmXHk/vEmHVFJDk9US/xJdCNeiBKRdgR7JHjihQTPBE/UXyJQNRJgnmjbmMiMz9FiJsZ9P7uZQd5DLaK6v5Xwx1A3/jJM7FDKdqT0uFdIwAE1EEiUjUCC2vgaO8GlZBJh22gL0TYiUWbCr0TEQsyE341gE8NIMGYw5Yh3HQk65wkyZ4LNPA5sEwSJYJCPwZDQr18+Wn4BDFT+ENg0V4gAAAAASUVORK5CYII=',
    'pix_text' => '00020101021226860014br.gov.bcb.pix2564pix-qrcode.asaas.com/emv/2c6efd4b',
    'value' => 10.00
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Validação Final das Correções</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .pix-qrcode { text-align: center; margin: 20px 0; }
        .pix-code-text { width: 100%; height: 80px; margin: 10px 0; }
        button { background: #4CAF50; color: white; border: none; padding: 10px 15px; cursor: pointer; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
    </style>
    <?php wp_head(); ?>
</head>
<body>
    <h1>Validação Final - Processamento de PIX</h1>
    
    <div class="test-section">
        <h2>1. Verificação de Objetos JavaScript</h2>
        <div id="objects-check"></div>
        <button id="check-objects">Verificar Objetos</button>
    </div>
    
    <div class="test-section">
        <h2>2. Teste Específico do AsaasFormUI.displaySuccess</h2>
        <form id="pix-test-form" class="single-donation-form" data-donation-type="single">
            <!-- Formulário vazio para teste -->
        </form>
        <button id="test-display-success">Testar displaySuccess()</button>
        <div id="display-result"></div>
    </div>
    
    <div class="test-section">
        <h2>3. Teste do AsaasFormUI.displayMessage</h2>
        <form id="message-test-form" class="single-donation-form">
            <!-- Formulário vazio para teste -->
        </form>
        <button id="test-display-message">Testar displayMessage()</button>
        <div id="message-result"></div>
    </div>
    
    <div class="test-section">
        <h2>4. Teste do AsaasFormAjax.showDonationSuccess</h2>
        <form id="ajax-test-form" class="single-donation-form" data-donation-type="single">
            <!-- Formulário vazio para teste -->
        </form>
        <button id="test-ajax-success">Testar showDonationSuccess()</button>
        <div id="ajax-result"></div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Teste 1: Verificar objetos JavaScript
        document.getElementById('check-objects').addEventListener('click', function() {
            var objectsCheck = document.getElementById('objects-check');
            objectsCheck.innerHTML = '<h3>Verificando objetos...</h3>';
            
            var objects = {
                'jQuery': window.jQuery,
                'AsaasFormUtils': window.AsaasFormUtils,
                'AsaasFormUI': window.AsaasFormUI,
                'AsaasFormAjax': window.AsaasFormAjax,
                'ajax_object': window.ajax_object
            };
            
            var html = '<ul>';
            var allAvailable = true;
            
            for (var name in objects) {
                var available = objects[name] !== undefined;
                html += '<li><strong>' + name + ':</strong> ';
                html += available ? 
                    '<span class="success">Disponível ✓</span>' : 
                    '<span class="error">Indisponível ✗</span>';
                
                if (!available) {
                    allAvailable = false;
                } else if (typeof objects[name] === 'object' && objects[name] !== null) {
                    html += '<ul>';
                    for (var prop in objects[name]) {
                        if (typeof objects[name][prop] === 'function') {
                            html += '<li>' + prop + '(): ';
                            html += '<span class="success">Função disponível ✓</span></li>';
                        }
                    }
                    html += '</ul>';
                }
                
                html += '</li>';
            }
            
            html += '</ul>';
            
            // Verificar funções críticas específicas
            var criticalFunctions = [
                { name: 'AsaasFormUI.displaySuccess', fn: window.AsaasFormUI && window.AsaasFormUI.displaySuccess },
                { name: 'AsaasFormUI.displayMessage', fn: window.AsaasFormUI && window.AsaasFormUI.displayMessage },
                { name: 'AsaasFormUI.setupPaymentMethodToggles', fn: window.AsaasFormUI && window.AsaasFormUI.setupPaymentMethodToggles },
                { name: 'AsaasFormAjax.showDonationSuccess', fn: window.AsaasFormAjax && window.AsaasFormAjax.showDonationSuccess }
            ];
            
            html += '<h3>Funções críticas:</h3><ul>';
            
            criticalFunctions.forEach(function(func) {
                html += '<li><strong>' + func.name + ':</strong> ';
                html += typeof func.fn === 'function' ? 
                    '<span class="success">Disponível ✓</span>' : 
                    '<span class="error">Indisponível ✗</span>';
                html += '</li>';
                
                if (typeof func.fn !== 'function') {
                    allAvailable = false;
                }
            });
            
            html += '</ul>';
            
            html += '<h3>Status geral: ' + 
                (allAvailable ? 
                    '<span class="success">Todos os objetos e funções estão disponíveis! ✓</span>' : 
                    '<span class="error">Alguns objetos ou funções estão faltando! ✗</span>') + 
                '</h3>';
                
            objectsCheck.innerHTML = html;
        });
        
        // Teste 2: Testar displaySuccess específicamente
        document.getElementById('test-display-success').addEventListener('click', function() {
            var form = document.getElementById('pix-test-form');
            var resultDiv = document.getElementById('display-result');
            resultDiv.innerHTML = '<h3>Testando AsaasFormUI.displaySuccess...</h3>';
            
            try {
                if (window.AsaasFormUI && typeof AsaasFormUI.displaySuccess === 'function') {
                    var result = AsaasFormUI.displaySuccess(form, <?php echo json_encode($test_data); ?>, 'single');
                    resultDiv.innerHTML += result ? 
                        '<p class="success">Função executada com sucesso!</p>' : 
                        '<p class="error">Função executada, mas retornou false.</p>';
                } else {
                    resultDiv.innerHTML += '<p class="error">AsaasFormUI.displaySuccess não está disponível!</p>';
                }
            } catch (error) {
                resultDiv.innerHTML += '<p class="error">Erro ao executar: ' + error.message + '</p>';
                console.error('Erro completo:', error);
            }
        });
        
        // Teste 3: Testar displayMessage
        document.getElementById('test-display-message').addEventListener('click', function() {
            var form = document.getElementById('message-test-form');
            var resultDiv = document.getElementById('message-result');
            resultDiv.innerHTML = '<h3>Testando AsaasFormUI.displayMessage...</h3>';
            
            try {
                if (window.AsaasFormUI && typeof AsaasFormUI.displayMessage === 'function') {
                    var result = AsaasFormUI.displayMessage(
                        form, 
                        'Esta é uma mensagem de teste!', 
                        'success'
                    );
                    resultDiv.innerHTML += result ? 
                        '<p class="success">Função executada com sucesso!</p>' : 
                        '<p class="error">Função executada, mas retornou false.</p>';
                } else {
                    resultDiv.innerHTML += '<p class="error">AsaasFormUI.displayMessage não está disponível!</p>';
                }
            } catch (error) {
                resultDiv.innerHTML += '<p class="error">Erro ao executar: ' + error.message + '</p>';
                console.error('Erro completo:', error);
            }
        });
        
        // Teste 4: Testar showDonationSuccess
        document.getElementById('test-ajax-success').addEventListener('click', function() {
            var form = document.getElementById('ajax-test-form');
            var resultDiv = document.getElementById('ajax-result');
            resultDiv.innerHTML = '<h3>Testando AsaasFormAjax.showDonationSuccess...</h3>';
            
            try {
                if (window.AsaasFormAjax && typeof AsaasFormAjax.showDonationSuccess === 'function') {
                    AsaasFormAjax.showDonationSuccess(
                        form, 
                        <?php echo json_encode($test_data); ?>, 
                        'single'
                    );
                    resultDiv.innerHTML += '<p class="success">Função executada sem erros!</p>';
                } else {
                    resultDiv.innerHTML += '<p class="error">AsaasFormAjax.showDonationSuccess não está disponível!</p>';
                }
            } catch (error) {
                resultDiv.innerHTML += '<p class="error">Erro ao executar: ' + error.message + '</p>';
                console.error('Erro completo:', error);
            }
        });
        
        // Auto-executar a verificação de objetos
        setTimeout(function() {
            document.getElementById('check-objects').click();
        }, 500);
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>