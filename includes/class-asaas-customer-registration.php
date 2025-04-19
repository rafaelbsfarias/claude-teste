<?php
/**
 * Classe principal do plugin.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

use Asaas_Customer_Registration\Admin\Asaas_Admin;
use Asaas_Customer_Registration\Public\Asaas_Public;

/**
 * Classe principal do plugin.
 *
 * Responsável por coordenar todas as funcionalidades do plugin.
 */
class Asaas_Customer_Registration {

    /**
     * Loader que registra todos os hooks com WordPress.
     *
     * @var Asaas_Loader
     * @since 1.0.0
     */
    protected $loader;

    /**
     * Inicializa a classe e define propriedades.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Carrega as dependências necessárias para o plugin.
     *
     * @since 1.0.0
     */
    private function load_dependencies() {
        $this->loader = new Asaas_Loader();
    }

    /**
     * Define a localização para internacionalização.
     *
     * @since 1.0.0
     */
    private function set_locale() {
        $plugin_i18n = new Asaas_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Registra os hooks relacionados à funcionalidade admin.
     *
     * @since 1.0.0
     */
    private function define_admin_hooks() {
        $plugin_admin = new Asaas_Admin();
        
        // Registra estilos e scripts
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Adiciona menu de configurações
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_settings_page');
        
        // Registra configurações
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
    }

    /**
     * Registra os hooks relacionados à funcionalidade pública.
     *
     * @since 1.0.0
     */
    private function define_public_hooks() {
        $plugin_public = new Asaas_Public();
        
        // Registra estilos e scripts
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Registra shortcodes
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');
        
        // Registra endpoints AJAX
        $this->loader->add_action('wp_ajax_asaas_register_customer', $plugin_public, 'process_register_customer');
        $this->loader->add_action('wp_ajax_nopriv_asaas_register_customer', $plugin_public, 'process_register_customer');
        
        $this->loader->add_action('wp_ajax_asaas_create_payment', $plugin_public, 'process_create_payment');
        $this->loader->add_action('wp_ajax_nopriv_asaas_create_payment', $plugin_public, 'process_create_payment');
    }

    /**
     * Executa o loader para registrar todos os hooks com WordPress.
     *
     * @since 1.0.0
     */
    public function run() {
        $this->loader->run();
    }
}