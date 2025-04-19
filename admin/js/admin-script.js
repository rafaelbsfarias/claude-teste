/**
 * JavaScript para a área administrativa do plugin Asaas Customer Registration
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

(function($) {
    'use strict';
    
    /**
     * Inicializa quando o documento estiver pronto
     */
    $(document).ready(function() {
        // Código para selecionar o texto do shortcode ao clicar
        enableShortcodeSelection();
    });
    
    /**
     * Habilita a seleção automática do texto do shortcode ao clicar
     */
    function enableShortcodeSelection() {
        $('code').on('click', function() {
            const selection = window.getSelection();
            const range = document.createRange();
            range.selectNodeContents(this);
            selection.removeAllRanges();
            selection.addRange(range);
        });
    }
    
})(jQuery);