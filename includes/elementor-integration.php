<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers handlers for Elementor template integration
 */
function asaas_register_elementor_template_handler() {
    if (!class_exists('\\Elementor\\Plugin')) {
        return;
    }
    
    add_filter('elementor/template_library/get_template_content', 'asaas_inject_donation_form_to_template', 10, 2);
}
add_action('init', 'asaas_register_elementor_template_handler');

/**
 * Injects donation form content into Elementor templates
 */
function asaas_inject_donation_form_to_template($content, $template_id) {
    // Log template request for debugging
    error_log("ASAAS: Elementor requesting template: {$template_id}");
    
    // Check if it's one of our donation form templates
    if ($template_id === 'D-V2') {
        // Single donation template
        return do_shortcode('[asaas_single_donation]');
    } elseif ($template_id === 'DR-V2') {
        // Recurring donation template
        return do_shortcode('[asaas_recurring_donation]');
    }
    
    return $content;
}