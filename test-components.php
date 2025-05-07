<?php
// Carregar o WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Verificar se o usuário tem permissão
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado');
}

// Carregar o componente
require_once ASAAS_PLUGIN_DIR . 'includes/components/form-components.php';

// Iniciar o HTML
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Componentes de Formulário</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        h2 { color: #0073aa; }
        .asaas-form-group { margin-bottom: 15px; }
        .asaas-form-group label { display: block; margin-bottom: 5px; }
        .asaas-form-group input, .asaas-form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .asaas-payment-methods { display: flex; gap: 15px; }
        .asaas-payment-method { display: flex; align-items: center; }
        .asaas-payment-method input { width: auto; margin-right: 5px; }
        .asaas-form-row { display: flex; gap: 15px; }
        .asaas-form-row .asaas-form-group { flex: 1; }
        .asaas-submit-button { background: #0073aa; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Teste de Componentes de Formulário Asaas</h1>
    
    <div class="test-section">
        <h2>Campos Pessoais</h2>
        <form>
            <?php Asaas_Form_Components::render_personal_fields(['full_name' => 'Usuário Teste']); ?>
        </form>
    </div>
    
    <div class="test-section">
        <h2>Campo de Valor da Doação</h2>
        <form>
            <?php Asaas_Form_Components::render_donation_value_field(['donation_value' => '50,00']); ?>
            <p>Recorrente:</p>
            <?php Asaas_Form_Components::render_donation_value_field(['donation_value' => '50,00'], true); ?>
        </form>
    </div>
    
    <div class="test-section">
        <h2>Métodos de Pagamento (Doação Única)</h2>
        <form>
            <?php Asaas_Form_Components::render_payment_method_fields(['payment_method' => 'pix']); ?>
        </form>
    </div>

    <div class="test-section">
        <h2>Métodos de Pagamento (Doação Recorrente)</h2>
        <form>
            <?php Asaas_Form_Components::render_payment_method_fields(['payment_method' => 'card'], true); ?>
        </form>
    </div>
    
    <div class="test-section">
        <h2>Campos de Cartão de Crédito</h2>
        <form>
            <?php Asaas_Form_Components::render_credit_card_fields(); ?>
        </form>
    </div>
    
    <div class="test-section">
        <h2>Formulário Completo (Doação Única)</h2>
        <form id="test-form-single" method="post">
            <?php 
            Asaas_Form_Components::render_hidden_fields('single_donation');
            Asaas_Form_Components::render_personal_fields();
            Asaas_Form_Components::render_donation_value_field();
            Asaas_Form_Components::render_payment_method_fields();
            Asaas_Form_Components::render_credit_card_fields();
            Asaas_Form_Components::render_honeypot_field();
            Asaas_Form_Components::render_submit_button();
            ?>
        </form>
    </div>
    
    <div class="test-section">
        <h2>Formulário Completo (Doação Recorrente)</h2>
        <form id="test-form-recurring" method="post">
            <?php 
            Asaas_Form_Components::render_hidden_fields('recurring_donation');
            Asaas_Form_Components::render_personal_fields();
            Asaas_Form_Components::render_donation_value_field([], true);
            Asaas_Form_Components::render_payment_method_fields([], true);
            Asaas_Form_Components::render_credit_card_fields();
            Asaas_Form_Components::render_honeypot_field();
            Asaas_Form_Components::render_submit_button(true);
            ?>
        </form>
    </div>

    <script>
        // Script para mostrar/ocultar campos do cartão baseado na seleção do método de pagamento
        document.addEventListener('DOMContentLoaded', function() {
            // Função para alternar campos de cartão
            function toggleCardFields(formId) {
                const form = document.getElementById(formId);
                const cardRadio = form.querySelector('input[value="card"]');
                const cardFields = form.querySelector('.asaas-card-fields');
                
                // Configuração inicial
                if (cardRadio && cardFields) {
                    if (cardRadio.checked) {
                        cardFields.style.display = 'block';
                    } else {
                        cardFields.style.display = 'none';
                    }
                    
                    // Adicionar listener
                    const paymentMethods = form.querySelectorAll('input[name="payment_method"]');
                    paymentMethods.forEach(method => {
                        method.addEventListener('change', function() {
                            cardFields.style.display = (this.value === 'card') ? 'block' : 'none';
                        });
                    });
                }
            }
            
            // Aplicar a todos os formulários de teste
            toggleCardFields('test-form-single');
            toggleCardFields('test-form-recurring');
        });
    </script>
</body>
</html>