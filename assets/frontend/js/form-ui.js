/**
 * Funções para manipulação da interface do usuário e exibição de campos
 */

(function() {
    'use strict';
    
    /**
     * Configura os toggles para exibir/ocultar campos de cartão quando o método de pagamento é alterado
     */
    function setupPaymentMethodToggles() {
        // Para formulário de doação única
        const paymentMethodSelect = document.querySelector('#single-donation-form #payment-method');
        const cardFields = document.querySelector('#single-donation-form #card-fields');
        
        if (paymentMethodSelect && cardFields) {
            // Configurar estado inicial com base na seleção
            toggleCardFields(paymentMethodSelect.value, cardFields);
            
            // Adicionar event listener para alterações futuras
            paymentMethodSelect.addEventListener('change', function() {
                toggleCardFields(this.value, cardFields);
            });
        }
        
        // Para formulário de doação recorrente, se existir
        const recurringPaymentMethodSelect = document.querySelector('#recurring-donation-form #payment-method');
        const recurringCardFields = document.querySelector('#recurring-donation-form #card-fields');
        
        if (recurringPaymentMethodSelect && recurringCardFields) {
            // Configurar estado inicial com base na seleção
            toggleCardFields(recurringPaymentMethodSelect.value, recurringCardFields);
            
            // Adicionar event listener para alterações futuras
            recurringPaymentMethodSelect.addEventListener('change', function() {
                toggleCardFields(this.value, recurringCardFields);
            });
        }
    }
    
    /**
     * Mostra ou esconde os campos de cartão com base no método de pagamento selecionado
     * 
     * @param {string} paymentMethod O método de pagamento selecionado
     * @param {HTMLElement} cardFieldsContainer O contêiner dos campos de cartão
     */
    function toggleCardFields(paymentMethod, cardFieldsContainer) {
        if (paymentMethod === 'card') {
            cardFieldsContainer.style.display = 'block';
            
            // Adicionar required aos campos de cartão
            const cardInputs = cardFieldsContainer.querySelectorAll('input');
            cardInputs.forEach(input => {
                input.setAttribute('required', '');
            });
        } else {
            cardFieldsContainer.style.display = 'none';
            
            // Remover required dos campos de cartão para não bloquear o envio
            const cardInputs = cardFieldsContainer.querySelectorAll('input');
            cardInputs.forEach(input => {
                input.removeAttribute('required');
            });
        }
    }
    
    // Expor funções públicas
    window.AsaasFormUI = {
        setupPaymentMethodToggles,
        toggleCardFields
    };
})();