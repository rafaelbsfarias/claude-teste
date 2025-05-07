/**
 * Funções para processamento AJAX e exibição de mensagens em formulários de doação
 */

// Garante que esta seja uma biblioteca reutilizável
(function() {
    'use strict';
    
    /**
     * Processa o envio do formulário de doação via AJAX
     * 
     * @param {HTMLFormElement} form Formulário a ser processado
     * @param {string} loadingMessage Mensagem de carregamento
     */
    function processDonationForm(form, loadingMessage) {
        // Adicionar mensagem de carregamento
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton ? submitButton.textContent : 'Enviar';
        
        if (submitButton) {
            submitButton.textContent = loadingMessage;
            submitButton.disabled = true;
        }
        
        // Limpar mensagens anteriores
        AsaasFormUtils.clearMessages(form);
        
        // Obter todos os campos do formulário
        const formData = new FormData(form);
        
        try {
            // Verificar se ajax_object existe
            if (typeof ajax_object === 'undefined') {
                throw new Error('ajax_object não está definido');
            }
            
            // Enviar para o backend via AJAX
            fetch(ajax_object.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(response => {
                // Restaurar o botão
                if (submitButton) {
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                }
                
                if (response.success) {
                    // Usar a mesma função de exibição de sucesso para ambos os tipos de doação
                    const donationType = form.id === 'recurring-donation-form' ? 'recurring' : 'single';
                    showDonationSuccess(form, response.data, donationType);
                } else {
                    // Mostrar mensagem de erro
                    let errorMessage = 'Ocorreu um erro ao processar sua doação.';
                    
                    if (response.data) {
                        if (response.data.errors) {
                            if (typeof response.data.errors === 'object') {
                                errorMessage = Object.values(response.data.errors).join('<br>');
                            } else {
                                errorMessage = response.data.errors;
                            }
                        } else if (response.data.message) {
                            errorMessage = response.data.message;
                        }
                    }
                    
                    showMessage(form, errorMessage, 'error');
                }
            })
            .catch(error => {
                // Restaurar o botão
                if (submitButton) {
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                }
                
                // Mostrar mensagem de erro com rolagem automática
                showMessage(form, 'Erro de conexão. Por favor, tente novamente.', 'error');
            });
        } catch (error) {
            // Restaurar o botão
            if (submitButton) {
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
            }
            
            // Mostrar mensagem de erro
            showMessage(form, 'Erro ao processar o formulário. Por favor, tente novamente.', 'error');
        }
    }
    
    /**
     * Exibe a mensagem de sucesso formatada após uma doação
     * 
     * @param {HTMLFormElement} form Formulário que foi enviado
     * @param {Object} data Os dados da doação
     * @param {string} donationType Tipo de doação ('recurring' ou 'single')
     */
    function showDonationSuccess(form, data, donationType) {
        // Ocultar elementos do formulário
        form.style.display = 'none';
        AsaasFormUtils.clearMessages(form);
        
        // Ocultar também o título h2 e o parágrafo introdutório que estão fora do formulário
        const formContainer = form.closest('.asaas-donation-form');
        if (formContainer) {
            Array.from(formContainer.children).forEach(child => {
                if (child !== form && (child.tagName === 'H2' || child.tagName === 'P')) {
                    child.style.display = 'none';
                }
            });
        }
        
        // Verificar se os dados estão aninhados (comum em respostas de API)
        const responseData = data.data || data;
        
        // Obter dados da resposta
        let value = AsaasFormUtils.getDataValue(responseData, ['value', 'donation_value']);
        let formattedValue = AsaasFormUtils.formatCurrencyValue(value);
        let paymentMethod = responseData.payment_method || '';
        
        // Cria o elemento de mensagem de sucesso
        const successDiv = document.createElement('div');
        successDiv.className = 'donation-success-container';
        
        // Verifica se é um pagamento via boleto
        if (donationType === 'single' && paymentMethod === 'boleto') {
            // Dados específicos do boleto
            let dueDate = AsaasFormUtils.getDataValue(responseData, ['due_date', 'dueDate']);
            dueDate = AsaasFormUtils.formatDate(dueDate);
            
            let bankSlipUrl = responseData.bank_slip_url || '';
            let invoiceUrl = responseData.invoice_url || '';
            let boletoNumber = AsaasFormUtils.getDataValue(responseData, ['nossoNumero'], '');
            
            // HTML para página de sucesso do boleto
            let html = `
                <div class="boleto-success">
                    <h2 class="success-title">Doação Realizada com Sucesso!</h2>
                    <div class="boleto-info">
                        <div class="info-row">
                            <span class="info-label">Valor:</span>
                            <span class="info-value">R$ ${formattedValue}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Vencimento:</span>
                            <span class="info-value">${dueDate}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Número do Boleto:</span>
                            <span class="info-value">${boletoNumber}</span>
                        </div>
                    </div>
                    <div class="boleto-actions">
                        <a href="${bankSlipUrl}" target="_blank" class="btn btn-download">Baixar Boleto</a>
                        <a href="${invoiceUrl}" target="_blank" class="btn btn-invoice">Visualizar Fatura</a>
                    </div>
                    <button class="btn btn-close" onclick="AsaasFormUtils.hideDonationSuccess(this)">Fechar</button>
                </div>
            `;
            
            successDiv.innerHTML = html;
        } else if (donationType === 'single' && paymentMethod === 'pix') {
            // Dados específicos do PIX
            let pixCode = responseData.pix_code || '';
            let pixText = responseData.pix_text || '';
            
            // HTML para página de sucesso do PIX - layout atualizado
            let html = `
                <div class="pix-success">
                    <h2 class="success-title">Doação via PIX Gerada com Sucesso!</h2>
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
        } else if (donationType === 'recurring') {
            // Código existente para doação recorrente...
            let nextDueDate = AsaasFormUtils.getDataValue(responseData, ['nextDueDate', 'next_due_date']);
            let status = AsaasFormUtils.getDataValue(responseData, ['status', 'subscription_status']);
            
            // Traduzir o status se for "ACTIVE"
            if (status && status.toUpperCase() === 'ACTIVE') {
                status = 'Ativa';
            }
            
            // Formatar data para padrão brasileiro
            nextDueDate = AsaasFormUtils.formatDate(nextDueDate);
            
            let html = `
                <h2>Doação mensal cadastrada com sucesso!</h2>
                <div class="donation-info">
                    <p><strong>Valor mensal:</strong> R$ ${formattedValue}</p>
                    <p><strong>Próxima cobrança:</strong> ${nextDueDate}</p>
                    <p><strong>Situação:</strong> ${status}</p>
                </div>
                <div class="thank-you-message">
                    <h3>Obrigado por sua generosidade!</h3>
                    <p>Sua contribuição é muito importante para nossa causa.</p>
                    <p>A doação será cobrada automaticamente todos os meses, sem ocupar seu limite no cartão de crédito.</p>
                </div>
            `;
            
            successDiv.innerHTML = html;
        } else {
            // Código para outros tipos de doação única...
            let paymentStatus = AsaasFormUtils.getDataValue(responseData, ['payment_status', 'status']);
            let bankSlipUrl = responseData.bank_slip_url || null;
            let invoiceUrl = responseData.invoice_url || null;
            
            // Traduzir status comuns do inglês
            if (paymentStatus && paymentStatus.toUpperCase() === 'CONFIRMED') {
                paymentStatus = 'Confirmado';
            } else if (paymentStatus && paymentStatus.toUpperCase() === 'PENDING') {
                paymentStatus = 'Pendente';
            }
            
            let html = `
                <h2>Doação realizada com sucesso!</h2>
                <div class="donation-info">
                    <p><strong>Valor doado:</strong> R$ ${formattedValue}</p>
                    <p><strong>Situação:</strong> ${paymentStatus}</p>
                </div>`;
            
            // Adicionar link do boleto se disponível
            if (bankSlipUrl) {
                html += `
                    <div class="payment-actions">
                        <p>Clique no botão abaixo para visualizar e imprimir o boleto:</p>
                        <a href="${bankSlipUrl}" target="_blank" class="button button-primary">Visualizar Boleto</a>
                    </div>`;
            }
            
            // Adicionar link da fatura se disponível
            if (invoiceUrl) {
                html += `
                    <div class="invoice-link">
                        <p>Você também pode <a href="${invoiceUrl}" target="_blank">acessar a fatura online</a>.</p>
                    </div>`;
            }
            
            html += `
                <div class="thank-you-message">
                    <h3>Obrigado por sua generosidade!</h3>
                    <p>Sua contribuição é muito importante para nossa causa.</p>
                </div>
            `;
            
            successDiv.innerHTML = html;
        }
        
        form.parentNode.insertBefore(successDiv, form);
    }
    
    /**
     * Exibe uma mensagem no formulário
     * 
     * @param {HTMLFormElement} form Formulário onde a mensagem será exibida
     * @param {string} message Mensagem a ser exibida
     * @param {string} type Tipo da mensagem ('success' ou 'error')
     */
    function showMessage(form, message, type) {
        // Limpar mensagens anteriores para evitar duplicação
        AsaasFormUtils.clearMessages(form);
        
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
    
    // Expor funções públicas
    window.AsaasFormAjax = {
        processDonationForm,
        showMessage,
        showDonationSuccess
    };
})();