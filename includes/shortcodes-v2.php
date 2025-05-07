<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once ASAAS_PLUGIN_DIR . 'includes/class-template-loader.php';

/**
 * Shortcode para formulário de doação única
 */
function asaas_single_donation_shortcode_v2($atts) {
    $atts = shortcode_atts([
        'title' => 'Doação',
    ], $atts, 'asaas_single_donation');
    
    return Asaas_Template_Loader::load_template('form-single-donation', ['form_data' => $atts]);
}

/**
 * Shortcode para formulário de doação recorrente
 */
function asaas_recurring_donation_shortcode_v2($atts) {
    $atts = shortcode_atts([
        'title' => 'Doação Mensal',
    ], $atts, 'asaas_recurring_donation');
    
    return Asaas_Template_Loader::load_template('form-recurring-donation', ['form_data' => $atts]);
}

/**
 * Registra os novos shortcodes
 */
function asaas_register_shortcodes_v2() {
    add_shortcode('asaas_single_donation_v2', 'asaas_single_donation_shortcode_v2');
    add_shortcode('asaas_recurring_donation_v2', 'asaas_recurring_donation_shortcode_v2');
}
add_action('init', 'asaas_register_shortcodes_v2');