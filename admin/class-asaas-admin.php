<?php
/**
 * Funcionalidades admin do plugin.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Admin;

/**
 * Classe responsável pelas funcionalidades da área administrativa.
 *
 * Aplica o princípio de Responsabilidade Única (SRP) do SOLID
 * focando apenas nas operações relacionadas à área administrativa.
 */
class Asaas_Admin {

    /**
     * Registra e enfileira os estilos para a área admin.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'asaas-admin-style',
            ASAAS_PLUGIN_URL . 'admin/css/admin-style.css',
            array(),
            ASAAS_VERSION,
            'all'
        );
    }
    
    /**
     * Registra e enfileira os scripts para a área admin.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'asaas-admin-script',
            ASAAS_PLUGIN_URL . 'admin/js/admin-script.js',
            array('jquery'),
            ASAAS_VERSION,
            true
        );
    }
    
    /**
     * Adiciona página de configurações ao menu WordPress.
     *
     * @since 1.0.0
     */
    public function add_settings_page() {
        add_options_page(
            __('Configurações Asaas', 'asaas-customer-registration'),
            __('Asaas Customer', 'asaas-customer-registration'),
            'manage_options',
            'asaas-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Renderiza a página de configurações.
     *
     * @since 1.0.0
     */
    public function render_settings_page() {
        // Carrega o template da página de configurações
        require_once ASAAS_PLUGIN_DIR . 'admin/views/settings-page.php';
    }
    
    /**
     * Registra as configurações do plugin.
     *
     * @since 1.0.0
     */
    public function register_settings() {
        // Registra seção de configurações
        add_settings_section(
            'asaas_api_settings',
            __('Configurações da API', 'asaas-customer-registration'),
            array($this, 'settings_section_callback'),
            'asaas-settings'
        );
        
        // Registra campo de API key
        add_settings_field(
            'asaas_api_key',
            __('API Key', 'asaas-customer-registration'),
            array($this, 'api_key_field_callback'),
            'asaas-settings',
            'asaas_api_settings'
        );
        
        // Registra campo de ambiente
        add_settings_field(
            'asaas_environment',
            __('Ambiente', 'asaas-customer-registration'),
            array($this, 'environment_field_callback'),
            'asaas-settings',
            'asaas_api_settings'
        );
        
        // Registra seção de configurações de pagamento
        add_settings_section(
            'asaas_payment_settings',
            __('Configurações de Pagamento', 'asaas-customer-registration'),
            array($this, 'payment_section_callback'),
            'asaas-settings'
        );
        
        // Registra campo de dias para vencimento
        add_settings_field(
            'asaas_due_days',
            __('Dias para Vencimento', 'asaas-customer-registration'),
            array($this, 'due_days_field_callback'),
            'asaas-settings',
            'asaas_payment_settings'
        );
        
        // Registra campo de valor padrão
        add_settings_field(
            'asaas_default_value',
            __('Valor Padrão (R$)', 'asaas-customer-registration'),
            array($this, 'default_value_field_callback'),
            'asaas-settings',
            'asaas_payment_settings'
        );
        
        // Registra campo de descrição padrão
        add_settings_field(
            'asaas_default_description',
            __('Descrição Padrão', 'asaas-customer-registration'),
            array($this, 'default_description_field_callback'),
            'asaas-settings',
            'asaas_payment_settings'
        );
        
        // Registra configurações com sanitização adequada
        register_setting(
            'asaas_settings',
            'asaas_api_key',
            array(
                'sanitize_callback' => 'sanitize_text_field',
                'default' => ''
            )
        );
        
        register_setting(
            'asaas_settings',
            'asaas_environment',
            array(
                'sanitize_callback' => array($this, 'sanitize_environment'),
                'default' => 'sandbox'
            )
        );
        
        register_setting(
            'asaas_settings',
            'asaas_due_days',
            array(
                'sanitize_callback' => array($this, 'sanitize_due_days'),
                'default' => 3
            )
        );
        
        register_setting(
            'asaas_settings',
            'asaas_default_value',
            array(
                'sanitize_callback' => array($this, 'sanitize_value'),
                'default' => '100.00'
            )
        );
        
        register_setting(
            'asaas_settings',
            'asaas_default_description',
            array(
                'sanitize_callback' => 'sanitize_text_field',
                'default' => __('Pagamento via site', 'asaas-customer-registration')
            )
        );
    }
    
    /**
     * Callback da seção de configurações da API.
     *
     * @since 1.0.0
     */
    public function settings_section_callback() {
        echo '<p>' . esc_html__('Configure sua integração com a API Asaas.', 'asaas-customer-registration') . '</p>';
    }
    
    /**
     * Callback da seção de configurações de pagamento.
     *
     * @since 1.0.0
     */
    public function payment_section_callback() {
        echo '<p>' . esc_html__('Configure as opções padrão para pagamentos.', 'asaas-customer-registration') . '</p>';
    }
    
    /**
     * Callback do campo API Key.
     *
     * @since 1.0.0
     */
    public function api_key_field_callback() {
        $api_key = get_option('asaas_api_key', '');
        echo '<input type="text" name="asaas_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__('Insira sua chave de API do Asaas.', 'asaas-customer-registration') . '</p>';
    }
    
    /**
     * Callback do campo Ambiente.
     *
     * @since 1.0.0
     */
    public function environment_field_callback() {
        $environment = get_option('asaas_environment', 'sandbox');
        echo '<select name="asaas_environment">';
        echo '<option value="sandbox" ' . selected($environment, 'sandbox', false) . '>' . esc_html__('Sandbox (Testes)', 'asaas-customer-registration') . '</option>';
        echo '<option value="production" ' . selected($environment, 'production', false) . '>' . esc_html__('Produção', 'asaas-customer-registration') . '</option>';
        echo '</select>';
        echo '<p class="description">' . esc_html__('Selecione o ambiente da API.', 'asaas-customer-registration') . '</p>';
    }
    
    /**
     * Callback do campo Dias para Vencimento.
     *
     * @since 1.0.0
     */
    public function due_days_field_callback() {
        $due_days = get_option('asaas_due_days', 3);
        echo '<input type="number" name="asaas_due_days" value="' . esc_attr($due_days) . '" min="1" class="small-text">';
        echo '<p class="description">' . esc_html__('Dias a partir da data atual para definir o vencimento do pagamento.', 'asaas-customer-registration') . '</p>';
    }
    
    /**
     * Callback do campo Valor Padrão.
     *
     * @since 1.0.0
     */
    public function default_value_field_callback() {
        $default_value = get_option('asaas_default_value', '100.00');
        echo '<input type="number" name="asaas_default_value" value="' . esc_attr($default_value) . '" min="0.01" step="0.01" class="regular-text">';
        echo '<p class="description">' . esc_html__('Valor padrão para pagamentos.', 'asaas-customer-registration') . '</p>';
    }
    
    /**
     * Callback do campo Descrição Padrão.
     *
     * @since 1.0.0
     */
    public function default_description_field_callback() {
        $default_description = get_option('asaas_default_description', __('Pagamento via site', 'asaas-customer-registration'));
        echo '<input type="text" name="asaas_default_description" value="' . esc_attr($default_description) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__('Descrição padrão para pagamentos.', 'asaas-customer-registration') . '</p>';
    }
    
    /**
     * Sanitiza o campo Ambiente.
     *
     * @param string $input Valor de entrada.
     * @return string Valor sanitizado.
     * @since 1.0.0
     */
    public function sanitize_environment($input) {
        $valid_options = array('sandbox', 'production');
        
        if (in_array($input, $valid_options, true)) {
            return $input;
        }
        
        // Se não for válido, retorna o valor padrão
        return 'sandbox';
    }
    
    /**
     * Sanitiza o campo Dias para Vencimento.
     *
     * @param mixed $input Valor de entrada.
     * @return int Valor sanitizado.
     * @since 1.0.0
     */
    public function sanitize_due_days($input) {
        $days = absint($input);
        
        // Garante que seja pelo menos 1
        if ($days < 1) {
            $days = 1;
        }
        
        return $days;
    }
    
    /**
     * Sanitiza o campo Valor.
     *
     * @param mixed $input Valor de entrada.
     * @return string Valor sanitizado.
     * @since 1.0.0
     */
    public function sanitize_value($input) {
        $value = (float) $input;
        
        // Garante que seja positivo
        if ($value <= 0) {
            $value = 0.01;
        }
        
        return number_format($value, 2, '.', '');
    }
}