<?php
/**
 * Template do formulário de cadastro e pagamento.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

// Se este arquivo for chamado diretamente, abortar.
if (!defined('WPINC')) {
    die;
}

// Atributos do shortcode já estão disponíveis na variável $attributes
?>

<div class="asaas-container">
    <!-- Step 1: Cadastro de cliente -->
    <div id="asaas-step-customer" class="asaas-step active">
        <h3><?php esc_html_e('Cadastro de Cliente', 'asaas-customer-registration'); ?></h3>
        
        <form id="asaas-customer-form" class="asaas-form">
            <div class="asaas-field">
                <label for="asaas-name">
                    <?php esc_html_e('Nome completo', 'asaas-customer-registration'); ?> <span class="required">*</span>
                </label>
                <input type="text" id="asaas-name" name="name" required>
            </div>
            
            <div class="asaas-field">
                <label for="asaas-cpfcnpj">
                    <?php esc_html_e('CPF/CNPJ', 'asaas-customer-registration'); ?> <span class="required">*</span>
                </label>
                <input type="text" id="asaas-cpfcnpj" name="cpfCnpj" required>
            </div>
            
            <div class="asaas-field asaas-actions">
                <button type="submit" id="asaas-customer-submit" class="asaas-button">
                    <?php esc_html_e('Cadastrar e Prosseguir', 'asaas-customer-registration'); ?>
                </button>
            </div>
            
            <div id="asaas-customer-message" class="asaas-message"></div>
        </form>
    </div>
    
    <!-- Step 2: Pagamento -->
    <div id="asaas-step-payment" class="asaas-step">
        <h3><?php esc_html_e('Pagamento', 'asaas-customer-registration'); ?></h3>
        
        <form id="asaas-payment-form" class="asaas-form">
            <input type="hidden" id="asaas-customer-id" name="customer_id">
            <input type="hidden" id="asaas-due-days" name="dueDate" value="<?php echo esc_attr($attributes['dias_vencimento']); ?>">
            
            <div class="asaas-field">
                <label for="asaas-value">
                    <?php esc_html_e('Valor (R$)', 'asaas-customer-registration'); ?> <span class="required">*</span>
                </label>
                <input type="number" id="asaas-value" name="value" step="0.01" min="0.01" value="<?php echo esc_attr($attributes['valor']); ?>" required>
            </div>
            
            <div class="asaas-field">
                <label for="asaas-description">
                    <?php esc_html_e('Descrição', 'asaas-customer-registration'); ?>
                </label>
                <input type="text" id="asaas-description" name="description" value="<?php echo esc_attr($attributes['descricao']); ?>">
            </div>
            
            <div class="asaas-field">
                <label>
                    <?php esc_html_e('Forma de pagamento', 'asaas-customer-registration'); ?> <span class="required">*</span>
                </label>
                <div class="asaas-payment-methods">
                    <div class="asaas-payment-method">
                        <input type="radio" id="asaas-pix" name="billingType" value="PIX" checked>
                        <label for="asaas-pix"><?php esc_html_e('PIX', 'asaas-customer-registration'); ?></label>
                    </div>
                    
                    <div class="asaas-payment-method">
                        <input type="radio" id="asaas-boleto" name="billingType" value="BOLETO">
                        <label for="asaas-boleto"><?php esc_html_e('Boleto', 'asaas-customer-registration'); ?></label>
                    </div>
                    
                    <div class="asaas-payment-method">
                        <input type="radio" id="asaas-credit-card" name="billingType" value="CREDIT_CARD">
                        <label for="asaas-credit-card"><?php esc_html_e('Cartão de Crédito', 'asaas-customer-registration'); ?></label>
                    </div>
                </div>
            </div>
            
            <div class="asaas-field asaas-actions">
                <button type="submit" id="asaas-payment-submit" class="asaas-button">
                    <?php esc_html_e('Gerar Pagamento', 'asaas-customer-registration'); ?>
                </button>
                
                <button type="button" id="asaas-back-button" class="asaas-button asaas-button-secondary">
                    <?php esc_html_e('Voltar', 'asaas-customer-registration'); ?>
                </button>
            </div>
            
            <div id="asaas-payment-message" class="asaas-message"></div>
        </form>
    </div>
    
    <!-- Step 3: Sucesso -->
    <div id="asaas-step-success" class="asaas-step">
        <h3><?php esc_html_e('Pagamento Gerado com Sucesso', 'asaas-customer-registration'); ?></h3>
        
        <div id="asaas-payment-details" class="asaas-payment-details">
            <!-- Detalhes do pagamento serão inseridos via JavaScript -->
        </div>
        
        <div id="asaas-payment-links" class="asaas-payment-links">
            <!-- Links de pagamento serão inseridos via JavaScript -->
        </div>
        
        <div class="asaas-field asaas-actions">
            <button type="button" id="asaas-new-payment-button" class="asaas-button">
                <?php esc_html_e('Novo Pagamento', 'asaas-customer-registration'); ?>
            </button>
        </div>
    </div>
</div>