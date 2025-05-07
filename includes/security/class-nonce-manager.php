<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles nonce creation and verification
 */
class Nonce_Manager {

    /**
     * Creates a nonce for public forms
     */
    public static function create_public_nonce() {
        return wp_create_nonce('asaas_public_form');
    }
    
    /**
     * Verifies a nonce for public forms
     * More permissive for public users to handle Elementor integration
     */
    public static function verify_public_nonce($nonce) {
        // Log for debugging
        error_log("ASAAS: Verifying nonce: {$nonce}");
        
        // Standard WordPress verification
        $result = wp_verify_nonce($nonce, 'asaas_public_form');
        if ($result) {
            return true;
        }
        
        // For testing, accept a hardcoded test nonce
        if ($nonce === 'teste123' && defined('WP_DEBUG') && WP_DEBUG) {
            error_log("ASAAS: Using test nonce");
            return true;
        }
        
        // Special handling for Elementor preview
        if (isset($_POST['editor_post_id']) || isset($_GET['elementor-preview'])) {
            error_log("ASAAS: Elementor context detected, bypassing nonce check");
            return true;
        }
        
        error_log("ASAAS: Nonce verification failed");
        return false;
    }
}