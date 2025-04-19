=== Asaas Customer Registration ===
Contributors: claude
Tags: asaas, payment, checkout, brazil, pix
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin para cadastro simples de clientes no Asaas e geração de pagamentos via PIX, boleto ou cartão de crédito.

== Description ==

O Asaas Customer Registration é um plugin para WordPress que permite o cadastro de clientes na plataforma Asaas e a geração de pagamentos de forma simples e direta.

= Características =

* Cadastro de clientes utilizando apenas nome e CPF/CNPJ
* Geração de pagamentos via PIX, boleto bancário ou cartão de crédito
* Validação de CPF/CNPJ
* Nenhum dado é armazenado no WordPress
* Interface amigável e responsiva
* Configuração simples

= Como usar =

1. Instale e ative o plugin
2. Configure sua chave de API do Asaas em "Configurações > Asaas Customer"
3. Use o shortcode `[asaas_form]` em qualquer página ou post para exibir o formulário
4. Personalize o formulário com atributos como `valor`, `descricao` e `dias_vencimento`

= Atributos do shortcode =

* `valor` - Define o valor padrão do pagamento (ex: `[asaas_form valor="149.90"]`)
* `descricao` - Define a descrição padrão do pagamento (ex: `[asaas_form descricao="Produto Premium"]`)
* `dias_vencimento` - Define os dias para vencimento (ex: `[asaas_form dias_vencimento="5"]`)

== Installation ==

1. Faça upload dos arquivos do plugin para o diretório `/wp-content/plugins/asaas-customer-registration/`, ou instale o plugin diretamente pelo instalador de plugins do WordPress
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Acesse "Configurações > Asaas Customer" para configurar sua chave de API e outras opções
4. Use o shortcode `[asaas_form]` em qualquer página ou post para exibir o formulário de cadastro e pagamento

== Frequently Asked Questions ==

= É necessário ter uma conta no Asaas? =

Sim, para utilizar este plugin você precisa ter uma conta no Asaas. Você pode criar uma conta em [asaas.com](https://www.asaas.com/).

= Onde encontro minha chave de API? =

Você pode encontrar sua chave de API no painel Asaas em: Configurações > Integrações.

= Os dados são armazenados no WordPress? =

Não, todos os dados são enviados diretamente para a API do Asaas. Nenhuma informação de cliente ou pagamento é armazenada no seu WordPress.

= Como posso personalizar o valor do pagamento? =

Você pode configurar um valor padrão em "Configurações > Asaas Customer" ou utilizar o atributo `valor` no shortcode, por exemplo: `[asaas_form valor="199.90"]`.

= O plugin suporta pagamentos recorrentes? =

Não, atualmente o plugin suporta apenas pagamentos únicos via PIX, boleto bancário ou cartão de crédito.

== Screenshots ==

1. Tela de configurações do plugin
2. Formulário de cadastro de cliente (Etapa 1)
3. Formulário de pagamento (Etapa 2)
4. Confirmação e links para pagamento (Etapa 3)

== Changelog ==

= 1.0.0 =
* Versão inicial do plugin

== Upgrade Notice ==

= 1.0.0 =
Versão inicial do plugin.

== Personalizações ==

O plugin utiliza CSS e JavaScript para estilizar e controlar o comportamento do formulário. Se você quiser personalizar a aparência, você pode adicionar seus próprios estilos CSS em seu tema ou plugin personalizado.