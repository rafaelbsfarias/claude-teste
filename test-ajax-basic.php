<?php
// filepath: c:\laragon\www\meu-lindo-wp\wp-content\plugins\asaas-easy-subscription-plugin\test-ajax-basic.php

require_once dirname(__FILE__) . '/../../../wp-load.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Teste AJAX Básico</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        button { padding: 10px; cursor: pointer; }
        pre { background: #f5f5f5; padding: 10px; border-left: 4px solid #0073aa; }
    </style>
</head>
<body>
    <h1>Teste AJAX Básico</h1>
    <p>Este teste verifica se a chamada AJAX básica funciona para usuários deslogados.</p>
    <button id="testBasicAjax">Testar AJAX Básico</button>
    <div id="basicResult">Aguardando...</div>
    
    <h2>Teste de Formulário</h2>
    <p>Este teste simula o envio do formulário de doação.</p>
    <button id="testFormAjax">Testar Formulário</button>
    <div id="formResult">Aguardando...</div>
    
    <script>
        // Teste AJAX básico
        document.getElementById("testBasicAjax").addEventListener("click", function() {
            var data = new FormData();
            data.append("action", "asaas_test_ajax");
            
            fetch("' . admin_url("admin-ajax.php") . '", {
                method: "POST",
                body: data
            })
            .then(response => response.text())
            .then(text => {
                document.getElementById("basicResult").innerHTML = 
                    "<pre>" + text + "</pre>";
            })
            .catch(error => {
                document.getElementById("basicResult").innerHTML = 
                    "<p class=\"error\">Erro: " + error + "</p>";
            });
        });
        
        // Teste de formulário
        document.getElementById("testFormAjax").addEventListener("click", function() {
            var data = new FormData();
            data.append("action", "process_donation_form");
            data.append("nonce", "teste123"); // Valor fictício para teste
            data.append("form_type", "single_donation");
            
            // Campos obrigatórios corrigidos
            data.append("full_name", "Usuário Teste");
            data.append("email", "teste@example.com");
            data.append("cpf_cnpj", "123.456.789-00");
            data.append("phone", "(11) 98765-4321");
            data.append("donation_value", "10,00");
            data.append("payment_method", "pix");
            
            fetch("' . admin_url("admin-ajax.php") . '", {
                method: "POST",
                body: data
            })
            .then(response => response.text())
            .then(text => {
                document.getElementById("formResult").innerHTML = 
                    "<pre>" + text + "</pre>";
            })
            .catch(error => {
                document.getElementById("formResult").innerHTML = 
                    "<p class=\"error\">Erro: " + error + "</p>";
            });
        });
    </script>
</body>
</html>';