<?php
function processRequest() {
    // Registre a solicitação no arquivo de log
    $logFile = 'log.txt';
    $logMessage = date('Y-m-d H:i:s') . " - Endpoint acessado: " . $_SERVER['REQUEST_URI'] . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);

   

//----ponto de extremidade-----------------------

function custom_endpoint_init() {
    add_rewrite_rule('^custom-endpoint/?', 'index.php?custom_endpoint=1', 'top');
}
add_action('init', 'custom_endpoint_init');

function custom_query_vars($vars) {
    $vars[] = 'custom_endpoint';
    return $vars;
}
add_filter('query_vars', 'custom_query_vars');

function custom_endpoint_template($template) {
    if (get_query_var('custom_endpoint')) {
        // Inclua o arquivo de template PHP que contém a lógica personalizada aqui
        include(get_template_directory() . '/custom-endpoint-template.php');
        exit;
    }
    return $template;
}
add_filter('template_include', 'custom_endpoint_template');

}
