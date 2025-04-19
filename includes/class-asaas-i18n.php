<?php
/**
 * Define a funcionalidade de internacionalização.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

/**
 * Define a funcionalidade de internacionalização do plugin.
 *
 * Carrega os text domains para tradução.
 */
class Asaas_i18n {

    /**
     * Carrega o text domain para tradução.
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            ASAAS_TEXT_DOMAIN,
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}