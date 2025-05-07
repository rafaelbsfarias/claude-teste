<?php

require_once dirname(__FILE__) . '/../../../wp-load.php';
require_once __DIR__ . '/admin/class-admin-settings.php';
include_once __DIR__ . '/includes/class-asaas-api.php';

echo '<h2>Teste da Classe Asaas_API_Conn</h2>';

// Tentar usar a classe somente se existir
if (class_exists('Asaas_API_Conn')) {
    try {
        $api = new Asaas_API_Conn();
        
        // CPF de teste - use um número fictício para testes
        $cpf_teste = '14742877004'; 
                      
        echo '<p>Verificando CPF/CNPJ: ' . $cpf_teste . '</p>';
        
        // Obter o objeto de operações com clientes
        $customers_api = $api->get_customers();
        $customer_id = $customers_api->find_by_cpf_cnpj($cpf_teste);
        
        if ($customer_id) {
            echo '<p>Cliente encontrado! ID: ' . $customer_id . '</p>';
        } else {
            echo '<p>Cliente não encontrado. Tentando criar...</p>';
            
            // Criar cliente de teste
            $result = $customers_api->create_customer([
                'name' => 'Cliente Teste',
                'cpfCnpj' => $cpf_teste,
                'email' => 'teste@exemplo.com'
            ]);
            
            if ($result['success']) {
                echo '<p>Cliente criado com sucesso! ID: ' . $result['data']['id'] . '</p>';
            } else {
                echo '<p style="color:red;">Erro ao criar cliente:</p>';
                echo '<ul style="color:red;">';
                foreach ($result['errors'] as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul>';
            }
        }
        
    } catch (Exception $e) {
        echo '<p style="color:red;">Erro da classe: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

echo '<p>Fim do teste</p>';