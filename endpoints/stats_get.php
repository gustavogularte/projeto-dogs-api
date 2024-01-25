<?php 

function apiStatsGet($request) {
  $user = get_current_user();
  $userID = $user->ID;

  if ($userID === 0) {
    $response = new WP_Error('error', 'Usuário não possui permissão', ['status' => 401]);
    return rest_ensure_response($response);
  }

  $args = [
    'post_type' => 'post',
    'author' => $userID,
    'post_per_page' => -1,
  ];
  $query = new WP_Query($args);
  $posts = $query->posts;

  $stats = [];
  if ($posts) {
    foreach($posts as $post) {
      $stats[] = [
        'id' => $post->ID,
        'title' => $post->post_title,
        'acessos' => get_post_meta($post->ID, 'acessos', true),
      ];
    }
  }

  return rest_ensure_response($stats);
}

function registerApiStatsGet() {
  register_rest_route('api', '/Stats', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'apiStatsGet',
  ]);
}
add_action('rest_api_init', 'registerApiStatsGet');
?>