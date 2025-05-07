<?php

require_once dirname(__FILE__) . '/../../../wp-load.php';
require_once __DIR__ . '/admin/class-admin-settings.php';
include_once __DIR__ . '/includes/class-asaas-api.php';

echo '<h2>Teste de Criação de Pagamento Único</h2>';

// Tentar usar a classe somente se existir
if (class_exists('Asaas_API_Conn')) {
    try {
        $api = new Asaas_API_Conn();
        
        // Cliente existente (usando o mesmo ID de cliente do teste de assinatura)
        $customer_id = 'cus_000006681796';
        
        // Token de cartão (usando o mesmo token do teste de assinatura)
        $card_token = 'e50c2bca-7be3-43b4-ac4d-e5c27a7e5a85';
        
        echo '<p>Criando pagamento único para cliente ID: ' . $customer_id . '</p>';
        
        // Dados do pagamento único
        $payment_data = [
            'customer' => $customer_id,
            'billingType' => 'CREDIT_CARD',
            'value' => 10.00,
            'dueDate' => date('Y-m-d'), // Data atual como data de vencimento
            'creditCardToken' => $card_token
        ];
        
        // Obter o objeto de operações com pagamentos
        $payments_api = $api->get_payments();
        $result = $payments_api->create_payment($payment_data);
        
        if ($result['success']) {
            echo '<p>Pagamento único criado com sucesso!</p>';
            echo '<ul>';
            echo '<li>ID do Pagamento: ' . $result['data']['id'] . '</li>';
            echo '<li>Status: ' . $result['data']['status'] . '</li>';
            echo '<li>Data de vencimento: ' . $result['data']['dueDate'] . '</li>';
            echo '<li>Valor: R$ ' . number_format($result['data']['value'], 2, ',', '.') . '</li>';
            echo '</ul>';
        } else {
            echo '<p style="color:red;">Erro ao criar pagamento:</p>';
            echo '<ul style="color:red;">';
            foreach ($result['errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
        }
        
    } catch (Exception $e) {
        echo '<p style="color:red;">Erro da classe: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

echo '<p>Fim do teste</p>';
?>