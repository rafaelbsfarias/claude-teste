/**
 * JavaScript para o front-end do plugin Asaas Customer Registration
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

(function($) {
    'use strict';
    
    // Armazena dados entre etapas
    let customerData = {};
    
    /**
     * Inicializa quando o documento estiver pronto
     */
    $(document).ready(function() {
        // Aplicar máscara para CPF/CNPJ
        setupCpfCnpjMask();
        
        // Manipular cadastro de cliente
        handleCustomerRegistration();
        
        // Manipular criação de pagamento
        handlePaymentCreation();
        
        // Manipular botões de navegação
        handleNavigation();
        
        // Estilizar a seleção de método de pagamento
        stylePaymentMethod();
    });
    
    /**
     * Configura máscara para CPF/CNPJ
     */
    function setupCpfCnpjMask() {
        $('#asaas-cpfcnpj').on('keyup', function() {
            const value = $(this).val().replace(/\D/g, '');
            
            if (value.length <= 11) {
                $(this).mask('000.000.000-00');
            } else {
                $(this).mask('00.000.000/0000-00');
            }
        });
    }
    
    /**
     * Manipula o envio do formulário de cadastro de cliente
     */
    function handleCustomerRegistration() {
        $('#asaas-customer-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const messageDiv = $('#asaas-customer-message');
            const submitButton = $('#asaas-customer-submit');
            
            // Limpar mensagens anteriores
            messageDiv.removeClass('success error').hide().text('');
            
            // Verificar campos obrigatórios
            const name = $('#asaas-name').val().trim();
            const cpfCnpj = $('#asaas-cpfcnpj').val().trim();
            
            if (!name || !cpfCnpj) {
                messageDiv.addClass('error').text(asaas_params.messages.required_fields).show();
                return;
            }
            
            // Desabilitar botão durante processamento
            submitButton.prop('disabled', true).text(asaas_params.messages.processing);
            
            // Preparar dados para envio
            const formData = {
                action: 'asaas_register_customer',
                nonce: asaas_params.nonce,
                name: name,
                cpfCnpj: cpfCnpj
            };
            
            // Enviar requisição AJAX
            $.post(asaas_params.ajax_url, formData, function(response) {
                if (response.success) {
                    // Armazenar dados do cliente
                    customerData.id = response.data.customer_id;
                    customerData.name = name;
                    
                    // Preencher ID do cliente no formulário de pagamento
                    $('#asaas-customer-id').val(customerData.id);
                    
                    // Exibir mensagem de sucesso
                    messageDiv.addClass('success').text(response.data.message).show();
                    
                    // Avançar para a próxima etapa após um breve delay
                    setTimeout(function() {
                        goToStep('payment');
                    }, 1000);
                } else {
                    // Exibir mensagem de erro
                    messageDiv.addClass('error').text(response.data.message).show();
                    submitButton.prop('disabled', false).text('Cadastrar e Prosseguir');
                }
            }).fail(function() {
                // Exibir mensagem de erro genérica
                messageDiv.addClass('error').text(asaas_params.messages.error).show();
                submitButton.prop('disabled', false).text('Cadastrar e Prosseguir');
            });
        });
    }
    
    /**
     * Manipula o envio do formulário de criação de pagamento
     */
    function handlePaymentCreation() {
        $('#asaas-payment-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const messageDiv = $('#asaas-payment-message');
            const submitButton = $('#asaas-payment-submit');
            
            // Limpar mensagens anteriores
            messageDiv.removeClass('success error').hide().text('');
            
            // Verificar campos obrigatórios
            const customerId = $('#asaas-customer-id').val();
            const value = $('#asaas-value').val();
            const billingType = $('input[name="billingType"]:checked').val();
            
            if (!customerId || !value || !billingType) {
                messageDiv.addClass('error').text(asaas_params.messages.required_fields).show();
                return;
            }
            
            // Desabilitar botão durante processamento
            submitButton.prop('disabled', true).text(asaas_params.messages.processing);
            
            // Preparar dados para envio
            const formData = {
                action: 'asaas_create_payment',
                nonce: asaas_params.nonce,
                customer_id: customerId,
                value: value,
                billingType: billingType,
                description: $('#asaas-description').val(),
                dueDate: $('#asaas-due-days').val()
            };
            
            // Enviar requisição AJAX
            $.post(asaas_params.ajax_url, formData, function(response) {
                if (response.success) {
                    // Exibir mensagem de sucesso
                    messageDiv.addClass('success').text(response.data.message).show();
                    
                    // Preencher detalhes do pagamento
                    fillPaymentDetails(response.data.payment);
                    
                    // Avançar para a próxima etapa após um breve delay
                    setTimeout(function() {
                        goToStep('success');
                    }, 1000);
                } else {
                    // Exibir mensagem de erro
                    messageDiv.addClass('error').text(response.data.message).show();
                    submitButton.prop('disabled', false).text('Gerar Pagamento');
                }
            }).fail(function() {
                // Exibir mensagem de erro genérica
                messageDiv.addClass('error').text(asaas_params.messages.error).show();
                submitButton.prop('disabled', false).text('Gerar Pagamento');
            });
        });
    }
    
    /**
     * Preenche os detalhes do pagamento na etapa de sucesso
     * 
     * @param {Object} payment Dados do pagamento
     */
    function fillPaymentDetails(payment) {
        // Formatar valores
        const formattedValue = formatCurrency(payment.value);
        const formattedDate = formatDate(payment.dueDate);
        const billingTypeText = getBillingTypeText(payment.billingType);
        
        // Construir HTML dos detalhes
        let detailsHtml = '';
        detailsHtml += '<div class="asaas-payment-detail"><span>ID do Pagamento:</span><span>' + payment.id + '</span></div>';
        detailsHtml += '<div class="asaas-payment-detail"><span>Valor:</span><span>' + formattedValue + '</span></div>';
        detailsHtml += '<div class="asaas-payment-detail"><span>Forma de Pagamento:</span><span>' + billingTypeText + '</span></div>';
        detailsHtml += '<div class="asaas-payment-detail"><span>Status:</span><span>Pendente</span></div>';
        detailsHtml += '<div class="asaas-payment-detail"><span>Data de Vencimento:</span><span>' + formattedDate + '</span></div>';
        
        // Construir HTML dos links
        let linksHtml = '';
        linksHtml += '<a href="' + payment.invoiceUrl + '" target="_blank">Ver Fatura</a>';
        
        // Adicionar link específico para boleto
        if (payment.billingType === 'BOLETO' && payment.bankSlipUrl) {
            linksHtml += '<a href="' + payment.bankSlipUrl + '" target="_blank">Ver Boleto</a>';
        }
        
        // Inserir HTML
        $('#asaas-payment-details').html(detailsHtml);
        $('#asaas-payment-links').html(linksHtml);
    }
    
    /**
     * Manipula botões de navegação
     */
    function handleNavigation() {
        // Botão voltar (etapa 2 para etapa 1)
        $('#asaas-back-button').on('click', function() {
            goToStep('customer');
        });
        
        // Botão novo pagamento (etapa 3 para etapa 2)
        $('#asaas-new-payment-button').on('click', function() {
            // Resetar formulário de pagamento
            $('#asaas-payment-form')[0].reset();
            $('#asaas-customer-id').val(customerData.id);
            $('#asaas-payment-message').removeClass('success error').hide().text('');
            
            // Voltar para etapa 2
            goToStep('payment');
        });
    }
    
    /**
     * Estiliza a seleção de método de pagamento
     */
    function stylePaymentMethod() {
        $('.asaas-payment-method').on('click', function() {
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    }
    
    /**
     * Navega para uma etapa específica
     * 
     * @param {string} step Nome da etapa (customer, payment, success)
     */
    function goToStep(step) {
        $('.asaas-step').removeClass('active');
        $('#asaas-step-' + step).addClass('active');
        
        // Reset dos botões submit
        $('#asaas-customer-submit').prop('disabled', false).text('Cadastrar e Prosseguir');
        $('#asaas-payment-submit').prop('disabled', false).text('Gerar Pagamento');
        
        // Scroll para o topo do formulário
        $('html, body').animate({
            scrollTop: $('.asaas-container').offset().top - 50
        }, 500);
    }
    
    /**
     * Formata um valor como moeda brasileira
     * 
     * @param {number} value Valor a ser formatado
     * @return {string} Valor formatado
     */
    function formatCurrency(value) {
        return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',');
    }
    
    /**
     * Formata uma data no padrão brasileiro
     * 
     * @param {string} dateString Data no formato ISO (YYYY-MM-DD)
     * @return {string} Data formatada (DD/MM/YYYY)
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }
    
    /**
     * Retorna o texto descritivo para o tipo de pagamento
     * 
     * @param {string} billingType Tipo de pagamento
     * @return {string} Texto descritivo
     */
    function getBillingTypeText(billingType) {
        switch (billingType) {
            case 'PIX':
                return 'PIX';
            case 'BOLETO':
                return 'Boleto Bancário';
            case 'CREDIT_CARD':
                return 'Cartão de Crédito';
            default:
                return billingType;
        }
    }
    
})(jQuery);