<?php
// filepath: c:\laragon\www\meu-lindo-wp\wp-content\plugins\asaas-easy-subscription-plugin\test-public-user.php

// Carrega o WordPress
require_once dirname(__FILE__) . '/../../../wp-load.php';

// Força logout para simular um usuário deslogado
wp_logout();

// Cabeçalhos e estilos básicos
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Doação - Usuário Deslogado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        h1, h2, h3 {
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .test-section {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .button {
            background: #0073aa;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .button:hover {
            background: #005c8a;
        }
        #results {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #0073aa;
        }
        code {
            background: #f1f1f1;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Teste de Doação - Usuário Deslogado</h1>';

// Verificar status do usuário
echo '<div class="test-section">
        <h2>Status do Usuário</h2>';
if (is_user_logged_in()) {
    echo '<p class="error">ERRO: Usuário está logado. Este teste deve ser executado com um usuário deslogado.</p>';
} else {
    echo '<p class="success">OK: Usuário está deslogado, como esperado.</p>';
}
echo '</div>';

// Teste de verificação do nonce
echo '<div class="test-section">
        <h2>Teste de Verificação de Nonce</h2>';

$nonce = wp_create_nonce('asaas_public_form');
echo '<p>Nonce público gerado: <code>' . $nonce . '</code></p>';

// Verifica usando a classe do plugin se disponível
if (class_exists('Nonce_Manager')) {
    $verification = Nonce_Manager::verify_nonce($nonce, 'asaas_public_form');
    if ($verification) {
        echo '<p class="success">OK: Nonce verificado com sucesso usando Nonce_Manager.</p>';
    } else {
        echo '<p class="error">ERRO: Nonce falhou na verificação usando Nonce_Manager.</p>';
    }
} else {
    // Verificação padrão do WordPress
    $verification = wp_verify_nonce($nonce, 'asaas_public_form');
    if ($verification) {
        echo '<p class="success">OK: Nonce verificado com sucesso usando wp_verify_nonce().</p>';
    } else {
        echo '<p class="error">ERRO: Nonce falhou na verificação usando wp_verify_nonce().</p>';
    }
}
echo '</div>';

// Obter a URL do admin-ajax.php
$ajax_url = admin_url('admin-ajax.php');

// Teste de simulação de submissão AJAX
echo '<div class="test-section">
        <h2>Simulação de Submissão de Formulário</h2>
        <p>Este teste simula uma requisição AJAX para processar uma doação.</p>
        <p>Clique no botão abaixo para executar o teste:</p>
        <button id="test-ajax" class="button">Executar Teste AJAX</button>
        <div id="results">Aguardando execução do teste...</div>
        
        <script>
            document.getElementById("test-ajax").addEventListener("click", function() {
                var resultsDiv = document.getElementById("results");
                resultsDiv.innerHTML = "<p>Enviando requisição...</p>";
                
                // Criar dados simulados do formulário
                var formData = new FormData();
                formData.append("action", "process_donation_form");
                formData.append("nonce", "' . $nonce . '");
                formData.append("form_type", "single_donation");
                formData.append("donation_value", "10,00");
                formData.append("payment_method", "pix");
                formData.append("name", "Usuário Teste");
                formData.append("email", "teste@exemplo.com");
                formData.append("cpf", "123.456.789-00");
                formData.append("phone", "(11) 98765-4321");
                
                // Enviar requisição AJAX
                fetch("' . $ajax_url . '", {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin"
                })
                .then(response => {
                    // Examinar a resposta bruta primeiro
                    return response.text().then(text => {
                        try {
                            // Tenta analisar como JSON
                            return JSON.parse(text);
                        } catch (e) {
                            // Se não for JSON válido, mostra o texto bruto
                            resultsDiv.innerHTML = "<p class=\"error\">A resposta não é um JSON válido:</p>";
                            resultsDiv.innerHTML += "<pre>" + text + "</pre>";
                            resultsDiv.innerHTML += "<p class=\"error\">Erro de análise: " + e.message + "</p>";
                            throw new Error("Resposta inválida");
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        resultsDiv.innerHTML = "<p class=\"success\">Sucesso! O formulário foi processado corretamente.</p>";
                        resultsDiv.innerHTML += "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
                    } else {
                        resultsDiv.innerHTML = "<p class=\"error\">Erro: " + (data.data && data.data.message ? data.data.message : "Erro desconhecido") + "</p>";
                        resultsDiv.innerHTML += "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
                    }
                })
                .catch(error => {
                    resultsDiv.innerHTML += "<p class=\"error\">Erro na requisição: " + error + "</p>";
                });
            });
        </script>
    </div>';

// Mensagem final
echo '<div class="test-section" style="border-bottom: none;">
        <h2>Teste Manual com Formulário Real</h2>
        <p>Para testar o funcionamento completo, acesse a página que contém seu shortcode de doação enquanto estiver deslogado.</p>
        <p>Exemplo: <a href="' . home_url('/doacao') . '" target="_blank">Página de Doação</a> (ajuste o link conforme necessário)</p>
    </div>';

// Rodapé
echo '</div>
</body>
</html>';

// Registrar handler AJAX simplificado
add_action('wp_ajax_test_ajax_public', 'test_ajax_public');
add_action('wp_ajax_nopriv_test_ajax_public', 'test_ajax_public');

function test_ajax_public() {
    echo json_encode(['success' => true, 'message' => 'AJAX funcionando!']);
    wp_die();
}