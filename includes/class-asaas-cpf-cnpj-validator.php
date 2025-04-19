<?php
/**
 * Validador para CPF e CNPJ.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

/**
 * Classe para validação de CPF e CNPJ.
 *
 * Estende a classe base Asaas_Validator, aplicando o princípio de
 * Herança (Liskov Substitution Principle) do SOLID.
 */
class Asaas_Cpf_Cnpj_Validator extends Asaas_Validator {

    /**
     * Valida um CPF ou CNPJ.
     *
     * @param string $document CPF ou CNPJ a ser validado.
     * @return bool True se o documento é válido, false caso contrário.
     * @since 1.0.0
     */
    public function validate($document) {
        // Limpa mensagens de erro anteriores
        $this->clear_error_messages();
        
        // Remove qualquer formatação
        $document = preg_replace('/[^0-9]/', '', $document);
        
        // Verifica se é CPF (11 dígitos) ou CNPJ (14 dígitos)
        if (strlen($document) === 11) {
            return $this->validate_cpf($document);
        } elseif (strlen($document) === 14) {
            return $this->validate_cnpj($document);
        } else {
            $this->add_error_message(
                __('Documento inválido. Um CPF deve ter 11 dígitos e um CNPJ deve ter 14 dígitos.', 'asaas-customer-registration')
            );
            return false;
        }
    }
    
    /**
     * Valida um CPF.
     *
     * @param string $cpf CPF a ser validado (apenas dígitos).
     * @return bool True se o CPF é válido, false caso contrário.
     * @since 1.0.0
     */
    private function validate_cpf($cpf) {
        // Verifica se é uma sequência de dígitos repetidos
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $this->add_error_message(
                __('CPF inválido. Sequências de dígitos repetidos não são válidas.', 'asaas-customer-registration')
            );
            return false;
        }
        
        // Cálculo do primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }
        
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        // Verifica o primeiro dígito verificador
        if ($cpf[9] != $digit1) {
            $this->add_error_message(
                __('CPF inválido. Primeiro dígito verificador incorreto.', 'asaas-customer-registration')
            );
            return false;
        }
        
        // Cálculo do segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }
        
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        // Verifica o segundo dígito verificador
        if ($cpf[10] != $digit2) {
            $this->add_error_message(
                __('CPF inválido. Segundo dígito verificador incorreto.', 'asaas-customer-registration')
            );
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida um CNPJ.
     *
     * @param string $cnpj CNPJ a ser validado (apenas dígitos).
     * @return bool True se o CNPJ é válido, false caso contrário.
     * @since 1.0.0
     */
    private function validate_cnpj($cnpj) {
        // Verifica se é uma sequência de dígitos repetidos
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            $this->add_error_message(
                __('CNPJ inválido. Sequências de dígitos repetidos não são válidas.', 'asaas-customer-registration')
            );
            return false;
        }
        
        // Cálculo do primeiro dígito verificador
        $multipliers = array(5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
        $sum = 0;
        
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $multipliers[$i];
        }
        
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        // Verifica o primeiro dígito verificador
        if ($cnpj[12] != $digit1) {
            $this->add_error_message(
                __('CNPJ inválido. Primeiro dígito verificador incorreto.', 'asaas-customer-registration')
            );
            return false;
        }
        
        // Cálculo do segundo dígito verificador
        $multipliers = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
        $sum = 0;
        
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $multipliers[$i];
        }
        
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        // Verifica o segundo dígito verificador
        if ($cnpj[13] != $digit2) {
            $this->add_error_message(
                __('CNPJ inválido. Segundo dígito verificador incorreto.', 'asaas-customer-registration')
            );
            return false;
        }
        
        return true;
    }
}