(function($) {
    'use strict';
    
    // Toggle credit card fields based on payment method
    $(document).on('change', '#payment_method', function() {
        if ($(this).val() === 'CREDIT_CARD') {
            $('#credit-card-fields').show();
        } else {
            $('#credit-card-fields').hide();
        }
    });
    
    // Handle one-time payment form submission
    $(document).on('submit', '#asaas-payment-form', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var responseDiv = $('#asaas-payment-response');
        
        responseDiv.html('<p>Processing payment...</p>');
        
        $.ajax({
            url: asaas_ajax.ajax_url,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var payment = response.data.payment;
                    var html = '<div class="asaas-success">';
                    html += '<h3>Payment Created Successfully</h3>';
                    
                    if (payment.billingType === 'PIX') {
                        // Display PIX information
                        html += '<h4>PIX Payment</h4>';
                        if (payment.pixInfo && payment.pixInfo.encodedImage) {
                            html += '<div class="pix-qrcode"><img src="data:image/png;base64,' + payment.pixInfo.encodedImage + '" /></div>';
                            html += '<p>PIX Code: <input type="text" readonly value="' + payment.pixInfo.payload + '" onclick="this.select();" /></p>';
                        }
                        html += '<p><a href="' + payment.invoiceUrl + '" target="_blank">View Invoice</a></p>';
                    } else if (payment.billingType === 'BOLETO') {
                        // Display Boleto information
                        html += '<h4>Boleto Payment</h4>';
                        html += '<p><a href="' + payment.bankSlipUrl + '" target="_blank" class="button">Download Boleto</a></p>';
                        html += '<p><a href="' + payment.invoiceUrl + '" target="_blank">View Invoice</a></p>';
                    } else if (payment.billingType === 'CREDIT_CARD') {
                        // Display Credit Card information
                        html += '<h4>Credit Card Payment</h4>';
                        html += '<p>Status: ' + payment.status + '</p>';
                        html += '<p><a href="' + payment.invoiceUrl + '" target="_blank">View Invoice</a></p>';
                    }
                    
                    html += '</div>';
                    responseDiv.html(html);
                    form.hide();
                } else {
                    responseDiv.html('<div class="asaas-error"><p>Error: ' + response.data + '</p></div>');
                }
            },
            error: function() {
                responseDiv.html('<div class="asaas-error"><p>Server error. Please try again.</p></div>');
            }
        });
    });
    
    // Handle subscription form submission
    $(document).on('submit', '#asaas-subscription-form', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var responseDiv = $('#asaas-subscription-response');
        
        responseDiv.html('<p>Processing subscription...</p>');
        
        $.ajax({
            url: asaas_ajax.ajax_url,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var subscription = response.data.subscription;
                    var html = '<div class="asaas-success">';
                    html += '<h3>Subscription Created Successfully</h3>';
                    html += '<p>Status: ' + subscription.status + '</p>';
                    html += '<p>Next Payment: ' + subscription.nextDueDate + '</p>';
                    html += '<p>Card: **** **** **** ' + subscription.creditCard.creditCardNumber + '</p>';
                    html += '</div>';
                    responseDiv.html(html);
                    form.hide();
                } else {
                    responseDiv.html('<div class="asaas-error"><p>Error: ' + response.data + '</p></div>');
                }
            },
            error: function() {
                responseDiv.html('<div class="asaas-error"><p>Server error. Please try again.</p></div>');
            }
        });
    });
    
})(jQuery);