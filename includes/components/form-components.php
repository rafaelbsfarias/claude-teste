<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Biblioteca de componentes reutilizáveis para formulários
 */
class Asaas_Form_Components {
    /**
     * Renderiza os campos de informação pessoal (nome, email, cpf/cnpj)
     */
    public static function render_personal_fields($values = []) {
        ?>
        <div class="asaas-form-group">
            <label for="full-name">Nome Completo:</label>
            <input type="text" id="full-name" name="full_name" maxlength="50" 
                   placeholder="Coloque seu nome aqui" 
                   value="<?php echo esc_attr($values['full_name'] ?? ''); ?>" required>
        </div>

        <div class="asaas-form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" maxlength="64" 
                   placeholder="Coloque seu e-mail aqui" 
                   value="<?php echo esc_attr($values['email'] ?? ''); ?>" required>
        </div>

        <div class="asaas-form-group">
            <label for="cpf-cnpj">CPF ou CNPJ:</label>
            <input type="text" id="cpf-cnpj" name="cpf_cnpj" maxlength="14" 
                   placeholder="Somente números" 
                   value="<?php echo esc_attr($values['cpf_cnpj'] ?? ''); ?>" required>
        </div>
        <?php
    }

    /**
     * Renderiza o campo de valor da doação
     */
    public static function render_donation_value_field($values = [], $is_recurring = false) {
        ?>
        <div class="asaas-form-group">
            <label for="donation-value">
                <?php echo $is_recurring ? 'Valor Mensal:' : 'Valor da Doação:'; ?>
            </label>
            <input type="text" id="donation-value" name="donation_value" 
                   placeholder="Ex.: 50,00" 
                   value="<?php echo esc_attr($values['donation_value'] ?? ''); ?>" required>
        </div>
        <?php
    }

    /**
     * Renderiza os campos de seleção de método de pagamento usando dropdown
     */
    public static function render_payment_method_fields($values = [], $is_recurring = false) {
        $selected_method = $values['payment_method'] ?? '';
        $available_methods = $is_recurring 
            ? ['card'] 
            : ['card', 'pix', 'boleto'];
        ?>
        <div class="asaas-form-group">
            <label for="payment-method">Método de Pagamento:</label>
            <select id="payment-method" name="payment_method" required>
                <option value="">Selecione o método de pagamento</option>
                
                <?php if (in_array('card', $available_methods)): ?>
                <option value="card" <?php selected($selected_method, 'card'); ?>>
                    Cartão de Crédito
                </option>
                <?php endif; ?>
                
                <?php if (in_array('pix', $available_methods)): ?>
                <option value="pix" <?php selected($selected_method, 'pix'); ?>>
                    PIX
                </option>
                <?php endif; ?>
                
                <?php if (in_array('boleto', $available_methods)): ?>
                <option value="boleto" <?php selected($selected_method, 'boleto'); ?>>
                    Boleto Bancário
                </option>
                <?php endif; ?>
            </select>
        </div>
        <?php
    }

    /**
     * Renderiza os campos de cartão de crédito
     */
    public static function render_credit_card_fields($values = []) {
        ?>
        <div id="card-fields" class="asaas-card-fields" style="display: none;">
            <div class="asaas-form-group">
                <label for="card-number">Número do Cartão:</label>
                <input type="text" id="card-number" name="card_number" maxlength="16" 
                       placeholder="Somente números">
            </div>

            <div class="asaas-form-row">
                <div class="asaas-form-group">
                    <label for="expiry-month">Mês de Validade:</label>
                    <input type="text" id="expiry-month" name="expiry_month" maxlength="2" 
                           placeholder="MM">
                </div>

                <div class="asaas-form-group">
                    <label for="expiry-year">Ano de Validade:</label>
                    <input type="text" id="expiry-year" name="expiry_year" maxlength="4" 
                           placeholder="AAAA">
                </div>

                <div class="asaas-form-group">
                    <label for="ccv">CCV:</label>
                    <input type="text" id="ccv" name="ccv" maxlength="3" 
                           placeholder="Ex.: 123">
                </div>
            </div>

            <div class="asaas-form-group">
                <label for="cep">CEP do Titular:</label>
                <input type="text" id="cep" name="cep" maxlength="8" 
                       placeholder="Somente números">
            </div>

            <div class="asaas-form-group">
                <label for="address-number">Número do Endereço:</label>
                <input type="text" id="address-number" name="address_number" 
                       placeholder="Ex.: 123">
            </div>

            <div class="asaas-form-group">
                <label for="phone">Telefone com DDD:</label>
                <input type="text" id="phone" name="phone" 
                       placeholder="Somente números">
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza o campo honeypot para proteção contra spam
     */
    public static function render_honeypot_field() {
        ?>
        <div class="asaas-honeypot" style="position:absolute; left:-9999px;">
            <label for="website">Website</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>
        <?php
    }

    /**
     * Renderiza o botão de envio do formulário
     */
    public static function render_submit_button($is_recurring = false) {
        $text = $is_recurring ? 'Realizar Doação Mensal' : 'Realizar Doação';
        ?>
        <button type="submit" class="asaas-submit-button"><?php echo esc_html($text); ?></button>
        <?php
    }
}