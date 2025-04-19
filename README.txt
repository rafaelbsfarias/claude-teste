=== Asaas Payment Integration ===
Contributors: wordpressdeveloper
Tags: asaas, payment, payment gateway, checkout
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple WordPress plugin for Asaas payment integration.

== Description ==

This plugin integrates WordPress with Asaas payment gateway, allowing you to:

* Create customers in Asaas
* Process one-time payments (PIX, Boleto, Credit Card)
* Create monthly subscriptions (Credit Card)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/asaas-payment-integration` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to 'Asaas Payments' in the admin menu to see the plugin settings.
4. Use the shortcodes on your pages.

== Frequently Asked Questions ==

= What shortcodes are available? =

* `[asaas_payment]` - For one-time payments (PIX, Boleto, Credit Card)
* `[asaas_subscription]` - For recurring payments (Credit Card only)

= How can I customize the payment amount and description? =

You can customize the shortcodes with attributes:
* `[asaas_payment value="150.00" description="Product XYZ" due_date="2025-05-01"]`
* `[asaas_subscription value="49.90" description="Monthly Plan" next_due_date="2025-05-01"]`

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release