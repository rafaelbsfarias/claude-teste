<?php
// Ativar relatório de erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carregar WordPress
$wp_load_path = realpath(dirname(__FILE__) . '/../../../../wp-load.php');
if (!file_exists($wp_load_path)) {
    die('Não foi possível localizar wp-load.php. Caminho verificado: ' . $wp_load_path);
}
require_once $wp_load_path;

// Definir diretório do plugin
$plugin_dir = dirname(dirname(__FILE__));
$plugin_url = plugins_url('', dirname(__FILE__));

// Dados simulados para teste
$test_data = [
    'payment_method' => 'pix',
    'pix_code' => 'iVBORw0KGgoAAAANSUhEUgAAAMgAAADIAQAAAACFI5MzAAAA8klEQVR4Xu2WwW7DMAxD9dH/f7n7UkCNbMnNMHQnGAZiPhCiKIlaW758+Tj5QFtdXTu3xLrYvqpqQDvzCacQjXhHvJnvOmJkk01yidJJ3onoJEYi6iR9Et1JfxKnmXHk/vEmHVFJDk9US/xJdCNeiBKRdgR7JHjihQTPBE/UXyJQNRJgnmjbmMiMz9FiJsZ9P7uZQd5DLaK6v5Xwx1A3/jJM7FDKdqT0uFdIwAE1EEiUjUCC2vgaO8GlZBJh22gL0TYiUWbCr0TEQsyE341gE8NIMGYw5Yh3HQk65wkyZ4LNPA5sEwSJYJCPwZDQr18+Wn4BDFT+ENg0V4gAAAAASUVORK5CYII=',
    'pix_text' => '00020101021226860014br.gov.bcb.pix2564pix-qrcode.asaas.com/emv/2c6efd4b',
    'value' => 10.00,
    'customer_name' => 'Cliente Teste'
];

// Função para registrar scripts
function register_test_scripts() {
    global $plugin_url, $plugin_dir;
    
    // Carregar scripts INDIVIDUALMENTE para observar ordem de carregamento
    wp_enqueue_script('jquery', includes_url('/js/jquery/jquery.min.js'), [], false);
    
    // Verificar e registrar cada arquivo separadamente para melhor diagnóstico
    $js_files = [
        'form-utils' => $plugin_dir . '/assets/frontend/js/form-utils.js',
        'form-ui' => $plugin_dir . '/assets/frontend/js/form-ui.js',
        'form-ajax' => $plugin_dir . '/assets/frontend/js/form-ajax.js',
        'form-masks' => $plugin_dir . '/assets/frontend/js/form-masks.js'
    ];
    
    foreach ($js_files as $handle => $path) {
        if (file_exists($path)) {
            wp_enqueue_script(
                'asaas-' . $handle, 
                $plugin_url . '/assets/frontend/js/' . $handle . '.js', 
                ['jquery'], 
                filemtime($path) // Usar filemtime para evitar cache durante testes
            );
        }
    }
    
    // Incluir CSS para testes
    if (file_exists($plugin_dir . '/assets/frontend/css/form-style.css')) {
        wp_enqueue_style(
            'asaas-form-style', 
            $plugin_url . '/assets/frontend/css/form-style.css',
            [],
            filemtime($plugin_dir . '/assets/frontend/css/form-style.css')
        );
    }
    
    // Script de monitoramento para o teste
    wp_add_inline_script('asaas-form-ajax', 'console.log("Script de teste carregado");', 'after');
}

add_action('wp_enqueue_scripts', 'register_test_scripts');
do_action('wp_enqueue_scripts');

echo '<div class="card">';
echo '<h2>Depuração de Caminhos</h2>';
echo '<p>Plugin Dir: <code>' . esc_html($plugin_dir) . '</code></p>';
echo '<p>Plugin URL: <code>' . esc_html($plugin_url) . '</code></p>';
echo '<p>Script URL (form-ui.js): <code>' . esc_html($plugin_url . '/assets/frontend/js/form-ui.js') . '</code></p>';

// Verificar se os arquivos existem no servidor
$files_to_check = [
    'form-utils.js' => $plugin_dir . '/assets/frontend/js/form-utils.js',
    'form-ui.js' => $plugin_dir . '/assets/frontend/js/form-ui.js',
    'form-ajax.js' => $plugin_dir . '/assets/frontend/js/form-ajax.js',
    'form-masks.js' => $plugin_dir . '/assets/frontend/js/form-masks.js',
    'form-style.css' => $plugin_dir . '/assets/frontend/css/form-style.css',
];

echo '<h3>Verificação de Arquivos:</h3><ul>';
foreach ($files_to_check as $name => $path) {
    echo '<li>' . esc_html($name) . ': ' . (file_exists($path) ? 
        '<span style="color:green">Existe</span>' : 
        '<span style="color:red">Não existe</span> (procurado em: ' . esc_html($path) . ')') . '</li>';
}
echo '</ul></div>';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de AsaasFormUI.displaySuccess</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 960px; margin: 0 auto; padding: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #333; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin: 20px 0; padding: 20px; }
        .success { color: #2ecc71; }
        .warning { color: #f39c12; }
        .error { color: #e74c3c; }
        .code-block { font-family: monospace; background: #f5f5f5; padding: 15px; border-radius: 5px; overflow: auto; }
        button { background: #3498db; color: #fff; border: none; padding: 12px 18px; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px; }
        button:hover { background: #2980b9; }
        button:disabled { background: #95a5a6; cursor: not-allowed; }
        .log-container { 
            background: #2c3e50; 
            color: #ecf0f1; 
            border-radius: 5px; 
            padding: 10px 15px; 
            font-family: monospace; 
            height: 250px; 
            overflow: auto;
            margin-top: 10px;
        }
        .log-entry { margin: 5px 0; border-bottom: 1px solid #34495e; padding-bottom: 5px; }
        .log-info { color: #3498db; }
        .log-success { color: #2ecc71; }
        .log-warning { color: #f39c12; }
        .log-error { color: #e74c3c; }
        .test-section { margin: 20px 0; border-left: 4px solid #3498db; padding-left: 15px; }
        pre { margin: 0; }
        
        #form-ui-content, #form-prototype, 
        #form-constructor { white-space: pre-wrap; font-size: 14px; }
        
        .script-status { display: flex; justify-content: space-between; background: #f8f9fa; padding: 10px; border-left: 4px solid #6c757d; margin: 10px 0; }
        .script-status .name { font-weight: bold; }
        .script-status .status-available { color: #28a745; }
        .script-status .status-missing { color: #dc3545; }
    </style>
    <?php wp_head(); ?>
</head>
<body>
    <h1>Diagnóstico do AsaasFormUI.displaySuccess</h1>
    
    <div class="card">
        <h2>1. Status dos Scripts</h2>
        <div id="script-status"></div>
        <p>Ver console do navegador para detalhes adicionais (F12)</p>
    </div>
    
    <div class="card">
        <h2>2. Inspeção do Objeto AsaasFormUI</h2>
        <div class="test-section">
            <h3>AsaasFormUI Disponível?</h3>
            <div id="ui-availability"></div>
        </div>
        
        <div class="test-section">
            <h3>Propriedades e Métodos:</h3>
            <pre id="ui-properties" class="code-block"></pre>
        </div>
        
        <div class="test-section">
            <h3>Código do AsaasFormUI (toString):</h3>
            <pre id="form-ui-content" class="code-block"></pre>
        </div>
        
        <div class="test-section">
            <h3>Protótipo da displaySuccess:</h3>
            <pre id="form-prototype" class="code-block"></pre>
        </div>
    </div>
    
    <div class="card">
        <h2>3. Teste de Chamada Direta</h2>
        <div class="test-section">
            <p>Testa a chamada direta da função displaySuccess com dados simulados.</p>
            <form id="test-form" class="single-donation-form" data-donation-type="single">
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" id="name" name="name" value="Cliente de Teste">
                </div>
                
                <div class="form-group">
                    <label for="value">Valor</label>
                    <input type="number" id="value" name="value" value="10.00">
                </div>
                
                <div class="form-group">
                    <label>Método de Pagamento</label>
                    <div>
                        <input type="radio" id="payment_method_pix" name="payment_method" value="pix" checked>
                        <label for="payment_method_pix">PIX</label>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary">Doar</button>
            </form>
            
            <div class="buttons-container">
                <button id="test-direct-call" class="btn-primary">Chamar displaySuccess diretamente</button>
                <button id="test-fallback-call" class="btn-primary">Chamar showDonationSuccess (fallback)</button>
                <button id="test-manual-instance" class="btn-primary">Criar e testar instância manual</button>
            </div>
            
            <div id="direct-call-result"></div>
        </div>
        
        <div class="test-section">
            <h3>Log de Execução:</h3>
            <div id="test-log" class="log-container"></div>
        </div>
    </div>
    
    <div class="card">
        <h2>4. Inspecionar Código-fonte</h2>
        <div class="test-section">
            <h3>form-ui.js</h3>
            <div id="form-ui-file" class="code-block">
                <p>Carregando conteúdo do arquivo...</p>
            </div>
            <button id="reload-file" class="btn-primary">Recarregar arquivo</button>
        </div>
    </div>
    
    <div class="card">
        <h2>5. Função displaySuccess Manual</h2>
        <div class="test-section">
            <p>Implementação manual da função displaySuccess para teste:</p>
            <button id="test-manual-implementation" class="btn-primary">Testar Implementação Manual</button>
            <div id="manual-implementation-result"></div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var logContainer = document.getElementById('test-log');
        var testForm = document.getElementById('test-form');
        var directCallResult = document.getElementById('direct-call-result');
        
        // Adicionar log para debugging
        function addLog(message, type) {
            var logEntry = document.createElement('div');
            logEntry.className = 'log-entry log-' + (type || 'info');
            logEntry.innerHTML = '<pre>' + message + '</pre>';
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
            console.log((type || 'INFO') + ': ' + message);
        }
        
        // Verificar status dos scripts
        function checkScriptStatus() {
            var scriptStatusDiv = document.getElementById('script-status');
            var scripts = [
                { name: 'jQuery', object: window.jQuery },
                { name: 'AsaasFormUtils', object: window.AsaasFormUtils },
                { name: 'AsaasFormUI', object: window.AsaasFormUI },
                { name: 'AsaasFormAjax', object: window.AsaasFormAjax }
            ];
            
            scripts.forEach(function(script) {
                var status = script.object ? 'disponível' : 'não disponível';
                var statusClass = script.object ? 'status-available' : 'status-missing';
                
                var scriptStatusEntry = document.createElement('div');
                scriptStatusEntry.className = 'script-status';
                scriptStatusEntry.innerHTML = 
                    '<span class="name">' + script.name + ':</span>' +
                    '<span class="' + statusClass + '">' + status + '</span>';
                
                scriptStatusDiv.appendChild(scriptStatusEntry);
                addLog('Script ' + script.name + ': ' + status, script.object ? 'success' : 'error');
            });
        }
        
        // Inspecionar o objeto AsaasFormUI
        function inspectAsaasFormUI() {
            var uiAvailability = document.getElementById('ui-availability');
            var uiProperties = document.getElementById('ui-properties');
            var formUiContent = document.getElementById('form-ui-content');
            var formPrototype = document.getElementById('form-prototype');
            
            if (window.AsaasFormUI) {
                uiAvailability.innerHTML = '<span class="success">✓ AsaasFormUI está disponível</span>';
                addLog('AsaasFormUI está disponível', 'success');
                
                // Listar propriedades
                try {
                    var props = [];
                    for (var prop in window.AsaasFormUI) {
                        var type = typeof window.AsaasFormUI[prop];
                        props.push(prop + ' (' + type + ')');
                    }
                    uiProperties.textContent = props.join('\n');
                    addLog('Propriedades encontradas: ' + props.length, 'info');
                } catch (e) {
                    uiProperties.textContent = 'Erro ao listar propriedades: ' + e.message;
                    addLog('Erro ao listar propriedades: ' + e.message, 'error');
                }
                
                // Mostrar o conteúdo da função
                try {
                    formUiContent.textContent = 'AsaasFormUI: ' + window.AsaasFormUI.toString();
                    addLog('Conteúdo do objeto obtido com sucesso', 'success');
                } catch (e) {
                    formUiContent.textContent = 'Erro ao obter conteúdo: ' + e.message;
                    addLog('Erro ao obter conteúdo: ' + e.message, 'error');
                }
                
                // Analisar a função displaySuccess
                try {
                    if (typeof window.AsaasFormUI.displaySuccess === 'function') {
                        formPrototype.textContent = 'displaySuccess: ' + window.AsaasFormUI.displaySuccess.toString();
                        addLog('Função displaySuccess encontrada', 'success');
                    } else {
                        formPrototype.textContent = 'displaySuccess não é uma função (tipo: ' + 
                            typeof window.AsaasFormUI.displaySuccess + ')';
                        addLog('displaySuccess não é uma função', 'warning');
                    }
                } catch (e) {
                    formPrototype.textContent = 'Erro ao analisar displaySuccess: ' + e.message;
                    addLog('Erro ao analisar displaySuccess: ' + e.message, 'error');
                }
            } else {
                uiAvailability.innerHTML = '<span class="error">✗ AsaasFormUI não está disponível</span>';
                uiProperties.textContent = 'N/A';
                formUiContent.textContent = 'N/A';
                formPrototype.textContent = 'N/A';
                addLog('AsaasFormUI não está disponível', 'error');
            }
        }
        
        // Testar chamada direta da função displaySuccess
        document.getElementById('test-direct-call').addEventListener('click', function() {
            addLog('Tentando chamar displaySuccess diretamente...', 'info');
            directCallResult.innerHTML = '';
            
            var testData = <?php echo json_encode($test_data); ?>;
            
            try {
                if (window.AsaasFormUI && typeof window.AsaasFormUI.displaySuccess === 'function') {
                    // Clone o form para não afetar o original
                    var clonedForm = testForm.cloneNode(true);
                    directCallResult.appendChild(clonedForm);
                    
                    addLog('Parâmetros: ' + JSON.stringify({
                        form: 'HTMLFormElement',
                        data: testData,
                        donationType: 'single'
                    }, null, 2), 'info');
                    
                    // Chamar a função
                    var result = window.AsaasFormUI.displaySuccess(clonedForm, testData, 'single');
                    
                    addLog('displaySuccess executado com sucesso!', 'success');
                    addLog('Resultado: ' + (result !== false ? 'Retornou valor' : 'Falha'), 
                        result !== false ? 'success' : 'warning');
                } else {
                    throw new Error('AsaasFormUI.displaySuccess não é uma função');
                }
            } catch (e) {
                addLog('Erro ao chamar displaySuccess: ' + e.message, 'error');
                addLog('Stack: ' + e.stack, 'error');
                directCallResult.innerHTML = '<div class="error">Erro: ' + e.message + '</div>';
            }
        });
        
        // Testar chamada do fallback
        document.getElementById('test-fallback-call').addEventListener('click', function() {
            addLog('Tentando chamar showDonationSuccess (fallback)...', 'info');
            directCallResult.innerHTML = '';
            
            var testData = <?php echo json_encode($test_data); ?>;
            
            try {
                if (window.AsaasFormAjax && typeof window.AsaasFormAjax.showDonationSuccess === 'function') {
                    // Clone o form para não afetar o original
                    var clonedForm = testForm.cloneNode(true);
                    directCallResult.appendChild(clonedForm);
                    
                    // Chamar a função
                    window.AsaasFormAjax.showDonationSuccess(clonedForm, testData, 'single');
                    
                    addLog('showDonationSuccess executado com sucesso!', 'success');
                } else {
                    throw new Error('AsaasFormAjax.showDonationSuccess não é uma função');
                }
            } catch (e) {
                addLog('Erro ao chamar showDonationSuccess: ' + e.message, 'error');
                directCallResult.innerHTML = '<div class="error">Erro: ' + e.message + '</div>';
            }
        });
        
        // Testar criação manual de instância com displaySuccess
        document.getElementById('test-manual-instance').addEventListener('click', function() {
            addLog('Criando instância manual de AsaasFormUI...', 'info');
            directCallResult.innerHTML = '';
            
            try {
                // Implementação manual da função displaySuccess
                window.AsaasFormUI = window.AsaasFormUI || {};
                
                // Definir a função manualmente
                window.AsaasFormUI.displaySuccess = function(form, data, donationType) {
                    addLog('Função displaySuccess MANUAL chamada com sucesso!', 'success');
                    
                    // Verificar se é PIX
                    if (donationType === 'single' && data.payment_method === 'pix') {
                        // Ocultar o formulário
                        form.style.display = 'none';
                        
                        // Criar o elemento de sucesso do PIX
                        var successDiv = document.createElement('div');
                        successDiv.className = 'pix-success';
                        successDiv.innerHTML = `
                            <h2 class="success-title">PIX Manual: Só mais um passo!</h2>
                            <p>Leia o QR Code ou copie o código abaixo para realizar sua doação</p>
                            <div class="pix-qrcode">
                                <img src="data:image/png;base64,${data.pix_code}" alt="QR Code PIX">
                            </div>
                            <textarea class="pix-code-text" readonly>${data.pix_text}</textarea>
                            <button class="btn btn-primary">Copiar</button>
                        `;
                        
                        // Inserir após o formulário
                        form.parentNode.insertBefore(successDiv, form.nextSibling);
                        return true;
                    }
                    
                    return false;
                };
                
                addLog('Função displaySuccess adicionada manualmente ao AsaasFormUI', 'success');
                
                // Agora testar
                var testData = <?php echo json_encode($test_data); ?>;
                var clonedForm = testForm.cloneNode(true);
                directCallResult.appendChild(clonedForm);
                
                var result = window.AsaasFormUI.displaySuccess(clonedForm, testData, 'single');
                addLog('Teste com função manual: ' + (result ? 'Sucesso' : 'Falhou'), result ? 'success' : 'error');
                
            } catch (e) {
                addLog('Erro ao criar/testar instância manual: ' + e.message, 'error');
                directCallResult.innerHTML = '<div class="error">Erro: ' + e.message + '</div>';
            }
        });
        
        // Carregar e mostrar o conteúdo do arquivo form-ui.js
        function loadFileContent() {
            var formUiFile = document.getElementById('form-ui-file');
            
            fetch('<?php echo $plugin_url; ?>/assets/frontend/js/form-ui.js')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar o arquivo: ' + response.status);
                    }
                    return response.text();
                })
                .then(content => {
                    formUiFile.innerHTML = '<pre>' + escapeHtml(content) + '</pre>';
                    addLog('Arquivo form-ui.js carregado com sucesso', 'success');
                })
                .catch(error => {
                    formUiFile.innerHTML = '<div class="error">Erro ao carregar o arquivo: ' + error.message + '</div>';
                    addLog('Erro ao carregar arquivo: ' + error.message, 'error');
                });
        }
        
        document.getElementById('reload-file').addEventListener('click', loadFileContent);
        
        // Testar implementação manual completa
        document.getElementById('test-manual-implementation').addEventListener('click', function() {
            var manualImplementationResult = document.getElementById('manual-implementation-result');
            manualImplementationResult.innerHTML = '';
            
            addLog('Testando implementação manual completa...', 'info');
            
            try {
                // Implementação manual da função displaySuccess
                function manualDisplaySuccess(form, data, donationType) {
                    // Ocultar elementos do formulário
                    form.style.display = 'none';
                    
                    // Verificar se os dados estão aninhados (comum em respostas de API)
                    const responseData = data.data || data;
                    
                    // Obter dados da resposta
                    let value = responseData.value || responseData.donation_value || 0;
                    let formattedValue = (parseFloat(value) || 0).toFixed(2).replace('.', ',');
                    let paymentMethod = responseData.payment_method || '';
                    
                    // Cria o elemento de mensagem de sucesso
                    const successDiv = document.createElement('div');
                    successDiv.className = 'donation-success-container';
                    
                    // Para pagamento PIX
                    if (donationType === 'single' && paymentMethod === 'pix') {
                        // Dados específicos do PIX
                        let pixCode = responseData.pix_code || '';
                        let pixText = responseData.pix_text || '';
                        
                        // HTML para página de sucesso do PIX
                        successDiv.innerHTML = `
                            <div class="pix-success">
                                <h2 class="success-title">Implementação Manual: Só mais um passo!</h2>
                                <p>Leia o QR Code ou copie o código abaixo para realizar sua doação</p>
                                <div class="pix-info">
                                    <div class="info-row">
                                        <span class="info-label">Valor:</span>
                                        <span class="info-value">R$ ${formattedValue}</span>
                                    </div>
                                </div>
                                
                                <div class="pix-qrcode">
                                    <img src="data:image/png;base64,${pixCode}" alt="QR Code PIX">
                                </div>
                                
                                <textarea class="pix-code-text" readonly>${pixText}</textarea>
                                
                                <button class="btn btn-copy-wide">Copiar</button>
                            </div>
                        `;
                    } else {
                        // Mensagem genérica para outros tipos de pagamento
                        successDiv.innerHTML = `
                            <h2>Doação realizada com sucesso!</h2>
                            <p>Método: ${paymentMethod}</p>
                            <p>Valor: R$ ${formattedValue}</p>
                        `;
                    }
                    
                    // Inserir após o formulário
                    form.parentNode.insertBefore(successDiv, form.nextSibling);
                    return true;
                }
                
                // Clone o form para não afetar o original
                var clonedForm = testForm.cloneNode(true);
                manualImplementationResult.appendChild(clonedForm);
                
                var testData = <?php echo json_encode($test_data); ?>;
                
                // Chamar a função manual
                var result = manualDisplaySuccess(clonedForm, testData, 'single');
                
                addLog('Implementação manual testada com sucesso!', 'success');
            } catch (e) {
                addLog('Erro na implementação manual: ' + e.message, 'error');
                manualImplementationResult.innerHTML = '<div class="error">Erro: ' + e.message + '</div>';
            }
        });
        
        // Utilitário para escapar HTML
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Executar verificações iniciais
        checkScriptStatus();
        inspectAsaasFormUI();
        loadFileContent();
        
        // Log inicial
        addLog('Diagnóstico iniciado. Verifique também o console do navegador (F12).', 'info');
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>