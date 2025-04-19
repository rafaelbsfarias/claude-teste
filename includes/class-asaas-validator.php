<?php
/**
 * Classe base para validadores.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

use Asaas_Customer_Registration\Includes\Interfaces\Asaas_Validator_Interface;

/**
 * Classe base abstrata para validadores.
 *
 * Implementa a interface Asaas_Validator_Interface com funcionalidades
 * comuns a todos os validadores, seguindo o princípio de Open/Closed (OCP)
 * do SOLID para permitir extensão através de classes especializadas.
 */
abstract class Asaas_Validator implements Asaas_Validator_Interface {

    /**
     * Armazena as mensagens de erro.
     *
     * @var array
     * @since 1.0.0
     */
    protected $error_messages = array();
    
    /**
     * Valida os dados conforme regras específicas.
     *
     * Método abstrato que deve ser implementado pelas classes filhas.
     *
     * @param mixed $data Dados a serem validados.
     * @return bool True se os dados são válidos, false caso contrário.
     * @since 1.0.0
     */
    abstract public function validate($data);
    
    /**
     * Retorna mensagens de erro após validação falhar.
     *
     * @return array Array com mensagens de erro.
     * @since 1.0.0
     */
    public function get_error_messages() {
        return $this->error_messages;
    }
    
    /**
     * Adiciona uma mensagem de erro.
     *
     * @param string $message Mensagem de erro.
     * @since 1.0.0
     */
    protected function add_error_message($message) {
        $this->error_messages[] = $message;
    }
    
    /**
     * Limpa as mensagens de erro.
     *
     * @since 1.0.0
     */
    protected function clear_error_messages() {
        $this->error_messages = array();
    }
}