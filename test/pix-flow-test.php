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

// Definir diretório do plugin PRIMEIRO
$plugin_dir = dirname(dirname(__FILE__));
$plugin_url = plugins_url('', dirname(__FILE__));

// Log de debug
function debug_log($message, $type = 'info') {
    echo "<div class='debug-message debug-$type'><strong>$type:</strong> $message</div>";
}

// Verificar se as classes necessárias existem
$class_api_path = $plugin_dir . '/includes/class-asaas-api.php';
$class_processor_path = $plugin_dir . '/includes/class-form-processor.php';

if (!file_exists($class_api_path)) {
    debug_log("Arquivo não encontrado: $class_api_path", 'error');
} else {
    require_once $class_api_path;
    debug_log("Classe API carregada", 'success');
}

if (!file_exists($class_processor_path)) {
    debug_log("Arquivo não encontrado: $class_processor_path", 'error');
} else {
    require_once $class_processor_path;
    debug_log("Classe Form Processor carregada", 'success');
}

// Função para registrar scripts com tratamento de erros
function register_asaas_scripts() {
    global $plugin_url, $plugin_dir;
    
    // Verificar arquivos antes de registrar
    $js_files = [
        'form-utils.js' => $plugin_dir . '/assets/frontend/js/form-utils.js',
        'form-ui.js' => $plugin_dir . '/assets/frontend/js/form-ui.js',
        'form-ajax.js' => $plugin_dir . '/assets/frontend/js/form-ajax.js',
    ];
    
    foreach ($js_files as $name => $path) {
        if (!file_exists($path)) {
            debug_log("Arquivo JavaScript não encontrado: $path", 'error');
        } else {
            debug_log("Arquivo $name encontrado: $path", 'success');
        }
    }
    
    // Registrar scripts apenas se existirem
    wp_register_script('jquery', includes_url('/js/jquery/jquery.min.js'), [], false, true);
    
    if (file_exists($plugin_dir . '/assets/frontend/js/form-utils.js')) {
        wp_register_script('asaas-form-utils', $plugin_url . '/assets/frontend/js/form-utils.js', ['jquery'], '1.0', true);
        wp_enqueue_script('asaas-form-utils');
    }
    
    if (file_exists($plugin_dir . '/assets/frontend/js/form-ui.js')) {
        wp_register_script('asaas-form-ui', $plugin_url . '/assets/frontend/js/form-ui.js', ['jquery', 'asaas-form-utils'], '1.0', true);
        wp_enqueue_script('asaas-form-ui');
    }
    
    if (file_exists($plugin_dir . '/assets/frontend/js/form-ajax.js')) {
        wp_register_script('asaas-form-ajax', $plugin_url . '/assets/frontend/js/form-ajax.js', ['jquery', 'asaas-form-ui'], '1.0', true);
        wp_enqueue_script('asaas-form-ajax');
        
        wp_localize_script('asaas-form-ajax', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('asaas_nonce')
        ]);
    }
    
    // Também incluir CSS
    if (file_exists($plugin_dir . '/assets/frontend/css/form-style.css')) {
        wp_enqueue_style('asaas-form-style', $plugin_url . '/assets/frontend/css/form-style.css');
    }
}

// Registrar os scripts
add_action('wp_enqueue_scripts', 'register_asaas_scripts');
do_action('wp_enqueue_scripts');

// Dados simulados para teste
$test_data = [
    'payment_method' => 'pix',
    'pix_code' => 'iVBORw0KGgoAAAANSUhEUgAAAMgAAADIAQAAAACFI5MzAAAA8klEQVR4Xu2WwW7DMAxD9dH/f7n7UkCNbMnNMHQnGAZiPhCiKIlaW758+Tj5QFtdXTu3xLrYvqpqQDvzCacQjXhHvJnvOmJkk01yidJJ3onoJEYi6iR9Et1JfxKnmXHk/vEmHVFJDk9US/xJdCNeiBKRdgR7JHjihQTPBE/UXyJQNRJgnmjbmMiMz9FiJsZ9P7uZQd5DLaK6v5Xwx1A3/jJM7FDKdqT0uFdIwAE1EEiUjUCC2vgaO8GlZBJh22gL0TYiUWbCr0TEQsyE341gE8NIMGYw5Yh3HQk65wkyZ4LNPA5sEwSJYJCPwZDQr18+Wn4BDFT+ENg0V4gAAAAASUVORK5CYII=',
    'pix_text' => '00020101021226860014br.gov.bcb.pix2564pix-qrcode.asaas.com/emv/2c6efd4b',
    'value' => 10.00,
    'customer_name' => 'Cliente Teste'
];

// Página HTML com tratamento de erros robusto
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Fluxo de Pagamento PIX</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #2c3e50; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px 0; padding: 20px; }
        .success { color: #2ecc71; }
        .warning { color: #f39c12; }
        .error { color: #e74c3c; }
        .step { padding: 20px; margin: 10px 0; border-left: 4px solid #3498db; background: #f9f9f9; }
        button { background: #3498db; color: #fff; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #2980b9; }
        button:disabled { background: #95a5a6; cursor: not-allowed; }
        input, select { padding: 8px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        .form-group { margin-bottom: 15px; }
        .pix-qrcode { text-align: center; margin: 20px 0; }
        .pix-code-text { width: 100%; height: 80px; margin: 10px 0; }
        .debug-message { margin: 5px 0; padding: 8px; border-radius: 4px; }
        .debug-error { background: #ffebee; border-left: 4px solid #e74c3c; }
        .debug-success { background: #e8f5e9; border-left: 4px solid #2ecc71; }
        .debug-info { background: #e3f2fd; border-left: 4px solid #3498db; }
        #script-status { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .debug-section {
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .log-content {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            background: #fff;
        }
        .diagnostic-info {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .parameters-info {
            background: #fff8e1;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #ffc107;
        }
        pre {
            background: #f5f5f5;
            padding: 8px;
            border-radius: 3px;
            overflow-x: auto;
            font-size: 12px;
        }
        code {
            background: #f1f1f1;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
    <?php wp_head(); ?>
</head>
<body>
    <h1>Teste de Fluxo de Pagamento PIX</h1>
    
    <div id="script-status" class="card">
        <h2>Status dos Scripts</h2>
        <div id="script-check-result"></div>
    </div>
    
    <div class="card">
        <h2>1. Simulação de Formulário</h2>
        <form id="test-donation-form" class="single-donation-form" data-donation-type="single">
            <div class="form-group">
                <label for="name">Nome:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($test_data['customer_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="teste@exemplo.com" required>
            </div>
            
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="123.456.789-09" required>
            </div>
            
            <div class="form-group">
                <label for="donation_value">Valor da Doação:</label>
                <input type="number" id="donation_value" name="donation_value" value="<?php echo htmlspecialchars($test_data['value']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Método de Pagamento:</label>
                <div>
                    <input type="radio" id="payment_method_pix" name="payment_method" value="pix" checked>
                    <label for="payment_method_pix">PIX</label>
                </div>
                <div>
                    <input type="radio" id="payment_method_credit_card" name="payment_method" value="credit_card">
                    <label for="payment_method_credit_card">Cartão de Crédito</label>
                </div>
                <div>
                    <input type="radio" id="payment_method_boleto" name="payment_method" value="boleto">
                    <label for="payment_method_boleto">Boleto</label>
                </div>
            </div>
            
            <div class="payment-method-fields payment-method-fields-pix">
                <p>PIX selecionado - Você receberá o QR Code para pagamento após a submissão.</p>
            </div>
            
            <div class="payment-method-fields payment-method-fields-credit_card" style="display: none;">
                <p>Campos do cartão iriam aparecer aqui</p>
            </div>
            
            <div class="payment-method-fields payment-method-fields-boleto" style="display: none;">
                <p>Opções do boleto apareceriam aqui</p>
            </div>
            
            <button type="button" id="simulate-submit" class="btn-primary">Simular Envio do Formulário</button>
        </form>
    </div>
    
    <div class="card">
        <h2>2. Simulação de Processamento</h2>
        <div id="processing-steps"></div>
        <button type="button" id="simulate-processing" class="btn-primary" disabled>Simular Processamento</button>
    </div>
    
    <div class="card">
        <h2>3. Simulação de Resposta</h2>
        <div id="response-result"></div>
        <button type="button" id="simulate-response" class="btn-primary" disabled>Simular Resposta</button>
    </div>
    
    <div class="card">
        <h2>4. Exibição Final</h2>
        <div id="final-display"></div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar o status dos scripts necessários
        var scriptCheckResult = document.getElementById('script-check-result');
        var scriptsToCheck = [
            { name: 'jQuery', object: window.jQuery },
            { name: 'AsaasFormUtils', object: window.AsaasFormUtils },
            { name: 'AsaasFormUI', object: window.AsaasFormUI },
            { name: 'AsaasFormAjax', object: window.AsaasFormAjax },
            { name: 'ajax_object', object: window.ajax_object }
        ];
        
        var allScriptsLoaded = true;
        scriptsToCheck.forEach(function(script) {
            var status = script.object ? 'disponível' : 'não disponível';
            var statusClass = script.object ? 'success' : 'error';
            scriptCheckResult.innerHTML += '<div class="debug-' + statusClass + '"><strong>' + script.name + ':</strong> ' + status + '</div>';
            
            if (!script.object) {
                allScriptsLoaded = false;
            }
        });
        
        if (!allScriptsLoaded) {
            scriptCheckResult.innerHTML += '<div class="debug-error"><strong>ALERTA:</strong> Alguns scripts necessários não foram carregados! O teste pode não funcionar corretamente.</div>';
        } else {
            scriptCheckResult.innerHTML += '<div class="debug-success"><strong>OK:</strong> Todos os scripts necessários foram carregados!</div>';
        }

        // ****** ADICIONADO: Monitorar a função displaySuccess ******
        // Criar área de log para função displaySuccess
        var displaySuccessLog = document.createElement('div');
        displaySuccessLog.id = 'display-success-log';
        displaySuccessLog.className = 'debug-section';
        displaySuccessLog.innerHTML = '<h3>Log de Chamadas: displaySuccess</h3><div class="log-content"></div>';
        scriptCheckResult.parentNode.appendChild(displaySuccessLog);

        // Se AsaasFormUI existe, criar um proxy para monitorar a função displaySuccess
        if (window.AsaasFormUI && typeof AsaasFormUI.displaySuccess === 'function') {
            // Salvar a função original
            var originalDisplaySuccess = AsaasFormUI.displaySuccess;
            
            // Substituir pela versão monitorada
            AsaasFormUI.displaySuccess = function(form, data, donationType) {
                // Log dos parâmetros recebidos
                var logItem = document.createElement('div');
                logItem.className = 'debug-info';
                
                // Obter payment_method dos dados
                var paymentMethod = '';
                if (data && (data.payment_method || (data.data && data.data.payment_method))) {
                    paymentMethod = data.payment_method || (data.data && data.data.payment_method);
                }
                
                logItem.innerHTML = '<strong>displaySuccess chamada:</strong>' +
                    '<br>donationType: <code>' + donationType + '</code>' + 
                    '<br>payment_method: <code>' + paymentMethod + '</code>' +
                    '<br>data: <pre>' + JSON.stringify(data, null, 2) + '</pre>';
                
                document.querySelector('#display-success-log .log-content').appendChild(logItem);
                
                // Chamar a função original
                return originalDisplaySuccess.apply(this, arguments);
            };
            
            scriptCheckResult.innerHTML += '<div class="debug-success"><strong>Monitoramento:</strong> AsaasFormUI.displaySuccess está sendo monitorada</div>';
        } else {
            document.querySelector('#display-success-log .log-content').innerHTML = 
                '<div class="debug-error">AsaasFormUI.displaySuccess não disponível para monitoramento</div>';
        }

        // Também monitorar a função de fallback
        if (window.AsaasFormAjax && typeof AsaasFormAjax.showDonationSuccess === 'function') {
            var originalShowDonationSuccess = AsaasFormAjax.showDonationSuccess;
            
            AsaasFormAjax.showDonationSuccess = function(form, data, donationType) {
                var logItem = document.createElement('div');
                logItem.className = 'debug-warning';
                
                var paymentMethod = '';
                if (data && (data.payment_method || (data.data && data.data.payment_method))) {
                    paymentMethod = data.payment_method || (data.data && data.data.payment_method);
                }
                
                logItem.innerHTML = '<strong>FALLBACK chamado:</strong>' +
                    '<br>donationType: <code>' + donationType + '</code>' + 
                    '<br>payment_method: <code>' + paymentMethod + '</code>';
                
                document.querySelector('#display-success-log .log-content').appendChild(logItem);
                
                return originalShowDonationSuccess.apply(this, arguments);
            };
            
            scriptCheckResult.innerHTML += '<div class="debug-success"><strong>Monitoramento:</strong> AsaasFormAjax.showDonationSuccess está sendo monitorada</div>';
        }
        // ****** FIM DA ADIÇÃO ******
        
        // Referencias DOM
        var testForm = document.getElementById('test-donation-form');
        var simulateSubmitBtn = document.getElementById('simulate-submit');
        var simulateProcessingBtn = document.getElementById('simulate-processing');
        var simulateResponseBtn = document.getElementById('simulate-response');
        var processingSteps = document.getElementById('processing-steps');
        var responseResult = document.getElementById('response-result');
        var finalDisplay = document.getElementById('final-display');
        
        // Dados de formulário
        var formData = <?php echo json_encode($test_data); ?>;
        var formResponse = null;
        
        // Setup dos toggles de método de pagamento
        if (window.AsaasFormUI && typeof AsaasFormUI.setupPaymentMethodToggles === 'function') {
            try {
                AsaasFormUI.setupPaymentMethodToggles(testForm);
                scriptCheckResult.innerHTML += '<div class="debug-success"><strong>Sucesso:</strong> AsaasFormUI.setupPaymentMethodToggles executado.</div>';
            } catch (e) {
                scriptCheckResult.innerHTML += '<div class="debug-error"><strong>Erro:</strong> ' + e.message + ' ao executar AsaasFormUI.setupPaymentMethodToggles</div>';
                console.error('Erro ao executar setupPaymentMethodToggles:', e);
                
                // Implementação de fallback para os toggles
                fallbackSetupToggles();
            }
        } else {
            scriptCheckResult.innerHTML += '<div class="debug-warning"><strong>Aviso:</strong> AsaasFormUI.setupPaymentMethodToggles não disponível, usando fallback.</div>';
            fallbackSetupToggles();
        }
        
        // Função de fallback para setup de toggles
        function fallbackSetupToggles() {
            var radios = testForm.querySelectorAll('input[name="payment_method"]');
            radios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    var containers = testForm.querySelectorAll('.payment-method-fields');
                    containers.forEach(function(container) {
                        container.style.display = 'none';
                    });
                    
                    var targetContainer = testForm.querySelector('.payment-method-fields-' + radio.value);
                    if (targetContainer) {
                        targetContainer.style.display = 'block';
                    }
                });
            });
            
            // Acionar o evento para o método inicial
            var initialRadio = testForm.querySelector('input[name="payment_method"]:checked');
            if (initialRadio) {
                var event = new Event('change');
                initialRadio.dispatchEvent(event);
            }
        }
        
        // Simular envio do formulário
        simulateSubmitBtn.addEventListener('click', function() {
            try {
                // Atualizar dados do formulário
                formData.customer_name = testForm.querySelector('#name').value;
                formData.value = parseFloat(testForm.querySelector('#donation_value').value) || 10.00;
                formData.payment_method = testForm.querySelector('input[name="payment_method"]:checked').value;
                
                // Mostrar dados submetidos
                processingSteps.innerHTML = '<div class="step">' +
                    '<h3>Formulário Submetido</h3>' +
                    '<p><strong>Nome:</strong> ' + formData.customer_name + '</p>' +
                    '<p><strong>Valor:</strong> R$ ' + formData.value.toFixed(2) + '</p>' +
                    '<p><strong>Método:</strong> ' + formData.payment_method + '</p>' +
                    '</div>';
                    
                // Habilitar próximo passo
                simulateProcessingBtn.disabled = false;
                simulateSubmitBtn.disabled = true;
            } catch (e) {
                processingSteps.innerHTML = '<div class="step error">' +
                    '<h3>Erro ao processar o formulário</h3>' +
                    '<p>' + e.message + '</p>' +
                    '</div>';
                console.error('Erro ao processar o formulário:', e);
            }
        });
        
        // Simular processamento
        simulateProcessingBtn.addEventListener('click', function() {
            try {
                // Simular processamento
                processingSteps.innerHTML += '<div class="step">' +
                    '<h3>Processando Pagamento</h3>' +
                    '<p>Enviando dados para API da Asaas...</p>' +
                    '<p>Processando transação...</p>' +
                    '<p>Gerando QR Code PIX...</p>' +
                    '<p class="success">Pagamento processado com sucesso!</p>' +
                    '</div>';
                    
                // Simular a resposta da API
                formResponse = {
                    payment_method: formData.payment_method,
                    pix_code: formData.pix_code,
                    pix_text: formData.pix_text,
                    value: formData.value,
                    customer_name: formData.customer_name,
                    status: 'PENDING',
                    transaction_id: 'PIX_' + Math.floor(Math.random() * 1000000)
                };
                
                // Habilitar próximo passo
                simulateResponseBtn.disabled = false;
                simulateProcessingBtn.disabled = true;
            } catch (e) {
                processingSteps.innerHTML += '<div class="step error">' +
                    '<h3>Erro ao processar pagamento</h3>' +
                    '<p>' + e.message + '</p>' +
                    '</div>';
                console.error('Erro ao simular processamento:', e);
            }
        });
        
        // Simular resposta e exibir QR code
        simulateResponseBtn.addEventListener('click', function() {
            try {
                // Mostrar resposta
                responseResult.innerHTML = '<div class="step">' +
                    '<h3>Resposta Recebida</h3>' +
                    '<p><strong>Status:</strong> <span class="success">Sucesso</span></p>' +
                    '<p><strong>ID da Transação:</strong> ' + formResponse.transaction_id + '</p>' +
                    '<pre style="background:#f5f5f5;padding:10px;overflow:auto;">' + 
                    JSON.stringify(formResponse, null, 2) + 
                    '</pre>' +
                    '</div>';
                
                // Mostrar visualização final - Estratégia com fallbacks
                displayQRCode();
                
                // Desabilitar botão
                simulateResponseBtn.disabled = true;
            } catch (e) {
                responseResult.innerHTML += '<div class="step error">' +
                    '<h3>Erro ao processar resposta</h3>' +
                    '<p>' + e.message + '</p>' +
                    '</div>';
                console.error('Erro ao simular resposta:', e);
            }
        });
        
        // Função para exibir QR Code com fallbacks
        function displayQRCode() {
            // Limpar exibição anterior
            finalDisplay.innerHTML = '';
            
            try {
                // Adicionar informações de diagnóstico
                finalDisplay.innerHTML += '<div class="diagnostic-info">' +
                    '<h3>Informações de Diagnóstico</h3>' +
                    '<ul>' +
                    '<li><strong>AsaasFormUI disponível:</strong> ' + (window.AsaasFormUI ? 'Sim' : 'Não') + '</li>' +
                    '<li><strong>displaySuccess disponível:</strong> ' + (window.AsaasFormUI && typeof AsaasFormUI.displaySuccess === 'function' ? 'Sim' : 'Não') + '</li>' +
                    '<li><strong>AsaasFormAjax disponível:</strong> ' + (window.AsaasFormAjax ? 'Sim' : 'Não') + '</li>' +
                    '<li><strong>showDonationSuccess disponível:</strong> ' + (window.AsaasFormAjax && typeof AsaasFormAjax.showDonationSuccess === 'function' ? 'Sim' : 'Não') + '</li>' +
                    '</ul>' +
                    '</div>';
                
                // Exibir dados que serão passados para a função
                finalDisplay.innerHTML += '<div class="parameters-info">' +
                    '<h3>Parâmetros Previstos</h3>' +
                    '<p><strong>donationType:</strong> <code>single</code> (definido no código)</p>' +
                    '<p><strong>payment_method:</strong> <code>' + formResponse.payment_method + '</code> (do formResponse)</p>' +
                    '</div>';

                // Abordagem 1: Usar AsaasFormUI.displaySuccess
                if (window.AsaasFormUI && typeof AsaasFormUI.displaySuccess === 'function') {
                    // Clonar o formulário para não afetar o original
                    var clonedForm = testForm.cloneNode(true);
                    finalDisplay.appendChild(clonedForm);
                    
                    // Exibir sucesso usando a função real
                    AsaasFormUI.displaySuccess(clonedForm, formResponse, 'single');
                    
                    finalDisplay.innerHTML += '<div class="step success">' +
                        '<p>QR Code exibido com sucesso usando AsaasFormUI.displaySuccess!</p>' +
                        '</div>';
                    return; // Se funcionou, encerrar aqui
                } 
                
                // Abordagem 2: Usar AsaasFormAjax.showDonationSuccess
                if (window.AsaasFormAjax && typeof AsaasFormAjax.showDonationSuccess === 'function') {
                    // Clonar o formulário para não afetar o original
                    var clonedForm = testForm.cloneNode(true);
                    finalDisplay.appendChild(clonedForm);
                    
                    // Exibir sucesso usando a função alternativa
                    AsaasFormAjax.showDonationSuccess(clonedForm, formResponse, 'single');
                    
                    finalDisplay.innerHTML += '<div class="step warning">' +
                        '<p>QR Code exibido usando AsaasFormAjax.showDonationSuccess (fallback)!</p>' +
                        '</div>';
                    return; // Se funcionou, encerrar aqui
                }
                
                // Abordagem 3: Fallback manual se nenhum dos métodos anteriores funcionou
                finalDisplay.innerHTML = '<div class="step error">' +
                    '<p>Nenhuma função de exibição de QR Code disponível! Usando implementação manual:</p>' +
                    '</div>' +
                    '<div class="pix-success">' +
                    '<h2 class="success-title">Só mais um passo!</h2>' +
                    '<p>Leia o QR Code ou copie o código abaixo para realizar sua doação</p>' +
                    '<div class="pix-info">' +
                        '<div class="info-row">' +
                            '<span class="info-label">Valor:</span>' +
                            '<span class="info-value">R$ ' + formResponse.value.toFixed(2).replace('.', ',') + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="pix-qrcode">' +
                        '<img src="data:image/png;base64,' + formResponse.pix_code + '" alt="QR Code PIX">' +
                    '</div>' +
                    '<textarea id="pix-code-text" class="pix-code-text" readonly>' + formResponse.pix_text + '</textarea>' +
                    '<button class="btn btn-copy-wide" onclick="copyPixCode()">Copiar</button>' +
                    '</div>';
                
                // Adicionar função de cópia para o fallback manual
                window.copyPixCode = function() {
                    var pixCodeText = document.getElementById('pix-code-text');
                    if (pixCodeText) {
                        pixCodeText.select();
                        try {
                            document.execCommand('copy');
                            alert('Código PIX copiado!');
                        } catch (e) {
                            alert('Não foi possível copiar automaticamente: ' + e.message);
                        }
                    }
                };
            } catch (e) {
                finalDisplay.innerHTML = '<div class="step error">' +
                    '<h3>Erro ao exibir QR Code</h3>' +
                    '<p>' + e.message + '</p>' +
                    '<p>Stack: ' + e.stack + '</p>' +
                    '</div>';
                console.error('Erro ao exibir QR Code:', e);
            }
        }
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>