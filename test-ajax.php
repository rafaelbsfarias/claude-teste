<?php
// Adicione ao final do arquivo ajax-handler.php
/**
 * Handler AJAX simples para teste
 */
function asaas_test_ajax() {
    wp_send_json_success([
        'message' => 'AJAX está funcionando corretamente!'
    ]);
    wp_die();
}
add_action('wp_ajax_asaas_test_ajax', 'asaas_test_ajax');
add_action('wp_ajax_nopriv_asaas_test_ajax', 'asaas_test_ajax');

// Adicione um botão de teste simples na página
function asaas_add_test_button($content) {
    if (!is_singular()) return $content;
    
    $button = '<div style="margin: 20px 0; text-align: center;">';
    $button .= '<button type="button" id="asaas-test-ajax" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Testar AJAX</button>';
    $button .= '<div id="asaas-test-result" style="margin-top: 10px;"></div>';
    $button .= '</div>';
    
    $button .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (document.getElementById("asaas-test-ajax")) {
                document.getElementById("asaas-test-ajax").addEventListener("click", function() {
                    console.log("Botão de teste AJAX clicado");
                    if (typeof ajax_object === "undefined") {
                        document.getElementById("asaas-test-result").innerHTML = "<p style=\"color:red;\">Erro: ajax_object não está definido!</p>";
                        return;
                    }
                    
                    var formData = new FormData();
                    formData.append("action", "asaas_test_ajax");
                    
                    fetch(ajax_object.ajax_url, {
                        method: "POST",
                        body: formData,
                        credentials: "same-origin"
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Resposta do teste AJAX:", data);
                        document.getElementById("asaas-test-result").innerHTML = "<p style=\"color:green;\">" + data.data.message + "</p>";
                    })
                    .catch(error => {
                        console.error("Erro no teste AJAX:", error);
                        document.getElementById("asaas-test-result").innerHTML = "<p style=\"color:red;\">Erro: " + error.message + "</p>";
                    });
                });
            }
        });
    </script>';
    
    return $content . $button;
}
add_filter('the_content', 'asaas_add_test_button');