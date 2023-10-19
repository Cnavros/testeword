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
// Função para processar a solicitação
function categoryBR1($news, $country) {
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

    $response = file_get_contents($searchUrl);

    if ($response !== false) {
        // Lida com erros na solicitação HTTP, se houver
        return 'Erro ao buscar o URL ' . $searchUrl;
    }

    $searchResults = [];

    if (preg_match_all('/<div class="g">.*?<\/div>/s', $response, $searchResults)) {
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
    $articleResponse = file_get_contents($link);

    if ($articleResponse !== false) {
        $articleDoc = new DOMDocument();
        @$articleDoc->loadHTML($articleResponse);

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

    // Após a obtenção do link e da classificação, crie uma matriz de resposta
    $response = [
        'classification' => $classification,
        'link' => $link,
    ];

    // Configure os cabeçalhos para indicar que a resposta é JSON
    header('Content-Type: application/json');

    // Codifique a matriz de resposta como JSON e imprima na saída
    echo json_encode($response);
}

function log_activity($message, $news, $country) {
    // Registre a atividade no banco de dados ou em um arquivo de log
    // Implemente a lógica de registro de atividade adequada aqui
}

// Obter parâmetros da solicitação
$news = isset($_GET['news']) ? $_GET['news'] : '';
$country = isset($_GET['country']) ? $_GET['country'] : '';

// Chame a função para processar a solicitação
categoryBR1($news, $country);
<script>
  // Envie um evento personalizado para rastrear as solicitações do aplicativo
  gtag('event', 'SolicitacaoApp', {
    'event_category': 'AppInteractions',
    'event_label': 'NomeDaSolicitacao' // Substitua pelo nome da sua solicitação
  });
</script>
