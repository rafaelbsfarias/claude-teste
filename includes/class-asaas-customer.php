<?php
/**
 * Gerenciamento de clientes do Asaas.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

use Asaas_Customer_Registration\Includes\Interfaces\Asaas_Api_Interface;
use Asaas_Customer_Registration\Includes\Interfaces\Asaas_Validator_Interface;
use WP_Error;

/**
 * Classe responsável por operações com clientes Asaas.
 *
 * Aplica o princípio de Responsabilidade Única (SRP) do SOLID
 * focando apenas nas operações relacionadas a clientes.
 */
class Asaas_Customer {

    /**
     * Instância da API Asaas.
     *
     * @var Asaas_Api_Interface
     * @since 1.0.0
     */
    private $api;
    
    /**
     * Validador de CPF/CNPJ.
     *
     * @var Asaas_Validator_Interface
     * @since 1.0.0
     */
    private $validator;
    
    /**
     * Inicializa a classe e define dependências.
     *
     * @param Asaas_Api_Interface      $api       Instância da API Asaas.
     * @param Asaas_Validator_Interface $validator Validador de CPF/CNPJ.
     * @since 1.0.0
     */
    public function __construct(Asaas_Api_Interface $api, Asaas_Validator_Interface $validator) {
        $this->api = $api;
        $this->validator = $validator;
    }
    
    /**
     * Cria um cliente na API Asaas.
     *
     * @param string $name    Nome do cliente.
     * @param string $cpf_cnpj CPF ou CNPJ do cliente.
     * @return array|WP_Error Dados do cliente criado ou erro.
     * @since 1.0.0
     */
    public function create($name, $cpf_cnpj) {
        // Remove formatação do CPF/CNPJ
        $cpf_cnpj = preg_replace('/[^0-9]/', '', $cpf_cnpj);
        
        // Valida o CPF/CNPJ
        if (!$this->validator->validate($cpf_cnpj)) {
            return new WP_Error(
                'invalid_document',
                $this->get_validation_errors()
            );
        }
        
        // Prepara os dados do cliente
        $customer_data = array(
            'name' => $name,
            'cpfCnpj' => $cpf_cnpj
        );
        
        // Cria o cliente na API
        return $this->api->create_customer($customer_data);
    }
    
    /**
     * Obtém um cliente pelo ID.
     *
     * @param string $customer_id ID do cliente.
     * @return array|WP_Error Dados do cliente ou erro.
     * @since 1.0.0
     */
    public function get($customer_id) {
        return $this->api->get_customer($customer_id);
    }
    
    /**
     * Obtém as mensagens de erro de validação.
     *
     * @return string Mensagens de erro formatadas.
     * @since 1.0.0
     */
    private function get_validation_errors() {
        $errors = $this->validator->get_error_messages();
        
        if (empty($errors)) {
            return __('Documento inválido.', 'asaas-customer-registration');
        }
        
        return implode(' ', $errors);
    }
}