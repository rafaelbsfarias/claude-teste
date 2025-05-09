/**
 * Funções para manipulação da interface do usuário e exibição de campos
 */

(function() {
    'use strict';
    ////////////////////////////////////////



    (function() {
        'use strict';
      
        /**
         * @class PaymentMethodToggler
         * Encapsula a lógica de mostrar/esconder campos de cartão e de required.
         */
        class PaymentMethodToggler {
          /**
           * @param {HTMLFormElement} formElement
           */
          constructor(formElement) {
            this.form = formElement;
            this.select = this.form.querySelector('.payment-method');
            this.cardContainer = this.form.querySelector('.card-fields');
          }
      
          init() {
            if (!this.select || !this.cardContainer) return;
            this._toggle(this.select.value);
            this.select.addEventListener('change', () => {
              this._toggle(this.select.value);
            });
          }
      
          /**
           * @param {string} paymentMethod
           * @private
           */
          _toggle(paymentMethod) {
            const show = paymentMethod === 'card';
            this.cardContainer.style.display = show ? '' : 'none';
            this.cardContainer
              .querySelectorAll('input')
              .forEach(input => {
                if (show) input.setAttribute('required', '');
                else     input.removeAttribute('required');
              });
          }
        }
      
        /**
         * @class FormUIController
         * Encontra todos os formulários e aplica o PaymentMethodToggler
         */
        class FormUIController {
          static init() {
            // selecione ambos single e recurring (ajuste os seletores ao seu HTML)
            const forms = Array.from(document.querySelectorAll('.single-donation-form, .recurring-donation-form'));
            forms.forEach(form => new PaymentMethodToggler(form).init());
          }
        }
      
        // exporta apenas o método que o form-script.js espera
        window.AsaasFormUI = window.AsaasFormUI || {};
        window.AsaasFormUI.setupPaymentMethodToggles = FormUIController.init;
      
        // — aqui continuam as outras funções (displaySuccess, etc.) —
      })();
      




    //////////////////////////////////////
    
    /**
     * Exibe uma mensagem no formulário
     * 
     * @param {HTMLFormElement} form Formulário onde a mensagem será exibida
     * @param {string} message Mensagem a ser exibida
     * @param {string} type Tipo da mensagem ('success' ou 'error')
     */
    function displayMessage(form, message, type) {
        // Limpar mensagens anteriores
        if (window.AsaasFormUtils && AsaasFormUtils.clearMessages) {
            AsaasFormUtils.clearMessages(form);
        }
        
        // Criar o elemento de mensagem
        const messageDiv = document.createElement('div');
        messageDiv.className = `asaas-message asaas-message-${type}`;
        messageDiv.innerHTML = message;
        
        // Adicionar ID único para a mensagem de erro (para referência de rolagem)
        if (type === 'error') {
            messageDiv.id = 'asaas-error-message';
        }
        
        // Inserir a mensagem antes do formulário
        form.parentNode.insertBefore(messageDiv, form);
        
        // Se for mensagem de erro, rolar até ela
        if (type === 'error') {
            setTimeout(() => {
                messageDiv.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }, 100);
        }
    }
    
    /**
     * Exibe a mensagem de sucesso formatada após uma doação
     * 
     * @param {HTMLFormElement} form Formulário que foi enviado
     * @param {Object} data Os dados da doação
     * @param {string} donationType Tipo de doação ('recurring' ou 'single')
     */
    function displaySuccess(form, data, donationType) {
        // Log de depuração para ver o que está chegando
        console.log('Raw response:', data);
        console.log('Payment method:', data.data?.payment_method);
        
        // Verificar se os dados estão aninhados (comum em respostas de API)
        const responseData = data.data || data;
        
        // Obter dados da resposta
        let value = responseData.value || responseData.donation_value || '';
        let formattedValue = value; // Formatação ocorrerá depois
        let paymentMethod = responseData.payment_method || '';
        
        // Cria o elemento de mensagem de sucesso
        const successDiv = document.createElement('div');
        successDiv.className = 'donation-success-container';
        
        // Verifica se é um pagamento via PIX
        if (donationType === 'single' && paymentMethod === 'pix') {
            // Obter dados específicos do PIX diretamente
            let pixCode = responseData.pix_code || '';
            let pixText = responseData.pix_text || '';
            
            // Verificar se os dados do PIX estão disponíveis
            if (pixCode && pixText) {
                let html = `
                    <div class="pix-success">
                        <h2 class="success-title">Só mais um passo!</h2>
                        <p>Leia o QR Code ou copie o código abaixo para realizar sua doação</p>
                        <div class="pix-info">
                            <div class="info-row">
                                <span class="info-label">Valor:</span>
                                <span class="info-value">R$ ${formattedValue}</span>
                            </div>
                        </div>
                        
                        <div class="pix-qrcode">
                            <img src="data:image/png;base64,${pixCode}" alt="QR Code PIX">
                        </div>
                        
                        <textarea id="pix-code-text" class="pix-code-text" readonly>${pixText}</textarea>
                        
                        <button class="btn btn-copy-wide" onclick="AsaasFormUtils.copyPixCode()">Copiar</button>
                    </div>
                `;
                
                successDiv.innerHTML = html;
            } else {
                // Mensagem padrão de sucesso
                let html = `
                    <h2>Doação realizada com sucesso!</h2>
                    <p>Obrigado por sua contribuição.</p>
                `;
                successDiv.innerHTML = html;
            }
        } else {
            // Mensagem padrão de sucesso para outros métodos de pagamento
            let html = `
                <h2>Doação realizada com sucesso!</h2>
                <p>Obrigado por sua contribuição.</p>
            `;
            successDiv.innerHTML = html;
        }
        
        // Limpa o formulário e exibe a mensagem de sucesso
        form.innerHTML = '';
        form.appendChild(successDiv);
    }
    
    /**
     * Define o estado do botão de submit (habilitado/desabilitado e texto)
     * 
     * @param {HTMLButtonElement} button O elemento do botão
     * @param {boolean} isLoading Se está carregando ou não
     * @param {string} text Texto a ser exibido
     */
    function setSubmitButtonState(button, isLoading, text) {
        if (button) {
            button.disabled = isLoading;
            button.textContent = text;
            
            // Adicionar/remover classe de carregamento se desejar estilização adicional
            if (isLoading) {
                button.classList.add('loading');
            } else {
                button.classList.remove('loading');
            }
        }
    }
    
    // Expor funções públicas
    window.AsaasFormUI = window.AsaasFormUI || {};
    window.AsaasFormUI.setupPaymentMethodToggles = FormUIController.init;
    window.AsaasFormUI.displaySuccess = displaySuccess;
    window.AsaasFormUI.displayMessage = displayMessage;
    window.AsaasFormUI.setSubmitButtonState = setSubmitButtonState;
})();