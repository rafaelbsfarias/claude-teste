<?php
/**
 * Gerenciamento de pagamentos do Asaas.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

use Asaas_Customer_Registration\Includes\Interfaces\Asaas_Api_Interface;
use WP_Error;

/**
 * Classe responsável por operações com pagamentos Asaas.
 *
 * Aplica o princípio de Responsabilidade Única (SRP) do SOLID
 * focando apenas nas operações relacionadas a pagamentos.
 */
class Asaas_Payment {

    /**
     * Instância da API Asaas.
     *
     * @var Asaas_Api_Interface
     * @since 1.0.0
     */
    private $api;
    
    /**
     * Inicializa a classe e define dependências.
     *
     * @param Asaas_Api_Interface $api Instância da API Asaas.
     * @since 1.0.0
     */
    public function __construct(Asaas_Api_Interface $api) {
        $this->api = $api;
    }
    
    /**
     * Cria um pagamento na API Asaas.
     *
     * @param string  $customer_id  ID do cliente.
     * @param float   $value        Valor do pagamento.
     * @param string  $billing_type Tipo de pagamento (PIX, BOLETO, CREDIT_CARD).
     * @param string  $description  Descrição do pagamento (opcional).
     * @param integer $due_days     Dias para vencimento (opcional, padrão 3).
     * @return array|WP_Error Dados do pagamento criado ou erro.
     * @since 1.0.0
     */
    public function create($customer_id, $value, $billing_type, $description = '', $due_days = 3) {
        // Valida o tipo de pagamento
        if (!$this->is_valid_billing_type($billing_type)) {
            return new WP_Error(
                'invalid_billing_type',
                __('Tipo de pagamento inválido. Tipos válidos: PIX, BOLETO, CREDIT_CARD.', 'asaas-customer-registration')
            );
        }
        
        // Valida o valor
        if (!is_numeric($value) || $value <= 0) {
            return new WP_Error(
                'invalid_value',
                __('O valor do pagamento deve ser maior que zero.', 'asaas-customer-registration')
            );
        }
        
        // Calcula a data de vencimento
        $due_date = gmdate('Y-m-d', strtotime("+{$due_days} days"));
        
        // Prepara os dados do pagamento
        $payment_data = array(
            'customer' => $customer_id,
            'billingType' => $billing_type,
            'value' => (float) $value,
            'dueDate' => $due_date
        );
        
        // Adiciona descrição se fornecida
        if (!empty($description)) {
            $payment_data['description'] = $description;
        }
        
        // Cria o pagamento na API
        return $this->api->create_payment($payment_data);
    }
    
    /**
     * Obtém um pagamento pelo ID.
     *
     * @param string $payment_id ID do pagamento.
     * @return array|WP_Error Dados do pagamento ou erro.
     * @since 1.0.0
     */
    public function get($payment_id) {
        return $this->api->get_payment($payment_id);
    }
    
    /**
     * Verifica se o tipo de pagamento é válido.
     *
     * @param string $billing_type Tipo de pagamento.
     * @return bool True se o tipo é válido, false caso contrário.
     * @since 1.0.0
     */
    private function is_valid_billing_type($billing_type) {
        $valid_types = array('PIX', 'BOLETO', 'CREDIT_CARD');
        return in_array($billing_type, $valid_types, true);
    }
}