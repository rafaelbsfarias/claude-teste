<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Elementor widget
 */
function asaas_register_elementor_widgets() {
    if (!class_exists('\Elementor\Plugin')) {
        return;
    }
    
    // Include widget class
    require_once ASAAS_PLUGIN_DIR . 'includes/elementor/class-asaas-donation-widget.php';
    
    // Register the widget
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Asaas_Donation_Widget());
}
add_action('elementor/widgets/widgets_registered', 'asaas_register_elementor_widgets');