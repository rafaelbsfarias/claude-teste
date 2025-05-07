<?php
if (!defined('ABSPATH')) {
    exit;
}

// Verificar se a classe de componentes foi carregada
if (!class_exists('Asaas_Form_Components')) {
    require_once ASAAS_PLUGIN_DIR . 'includes/components/form-components.php';
}

// Definir o tipo de doação para este formulário
$donation_type = 'single';
$nonce_action = Asaas_Nonce_Manager::ACTION_SINGLE_DONATION;
?>

<div class="asaas-donation-form">
    <h2>Doação</h2>
    <p>Preencha as informações abaixo para realizar sua doação.</p>
    
    <form id="asaas-single-donation-form" class="asaas-form" method="post">
        <input type="hidden" name="action" value="process_donation">
        <input type="hidden" name="donation_type" value="<?php echo esc_attr($donation_type); ?>">
        <?php Asaas_Nonce_Manager::generate_nonce_field($nonce_action); ?>
        
        <?php 
        // Renderizar campos usando componentes
        Asaas_Form_Components::render_personal_fields();
        Asaas_Form_Components::render_donation_value_field();
        Asaas_Form_Components::render_payment_method_fields();
        
        // Adicionar os campos do cartão de crédito (inicialmente escondidos)
        Asaas_Form_Components::render_credit_card_fields();
        
        Asaas_Form_Components::render_submit_button();
        ?>
    </form>
</div>

<!-- Script para mostrar/esconder campos de cartão -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pegar o formulário
    const form = document.getElementById('asaas-single-donation-form');
    if (!form) return;
    
    // Pegar os campos de cartão
    const cardFields = form.querySelector('.asaas-card-fields');
    if (!cardFields) return;
    
    // Pegar o dropdown de método de pagamento
    const paymentSelect = form.querySelector('select[name="payment_method"]');
    if (!paymentSelect) return;
    
    // Função para mostrar/esconder campos de cartão
    function toggleCardFields() {
        cardFields.style.display = paymentSelect.value === 'card' ? 'block' : 'none';
    }
    
    // Configurar estado inicial
    toggleCardFields();
    
    // Adicionar listener para mudanças
    paymentSelect.addEventListener('change', toggleCardFields);
});
</script>