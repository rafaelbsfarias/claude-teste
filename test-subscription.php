<?php

require_once dirname(__FILE__) . '/../../../wp-load.php';
require_once __DIR__ . '/admin/class-admin-settings.php';
include_once __DIR__ . '/includes/class-asaas-api.php';

echo '<h2>Teste de Criação de Assinatura Recorrente</h2>';

// Tentar usar a classe somente se existir
if (class_exists('Asaas_API_Conn')) {
    try {
        $api = new Asaas_API_Conn();
        
        // Cliente existente (substitua pelo ID real de um cliente)
        $customer_id = 'cus_000006681796';
        
        // Token de cartão (substitua pelo token real gerado anteriormente)
        $card_token = 'e50c2bca-7be3-43b4-ac4d-e5c27a7e5a85';
        
        echo '<p>Criando assinatura para cliente ID: ' . $customer_id . '</p>';
        
        // Dados da assinatura
        $subscription_data = [
            'customer' => $customer_id,
            'billingType' => 'CREDIT_CARD',
            'cycle' => 'MONTHLY',
            'value' => 10.00,
            'nextDueDate' => date('Y-m-d'),
            'creditCardToken' => $card_token
        ];
        
        // Obter o objeto de operações com assinaturas
        $subscriptions_api = $api->get_subscriptions();
        $result = $subscriptions_api->create_subscription($subscription_data);
        
        if ($result['success']) {
            echo '<p>Assinatura criada com sucesso!</p>';
            echo '<ul>';
            echo '<li>ID da Assinatura: ' . $result['data']['id'] . '</li>';
            echo '<li>Status: ' . $result['data']['status'] . '</li>';
            echo '<li>Próximo vencimento: ' . $result['data']['nextDueDate'] . '</li>';
            echo '<li>Valor: R$ ' . number_format($result['data']['value'], 2, ',', '.') . '</li>';
            echo '</ul>';
        } else {
            echo '<p style="color:red;">Erro ao criar assinatura:</p>';
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