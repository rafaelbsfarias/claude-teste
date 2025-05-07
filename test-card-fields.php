<?php
// Carregar o WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Verificar se o usuário tem permissão
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado');
}

// Iniciar o HTML
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Campos de Cartão</title>
    <?php
    // Enfileirar os estilos e scripts do formulário
    wp_enqueue_style('asaas-form-style');
    wp_enqueue_script('jquery');
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-masks');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-payment-method');
    wp_enqueue_script('asaas-form-ajax');
    wp_enqueue_script('asaas-form-script');
    
    // Imprimir os estilos e scripts
    wp_print_styles();
    wp_print_scripts();
    ?>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        h2 { color: #0073aa; }
    </style>
</head>
<body>
    <h1>Teste de Visibilidade dos Campos de Cartão</h1>
    
    <div class="test-section">
        <h2>Doação Única - Versão 2</h2>
        <?php 
        // Forçar o uso do template V2
        Asaas_Feature_Manager::enable_feature('use_v2_templates');
        echo do_shortcode('[asaas_single_donation_v2]'); 
        ?>
    </div>
    
    <div class="test-section">
        <h2>Doação Recorrente - Versão 2</h2>
        <?php echo do_shortcode('[asaas_recurring_donation_v2]'); ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Página de teste carregada');
        console.log('Procurando formulários...');
        
        const singleForm = document.getElementById('asaas-single-donation-form');
        console.log('Formulário de doação única encontrado:', !!singleForm);
        
        const recurringForm = document.getElementById('asaas-recurring-donation-form');
        console.log('Formulário de doação recorrente encontrado:', !!recurringForm);
        
        if (singleForm) {
            const cardFields = singleForm.querySelector('.asaas-card-fields');
            console.log('Campos de cartão no formulário único encontrados:', !!cardFields);
            
            const paymentMethods = singleForm.querySelectorAll('input[name="payment_method"]');
            console.log('Métodos de pagamento encontrados:', paymentMethods.length);
        }
    });
    </script>
</body>
</html>