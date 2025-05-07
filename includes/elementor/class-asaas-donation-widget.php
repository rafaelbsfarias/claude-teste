<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Asaas Donation Form Widget for Elementor
 */
class Asaas_Donation_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'asaas_donation_widget';
    }
    
    public function get_title() {
        return __('Asaas Donation Form', 'asaas-easy-subscription-plugin');
    }
    
    public function get_icon() {
        return 'eicon-form-horizontal';
    }
    
    public function get_categories() {
        return ['general'];
    }
    
    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Form Settings', 'asaas-easy-subscription-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'form_type',
            [
                'label' => __('Donation Type', 'asaas-easy-subscription-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'single',
                'options' => [
                    'single' => __('Single Donation', 'asaas-easy-subscription-plugin'),
                    'recurring' => __('Recurring Donation', 'asaas-easy-subscription-plugin'),
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if ($settings['form_type'] == 'recurring') {
            echo do_shortcode('[asaas_recurring_donation]');
        } else {
            echo do_shortcode('[asaas_single_donation]');
        }
    }
    
    // Add a plain content method for non-frontend rendering
    protected function content_template() {
        ?>
        <div class="elementor-asaas-donation-form-placeholder">
            <h3>Asaas Donation Form</h3>
            <p>This will display a donation form on the frontend.</p>
        </div>
        <?php
    }
}