<?php
/**
 * Template para página de configurações.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

// Se este arquivo for chamado diretamente, abortar.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        // Saída dos campos de configuração
        settings_fields('asaas_settings');
        
        // Saída das seções de configuração
        do_settings_sections('asaas-settings');
        
        // Saída do botão de envio
        submit_button();
        ?>
    </form>
    
    <div class="asaas-admin-info">
        <h2><?php esc_html_e('Informações de Uso', 'asaas-customer-registration'); ?></h2>
        
        <div class="asaas-info-section">
            <h3><?php esc_html_e('Shortcode', 'asaas-customer-registration'); ?></h3>
            <p><?php esc_html_e('Use o shortcode abaixo em qualquer página ou post para exibir o formulário de cadastro e pagamento:', 'asaas-customer-registration'); ?></p>
            <code>[asaas_form]</code>
        </div>
        
        <div class="asaas-info-section">
            <h3><?php esc_html_e('Atributos do Shortcode', 'asaas-customer-registration'); ?></h3>
            <p><?php esc_html_e('Você pode personalizar o formulário com os seguintes atributos:', 'asaas-customer-registration'); ?></p>
            
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Atributo', 'asaas-customer-registration'); ?></th>
                        <th><?php esc_html_e('Descrição', 'asaas-customer-registration'); ?></th>
                        <th><?php esc_html_e('Exemplo', 'asaas-customer-registration'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>valor</code></td>
                        <td><?php esc_html_e('Define o valor padrão do pagamento.', 'asaas-customer-registration'); ?></td>
                        <td><code>[asaas_form valor="150.00"]</code></td>
                    </tr>
                    <tr>
                        <td><code>descricao</code></td>
                        <td><?php esc_html_e('Define a descrição padrão do pagamento.', 'asaas-customer-registration'); ?></td>
                        <td><code>[asaas_form descricao="Produto Premium"]</code></td>
                    </tr>
                    <tr>
                        <td><code>dias_vencimento</code></td>
                        <td><?php esc_html_e('Define os dias para vencimento.', 'asaas-customer-registration'); ?></td>
                        <td><code>[asaas_form dias_vencimento="5"]</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="asaas-info-section">
            <h3><?php esc_html_e('Suporte', 'asaas-customer-registration'); ?></h3>
            <p>
                <?php 
                printf(
                    esc_html__('Para suporte e informações sobre a API Asaas, visite %s.', 'asaas-customer-registration'),
                    '<a href="https://docs.asaas.com/" target="_blank">docs.asaas.com</a>'
                );
                ?>
            </p>
        </div>
    </div>
</div>