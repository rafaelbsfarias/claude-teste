<?php
/**
 * Asaas API Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create a customer in Asaas
 *
 * @param array $customer_data Customer information
 * @return array|WP_Error Response data or error
 */
function asaas_create_customer($customer_data) {
    $url = ASAAS_API_URL . '/customers';
    
    $args = array(
        'headers' => array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'access_token' => ASAAS_API_TOKEN
        ),
        'body' => json_encode($customer_data),
        'method' => 'POST',
        'timeout' => 30
    );
    
    $response = wp_remote_post($url, $args);
    
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

/**
 * Create a payment in Asaas
 *
 * @param array $payment_data Payment information
 * @return array|WP_Error Response data or error
 */
function asaas_create_payment($payment_data) {
    $url = ASAAS_API_URL . '/payments';
    
    $args = array(
        'headers' => array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'access_token' => ASAAS_API_TOKEN
        ),
        'body' => json_encode($payment_data),
        'method' => 'POST',
        'timeout' => 30
    );
    
    $response = wp_remote_post($url, $args);
    
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

/**
 * Create a subscription in Asaas
 *
 * @param array $subscription_data Subscription information
 * @return array|WP_Error Response data or error
 */
function asaas_create_subscription($subscription_data) {
    $url = ASAAS_API_URL . '/subscriptions';
    
    $args = array(
        'headers' => array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'access_token' => ASAAS_API_TOKEN
        ),
        'body' => json_encode($subscription_data),
        'method' => 'POST',
        'timeout' => 30
    );
    
    $response = wp_remote_post($url, $args);
    
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

/**
 * Get PIX QR Code information
 *
 * @param string $payment_id Asaas payment ID
 * @return array|WP_Error Response data or error
 */
function asaas_get_pix_info($payment_id) {
    $url = ASAAS_API_URL . '/payments/' . $payment_id . '/pixQrCode';
    
    $args = array(
        'headers' => array(
            'Accept' => 'application/json',
            'access_token' => ASAAS_API_TOKEN
        ),
        'method' => 'GET',
        'timeout' => 30
    );
    
    $response = wp_remote_get($url, $args);
    
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}