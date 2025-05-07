<?php

require_once dirname(__FILE__) . '/../../../wp-load.php';
require_once __DIR__ . '/admin/class-admin-settings.php';
include_once __DIR__ . '/includes/class-asaas-api.php';

echo '<h2>Teste de Tokenização de Cartão</h2>';

// Tentar usar a classe somente se existir
if (class_exists('Asaas_API_Conn')) {
    try {
        $api = new Asaas_API_Conn();
        
        // Cliente existente (substitua pelo ID real de um cliente)
        $customer_id = 'cus_000006681796';
        
        echo '<p>Tokenizando cartão para cliente ID: ' . $customer_id . '</p>';
        
        // Dados do cartão (use dados de teste)
        $card_data = [
            'holder_name' => 'Cliente Teste',
            'number' => '6062824912167413',
            'expiry_month' => '05',
            'expiry_year' => '2026',
            'ccv' => '797'
        ];
        
        // Dados do titular
        $holder_info = [
            'name' => 'Cliente Teste',
            'email' => 'teste@exemplo.com',
            'cpf_cnpj' => '14742877004',
            'postal_code' => '40070100',
            'address_number' => '123',
            'phone' => '71999999999'
        ];
        
        // Obter o objeto de operações com cartões
        $credit_cards_api = $api->get_credit_cards();
        $result = $credit_cards_api->tokenize_credit_card($card_data, $holder_info, $customer_id);
        
        if ($result['success']) {
            echo '<p>Cartão tokenizado com sucesso!</p>';
            echo '<ul>';
            echo '<li>Token: ' . $result['data']['creditCardToken'] . '</li>';
            echo '<li>Últimos 4 dígitos: ' . $result['data']['creditCardNumber'] . '</li>';
            echo '<li>Bandeira: ' . $result['data']['creditCardBrand'] . '</li>';
            echo '</ul>';
        } else {
            echo '<p style="color:red;">Erro ao tokenizar cartão:</p>';
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