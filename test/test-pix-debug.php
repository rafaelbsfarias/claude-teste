<?php
// filepath: c:\laragon\www\meu-lindo-wp\wp-content\plugins\asaas-easy-subscription-plugin\test\pix-debug.php

// Determinar o caminho correto para wp-load.php de forma mais robusta
$wp_load_path = realpath(dirname(__FILE__) . '/../../../../wp-load.php');

if (!file_exists($wp_load_path)) {
    die('Não foi possível localizar wp-load.php. Caminho verificado: ' . $wp_load_path);
}

// Carregar o WordPress
require_once $wp_load_path;

// Configuração básica
$plugin_dir = dirname(dirname(__FILE__));
$plugin_url = plugins_url('', dirname(__FILE__));

// Verificar se os arquivos dos scripts existem
$js_files = [
    'form-utils.js' => $plugin_dir . '/assets/frontend/js/form-utils.js',
    'form-ui.js' => $plugin_dir . '/assets/frontend/js/form-ui.js',
    'form-ajax.js' => $plugin_dir . '/assets/frontend/js/form-ajax.js'
];

// Registrar e enfileirar os scripts
function enqueue_test_scripts() {
    global $plugin_url;
    
    // Registrar scripts com as dependências corretas
    wp_register_script('asaas-form-utils', $plugin_url . '/assets/frontend/js/form-utils.js', ['jquery'], '1.0', true);
    wp_register_script('asaas-form-ui', $plugin_url . '/assets/frontend/js/form-ui.js', ['jquery', 'asaas-form-utils'], '1.0', true);
    wp_register_script('asaas-form-ajax', $plugin_url . '/assets/frontend/js/form-ajax.js', ['jquery', 'asaas-form-ui'], '1.0', true);
    
    // Enfileirar os scripts
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-ajax');
    
    // Adicionar objeto ajax
    wp_localize_script('asaas-form-ajax', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asaas_nonce')
    ]);
}

// Garantir que os scripts sejam carregados
add_action('wp_enqueue_scripts', 'enqueue_test_scripts');
do_action('wp_enqueue_scripts');

// Dados de teste para simular resposta PIX
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
    <title>Teste de Renderização PIX</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .pix-qrcode { text-align: center; margin: 20px 0; }
        .pix-code-text { width: 100%; height: 80px; margin: 10px 0; }
        button { background: #4CAF50; color: white; border: none; padding: 10px 15px; cursor: pointer; }
    </style>
    <?php wp_head(); ?>
</head>
<body>
    <h1>Teste de Renderização de QR Code PIX</h1>
    
    <div class="test-section">
        <h2>1. Verificação de Scripts</h2>
        <div id="scripts-check"></div>
    </div>
    
    <div class="test-section">
        <h2>2. Teste de Exibição do QR Code</h2>
        <form id="test-form" class="single-donation-form" data-donation-type="single">
            <!-- Formulário vazio apenas para teste -->
        </form>
        <button id="test-display">Testar Exibição do QR Code</button>
        <div id="qr-result"></div>
    </div>
    
    <div class="test-section">
        <h2>3. Implementação Manual do QR Code</h2>
        <div id="manual-qr">
            <div class="pix-success">
                <h2 class="success-title">Só mais um passo!</h2>
                <p>Leia o QR Code ou copie o código abaixo para realizar sua doação</p>
                <div class="pix-info">
                    <div class="info-row">
                        <span class="info-label">Valor:</span>
                        <span class="info-value">R$ <?php echo number_format($test_data['value'], 2, ',', '.'); ?></span>
                    </div>
                </div>
                
                <div class="pix-qrcode">
                    <img src="data:image/png;base64,<?php echo $test_data['pix_code']; ?>" alt="QR Code PIX">
                </div>
                
                <textarea id="pix-code-text" class="pix-code-text" readonly><?php echo $test_data['pix_text']; ?></textarea>
                
                <button class="btn btn-copy-wide" onclick="alert('Código copiado!')">Copiar</button>
            </div>
        </div>
    </div>
    
    <?php wp_footer(); ?>
    
    <script>
    // Verificar se os scripts foram carregados
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var scriptsCheck = document.getElementById('scripts-check');
            var objects = [
                { name: 'AsaasFormUtils', obj: window.AsaasFormUtils },
                { name: 'AsaasFormUI', obj: window.AsaasFormUI },
                { name: 'AsaasFormAjax', obj: window.AsaasFormAjax },
                { name: 'ajax_object', obj: window.ajax_object }
            ];
            
            var allLoaded = true;
            objects.forEach(function(item) {
                var loaded = typeof item.obj !== 'undefined';
                var html = '<p><strong>' + item.name + ':</strong> ';
                html += loaded ? 
                    '<span class="success">Carregado ✓</span>' : 
                    '<span class="error">Não carregado ✗</span>';
                html += '</p>';
                
                if (!loaded) allLoaded = false;
                scriptsCheck.innerHTML += html;
            });
            
            // Verificar função de sucesso
            if (typeof AsaasFormUI !== 'undefined') {
                var hasDisplaySuccess = typeof AsaasFormUI.displaySuccess === 'function';
                scriptsCheck.innerHTML += '<p><strong>AsaasFormUI.displaySuccess:</strong> ' + 
                    (hasDisplaySuccess ? 
                        '<span class="success">Disponível ✓</span>' : 
                        '<span class="error">Indisponível ✗</span>') + 
                    '</p>';
                
                if (!hasDisplaySuccess) allLoaded = false;
            }
            
            // Resumo
            scriptsCheck.innerHTML += '<p><strong>Status geral:</strong> ' + 
                (allLoaded ? 
                    '<span class="success">Scripts carregados corretamente</span>' : 
                    '<span class="error">Problema no carregamento de scripts - verifique o console</span>') + 
                '</p>';
            
            // Teste de exibição
            document.getElementById('test-display').addEventListener('click', function() {
                var testForm = document.getElementById('test-form');
                var resultDiv = document.getElementById('qr-result');
                
                resultDiv.innerHTML = '<p>Tentando exibir QR code via função normal...</p>';
                
                try {
                    if (typeof AsaasFormUI !== 'undefined' && typeof AsaasFormUI.displaySuccess === 'function') {
                        AsaasFormUI.displaySuccess(testForm, <?php echo json_encode($test_data); ?>, 'single');
                        resultDiv.innerHTML += '<p class="success">QR code exibido pela função normal</p>';
                    } else {
                        resultDiv.innerHTML += '<p class="error">AsaasFormUI.displaySuccess não está disponível</p>';
                        
                        // Fallback para AsaasFormAjax
                        if (typeof AsaasFormAjax !== 'undefined' && typeof AsaasFormAjax.showDonationSuccess === 'function') {
                            resultDiv.innerHTML += '<p>Tentando via AsaasFormAjax.showDonationSuccess...</p>';
                            AsaasFormAjax.showDonationSuccess(testForm, <?php echo json_encode($test_data); ?>, 'single');
                            resultDiv.innerHTML += '<p class="success">QR code exibido pelo método alternativo</p>';
                        } else {
                            resultDiv.innerHTML += '<p class="error">Método alternativo também não disponível</p>';
                        }
                    }
                } catch (e) {
                    resultDiv.innerHTML += '<p class="error">Erro ao exibir QR code: ' + e.message + '</p>';
                    console.error('Erro ao exibir QR code:', e);
                }
            });
        }, 500);
    });
    </script>
</body>
</html>