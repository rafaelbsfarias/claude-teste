<?php
/**
 * Asaas Payment Shortcodes
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register shortcodes
add_shortcode('asaas_payment', 'asaas_payment_shortcode');
add_shortcode('asaas_subscription', 'asaas_subscription_shortcode');

// Add necessary scripts and styles
add_action('wp_enqueue_scripts', 'asaas_enqueue_scripts');

function asaas_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('asaas-script', plugin_dir_url(__FILE__) . 'js/asaas-script.js', array('jquery'), '1.0.0', true);
    
    wp_localize_script('asaas-script', 'asaas_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asaas-nonce')
    ));
    
    wp_enqueue_style('asaas-style', plugin_dir_url(__FILE__) . 'css/asaas-style.css', array(), '1.0.0');
}

/**
 * One-time payment shortcode
 */
function asaas_payment_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'value' => '100.00',
            'description' => 'Payment',
            'due_date' => gmdate('Y-m-d', strtotime('+5 days')),
        ),
        $atts,
        'asaas_payment'
    );
    
    ob_start();
    ?>
    <div class="asaas-payment-form">
        <form id="asaas-payment-form" method="post">
            <?php wp_nonce_field('asaas_payment_nonce', 'asaas_payment_nonce'); ?>
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="cpf_cnpj">CPF/CNPJ</label>
                <input type="text" id="cpf_cnpj" name="cpf_cnpj" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="PIX">PIX</option>
                    <option value="BOLETO">Boleto</option>
                    <option value="CREDIT_CARD">Credit Card</option>
                </select>
            </div>
            
            <div id="credit-card-fields" style="display: none;">
                <div class="form-group">
                    <label for="card_holder_name">Card Holder Name</label>
                    <input type="text" id="card_holder_name" name="card_holder_name">
                </div>
                
                <div class="form-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry_month">Expiry Month</label>
                        <input type="text" id="expiry_month" name="expiry_month" placeholder="MM">
                    </div>
                    
                    <div class="form-group">
                        <label for="expiry_year">Expiry Year</label>
                        <input type="text" id="expiry_year" name="expiry_year" placeholder="YYYY">
                    </div>
                    
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code">
                </div>
                
                <div class="form-group">
                    <label for="address_number">Address Number</label>
                    <input type="text" id="address_number" name="address_number">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone">
                </div>
            </div>
            
            <input type="hidden" name="value" value="<?php echo esc_attr($atts['value']); ?>">
            <input type="hidden" name="description" value="<?php echo esc_attr($atts['description']); ?>">
            <input type="hidden" name="due_date" value="<?php echo esc_attr($atts['due_date']); ?>">
            <input type="hidden" name="action" value="asaas_process_payment">
            
            <div class="form-group">
                <button type="submit" class="button button-primary">Make Payment</button>
            </div>
        </form>
        
        <div id="asaas-payment-response"></div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Subscription shortcode
 */
function asaas_subscription_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'value' => '100.00',
            'description' => 'Monthly Subscription',
            'next_due_date' => gmdate('Y-m-d', strtotime('+5 days')),
        ),
        $atts,
        'asaas_subscription'
    );
    
    ob_start();
    ?>
    <div class="asaas-subscription-form">
        <form id="asaas-subscription-form" method="post">
            <?php wp_nonce_field('asaas_subscription_nonce', 'asaas_subscription_nonce'); ?>
            
            <div class="form-group">
                <label for="sub_name">Full Name</label>
                <input type="text" id="sub_name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="sub_cpf_cnpj">CPF/CNPJ</label>
                <input type="text" id="sub_cpf_cnpj" name="cpf_cnpj" required>
            </div>
            
            <div class="form-group">
                <label for="sub_email">Email</label>
                <input type="email" id="sub_email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="sub_card_holder_name">Card Holder Name</label>
                <input type="text" id="sub_card_holder_name" name="card_holder_name" required>
            </div>
            
            <div class="form-group">
                <label for="sub_card_number">Card Number</label>
                <input type="text" id="sub_card_number" name="card_number" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sub_expiry_month">Expiry Month</label>
                    <input type="text" id="sub_expiry_month" name="expiry_month" placeholder="MM" required>
                </div>
                
                <div class="form-group">
                    <label for="sub_expiry_year">Expiry Year</label>
                    <input type="text" id="sub_expiry_year" name="expiry_year" placeholder="YYYY" required>
                </div>
                
                <div class="form-group">
                    <label for="sub_cvv">CVV</label>
                    <input type="text" id="sub_cvv" name="cvv" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="sub_postal_code">Postal Code</label>
                <input type="text" id="sub_postal_code" name="postal_code" required>
            </div>
            
            <div class="form-group">
                <label for="sub_address_number">Address Number</label>
                <input type="text" id="sub_address_number" name="address_number" required>
            </div>
            
            <div class="form-group">
                <label for="sub_phone">Phone</label>
                <input type="text" id="sub_phone" name="phone" required>
            </div>
            
            <input type="hidden" name="value" value="<?php echo esc_attr($atts['value']); ?>">
            <input type="hidden" name="description" value="<?php echo esc_attr($atts['description']); ?>">
            <input type="hidden" name="next_due_date" value="<?php echo esc_attr($atts['next_due_date']); ?>">
            <input type="hidden" name="action" value="asaas_process_subscription">
            
            <div class="form-group">
                <button type="submit" class="button button-primary">Subscribe</button>
            </div>
        </form>
        
        <div id="asaas-subscription-response"></div>
    </div>
    <?php
    return ob_get_clean();
}

// Process payment AJAX
add_action('wp_ajax_asaas_process_payment', 'asaas_process_payment_ajax');
add_action('wp_ajax_nopriv_asaas_process_payment', 'asaas_process_payment_ajax');

function asaas_process_payment_ajax() {
    // Verify nonce
    if (!isset($_POST['asaas_payment_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['asaas_payment_nonce'])), 'asaas_payment_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    // Get form data
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $cpf_cnpj = isset($_POST['cpf_cnpj']) ? sanitize_text_field(wp_unslash($_POST['cpf_cnpj'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $payment_method = isset($_POST['payment_method']) ? sanitize_text_field(wp_unslash($_POST['payment_method'])) : '';
    $value = isset($_POST['value']) ? (float) $_POST['value'] : 0;
    $description = isset($_POST['description']) ? sanitize_text_field(wp_unslash($_POST['description'])) : '';
    $due_date = isset($_POST['due_date']) ? sanitize_text_field(wp_unslash($_POST['due_date'])) : '';
    
    // Create customer
    $customer_data = array(
        'name' => $name,
        'cpfCnpj' => $cpf_cnpj,
        'email' => $email
    );
    
    $customer_response = asaas_create_customer($customer_data);
    
    if (is_wp_error($customer_response) || isset($customer_response['errors'])) {
        wp_send_json_error('Error creating customer: ' . json_encode($customer_response));
    }
    
    $customer_id = $customer_response['id'];
    
    // Prepare payment data
    $payment_data = array(
        'customer' => $customer_id,
        'billingType' => $payment_method,
        'value' => $value,
        'dueDate' => $due_date,
        'description' => $description
    );
    
    // Add credit card information if needed
    if ($payment_method === 'CREDIT_CARD') {
        $card_holder_name = isset($_POST['card_holder_name']) ? sanitize_text_field(wp_unslash($_POST['card_holder_name'])) : '';
        $card_number = isset($_POST['card_number']) ? sanitize_text_field(wp_unslash($_POST['card_number'])) : '';
        $expiry_month = isset($_POST['expiry_month']) ? sanitize_text_field(wp_unslash($_POST['expiry_month'])) : '';
        $expiry_year = isset($_POST['expiry_year']) ? sanitize_text_field(wp_unslash($_POST['expiry_year'])) : '';
        $cvv = isset($_POST['cvv']) ? sanitize_text_field(wp_unslash($_POST['cvv'])) : '';
        $postal_code = isset($_POST['postal_code']) ? sanitize_text_field(wp_unslash($_POST['postal_code'])) : '';
        $address_number = isset($_POST['address_number']) ? sanitize_text_field(wp_unslash($_POST['address_number'])) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
        
        $payment_data['creditCard'] = array(
            'holderName' => $card_holder_name,
            'number' => $card_number,
            'expiryMonth' => $expiry_month,
            'expiryYear' => $expiry_year,
            'ccv' => $cvv
        );
        
        $payment_data['creditCardHolderInfo'] = array(
            'name' => $card_holder_name,
            'email' => $email,
            'cpfCnpj' => $cpf_cnpj,
            'postalCode' => $postal_code,
            'addressNumber' => $address_number,
            'phone' => $phone
        );
    }
    
    // Create payment
    $payment_response = asaas_create_payment($payment_data);
    
    if (is_wp_error($payment_response) || isset($payment_response['errors'])) {
        wp_send_json_error('Error creating payment: ' . json_encode($payment_response));
    }
    
    // Get PIX QR Code if using PIX
    if ($payment_method === 'PIX') {
        $pix_info = asaas_get_pix_info($payment_response['id']);
        $payment_response['pixInfo'] = $pix_info;
    }
    
    // Return success response
    wp_send_json_success(array(
        'customer' => $customer_response,
        'payment' => $payment_response
    ));
}

// Process subscription AJAX
add_action('wp_ajax_asaas_process_subscription', 'asaas_process_subscription_ajax');
add_action('wp_ajax_nopriv_asaas_process_subscription', 'asaas_process_subscription_ajax');

function asaas_process_subscription_ajax() {
    // Verify nonce
    if (!isset($_POST['asaas_subscription_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['asaas_subscription_nonce'])), 'asaas_subscription_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    // Get form data
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $cpf_cnpj = isset($_POST['cpf_cnpj']) ? sanitize_text_field(wp_unslash($_POST['cpf_cnpj'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $value = isset($_POST['value']) ? (float) $_POST['value'] : 0;
    $description = isset($_POST['description']) ? sanitize_text_field(wp_unslash($_POST['description'])) : '';
    $next_due_date = isset($_POST['next_due_date']) ? sanitize_text_field(wp_unslash($_POST['next_due_date'])) : '';
    
    // Credit card data
    $card_holder_name = isset($_POST['card_holder_name']) ? sanitize_text_field(wp_unslash($_POST['card_holder_name'])) : '';
    $card_number = isset($_POST['card_number']) ? sanitize_text_field(wp_unslash($_POST['card_number'])) : '';
    $expiry_month = isset($_POST['expiry_month']) ? sanitize_text_field(wp_unslash($_POST['expiry_month'])) : '';
    $expiry_year = isset($_POST['expiry_year']) ? sanitize_text_field(wp_unslash($_POST['expiry_year'])) : '';
    $cvv = isset($_POST['cvv']) ? sanitize_text_field(wp_unslash($_POST['cvv'])) : '';
    $postal_code = isset($_POST['postal_code']) ? sanitize_text_field(wp_unslash($_POST['postal_code'])) : '';
    $address_number = isset($_POST['address_number']) ? sanitize_text_field(wp_unslash($_POST['address_number'])) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    
    // Create customer
    $customer_data = array(
        'name' => $name,
        'cpfCnpj' => $cpf_cnpj,
        'email' => $email
    );
    
    $customer_response = asaas_create_customer($customer_data);
    
    if (is_wp_error($customer_response) || isset($customer_response['errors'])) {
        wp_send_json_error('Error creating customer: ' . json_encode($customer_response));
    }
    
    $customer_id = $customer_response['id'];
    
    // Prepare subscription data
    $subscription_data = array(
        'customer' => $customer_id,
        'billingType' => 'CREDIT_CARD',
        'cycle' => 'MONTHLY',
        'value' => $value,
        'nextDueDate' => $next_due_date,
        'description' => $description,
        'creditCard' => array(
            'holderName' => $card_holder_name,
            'number' => $card_number,
            'expiryMonth' => $expiry_month,
            'expiryYear' => $expiry_year,
            'ccv' => $cvv
        ),
        'creditCardHolderInfo' => array(
            'name' => $card_holder_name,
            'email' => $email,
            'cpfCnpj' => $cpf_cnpj,
            'postalCode' => $postal_code,
            'addressNumber' => $address_number,
            'phone' => $phone
        )
    );
    
    // Create subscription
    $subscription_response = asaas_create_subscription($subscription_data);
    
    if (is_wp_error($subscription_response) || isset($subscription_response['errors'])) {
        wp_send_json_error('Error creating subscription: ' . json_encode($subscription_response));
    }
    
    // Return success response
    wp_send_json_success(array(
        'customer' => $customer_response,
        'subscription' => $subscription_response
    ));
}