<?php
// filepath: c:\laragon\www\meu-lindo-wp\wp-content\plugins\asaas-easy-subscription-plugin\test-pix-workflow.php

require_once dirname(__FILE__) . '/../../../wp-load.php';
require_once __DIR__ . '/admin/class-admin-settings.php';
include_once __DIR__ . '/includes/class-asaas-api.php';

echo '<h2>Teste de Fluxo Completo do PIX</h2>';

// Testar apenas se a classe existir
if (class_exists('Asaas_API_Conn')) {
    try {
        $api = new Asaas_API_Conn();
        
        // Cliente existente para teste
        $customer_id = 'cus_000006681796';
        
        echo '<p>ETAPA 1: Criando pagamento PIX para cliente ID: ' . $customer_id . '</p>';
        
        // Passo 1: Criar um pagamento com método PIX
        $payment_data = [
            'customer' => $customer_id,
            'billingType' => 'PIX',
            'value' => 10.00,
            'dueDate' => date('Y-m-d')
        ];
        
        // Obter o objeto de operações com pagamentos
        $payments_api = $api->get_payments();
        $payment_result = $payments_api->create_payment($payment_data);
        
        if (!$payment_result['success']) {
            echo '<p style="color:red;">ERRO NA ETAPA 1: Falha ao criar pagamento PIX:</p>';
            echo '<ul style="color:red;">';
            foreach ($payment_result['errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
            exit;
        }
        
        echo '<p style="color:green;">✓ ETAPA 1 CONCLUÍDA: Pagamento PIX criado com sucesso!</p>';
        echo '<ul>';
        echo '<li>ID do Pagamento: ' . $payment_result['data']['id'] . '</li>';
        echo '<li>Status: ' . $payment_result['data']['status'] . '</li>';
        echo '<li>Valor: R$ ' . number_format($payment_result['data']['value'], 2, ',', '.') . '</li>';
        echo '</ul>';
        
        // Passo 2: Obter o QR Code PIX usando o método implementado
        echo '<p>ETAPA 2: Obtendo QR Code PIX para o pagamento...</p>';
        
        $payment_id = $payment_result['data']['id'];
        $pix_result = $payments_api->get_pix_qrcode($payment_id);
        
        if (!$pix_result['success']) {
            echo '<p style="color:red;">ERRO NA ETAPA 2: Falha ao obter QR Code PIX:</p>';
            echo '<ul style="color:red;">';
            foreach ($pix_result['errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
            exit;
        }
        
        echo '<p style="color:green;">✓ ETAPA 2 CONCLUÍDA: QR Code PIX obtido com sucesso!</p>';
        
        // Passo 3: Simular o processamento do formulário
        echo '<p>ETAPA 3: Simulando dados que chegariam do formulário...</p>';
        
        // Dados que viriam do formulário
        $simulated_form_data = [
            'payment_id' => $payment_id,
            'payment_method' => 'pix',
            'payment_status' => $payment_result['data']['status'],
            'donation_value' => $payment_result['data']['value'],
            'pix_code' => $pix_result['data']['encodedImage'],
            'pix_text' => $pix_result['data']['payload']
        ];
        
        echo '<p style="color:green;">✓ ETAPA 3 CONCLUÍDA: Dados do formulário simulados com sucesso!</p>';
        
        // Passo 4: Exibir o QR Code para testar a exibição
        echo '<p>ETAPA 4: Exibindo QR Code PIX como seria mostrado ao usuário...</p>';
        
        // Criar a visualização como seria apresentada ao usuário
        echo '<div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; max-width: 500px; margin: 0 auto; text-align: center;">';
        echo '<h2 style="color: #4CAF50; font-size: 24px; font-weight: 600; margin-bottom: 20px;">Doação via PIX Gerada com Sucesso!</h2>';
        
        echo '<div style="background-color: #f9f9f9; border-radius: 6px; padding: 20px; margin-bottom: 25px;">';
        echo '<div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 5px 0;">';
        echo '<span style="font-weight: 600; color: #333; text-align: left;">Valor:</span>';
        echo '<span style="color: #333; text-align: right;">R$ ' . number_format($simulated_form_data['donation_value'], 2, ',', '.') . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div style="margin: 20px auto; max-width: 250px;">';
        echo '<img src="data:image/png;base64,' . $simulated_form_data['pix_code'] . '" style="width: 100%; height: auto; display: block;" />';
        echo '</div>';
        
        echo '<div style="display: flex; align-items: center; margin: 20px auto; max-width: 450px;">';
        echo '<input type="text" value="' . $simulated_form_data['pix_text'] . '" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px 0 0 4px; font-family: monospace; font-size: 14px; overflow: hidden; height: 42px;" readonly>';
        echo '<button style="background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 0 4px 4px 0; cursor: pointer; height: 42px; font-weight: 600;">Copiar</button>';
        echo '</div>';
        
        echo '<button style="background-color: #007bff; color: white; position: relative; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; border: none;">Fechar</button>';
        echo '</div>';
        
        echo '<p style="color:green;">✓ ETAPA 4 CONCLUÍDA: QR Code PIX exibido com sucesso!</p>';
        
    } catch (Exception $e) {
        echo '<p style="color:red;">Erro da classe: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

echo '<p>Fim do teste</p>';
?>