<?php
// filepath: c:\laragon\www\meu-lindo-wp\wp-content\plugins\asaas-easy-subscription-plugin\test\script-diagnostics.php

// Carregar WordPress
$wp_load_path = realpath(dirname(__FILE__) . '/../../../../wp-load.php');
if (!file_exists($wp_load_path)) {
    die('Não foi possível localizar wp-load.php. Caminho verificado: ' . $wp_load_path);
}
require_once $wp_load_path;

// Configurações
$plugin_dir = dirname(dirname(__FILE__));
$plugin_url = plugins_url('', dirname(__FILE__));
$debug = true;

// Função para registrar scripts manualmente
function register_asaas_scripts() {
    global $plugin_url;
    wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), [], false, true);
    wp_register_script('asaas-form-utils', $plugin_url . '/assets/frontend/js/form-utils.js', ['jquery'], '1.0', true);
    wp_register_script('asaas-form-ui', $plugin_url . '/assets/frontend/js/form-ui.js', ['jquery', 'asaas-form-utils'], '1.0', true);
    wp_register_script('asaas-form-ajax', $plugin_url . '/assets/frontend/js/form-ajax.js', ['jquery', 'asaas-form-ui'], '1.0', true);
    wp_enqueue_script('jquery');
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-ajax');
    wp_localize_script('asaas-form-ajax', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asaas_nonce')
    ]);
}

// Verificar os scripts registrados no WordPress
function analyze_scripts() {
    global $wp_scripts, $plugin_dir, $debug;
    $results = [];
    
    // Scripts que devem estar presentes
    $required_scripts = [
        'asaas-form-utils',
        'asaas-form-ui',
        'asaas-form-ajax'
    ];
    
    // Verificar cada script
    foreach ($required_scripts as $handle) {
        $script_data = [];
        $script_data['handle'] = $handle;
        
        // Verificar registro
        $is_registered = isset($wp_scripts->registered[$handle]);
        $script_data['registered'] = $is_registered;
        
        if ($is_registered) {
            $script = $wp_scripts->registered[$handle];
            $script_data['src'] = $script->src;
            $script_data['deps'] = $script->deps;
            
            // Verificar se o arquivo existe
            if (strpos($script->src, site_url()) === 0) {
                $local_path = str_replace(site_url(), ABSPATH, $script->src);
                $script_data['exists'] = file_exists($local_path);
                if ($script_data['exists']) {
                    $script_data['size'] = filesize($local_path);
                    $script_data['modified'] = date('Y-m-d H:i:s', filemtime($local_path));
                    
                    // Análise de conteúdo
                    if ($debug) {
                        $content = file_get_contents($local_path);
                        $script_data['content_length'] = strlen($content);
                        
                        // Verificar exportações para window
                        preg_match('/window\.([A-Za-z0-9_]+)\s*=/', $content, $matches);
                        if (!empty($matches[1])) {
                            $script_data['exports'] = $matches[1];
                        } else {
                            $script_data['exports'] = 'Nenhuma exportação encontrada';
                        }
                        
                        // Verificar funções
                        preg_match_all('/function\s+([A-Za-z0-9_]+)\s*\(/', $content, $func_matches);
                        if (!empty($func_matches[1])) {
                            $script_data['functions'] = $func_matches[1];
                        }
                    }
                }
            } else {
                $script_data['exists'] = 'Externa';
            }
            
            // Verificar se está na fila
            $script_data['enqueued'] = in_array($handle, $wp_scripts->queue);
        }
        
        $results[$handle] = $script_data;
    }
    
    return $results;
}

// Verificar os arquivos JS diretamente
function analyze_js_files() {
    global $plugin_dir;
    $js_dir = $plugin_dir . '/assets/frontend/js/';
    $results = [];
    
    $files = [
        'form-utils.js',
        'form-ui.js',
        'form-ajax.js'
    ];
    
    foreach ($files as $file) {
        $path = $js_dir . $file;
        $file_data = [];
        $file_data['path'] = $path;
        $file_data['exists'] = file_exists($path);
        
        if ($file_data['exists']) {
            $file_data['size'] = filesize($path);
            $file_data['modified'] = date('Y-m-d H:i:s', filemtime($path));
            
            // Verificar conteúdo
            $content = file_get_contents($path);
            $file_data['content_length'] = strlen($content);
            
            // Verificar problemas comuns
            $file_data['errors'] = [];
            if (strpos($content, 'FormUIController') !== false && strpos($content, 'var FormUIController') === false) {
                $file_data['errors'][] = 'Referência a FormUIController sem definição';
            }
            if (strpos($content, 'setupPaymentMethodToggles') !== false && strpos($content, 'function setupPaymentMethodToggles') === false) {
                $file_data['errors'][] = 'Referência a setupPaymentMethodToggles sem definição';
            }
        }
        
        $results[$file] = $file_data;
    }
    
    return $results;
}

// Registrar os scripts
add_action('wp_enqueue_scripts', 'register_asaas_scripts');
do_action('wp_enqueue_scripts');

// Analisar os scripts
$script_analysis = analyze_scripts();
$file_analysis = analyze_js_files();

// Página HTML
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Scripts Asaas</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #2c3e50; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px 0; padding: 20px; }
        .success { color: #2ecc71; }
        .warning { color: #f39c12; }
        .error { color: #e74c3c; }
        .info { color: #3498db; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        tr:hover { background-color: #f5f5f5; }
        code { background: #f8f8f8; padding: 2px 4px; border-radius: 3px; font-family: monospace; }
        .object-list { background: #f8f8f8; padding: 10px; border-radius: 5px; margin: 10px 0; max-height: 400px; overflow-y: auto; }
        button { background: #3498db; color: #fff; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
    </style>
    <?php wp_head(); ?>
</head>
<body>
    <h1>Diagnóstico de Scripts Asaas</h1>
    
    <div class="card">
        <h2>Análise de Scripts Registrados</h2>
        <table>
            <tr>
                <th>Script</th>
                <th>Registrado</th>
                <th>Enfileirado</th>
                <th>Arquivo</th>
                <th>Dependências</th>
                <th>Detalhes</th>
            </tr>
            <?php foreach ($script_analysis as $handle => $data): ?>
            <tr>
                <td><strong><?php echo $handle; ?></strong></td>
                <td>
                    <?php if ($data['registered']): ?>
                    <span class="success">✓</span>
                    <?php else: ?>
                    <span class="error">✗</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($data['enqueued']) && $data['enqueued']): ?>
                    <span class="success">✓</span>
                    <?php else: ?>
                    <span class="error">✗</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($data['exists'])): ?>
                        <?php if ($data['exists'] === 'Externa'): ?>
                        <span class="info">Externa</span>
                        <?php elseif ($data['exists']): ?>
                        <span class="success">✓ (<?php echo number_format($data['size'] / 1024, 2); ?> KB)</span>
                        <?php else: ?>
                        <span class="error">✗ (Arquivo não encontrado)</span>
                        <?php endif; ?>
                    <?php else: ?>
                    <span class="error">?</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($data['deps']) && !empty($data['deps'])): ?>
                    <?php echo implode(', ', $data['deps']); ?>
                    <?php else: ?>
                    <span class="info">Nenhuma</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($data['exports'])): ?>
                    <strong>Exporta:</strong> <?php echo $data['exports']; ?><br>
                    <?php endif; ?>
                    <?php if (isset($data['functions']) && !empty($data['functions'])): ?>
                    <strong>Funções:</strong> <?php echo implode(', ', array_slice($data['functions'], 0, 5)); ?>
                    <?php if (count($data['functions']) > 5): ?>...<?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <div class="card">
        <h2>Análise de Arquivos JavaScript</h2>
        <table>
            <tr>
                <th>Arquivo</th>
                <th>Status</th>
                <th>Tamanho</th>
                <th>Modificado</th>
                <th>Problemas</th>
            </tr>
            <?php foreach ($file_analysis as $file => $data): ?>
            <tr>
                <td><strong><?php echo $file; ?></strong></td>
                <td>
                    <?php if ($data['exists']): ?>
                    <span class="success">✓</span>
                    <?php else: ?>
                    <span class="error">✗</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($data['exists']): ?>
                    <?php echo number_format($data['size'] / 1024, 2); ?> KB
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($data['exists']): ?>
                    <?php echo $data['modified']; ?>
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($data['errors']) && !empty($data['errors'])): ?>
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($data['errors'] as $error): ?>
                        <li class="error"><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <span class="success">Nenhum problema detectado</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <div class="card">
        <h2>Verificação de Objetos JavaScript</h2>
        <p>Esta seção verifica se os objetos JavaScript necessários foram carregados corretamente.</p>
        <button id="check-objects">Verificar Objetos JavaScript</button>
        <div id="js-objects-result" class="object-list"></div>
    </div>
    
    <div class="card">
        <h2>Teste de Funções</h2>
        <p>Esta seção testa as funções JavaScript críticas para exibição do QR Code PIX.</p>
        <button id="test-pix-display">Testar Exibição de QR Code</button>
        <div id="pix-test-result"></div>
    </div>
    
    <script>
    document.getElementById('check-objects').addEventListener('click', function() {
        var result = document.getElementById('js-objects-result');
        result.innerHTML = '<h3>Verificando objetos...</h3>';
        
        var html = '<table><tr><th>Objeto</th><th>Status</th><th>Tipo</th><th>Propriedades/Métodos</th></tr>';
        
        var objects = [
            'jQuery', 
            'AsaasFormUtils', 
            'AsaasFormUI', 
            'AsaasFormAjax', 
            'ajax_object'
        ];
        
        var allLoaded = true;
        
        objects.forEach(function(name) {
            var exists = window[name] !== undefined;
            if (!exists) allLoaded = false;
            
            html += '<tr>';
            html += '<td><strong>' + name + '</strong></td>';
            html += '<td>' + (exists ? 
                '<span class="success">Disponível ✓</span>' : 
                '<span class="error">Indisponível ✗</span>') + '</td>';
            
            html += '<td>' + (exists ? typeof window[name] : '-') + '</td>';
            
            html += '<td>';
            if (exists && typeof window[name] === 'object' && window[name] !== null) {
                var props = [];
                for (var prop in window[name]) {
                    if (typeof window[name][prop] === 'function') {
                        props.push('<code>' + prop + '()</code>');
                    } else {
                        props.push('<code>' + prop + '</code>');
                    }
                }
                if (props.length > 0) {
                    html += props.join(', ');
                }
            } else {
                html += '-';
            }
            html += '</td></tr>';
        });
        
        html += '</table>';
        
        // Verificar especificamente as funções críticas
        html += '<h3>Funções críticas:</h3>';
        html += '<table><tr><th>Função</th><th>Status</th></tr>';
        
        var criticalFunctions = [
            { object: 'AsaasFormUI', method: 'displaySuccess' },
            { object: 'AsaasFormUI', method: 'displayMessage' },
            { object: 'AsaasFormUI', method: 'setupPaymentMethodToggles' },
            { object: 'AsaasFormAjax', method: 'showDonationSuccess' }
        ];
        
        criticalFunctions.forEach(function(func) {
            var available = window[func.object] !== undefined && 
                            typeof window[func.object][func.method] === 'function';
            
            html += '<tr>';
            html += '<td><strong>' + func.object + '.' + func.method + '</strong></td>';
            html += '<td>' + (available ? 
                '<span class="success">Disponível ✓</span>' : 
                '<span class="error">Indisponível ✗</span>') + '</td>';
            html += '</tr>';
            
            if (!available) allLoaded = false;
        });
        
        html += '</table>';
        
        // Resumo
        html += '<h3>Resumo:</h3>';
        html += '<p>' + (allLoaded ? 
            '<span class="success">Todos os objetos e funções necessários estão disponíveis!</span>' : 
            '<span class="error">Nem todos os objetos/funções necessários estão disponíveis. Verifique os erros acima.</span>') + '</p>';
        
        result.innerHTML = html;
        
        // Verificar console por erros
        if (console.error) {
            var originalError = console.error;
            console.error = function() {
                var args = Array.prototype.slice.call(arguments);
                result.innerHTML += '<div class="error"><strong>Erro no console:</strong> ' + args.join(' ') + '</div>';
                originalError.apply(console, arguments);
            };
        }
    });
    
    document.getElementById('test-pix-display').addEventListener('click', function() {
        var result = document.getElementById('pix-test-result');
        result.innerHTML = '<h3>Testando exibição de QR Code...</h3>';
        
        // Dados de teste para o PIX
        var testData = {
            payment_method: 'pix',
            pix_code: 'iVBORw0KGgoAAAANSUhEUgAAAMgAAADIAQAAAACFI5MzAAAA8klEQVR4Xu2WwW7DMAxD9dH/f7n7UkCNbMnNMHQnGAZiPhCiKIlaW758+Tj5QFtdXTu3xLrYvqpqQDvzCacQjXhHvJnvOmJkk01yidJJ3onoJEYi6iR9Et1JfxKnmXHk/vEmHVFJDk9US/xJdCNeiBKRdgR7JHjihQTPBE/UXyJQNRJgnmjbmMiMz9FiJsZ9P7uZQd5DLaK6v5Xwx1A3/jJM7FDKdqT0uFdIwAE1EEiUjUCC2vgaO8GlZBJh22gL0TYiUWbCr0TEQsyE341gE8NIMGYw5Yh3HQk65wkyZ4LNPA5sEwSJYJCPwZDQr18+Wn4BDFT+ENg0V4gAAAAASUVORK5CYII=',
            pix_text: '00020101021226860014br.gov.bcb.pix2564pix-qrcode.asaas.com/emv/2c6efd4b',
            value: 10.00
        };
        
        // Criar um form de teste
        var testForm = document.createElement('form');
        testForm.className = 'single-donation-form';
        testForm.setAttribute('data-donation-type', 'single');
        result.appendChild(testForm);
        
        try {
            if (typeof AsaasFormUI !== 'undefined' && typeof AsaasFormUI.displaySuccess === 'function') {
                AsaasFormUI.displaySuccess(testForm, testData, 'single');
                result.innerHTML += '<p class="success">AsaasFormUI.displaySuccess chamado com sucesso!</p>';
            } else {
                result.innerHTML += '<p class="error">AsaasFormUI.displaySuccess não está disponível</p>';
                
                // Tentar método alternativo
                if (typeof AsaasFormAjax !== 'undefined' && typeof AsaasFormAjax.showDonationSuccess === 'function') {
                    AsaasFormAjax.showDonationSuccess(testForm, testData, 'single');
                    result.innerHTML += '<p class="success">AsaasFormAjax.showDonationSuccess chamado com sucesso!</p>';
                } else {
                    // Implementação manual como fallback
                    result.innerHTML += '<p class="warning">Implementação manual como fallback:</p>';
                    var fallbackHtml = '<div class="pix-success">' +
                        '<h2 class="success-title">Só mais um passo!</h2>' +
                        '<p>Leia o QR Code ou copie o código abaixo para realizar sua doação</p>' +
                        '<div class="pix-info">' +
                            '<div class="info-row">' +
                                '<span class="info-label">Valor:</span>' +
                                '<span class="info-value">R$ ' + (testData.value).toFixed(2).replace('.', ',') + '</span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="pix-qrcode">' +
                            '<img src="data:image/png;base64,' + testData.pix_code + '" alt="QR Code PIX">' +
                        '</div>' +
                        '<textarea id="pix-code-text" class="pix-code-text" readonly>' + testData.pix_text + '</textarea>' +
                        '<button class="btn btn-copy-wide" onclick="alert(\'Código copiado!\')">Copiar</button>' +
                    '</div>';
                    
                    var fallbackDiv = document.createElement('div');
                    fallbackDiv.innerHTML = fallbackHtml;
                    testForm.style.display = 'none';
                    testForm.parentNode.insertBefore(fallbackDiv, testForm);
                }
            }
        } catch (e) {
            result.innerHTML += '<p class="error">Erro ao testar exibição de QR Code: ' + e.message + '</p>';
            console.error('Erro ao testar exibição de QR Code:', e);
        }
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>