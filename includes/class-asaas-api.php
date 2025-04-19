<?php
/**
 * Implementação da API Asaas.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

use Asaas_Customer_Registration\Includes\Interfaces\Asaas_Api_Interface;
use WP_Error;

/**
 * Classe para comunicação com a API Asaas.
 *
 * Implementa a interface Asaas_Api_Interface seguindo o
 * princípio de Inversão de Dependência (DIP) do SOLID.
 */
class Asaas_Api implements Asaas_Api_Interface {

    /**
     * URL base da API Asaas.
     *
     * @var string
     * @since 1.0.0
     */
    private $api_url;
    
    /**
     * Chave de API Asaas.
     *
     * @var string
     * @since 1.0.0
     */
    private $api_key;
    
    /**
     * Inicializa a classe e define propriedades.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $environment = get_option('asaas_environment', 'sandbox');
        $this->api_key = get_option('asaas_api_key', '');
        
        // Define a URL base de acordo com o ambiente
        if ($environment === 'production') {
            $this->api_url = 'https://api.asaas.com/v3';
        } else {
            $this->api_url = 'https://api-sandbox.asaas.com/v3';
        }
    }
    
    /**
     * Faz uma requisição para a API Asaas.
     *
     * @param string $endpoint Endpoint da API.
     * @param array  $data     Dados a serem enviados.
     * @param string $method   Método HTTP (GET, POST, PUT, DELETE).
     * @return array|WP_Error  Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    private function make_request($endpoint, $data = array(), $method = 'GET') {
        // Verifica se a chave de API está configurada
        if (empty($this->api_key)) {
            return new WP_Error(
                'missing_api_key',
                __('API key não configurada. Configure em Configurações > Asaas Customer.', 'asaas-customer-registration')
            );
        }
        
        // URL completa para o endpoint
        $url = $this->api_url . '/' . $endpoint;
        
        // Argumentos da requisição
        $args = array(
            'method'  => $method,
            'headers' => array(
                'Content-Type' => 'application/json',
                'access_token' => $this->api_key
            ),
            'timeout' => 30
        );
        
        // Para métodos que exigem corpo, adiciona os dados
        if ($method === 'POST' || $method === 'PUT') {
            $args['body'] = wp_json_encode($data);
        }
        
        // Realiza a requisição
        $response = wp_remote_request($url, $args);
        
        // Verifica por erros na requisição
        if (is_wp_error($response)) {
            return $response;
        }
        
        // Obtém o código de resposta
        $response_code = wp_remote_retrieve_response_code($response);
        
        // Obtém o corpo da resposta
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        
        // Verifica se a resposta foi bem-sucedida
        if ($response_code < 200 || $response_code >= 300) {
            $error_message = isset($response_body['errors'][0]['description']) 
                ? $response_body['errors'][0]['description'] 
                : __('Erro desconhecido na comunicação com a API Asaas.', 'asaas-customer-registration');
            
            return new WP_Error(
                'api_error',
                $error_message,
                array('status' => $response_code)
            );
        }
        
        return $response_body;
    }
    
    /**
     * Cria um cliente na API Asaas.
     *
     * @param array $customer_data Dados do cliente.
     * @return array|WP_Error Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    public function create_customer($customer_data) {
        return $this->make_request('customers', $customer_data, 'POST');
    }
    
    /**
     * Cria um pagamento na API Asaas.
     *
     * @param array $payment_data Dados do pagamento.
     * @return array|WP_Error Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    public function create_payment($payment_data) {
        return $this->make_request('payments', $payment_data, 'POST');
    }
    
    /**
     * Obtém um cliente pelo ID.
     *
     * @param string $customer_id ID do cliente.
     * @return array|WP_Error Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    public function get_customer($customer_id) {
        return $this->make_request('customers/' . $customer_id);
    }
    
    /**
     * Obtém um pagamento pelo ID.
     *
     * @param string $payment_id ID do pagamento.
     * @return array|WP_Error Resposta da API ou objeto de erro.
     * @since 1.0.0
     */
    public function get_payment($payment_id) {
        return $this->make_request('payments/' . $payment_id);
    }
}