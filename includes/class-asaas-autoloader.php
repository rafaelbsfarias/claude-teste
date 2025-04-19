<?php
/**
 * Autoloader para classes do plugin.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

/**
 * Classe responsável pelo autoloading das classes do plugin.
 */
class Asaas_Autoloader {

    /**
     * Registra o autoloader.
     *
     * @since 1.0.0
     */
    public static function register() {
        spl_autoload_register(array(self::class, 'autoload'));
    }

    /**
     * Autoload das classes.
     *
     * @param string $class Nome completo da classe.
     * @since 1.0.0
     */
    public static function autoload($class) {
        // Namespace base do plugin
        $namespace = 'Asaas_Customer_Registration\\';

        // Verifica se a classe pertence ao namespace do plugin
        if (strpos($class, $namespace) !== 0) {
            return;
        }

        // Remove o namespace base
        $class = str_replace($namespace, '', $class);

        // Converte namespace em caminho de diretório
        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        
        // Converte CamelCase para separadores de arquivo
        $file_path = self::camel_case_to_file_name($class_path);

        // Caminho completo para o arquivo
        $file = ASAAS_PLUGIN_DIR . $file_path . '.php';

        // Carrega o arquivo se existir
        if (file_exists($file)) {
            require_once $file;
        }
    }

    /**
     * Converte nomes de classe em CamelCase para formato de arquivo com hífens.
     *
     * @param string $class_name Nome da classe em CamelCase.
     * @return string Nome do arquivo formatado com hífens e lowercase.
     * @since 1.0.0
     */
    private static function camel_case_to_file_name($class_name) {
        $parts = explode('\\', $class_name);
        $file_name = array_pop($parts);
        
        // Converte CamelCase para lowercase com hífens
        $file_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $file_name));
        
        // Reconstrói o caminho
        $parts[] = $file_name;
        $path = implode(DIRECTORY_SEPARATOR, $parts);
        
        return $path;
    }
}