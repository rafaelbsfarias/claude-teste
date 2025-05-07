<?php
// Carregar o WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Verificar se o usuário tem permissão
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado');
}

// Função para verificar e criar diretório
function check_and_create_directory($dir_path) {
    if (!file_exists($dir_path)) {
        if (wp_mkdir_p($dir_path)) {
            echo "<p style='color:green'>✓ Diretório criado: {$dir_path}</p>";
        } else {
            echo "<p style='color:red'>✗ Erro ao criar diretório: {$dir_path}</p>";
        }
    } else {
        echo "<p style='color:green'>✓ Diretório já existe: {$dir_path}</p>";
    }
}

// Função para verificar arquivo
function check_file($file_path) {
    if (file_exists($file_path)) {
        echo "<p style='color:green'>✓ Arquivo existe: {$file_path}</p>";
    } else {
        echo "<p style='color:red'>✗ Arquivo não encontrado: {$file_path}</p>";
    }
}

// Verificar estrutura de diretórios
$plugin_dir = plugin_dir_path(__FILE__);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verificação de Estrutura do Plugin</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #0073aa; }
        .section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Verificação de Estrutura do Plugin</h1>
    
    <div class="section">
        <h2>Diretórios</h2>
        <?php
        // Diretórios principais
        check_and_create_directory($plugin_dir . 'includes');
        check_and_create_directory($plugin_dir . 'templates');
        check_and_create_directory($plugin_dir . 'admin');
        check_and_create_directory($plugin_dir . 'assets');
        
        // Diretório de componentes
        check_and_create_directory($plugin_dir . 'includes/components');
        ?>
    </div>
    
    <div class="section">
        <h2>Arquivos Chave</h2>
        <?php
        // Verificar arquivos importantes
        check_file($plugin_dir . 'includes/components/form-components.php');
        check_file($plugin_dir . 'templates/v2-form-single-donation.php');
        check_file($plugin_dir . 'templates/v2-form-recurring-donation.php');
        ?>
    </div>
    
    <div class="section">
        <h2>Próximos Passos</h2>
        <p>Execute as seguintes ações:</p>
        <ol>
            <li>Se algum diretório não foi criado, crie-o manualmente.</li>
            <li>Se algum arquivo chave não existe, crie-o usando os snippets fornecidos.</li>
            <li>Execute o teste novamente após criar todos os arquivos necessários.</li>
        </ol>
        <a href="test-new-templates.php" class="button">Voltar para o Teste</a>
    </div>
</body>
</html>