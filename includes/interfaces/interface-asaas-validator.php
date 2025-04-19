<?php
/**
 * Interface para validadores.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes\Interfaces;

/**
 * Interface que define os métodos necessários para validação de dados.
 *
 * Seguindo o princípio de Segregação de Interface (ISP) do SOLID,
 * criamos uma interface específica para validações.
 */
interface Asaas_Validator_Interface {

    /**
     * Valida os dados conforme regras específicas.
     *
     * @param mixed $data Dados a serem validados.
     * @return bool True se os dados são válidos, false caso contrário.
     * @since 1.0.0
     */
    public function validate($data);
    
    /**
     * Retorna mensagens de erro após validação falhar.
     *
     * @return array Array com mensagens de erro.
     * @since 1.0.0
     */
    public function get_error_messages();
}