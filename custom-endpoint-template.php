<?php
// Registre o ponto de extremidade REST
add_action('rest_api_init', function () {
    register_rest_route('myplugin/v1', '/search', array(
        'methods' => 'GET',
        'callback' => 'categoryBR1',
        'args' => array(
            'news' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'country' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            )
        )
    ));
});

// Função de callback para o ponto de extremidade REST
function categoryBR1($request) {
    $news = $request->get_param('news');
    $country = $request->get_param('country');

    // Registre a solicitação bem-sucedida
    log_activity('Solicitação bem-sucedida', $news, $country);

    
       

      

    // Realize a lógica de pesquisa de notícias e obtenha classificações e links aqui
    // Esta lógica deve ser baseada em suas necessidades específicas

    $classification = 'Not found';
    $link = '';

    // ... Sua lógica de pesquisa de notícias ...
  function searchInText($text, $searchString) {
        $regex = '/' . preg_quote($searchString, '/') . '/i';
        preg_match_all($regex, $text, $matches);
        return count($matches[0]);
    }

    $tagText = '';
    $encodedTitle = rawurlencode($news);
    $site = 'site:projetocomprova.com.br/';

    $searchUrl = 'https://www.google.com/search?q=' . $encodedTitle . '+' . $site;

    $response = wp_safe_remote_get($searchUrl);

    if (is_wp_error($response)) {
        // Lida com erros na solicitação HTTP, se houver
        return 'Erro ao buscar o URL ' . $searchUrl;
    }

    $text = wp_remote_retrieve_body($response);
    $searchResults = [];

    if (preg_match_all('/<div class="g">.*?<\/div>/s', $text, $searchResults)) {
        $firstResult = $searchResults[0][0];
        preg_match('/<a href="([^"]+)"/', $firstResult, $linkMatches);
        $link = !empty($linkMatches[1]) ? $linkMatches[1] : '';
        // Continue com o restante da lógica para obter as tags e classificação
    } else {
        // Nenhum resultado de pesquisa encontrado
        return 'Nenhum resultado de pesquisa encontrado.';
    }

    // Continue com o restante da lógica para obter as tags e classificação
    $classification = 'Not found';

    // .....................................................................

    // Após a obtenção do link
// Realizar uma nova solicitação HTTP para obter o HTML completo do artigo
$articleResponse = wp_safe_remote_get($link);

if (!is_wp_error($articleResponse)) {
    $articleText = wp_remote_retrieve_body($articleResponse);
    $articleDoc = new DOMDocument();
    @$articleDoc->loadHTML($articleText);

    $answerTags = $articleDoc->getElementsByTagName('div');
    
    foreach ($answerTags as $tag) {
        if ($tag->getAttribute('class') === 'answer__tag') {
            $tagText = trim($tag->textContent);
            if ($tagText === 'Comprova Explica') {
                $classification = 'Comprova Explica';
            } elseif ($tagText === 'Enganoso') {
                $classification = 'FAKE';
            } elseif ($tagText === 'Comprovado') {
                $classification = 'FATO';
            } elseif ($tagText === 'Falso') {
                $classification = 'FAKE';
            }
        }
    }
} else {
    // Lida com erros na solicitação HTTP do artigo, se houver
}




   


    $response = array(
        'classification' => $classification,
        'link' => $link
    );

    return new WP_REST_Response($response, 200);
}

function log_activity($message, $news, $country) {
    // Registre a atividade no banco de dados, por exemplo, em uma tabela de registro de atividades
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'activity_log';

    $data = array(
        'message' => $message,
        'news' => $news,
        'country' => $country,
        'timestamp' => current_time('mysql'),
    );

    $wpdb->insert($table_name, $data);
}
add_action('rest_api_init', function () {
    register_rest_route('myplugin/v1', '/article-info', array(

        'methods' => 'GET',
        'callback' => 'getArticleInfo',
        ));
        });