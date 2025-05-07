<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue scripts and styles for the frontend
 */
function asaas_enqueue_scripts() {
    // Skip in admin or when Elementor is in edit mode
    if (is_admin()) {
        return;
    }
    
    // Check if we're in Elementor editor mode
    $is_elementor_edit_mode = false;
    if (class_exists('\Elementor\Plugin')) {
        $is_elementor_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
    }
    
    // Register and enqueue styles
    wp_register_style('asaas-form-style', ASAAS_PLUGIN_URL . 'assets/frontend/css/form-style.css', [], ASAAS_PLUGIN_VERSION);
    wp_enqueue_style('asaas-form-style');
    
    // Register scripts in proper dependency order
    wp_register_script('asaas-form-utils', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-utils.js', ['jquery'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-masks', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-masks.js', ['asaas-form-utils'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-ui', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-ui.js', ['asaas-form-utils'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-ajax', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-ajax.js', ['asaas-form-utils', 'jquery'], ASAAS_PLUGIN_VERSION, true);
    wp_register_script('asaas-form-script', ASAAS_PLUGIN_URL . 'assets/frontend/js/form-script.js', ['asaas-form-utils', 'asaas-form-masks', 'asaas-form-ui', 'asaas-form-ajax'], ASAAS_PLUGIN_VERSION, true);
    
    // Enqueue scripts
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-masks');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-ajax');
    wp_enqueue_script('asaas-form-script');
    
    // Localize script with correct AJAX URL and nonce
    wp_localize_script('asaas-form-ajax', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'site_url' => site_url(),
        'is_debug' => defined('WP_DEBUG') && WP_DEBUG,
        'nonce' => class_exists('Nonce_Manager') ? Nonce_Manager::create_public_nonce() : wp_create_nonce('asaas_public_form'),
        'is_elementor_edit' => $is_elementor_edit_mode
    ]);
}
// Use a later priority to ensure it works with Elementor
add_action('wp_enqueue_scripts', 'asaas_enqueue_scripts', 99);