<?php
/**
 * Funcionalidades públicas do plugin.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Public;

use Asaas_Customer_Registration\Includes\Asaas_Api;
use Asaas_Customer_Registration\Includes\Asaas_Customer;
use Asaas_Customer_Registration\Includes\Asaas_Payment;
use Asaas_Customer_Registration\Includes\Asaas_Cpf_Cnpj_Validator;
use WP_Error;

/**
 * Classe responsável pelas funcionalidades públicas do plugin.
 *
 * Aplica o princípio de Responsabilidade Única (SRP) do SOLID
 * focando apenas nas operações relacionadas à área pública.
 */
class Asaas_Public {

    /**
     * Registra e enfileira os estilos para a área pública.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'asaas-public-style',
            ASAAS_PLUGIN_URL . 'public/css/public-style.css',
            array(),
            ASAAS_VERSION,
            'all'
        );
    }
    
    /**
     * Registra e enfileira os scripts para a área pública.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        // jQuery Mask para formatação de CPF/CNPJ
        wp_enqueue_script(
            'jquery-mask',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js',
            array('jquery'),
            '1.14.16',
            true
        );
        
        // Script principal
        wp_enqueue_script(
            'asaas-public-script',
            ASAAS_PLUGIN_URL . 'public/js/public-script.js',
            array('jquery', 'jquery-mask'),
            ASAAS_VERSION,
            true
        );
        
        // Localize script para AJAX
        wp_localize_script(
            'asaas-public-script',
            'asaas_params',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('asaas_nonce'),
                'messages' => array(
                    'required_fields' => __('Todos os campos são obrigatórios.', 'asaas-customer-registration'),
                    'processing'      => __('Processando...', 'asaas-customer-registration'),
                    'success'         => __('Operação realizada com sucesso!', 'asaas-customer-registration'),
                    'error'           => __('Ocorreu um erro. Por favor, tente novamente.', 'asaas-customer-registration')
                )
            )
        );
    }
    
    /**
     * Registra os shortcodes do plugin.
     *
     * @since 1.0.0
     */
    public function register_shortcodes() {
        add_shortcode('asaas_form', array($this, 'render_form_shortcode'));
    }
    
    /**
     * Renderiza o shortcode do formulário.
     *
     * @param array $atts Atributos do shortcode.
     * @return string Conteúdo HTML do shortcode.
     * @since 1.0.0
     */
    public function render_form_shortcode($atts) {
        // Mescla os atributos com os valores padrão
        $attributes = shortcode_atts(
            array(
                'valor'           => get_option('asaas_default_value', '100.00'),
                'descricao'       => get_option('asaas_default_description', __('Pagamento via site', 'asaas-customer-registration')),
                'dias_vencimento' => get_option('asaas_due_days', 3)
            ),
            $atts
        );
        
        // Inicia o buffer de saída
        ob_start();
        
        // Inclui o template do formulário
        include ASAAS_PLUGIN_DIR . 'public/views/form-template.php';
        
        // Retorna o conteúdo do buffer
        return ob_get_clean();
    }
    
    /**
     * Processa a requisição AJAX para registrar cliente.
     *
     * @since 1.0.0
     */
    public function process_register_customer() {
        // Verifica o nonce para segurança
        check_ajax_referer('asaas_nonce', 'nonce');
        
        // Obtém e sanitiza os dados
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $cpf_cnpj = isset($_POST['cpfCnpj']) ? sanitize_text_field(wp_unslash($_POST['cpfCnpj'])) : '';
        
        // Verifica campos obrigatórios
        if (empty($name) || empty($cpf_cnpj)) {
            wp_send_json_error(array(
                'message' => __('Todos os campos são obrigatórios.', 'asaas-customer-registration')
            ));
            wp_die();
        }
        
        // Cria as dependências
        $api = new Asaas_Api();
        $validator = new Asaas_Cpf_Cnpj_Validator();
        $customer = new Asaas_Customer($api, $validator);
        
        // Cria o cliente na API
        $result = $customer->create($name, $cpf_cnpj);
        
        // Verifica se ocorreu algum erro
        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ));
            wp_die();
        }
        
        // Retorna sucesso com os dados do cliente
        wp_send_json_success(array(
            'message'     => __('Cliente cadastrado com sucesso!', 'asaas-customer-registration'),
            'customer_id' => $result['id']
        ));
        
        wp_die();
    }
    
    /**
     * Processa a requisição AJAX para criar pagamento.
     *
     * @since 1.0.0
     */
    public function process_create_payment() {
        // Verifica o nonce para segurança
        check_ajax_referer('asaas_nonce', 'nonce');
        
        // Obtém e sanitiza os dados
        $customer_id = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : '';
        $value = isset($_POST['value']) ? (float) $_POST['value'] : 0;
        $billing_type = isset($_POST['billingType']) ? sanitize_text_field(wp_unslash($_POST['billingType'])) : '';
        $description = isset($_POST['description']) ? sanitize_text_field(wp_unslash($_POST['description'])) : '';
        $due_days = isset($_POST['dueDate']) ? absint($_POST['dueDate']) : get_option('asaas_due_days', 3);
        
        // Verifica campos obrigatórios
        if (empty($customer_id) || $value <= 0 || empty($billing_type)) {
            wp_send_json_error(array(
                'message' => __('Todos os campos são obrigatórios e o valor deve ser maior que zero.', 'asaas-customer-registration')
            ));
            wp_die();
        }
        
        // Cria as dependências
        $api = new Asaas_Api();
        $payment = new Asaas_Payment($api);
        
        // Cria o pagamento na API
        $result = $payment->create($customer_id, $value, $billing_type, $description, $due_days);
        
        // Verifica se ocorreu algum erro
        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ));
            wp_die();
        }
        
        // Prepara os dados de resposta
        $payment_info = array(
            'id'          => $result['id'],
            'value'       => $result['value'],
            'billingType' => $result['billingType'],
            'status'      => $result['status'],
            'dueDate'     => $result['dueDate'],
            'invoiceUrl'  => $result['invoiceUrl']
        );
        
        // Adiciona URL específica dependendo do método de pagamento
        if ($billing_type === 'BOLETO' && isset($result['bankSlipUrl'])) {
            $payment_info['bankSlipUrl'] = $result['bankSlipUrl'];
        }
        
        // Retorna sucesso com os dados do pagamento
        wp_send_json_success(array(
            'message' => __('Pagamento criado com sucesso!', 'asaas-customer-registration'),
            'payment' => $payment_info
        ));
        
        wp_die();
    }
}