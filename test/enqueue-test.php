<?php
// filepath: c:\laragon\www\meu-lindo-wp\wp-content\plugins\asaas-easy-subscription-plugin\test\enqueue-test.php

// Carregar WordPress
$wp_load_path = realpath(dirname(__FILE__) . '/../../../../wp-load.php');
if (!file_exists($wp_load_path)) {
    die('Não foi possível localizar wp-load.php. Caminho verificado: ' . $wp_load_path);
}
require_once $wp_load_path;

// Obter contexto do plugin
$plugin_dir = dirname(dirname(__FILE__));
$plugin_file = $plugin_dir . '/asaas-easy-subscription-plugin.php';
$plugin_data = get_plugin_data($plugin_file);

// Função para verificar o sistema de enfileiramento
function check_plugin_enqueue() {
    global $wp_scripts, $wp_styles;
    
    // Scripts que deveriam estar enfileirados
    $expected_scripts = [
        'asaas-form-utils',
        'asaas-form-ui',
        'asaas-form-ajax'
    ];
    
    $results = [];
    
    // Verificar cada script
    foreach ($expected_scripts as $handle) {
        $script_info = [
            'handle' => $handle,
            'registered' => isset($wp_scripts->registered[$handle]),
            'enqueued' => in_array($handle, $wp_scripts->queue),
            'src' => isset($wp_scripts->registered[$handle]) ? $wp_scripts->registered[$handle]->src : null,
            'deps' => isset($wp_scripts->registered[$handle]) ? $wp_scripts->registered[$handle]->deps : []
        ];
        
        $results['scripts'][$handle] = $script_info;
    }
    
    // Verificar scripts localizados
    $results['localized'] = [];
    foreach ($wp_scripts->registered as $handle => $script) {
        if (!empty($script->extra['data'])) {
            $results['localized'][$handle] = true;
        }
    }
    
    return $results;
}

// Localizar arquivos de enfileiramento
function find_enqueue_files() {
    global $plugin_dir;
    
    $files = [];
    $enqueue_pattern = '/wp_enqueue_script|wp_register_script/';
    
    // Procurar nos diretórios comuns
    $search_paths = [
        $plugin_dir . '/includes/',
        $plugin_dir . '/admin/',
        $plugin_dir . '/'
    ];
    
    foreach ($search_paths as $path) {
        if (!is_dir($path)) continue;
        
        $dir_files = glob($path . '*.php');
        foreach ($dir_files as $file) {
            $content = file_get_contents($file);
            if (preg_match($enqueue_pattern, $content)) {
                $files[] = [
                    'path' => $file,
                    'name' => basename($file),
                    'relative_path' => str_replace($plugin_dir, '', $file),
                    'contains_enqueue' => true
                ];
            }
        }
    }
    
    return $files;
}

// Encontrar shortcodes
function find_shortcode_files() {
    global $plugin_dir;
    
    $files = [];
    $shortcode_pattern = '/add_shortcode/';
    
    // Procurar nos diretórios comuns
    $search_paths = [
        $plugin_dir . '/includes/',
        $plugin_dir . '/'
    ];
    
    foreach ($search_paths as $path) {
        if (!is_dir($path)) continue;
        
        $dir_files = glob($path . '*.php');
        foreach ($dir_files as $file) {
            $content = file_get_contents($file);
            if (preg_match($shortcode_pattern, $content)) {
                $shortcodes = [];
                preg_match_all('/add_shortcode\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
                if (!empty($matches[1])) {
                    $shortcodes = $matches[1];
                }
                
                $files[] = [
                    'path' => $file,
                    'name' => basename($file),
                    'relative_path' => str_replace($plugin_dir, '', $file),
                    'shortcodes' => $shortcodes
                ];
            }
        }
    }
    
    return $files;
}

// Carregar scripts do WordPress
wp_enqueue_script('jquery');
do_action('wp_enqueue_scripts');

// Verificar enfileiramento
$enqueue_results = check_plugin_enqueue();
$enqueue_files = find_enqueue_files();
$shortcode_files = find_shortcode_files();

// Simular o hook wp_head
ob_start();
do_action('wp_head');
$wp_head_output = ob_get_clean();

// Verificar se os scripts estão sendo impressos
$scripts_in_head = [];
foreach ($enqueue_results['scripts'] as $handle => $info) {
    if ($info['registered'] && $info['src']) {
        $scripts_in_head[$handle] = strpos($wp_head_output, $info['src']) !== false;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Enfileiramento - <?php echo $plugin_data['Name']; ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #2c3e50; }
        .card { background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px 0; padding: 20px; }
        .success { color: #2ecc71; }
        .warning { color: #f39c12; }
        .error { color: #e74c3c; }
        pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow: auto; font-family: monospace; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; }
        .file-path { font-family: monospace; word-break: break-all; }
        .fix-suggestion { background: #f9f7e8; padding: 12px; border-left: 4px solid #f1c40f; margin: 15px 0; }
    </style>
</head>
<body>
    <h1>Teste de Enfileiramento - <?php echo $plugin_data['Name']; ?></h1>
    <p>Versão do plugin: <?php echo $plugin_data['Version']; ?></p>
    
    <div class="card">
        <h2>Verificação de Scripts</h2>
        <table>
            <tr>
                <th>Script</th>
                <th>Registrado</th>
                <th>Enfileirado</th>
                <th>No Head</th>
                <th>Caminho</th>
                <th>Dependências</th>
            </tr>
            <?php foreach ($enqueue_results['scripts'] as $handle => $info): ?>
            <tr>
                <td><?php echo $handle; ?></td>
                <td>
                    <?php if ($info['registered']): ?>
                    <span class="success">✓</span>
                    <?php else: ?>
                    <span class="error">✗</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($info['enqueued']): ?>
                    <span class="success">✓</span>
                    <?php else: ?>
                    <span class="error">✗</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($scripts_in_head[$handle]) && $scripts_in_head[$handle]): ?>
                    <span class="success">✓</span>
                    <?php else: ?>
                    <span class="error">✗</span>
                    <?php endif; ?>
                </td>
                <td class="file-path"><?php echo $info['src'] ?: 'N/A'; ?></td>
                <td><?php echo !empty($info['deps']) ? implode(', ', $info['deps']) : 'Nenhuma'; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <?php if (isset($enqueue_results['localized']['asaas-form-ajax'])): ?>
        <p class="success">O script asaas-form-ajax está corretamente localizado com ajax_object.</p>
        <?php else: ?>
        <p class="error">O script asaas-form-ajax não está localizado com ajax_object!</p>
        <?php endif; ?>
        
        <?php
        // Verificar problemas comuns
        $has_problems = false;
        foreach ($enqueue_results['scripts'] as $handle => $info) {
            if (!$info['registered'] || !$info['enqueued']) {
                $has_problems = true;
                break;
            }
        }
        
        if ($has_problems):
        ?>
        <div class="fix-suggestion">
            <h3>Problemas Detectados</h3>
            <p>Alguns scripts não estão sendo registrados ou enfileirados corretamente. Verifique:</p>
            <ol>
                <li>Se os arquivos JavaScript existem nos caminhos corretos</li>
                <li>Se as funções wp_register_script e wp_enqueue_script estão sendo chamadas</li>
                <li>Se o hook wp_enqueue_scripts está sendo usado</li>
            </ol>
            <p>Exemplo de código correto:</p>
            <pre>function asaas_enqueue_scripts() {
    $plugin_url = plugins_url('', dirname(__FILE__));
    
    wp_register_script('asaas-form-utils', $plugin_url . '/assets/frontend/js/form-utils.js', ['jquery'], '1.0', true);
    wp_register_script('asaas-form-ui', $plugin_url . '/assets/frontend/js/form-ui.js', ['jquery', 'asaas-form-utils'], '1.0', true);
    wp_register_script('asaas-form-ajax', $plugin_url . '/assets/frontend/js/form-ajax.js', ['jquery', 'asaas-form-ui'], '1.0', true);
    
    wp_enqueue_script('asaas-form-utils');
    wp_enqueue_script('asaas-form-ui');
    wp_enqueue_script('asaas-form-ajax');
    
    wp_localize_script('asaas-form-ajax', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asaas_nonce')
    ]);
}

add_action('wp_enqueue_scripts', 'asaas_enqueue_scripts');</pre>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Arquivos que Contêm Enfileiramento</h2>
        <?php if (empty($enqueue_files)): ?>
        <p class="error">Nenhum arquivo com código de enfileiramento encontrado!</p>
        <?php else: ?>
        <table>
            <tr>
                <th>Arquivo</th>
                <th>Caminho Relativo</th>
                <th>Ação</th>
            </tr>
            <?php foreach ($enqueue_files as $file): ?>
            <tr>
                <td><?php echo $file['name']; ?></td>
                <td class="file-path"><?php echo $file['relative_path']; ?></td>
                <td><button onclick="viewFileContent('<?php echo $file['relative_path']; ?>')">Ver Conteúdo</button></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
        
        <div id="file-content-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000;">
            <div style="background:white; width:80%; max-width:1000px; height:80%; margin:5% auto; padding:20px; overflow:auto; border-radius:5px; position:relative;">
                <button onclick="closeModal()" style="position:absolute; top:10px; right:10px; background:#e74c3c; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">Fechar</button>
                <h3 id="modal-title"></h3>
                <pre id="file-content"></pre>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h2>Shortcodes Encontrados</h2>
        <?php if (empty($shortcode_files)): ?>
        <p class="error">Nenhum shortcode encontrado!</p>
        <?php else: ?>
        <table>
            <tr>
                <th>Arquivo</th>
                <th>Caminho Relativo</th>
                <th>Shortcodes</th>
                <th>Ação</th>
            </tr>
            <?php foreach ($shortcode_files as $file): ?>
            <tr>
                <td><?php echo $file['name']; ?></td>
                <td class="file-path"><?php echo $file['relative_path']; ?></td>
                <td><?php echo !empty($file['shortcodes']) ? implode(', ', $file['shortcodes']) : 'Nenhum'; ?></td>
                <td><button onclick="viewFileContent('<?php echo $file['relative_path']; ?>')">Ver Conteúdo</button></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    
    <script>
    function viewFileContent(path) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('modal-title').textContent = path;
                document.getElementById('file-content').textContent = this.responseText;
                document.getElementById('file-content-modal').style.display = 'block';
            }
        };
        xhr.open('GET', 'enqueue-test.php?file=' + encodeURIComponent(path), true);
        xhr.send();
    }
    
    function closeModal() {
        document.getElementById('file-content-modal').style.display = 'none';
    }
    </script>
</body>
</html>

<?php
// Lidar com solicitações para visualizar conteúdo de arquivo
if (isset($_GET['file'])) {
    $requested_file = $_GET['file'];
    $full_path = $plugin_dir . $requested_file;
    
    // Verificar se é um caminho válido dentro do diretório do plugin
    if (strpos(realpath($full_path), realpath($plugin_dir)) === 0 && file_exists($full_path)) {
        header('Content-Type: text/plain');
        echo file_get_contents($full_path);
    } else {
        echo "Arquivo não encontrado ou não autorizado.";
    }
    exit;
}
?>