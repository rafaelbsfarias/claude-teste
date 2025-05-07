/**
 * Gerencia a exibição dos campos de cartão de crédito nos formulários
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar formulário de doação única
        setupSingleDonationForm();
        
        // Configurar formulário de doação recorrente
        setupRecurringDonationForm();
        
        /**
         * Configuração do formulário de doação única
         */
        function setupSingleDonationForm() {
            const form = document.getElementById('asaas-single-donation-form');
            if (!form) return;
            
            const cardFields = form.querySelector('.asaas-card-fields');
            if (!cardFields) return;
            
            const paymentMethodSelect = form.querySelector('select[name="payment_method"]');
            if (!paymentMethodSelect) return;
            
            // Função para atualizar a visibilidade dos campos de cartão
            function updateCardFieldsVisibility() {
                const showCardFields = paymentMethodSelect.value === 'card';
                
                cardFields.style.display = showCardFields ? 'block' : 'none';
                
                // Se os campos de cartão estiverem visíveis, adicionar required
                // Caso contrário, remover required para permitir envio do formulário
                const cardInputs = cardFields.querySelectorAll('input');
                cardInputs.forEach(function(input) {
                    if (showCardFields) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });
            }
            
            // Configurar visibilidade inicial
            updateCardFieldsVisibility();
            
            // Adicionar listener para mudanças no dropdown
            paymentMethodSelect.addEventListener('change', updateCardFieldsVisibility);
        }
        
        /**
         * Configuração do formulário de doação recorrente
         */
        function setupRecurringDonationForm() {
            const form = document.getElementById('asaas-recurring-donation-form');
            if (!form) return;
            
            const cardFields = form.querySelector('.asaas-card-fields');
            if (!cardFields) return;
            
            // Para doação recorrente, os campos de cartão estão sempre visíveis
            cardFields.style.display = 'block';
            
            // Garantir que todos os campos de cartão sejam obrigatórios
            const cardInputs = cardFields.querySelectorAll('input');
            cardInputs.forEach(function(input) {
                input.setAttribute('required', 'required');
            });
        }
    });
})();