<?php
/**
 * Arquivo de teste funcional para múltiplos formulários na mesma página.
 * Salve em test/test-mult-form.php dentro do plugin.
 */

// Ajuste o caminho até o wp-load.php da sua instalação
require_once dirname(__DIR__, 4) . '/wp-load.php';

header('Content-Type: text/html; charset=utf-8');
echo '<h1>Teste Funcional: Múltiplos Formulários</h1>';

// Monte o conteúdo com 3 instâncias dos shortcodes
$content = '[single_donation_form] [recurring_donation_form] [single_donation_form]';

// Renderiza os shortcodes
$rendered = do_shortcode( $content );

// Exibe o HTML completo renderizado
echo '<h2>HTML Renderizado</h2>';
echo '<pre style="background:#f7f7f7;border:1px solid #ccc;padding:10px;">'
     . esc_html( $rendered )
     . '</pre>';

// Contagem de tags <form>
preg_match_all( '/<form\b[^>]*>/i', $rendered, $forms );
$form_count = count( $forms[0] );
echo '<p><strong>Forms encontrados:</strong> ' . $form_count . '</p>';

// Contagem de container .card-fields
preg_match_all( '/class="[^"]*card-fields[^"]*"/i', $rendered, $cardFields );
$card_count = count( $cardFields[0] );
echo '<p><strong>Containers .card-fields:</strong> ' . $card_count . '</p>';

// Verifica quantos estão ocultos por estilo inline
preg_match_all( '/style="[^"]*display\s*:\s*none[^"]*"/i', $rendered, $hidden );
$hidden_count = count( $hidden[0] );
echo '<p><strong>Containers ocultos (display:none):</strong> ' . $hidden_count . '</p>';

// Verifica se há IDs duplicados de form ou elementos
preg_match_all( '/id="([^"]+)"/i', $rendered, $allIds );
$ids = $allIds[1];
$dupes = array_diff_assoc( $ids, array_unique( $ids ) );
echo '<p><strong>IDs duplicados:</strong> '
   . (empty($dupes) ? 'nenhum' : implode(', ', array_unique($dupes)))
   . '</p>';

// Exiba um log bruto no console do servidor (error_log)
error_log( "Rendered HTML:\n" . $rendered );
error_log( "Form count: $form_count" );
error_log( "Card-fields count: $card_count" );
error_log( "Hidden count: $hidden_count" );
error_log( "Duplicate IDs: " . (empty($dupes) ? 'none' : implode(',', $dupes)) );

echo '<p style="color:green;">✅ Teste concluído. Consulte o log do servidor para detalhes adicionais.</p>';
