<?php
/**
 * Plugin Name: Asaas Customer Registration
 * Plugin URI: https://exemplo.com/asaas-customer-registration
 * Description: Plugin para cadastro simples de clientes no Asaas usando apenas nome e CPF/CNPJ
 * Version: 1.0.0
 * Author: Claude
 * Author URI: https://exemplo.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: asaas-customer-registration
 * Domain Path: /languages
 */

// Se este arquivo for chamado diretamente, abortar.
if (!defined('WPINC')) {
    die;
}

/**
 * Versão atual do plugin.
 */
define('ASAAS_VERSION', '1.0.0');

/**
 * Caminho para o diretório do plugin.
 */
define('ASAAS_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * URL para o diretório do plugin.
 */
define('ASAAS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Nome do texto domain.
 */
define('ASAAS_TEXT_DOMAIN', 'asaas-customer-registration');

/**
 * Carrega o autoloader.
 */
require_once ASAAS_PLUGIN_DIR . 'includes/class-asaas-autoloader.php';

/**
 * Carrega as funções de ativação, desativação e desinstalação.
 */
require_once ASAAS_PLUGIN_DIR . 'includes/class-asaas-activator.php';
require_once ASAAS_PLUGIN_DIR . 'includes/class-asaas-deactivator.php';

/**
 * Registra os hooks de ativação e desativação.
 */
register_activation_hook(__FILE__, array('Asaas_Customer_Registration\Includes\Asaas_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Asaas_Customer_Registration\Includes\Asaas_Deactivator', 'deactivate'));

/**
 * Inicializa o autoloader.
 */
Asaas_Customer_Registration\Includes\Asaas_Autoloader::register();

/**
 * Inicializa o plugin.
 */
function run_asaas_customer_registration() {
    $plugin = new Asaas_Customer_Registration\Includes\Asaas_Customer_Registration();
    $plugin->run();
}

run_asaas_customer_registration();