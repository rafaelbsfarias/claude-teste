<?php
// Carregar o WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Verificar se o usuário tem permissão
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado');
}

// Carregar classes necessárias
require_once ASAAS_PLUGIN_DIR . 'includes/class-form-processor.php';

// Verificar se há ação para processar
$action = isset($_POST['test_action']) ? $_POST['test_action'] : '';

// Inicializar resultado
$result = null;

// Processar a ação, se houver
if ($action === 'test_processor') {
    // Simular dados do formulário
    $test_data = [
        'full_name' => 'Usuário Teste',
        'email' => 'teste@exemplo.com',
        'cpf_cnpj' => '12345678909',
        'donation_value' => '50,00',
        'payment_method' => 'pix',
        'form_type' => 'single_donation',
        'asaas_nonce' => wp_create_nonce('asaas_nonce')
    ];
    
    // Processar usando o processador de formulários
    $processor = new Asaas_Form_Processor();
    $result = $processor->process_form($test_data);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Resposta da API</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; }
        .result { background: #f5f5f5; padding: 10px; margin-top: 20px; white-space: pre-wrap; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Teste de Resposta da API</h1>
    
    <div class="test-section">
        <h2>Teste do Processador de Formulários</h2>
        <p>Este teste simula uma submissão de formulário para o processador.</p>
        
        <form method="post">
            <input type="hidden" name="test_action" value="test_processor">
            <button type="submit">Testar Processador</button>
        </form>
        
        <?php if ($result): ?>
        <div class="result <?php echo $result['success'] ? 'success' : 'error'; ?>">
            <h3>Resultado:</h3>
            <pre><?php print_r($result); ?></pre>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="test-section">
        <h2>Estrutura dos Formulários</h2>
        <p>Comparação entre formulários antigos e novos:</p>
        
        <h3>Formulário Antigo (Campos)</h3>
        <ul>
            <?php
            // Listar os campos do formulário antigo
            $old_form = file_get_contents(ASAAS_PLUGIN_DIR . 'templates/form-single-donation.php');
            preg_match_all('/name="([^"]+)"/', $old_form, $matches);
            $old_fields = array_unique($matches[1]);
            foreach ($old_fields as $field) {
                echo "<li>{$field}</li>";
            }
            ?>
        </ul>
        
        <h3>Formulário Novo (Campos)</h3>
        <ul>
            <?php
            // Listar os campos do formulário novo
            $new_form = file_get_contents(ASAAS_PLUGIN_DIR . 'templates/v2-form-single-donation.php');
            preg_match_all('/name="([^"]+)"/', $new_form, $matches);
            $new_fields = array_unique($matches[1]);
            foreach ($new_fields as $field) {
                echo "<li>{$field}</li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>