/**
 * Funções para aplicação de máscaras e formatação de campos
 */

(function() {
    'use strict';
    
    /**
     * Aplica máscara monetária aos campos de entrada de valor
     */
    function applyMoneyMask() {
        const valueFields = document.querySelectorAll('input[name="donation_value"]');
        
        valueFields.forEach(field => {
            field.addEventListener('input', function(e) {
                // Salvar posição do cursor
                const start = this.selectionStart;
                const end = this.selectionEnd;
                const length = this.value.length;
                
                // Remover tudo que não for número
                let value = this.value.replace(/\D/g, '');
                
                // Converter para centavos
                value = (parseInt(value) / 100).toFixed(2);
                
                // Formatar com vírgula como separador decimal
                this.value = formatCurrency(value);
                
                // Ajustar a posição do cursor
                const newLength = this.value.length;
                const position = start + (newLength - length);
                this.setSelectionRange(position, position);
            });
            
            // Também formata ao focar no campo (caso já tenha algum valor)
            field.addEventListener('focus', function() {
                if (this.value) {
                    // Remove qualquer formatação existente
                    let value = this.value.replace(/\D/g, '');
                    if (value) {
                        // Se houver algum número, formata
                        value = (parseInt(value) / 100).toFixed(2);
                        this.value = formatCurrency(value);
                    }
                }
            });
            
            // Ao perder o foco, garante formatação completa
            field.addEventListener('blur', function() {
                if (this.value) {
                    // Remover qualquer formatação existente
                    let value = this.value.replace(/\D/g, '');
                    if (value) {
                        // Se houver algum número, formata
                        value = (parseInt(value) / 100).toFixed(2);
                        this.value = formatCurrency(value);
                    } else {
                        // Se não houver números, limpa o campo
                        this.value = '';
                    }
                }
            });
        });
    }
    
    /**
     * Formata um valor como moeda (R$ 1.234,56)
     * 
     * @param {string|number} value Valor a ser formatado
     * @returns {string} Valor formatado
     */
    function formatCurrency(value) {
        // Converter para número
        const numValue = parseFloat(value);
        
        if (isNaN(numValue)) {
            return '';
        }
        
        // Formatar com 2 casas decimais
        let formattedValue = numValue.toFixed(2).replace('.', ',');
        
        // Adicionar separadores de milhar
        const parts = formattedValue.split(',');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        return parts.join(',');
    }
    
    // Expor funções públicas
    window.AsaasFormMasks = {
        applyMoneyMask,
        formatCurrency
    };
})();