<div class="asaas-donation-form">
    <h2>Doação</h2>
    <p>Preencha as informações abaixo para realizar sua doação.</p>
    <form id="single-donation-form">
        <input type="hidden" name="action" value="process_donation">
        <input type="hidden" name="donation_type" value="<?php echo esc_attr($form_data['form_type']); ?>">
        
        <?php Asaas_Nonce_Manager::generate_nonce_field($form_data['nonce_action']); ?>
        <label for="full-name">Nome Completo:</label>
        <input type="text" id="full-name" name="full_name" maxlength="50" placeholder="Coloque seu nome aqui" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" maxlength="64" placeholder="Coloque seu e-mail aqui" required>

        <label for="cpf-cnpj">CPF ou CNPJ:</label>
        <input type="text" id="cpf-cnpj" name="cpf_cnpj" maxlength="14" placeholder="Somente números" required>

        <label for="donation-value">Valor da Doação:</label>
        <input type="text" id="donation-value" name="donation_value" placeholder="Ex.: 50,00" required>

        <div id="payment-methods">
            <label for="payment-method">Forma de Pagamento:</label>
            <select id="payment-method" name="payment_method" required>
                <option value="pix">PIX</option>
                <option value="boleto">Boleto</option>
                <option value="card">Cartão de Crédito</option>
            </select>
        </div>

        <div id="card-fields" style="display: none;">
            <label for="card-number">Número do Cartão:</label>
            <input type="text" id="card-number" name="card_number" maxlength="16" placeholder="Somente números">

            <label for="expiry-month">Mês de Validade:</label>
            <input type="text" id="expiry-month" name="expiry_month" maxlength="2" placeholder="MM">

            <label for="expiry-year">Ano de Validade:</label>
            <input type="text" id="expiry-year" name="expiry_year" maxlength="4" placeholder="AAAA">

            <label for="ccv">CCV:</label>
            <input type="text" id="ccv" name="ccv" maxlength="3" placeholder="Ex.: 123">
        
            <label for="cep">CEP do Titular:</label>
            <input type="text" id="cep" name="cep" maxlength="8" placeholder="Somente números" required>
    
            <label for="address-number">Número do Endereço:</label>
            <input type="text" id="address-number" name="address_number" placeholder="Ex.: 123" required>
    
            <label for="phone">Telefone com DDD:</label>
            <input type="text" id="phone" name="phone" placeholder="Somente números" required>
        </div>

        <button type="submit">Realizar Doação</button>
    </form>
</div>