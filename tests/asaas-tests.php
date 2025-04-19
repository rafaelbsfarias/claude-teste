<?php
/**
 * Unit Tests for Asaas Integration Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Asaas_Tests {
    
    /**
     * Run all tests
     */
    public function run_tests() {
        $this->test_create_customer();
        $this->test_create_payment_pix();
        $this->test_create_payment_boleto();
        $this->test_create_payment_credit_card();
        $this->test_create_subscription();
        $this->test_get_pix_info();
    }
    
    /**
     * Test customer creation
     */
    public function test_create_customer() {
        $customer_data = array(
            'name' => 'Test Customer',
            'cpfCnpj' => '24971563792',
            'email' => 'test@example.com'
        );
        
        $result = asaas_create_customer($customer_data);
        
        echo '<p><strong>Test Create Customer:</strong> ';
        if (isset($result['id']) && $result['object'] === 'customer') {
            echo 'PASSED';
        } else {
            echo 'FAILED - ' . json_encode($result);
        }
        echo '</p>';
        
        return isset($result['id']) ? $result['id'] : null;
    }
    
    /**
     * Test PIX payment creation
     */
    public function test_create_payment_pix() {
        $customer_id = $this->test_create_customer();
        
        if (!$customer_id) {
            echo '<p><strong>Test Create PIX Payment:</strong> SKIPPED - Customer creation failed</p>';
            return;
        }
        
        $payment_data = array(
            'customer' => $customer_id,
            'billingType' => 'PIX',
            'value' => 19.99,
            'dueDate' => gmdate('Y-m-d', strtotime('+5 days'))
        );
        
        $result = asaas_create_payment($payment_data);
        
        echo '<p><strong>Test Create PIX Payment:</strong> ';
        if (isset($result['id']) && $result['object'] === 'payment' && $result['billingType'] === 'PIX') {
            echo 'PASSED';
        } else {
            echo 'FAILED - ' . json_encode($result);
        }
        echo '</p>';
        
        return isset($result['id']) ? $result['id'] : null;
    }
    
    /**
     * Test Boleto payment creation
     */
    public function test_create_payment_boleto() {
        $customer_id = $this->test_create_customer();
        
        if (!$customer_id) {
            echo '<p><strong>Test Create Boleto Payment:</strong> SKIPPED - Customer creation failed</p>';
            return;
        }
        
        $payment_data = array(
            'customer' => $customer_id,
            'billingType' => 'BOLETO',
            'value' => 29.99,
            'dueDate' => gmdate('Y-m-d', strtotime('+5 days'))
        );
        
        $result = asaas_create_payment($payment_data);
        
        echo '<p><strong>Test Create Boleto Payment:</strong> ';
        if (isset($result['id']) && $result['object'] === 'payment' && $result['billingType'] === 'BOLETO') {
            echo 'PASSED';
        } else {
            echo 'FAILED - ' . json_encode($result);
        }
        echo '</p>';
    }
    
    /**
     * Test Credit Card payment creation
     */
    public function test_create_payment_credit_card() {
        $customer_id = $this->test_create_customer();
        
        if (!$customer_id) {
            echo '<p><strong>Test Create Credit Card Payment:</strong> SKIPPED - Customer creation failed</p>';
            return;
        }
        
        $payment_data = array(
            'customer' => $customer_id,
            'billingType' => 'CREDIT_CARD',
            'value' => 39.99,
            'dueDate' => gmdate('Y-m-d', strtotime('+1 day')),
            'creditCard' => array(
                'holderName' => 'Test Customer',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2030',
                'ccv' => '123'
            ),
            'creditCardHolderInfo' => array(
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'cpfCnpj' => '24971563792',
                'postalCode' => '12345678',
                'addressNumber' => '123',
                'phone' => '11999999999'
            )
        );
        
        $result = asaas_create_payment($payment_data);
        
        echo '<p><strong>Test Create Credit Card Payment:</strong> ';
        if (isset($result['id']) && $result['object'] === 'payment' && $result['billingType'] === 'CREDIT_CARD') {
            echo 'PASSED';
        } else {
            echo 'FAILED - ' . json_encode($result);
        }
        echo '</p>';
    }
    
    /**
     * Test subscription creation
     */
    public function test_create_subscription() {
        $customer_id = $this->test_create_customer();
        
        if (!$customer_id) {
            echo '<p><strong>Test Create Subscription:</strong> SKIPPED - Customer creation failed</p>';
            return;
        }
        
        $subscription_data = array(
            'customer' => $customer_id,
            'billingType' => 'CREDIT_CARD',
            'cycle' => 'MONTHLY',
            'value' => 49.99,
            'nextDueDate' => gmdate('Y-m-d', strtotime('+5 days')),
            'creditCard' => array(
                'holderName' => 'Test Customer',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2030',
                'ccv' => '123'
            ),
            'creditCardHolderInfo' => array(
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'cpfCnpj' => '24971563792',
                'postalCode' => '12345678',
                'addressNumber' => '123',
                'phone' => '11999999999'
            )
        );
        
        $result = asaas_create_subscription($subscription_data);
        
        echo '<p><strong>Test Create Subscription:</strong> ';
        if (isset($result['id']) && $result['object'] === 'subscription') {
            echo 'PASSED';
        } else {
            echo 'FAILED - ' . json_encode($result);
        }
        echo '</p>';
    }
    
    /**
     * Test getting PIX QR code info
     */
    public function test_get_pix_info() {
        $payment_id = $this->test_create_payment_pix();
        
        if (!$payment_id) {
            echo '<p><strong>Test Get PIX Info:</strong> SKIPPED - PIX payment creation failed</p>';
            return;
        }
        
        $result = asaas_get_pix_info($payment_id);
        
        echo '<p><strong>Test Get PIX Info:</strong> ';
        if (isset($result['payload']) && isset($result['encodedImage'])) {
            echo 'PASSED';
        } else {
            echo 'FAILED - ' . json_encode($result);
        }
        echo '</p>';
    }
}

// Add test page to admin menu
add_action('admin_menu', 'asaas_add_test_page');

function asaas_add_test_page() {
    add_submenu_page(
        'asaas-settings',
        'Asaas Tests',
        'Run Tests',
        'manage_options',
        'asaas-tests',
        'asaas_run_tests_page'
    );
}

function asaas_run_tests_page() {
    ?>
    <div class="wrap">
        <h1>Asaas Integration Tests</h1>
        
        <div class="notice notice-info">
            <p>Running tests will create real customers, payments, and subscriptions in your Asaas sandbox account.</p>
        </div>
        
        <form method="post">
            <?php wp_nonce_field('asaas_run_tests', 'asaas_run_tests_nonce'); ?>
            <p>
                <input type="submit" name="run_tests" class="button button-primary" value="Run All Tests">
            </p>
        </form>
        
        <?php
        // Run tests if requested
        if (isset($_POST['run_tests']) && isset($_POST['asaas_run_tests_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['asaas_run_tests_nonce'])), 'asaas_run_tests')) {
            echo '<div class="card">';
            echo '<h2>Test Results</h2>';
            
            $tests = new Asaas_Tests();
            $tests->run_tests();
            
            echo '</div>';
        }
        ?>
    </div>
    <?php
}