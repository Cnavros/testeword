function check_bot_and_add_description() {
    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

    // Verifica se o agente do usuário é um bot (robo.txt) e adiciona a meta descrição.
    if ( strpos( $user_agent, 'bot' ) !== false ) {
        add_action( 'wp_head', 'add_description_meta_tag' );
    }
}

function add_description_meta_tag() {
    echo '<meta name="description" content="Check and fight fake news with the trusted fact-checking tool. Join our community in the fight against misinformation and get access to accurate and authentic information">';
}

add_action('wp_head', 'custom_site_description');

//------Mega-menu---------------------------


//---Limpar Cache--------------

// Limpar o cache do WordPress
function clear_wp_cache() {
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }
}
add_action('admin_init', 'clear_wp_cache');


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





