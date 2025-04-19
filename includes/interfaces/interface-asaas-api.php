<?php
/**
 * Interface para API Asaas.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes\Interfaces;

/**
 * Interface que define os métodos necessários para comunicação com a API Asaas.
 *
 * Seguindo o princípio de Segregação de Interface (ISP) do SOLID,
 * criamos uma interface específica para operações da API.
 */
interface Asaas_Api_Interface {

    /**
     * Cria um cliente na API Asaas.
     *
     * @param array $customer_data Dados do cliente.
     * @return array|WP_Error Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    public function create_customer($customer_data);

    /**
     * Cria um pagamento na API Asaas.
     *
     * @param array $payment_data Dados do pagamento.
     * @return array|WP_Error Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    public function create_payment($payment_data);
    
    /**
     * Obtém um cliente pelo ID.
     *
     * @param string $customer_id ID do cliente.
     * @return array|WP_Error Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    public function get_customer($customer_id);
    
    /**
     * Obtém um pagamento pelo ID.
     *
     * @param string $payment_id ID do pagamento.
     * @return array|WP_Error Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    public function get_payment($payment_id);
}