<?php
// Carregar o WordPress
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Verificar se o usuário tem permissão
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado');
}

// Simular uma resposta do Asaas para teste
$mock_response = [
    'success' => true,
    'data' => [
        'message' => 'Donation processed successfully',
        'full_name' => 'Usuário de Teste',
        'email' => 'teste@exemplo.com',
        'cpf_cnpj' => '12345678909',
        'donation_value' => 50,
        'payment_method' => 'pix',
        'customer_created' => false,
        'customer_id' => 'cus_000123456789',
        'pix_code' => 'iVBORw0KGgoAAAANSUhEUgAAATYAAAE2AQMAAAAYWaYYAAAABlBMVEX///8AAABVwtN+AAABKUlEQVR4nO3YMY7DIBCFYVrOAHdBcpEcwUeIjsR1ttwlF0G6M2DL3mSlfaxVerTS/H/FYMQzAgQwVREREREREdFnir52M5Pbdm53eeM1m9TZpNWeg0o9twxlrermzBUlOh5LlXrGciqzJHfssOaKdTlLy5WfPdYzdpWkZqxio1Cz57DYZdZ4YXMFdo8PuHRi75frj/De6JHxG9udxLe2Pw3FKjaKP+6NFxYR0f8/ybOLufLBE9+rOmZY5VnHrpLbscVj6MXiVccOtvZumbVpqGKjiO3x6yc5i/ZWSe5nF0vT5OJ6nGXNd5LPjleR57iLJW/l11K5YrRKUvJkcNlSiGKPnT2W1m5bclb22NkxC9VauWL5YDbH5Q3Twdphm+/YuX8XC9VaueKIiIiIiIjoC31A9xfqxCKqnwAAAABJRU5ErkJggg==',
        'pix_text' => '00020101021226820014br.gov.bcb.pix2560qpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca2552040000530398654055005802BR5913Minha Empresa6008Sao Paulo62070503***63044EB4',
        'payment_id' => 'pay_p8pcn54d2tg7hnp2',
        'payment_status' => 'PENDING',
        'due_date' => '2025-05-07',
        'invoice_url' => 'https://sandbox.asaas.com/i/p8pcn54d2tg7hnp2',
        'bank_slip_url' => null,
        'nossoNumero' => null
    ]
];

// Carregar os scripts e estilos necessários
wp_enqueue_style('asaas-form-style');
wp_enqueue_script('jquery');
wp_enqueue_script('asaas-form-utils');
wp_enqueue_script('asaas-form-masks');
wp_enqueue_script('asaas-form-ui');
wp_enqueue_script('asaas-form-payment-method');
wp_enqueue_script('asaas-form-ajax');
wp_enqueue_script('asaas-form-script');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Exibição PIX</title>
    <?php wp_head(); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-section {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .mock-form {
            margin-top: 20px;
        }
        h1, h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Teste de Exibição do QR Code PIX</h1>

    <div class="test-section">
        <h2>Simulação de Resposta PIX</h2>
        <p>Este teste simula como ficará a exibição após o processamento do pagamento.</p>
        
        <!-- Formulário mock para testar a função displayPaymentResponse -->
        <div class="mock-form" id="mock-form">
            <!-- Aqui o formulário será substituído pela resposta -->
        </div>
        
        <button id="simulate-response" class="asaas-submit-button">Simular Resposta PIX</button>
    </div>
    
    <div class="test-section">
        <h2>Formulário Real de Doação</h2>
        <?php echo do_shortcode('[asaas_single_donation_v2]'); ?>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Botão para simular resposta PIX
        $('#simulate-response').on('click', function() {
            // Simular a resposta
            const mockResponse = <?php echo json_encode($mock_response); ?>;
            
            // Exibir a resposta simulada usando a função que definimos
            if (typeof displayPaymentResponse === 'function') {
                displayPaymentResponse($('#mock-form'), mockResponse.data);
            } else {
                // Fallback simples caso a função não exista ainda
                $('#mock-form').html(`
                    <div class="asaas-response">
                        <div class="asaas-success-message">
                            <h3>Doação realizada com sucesso!</h3>
                            <p>Obrigado por sua contribuição.</p>
                        </div>
                        <div class="asaas-pix-container">
                            <h4>Pagamento via PIX</h4>
                            <p>Escaneie o QR Code abaixo ou copie o código PIX:</p>
                            <div class="asaas-pix-qrcode">
                                <img src="data:image/png;base64,${mockResponse.data.pix_code}" alt="QR Code PIX">
                            </div>
                            <div class="asaas-pix-text">
                                <p>Código PIX:</p>
                                <div class="asaas-pix-copy">
                                    <input type="text" readonly value="${mockResponse.data.pix_text}">
                                    <button class="asaas-copy-button" data-clipboard="${mockResponse.data.pix_text}">Copiar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }
        });
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>