<?php
if (!defined('ABSPATH')) {
    exit;
}

// Verificar se a classe de componentes foi carregada
if (!class_exists('Asaas_Form_Components')) {
    require_once ASAAS_PLUGIN_DIR . 'includes/components/form-components.php';
}

// Definir o tipo de doação para este formulário
$donation_type = 'recurring';
$nonce_action = Asaas_Nonce_Manager::ACTION_RECURRING_DONATION;
?>

<div class="asaas-donation-form">
    <h2>Doação Mensal</h2>
    <p>Preencha as informações abaixo para realizar sua doação mensal. Fique tranquilo, essa modalidade de doação não ocupa o limite do seu cartão de crédito.</p>
    
    <form id="asaas-recurring-donation-form" class="asaas-form" method="post">
        <input type="hidden" name="action" value="process_donation">
        <input type="hidden" name="donation_type" value="<?php echo esc_attr($donation_type); ?>">
        <?php Asaas_Nonce_Manager::generate_nonce_field($nonce_action); ?>
        
        <?php 
        // Renderizar campos usando componentes
        Asaas_Form_Components::render_personal_fields();
        Asaas_Form_Components::render_donation_value_field([], true);
        
        // Para doação recorrente, apenas cartão é aceito, então definimos um valor fixo
        echo '<input type="hidden" name="payment_method" value="card">';
        
        // Renderizar campos de cartão (que devem ser mostrados por padrão)
        Asaas_Form_Components::render_credit_card_fields();
        Asaas_Form_Components::render_submit_button(true);
        ?>
    </form>
</div>

<!-- Script para mostrar os campos de cartão no formulário recorrente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Para o formulário de doação recorrente, os campos de cartão devem estar sempre visíveis
    const form = document.getElementById('asaas-recurring-donation-form');
    if (!form) return;
    
    const cardFields = form.querySelector('.asaas-card-fields');
    if (cardFields) {
        cardFields.style.display = 'block';
    }
});
</script>