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

// Configurar os novos formulários V2
jQuery(document).ready(function($) {
    // Formulário de doação única V2
    if ($('#asaas-single-donation-form').length) {
        setupFormAjax('#asaas-single-donation-form');
    }
    
    // Formulário de doação recorrente V2
    if ($('#asaas-recurring-donation-form').length) {
        setupFormAjax('#asaas-recurring-donation-form');
    }
    
    // Função centralizada para configurar AJAX nos formulários
    function setupFormAjax(formSelector) {
        $(formSelector).on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitButton = $form.find('button[type="submit"]');
            
            // Desabilitar o botão para evitar múltiplos envios
            $submitButton.prop('disabled', true).text('Processando...');
            
            // Limpar mensagens de erro anteriores
            $('.asaas-form-error').remove();
            
            // Coletar os dados do formulário
            var formData = $form.serialize();
            
            // Enviar a requisição AJAX
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Redirecionar ou mostrar mensagem de sucesso
                        if (response.data && response.data.redirect_url) {
                            window.location.href = response.data.redirect_url;
                        } else {
                            $form.html('<div class="asaas-success-message">' + 
                                       '<h3>Doação realizada com sucesso!</h3>' + 
                                       '<p>Obrigado por sua contribuição.</p></div>');
                        }
                    } else {
                        // Mostrar erros
                        $submitButton.prop('disabled', false).text('Tentar Novamente');
                        
                        var errorMsg = 'Ocorreu um erro ao processar sua doação.';
                        if (response.data && response.data.errors) {
                            errorMsg = response.data.errors.join('<br>');
                        }
                        
                        $form.prepend('<div class="asaas-form-error">' + errorMsg + '</div>');
                    }
                },
                error: function() {
                    $submitButton.prop('disabled', false).text('Tentar Novamente');
                    $form.prepend('<div class="asaas-form-error">' + 
                                 'Erro de conexão. Por favor, tente novamente.</div>');
                }
            });
        });
    }
});

// Adicione esta função ao arquivo existente, preservando o código atual
(function($) {
    $(document).ready(function() {
        // Encontrar formulários
        const singleForm = $('#asaas-single-donation-form');
        const recurringForm = $('#asaas-recurring-donation-form');
        
        // Configurar ambos os formulários se existirem
        if (singleForm.length) {
            setupFormSubmission(singleForm);
        }
        
        if (recurringForm.length) {
            setupFormSubmission(recurringForm);
        }
        
        // Função para configurar o envio do formulário
        function setupFormSubmission(form) {
            form.on('submit', function(e) {
                e.preventDefault();
                
                const submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Processando...');
                
                // Limpar mensagens anteriores
                form.find('.asaas-response').remove();
                
                $.ajax({
                    url: ajax_object.ajax_url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Mostrar mensagem de sucesso e informações de pagamento
                            displayPaymentResponse(form, response.data);
                        } else {
                            // Mostrar erro
                            displayErrorMessage(form, response.data?.errors || ['Erro ao processar pagamento.']);
                            submitButton.prop('disabled', false).text('Tentar Novamente');
                        }
                    },
                    error: function() {
                        displayErrorMessage(form, ['Erro de conexão. Por favor, tente novamente.']);
                        submitButton.prop('disabled', false).text('Tentar Novamente');
                    }
                });
            });
        }
        
        // Função para exibir a resposta do pagamento
        function displayPaymentResponse(form, data) {
            // Criar container de resposta
            const responseContainer = $('<div class="asaas-response"></div>');
            
            // Adicionar mensagem de sucesso
            responseContainer.append(`
                <div class="asaas-success-message">
                    <h3>Doação realizada com sucesso!</h3>
                    <p>Obrigado por sua contribuição.</p>
                </div>
            `);
            
            // Verificar o método de pagamento
            if (data.payment_method === 'pix') {
                // Exibir QR Code PIX
                responseContainer.append(`
                    <div class="asaas-pix-container">
                        <h4>Pagamento via PIX</h4>
                        <p>Escaneie o QR Code abaixo ou copie o código PIX:</p>
                        <div class="asaas-pix-qrcode">
                            <img src="data:image/png;base64,${data.pix_code}" alt="QR Code PIX">
                        </div>
                        <div class="asaas-pix-text">
                            <p>Código PIX:</p>
                            <div class="asaas-pix-copy">
                                <input type="text" readonly value="${data.pix_text}">
                                <button class="asaas-copy-button" data-clipboard="${data.pix_text}">Copiar</button>
                            </div>
                        </div>
                    </div>
                `);
                
                // Adicionar listener para o botão de cópia
                setTimeout(() => {
                    $('.asaas-copy-button').on('click', function() {
                        const text = $(this).data('clipboard');
                        navigator.clipboard.writeText(text).then(() => {
                            $(this).text('Copiado!');
                            setTimeout(() => {
                                $(this).text('Copiar');
                            }, 2000);
                        });
                    });
                }, 100);
            } else if (data.payment_method === 'boleto') {
                // Exibir link para boleto
                responseContainer.append(`
                    <div class="asaas-boleto-container">
                        <h4>Pagamento via Boleto</h4>
                        <p>Clique no botão abaixo para visualizar ou imprimir seu boleto:</p>
                        <a href="${data.bank_slip_url}" class="asaas-boleto-button" target="_blank">
                            Visualizar Boleto
                        </a>
                    </div>
                `);
            } else if (data.payment_method === 'card') {
                // Exibir confirmação de pagamento com cartão
                responseContainer.append(`
                    <div class="asaas-card-container">
                        <h4>Pagamento com Cartão de Crédito</h4>
                        <p>Seu pagamento foi processado com sucesso!</p>
                        <p>ID de pagamento: ${data.payment_id}</p>
                    </div>
                `);
            }
            
            // Substituir o formulário pela resposta
            form.html(responseContainer);
        }
        
        // Função para exibir mensagens de erro
        function displayErrorMessage(form, errors) {
            const errorContainer = $('<div class="asaas-form-error"></div>');
            
            if (Array.isArray(errors) && errors.length) {
                const errorList = $('<ul></ul>');
                errors.forEach(error => {
                    errorList.append(`<li>${error}</li>`);
                });
                errorContainer.append(errorList);
            } else {
                errorContainer.text('Ocorreu um erro ao processar sua doação.');
            }
            
            // Inserir no topo do formulário
            form.prepend(errorContainer);
        }
    });
})(jQuery);