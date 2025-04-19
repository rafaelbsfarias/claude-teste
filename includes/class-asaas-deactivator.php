<?php
/**
 * Executa na desativação do plugin.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

/**
 * Classe contendo funcionalidades executadas na desativação do plugin.
 */
class Asaas_Deactivator {

    /**
     * Método executado na desativação do plugin.
     *
     * @since 1.0.0
     */
    public static function deactivate() {
        // Limpar cache de reescrita
        flush_rewrite_rules();
        
        // Aqui poderíamos adicionar outras tarefas de limpeza,
        // mas como a especificação pede para não salvar dados,
        // não temos muitas tarefas a realizar.
    }
}