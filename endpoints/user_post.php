<?php 

function apiUserPost($request) {
  $response = [
    'ID' => '2',
    'user_login' => 'teste',
  ];
  return rest_ensure_response($response);
}

function registerApiUserPost() {
  register_rest_route('api', '/user', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'apiUserPost',
  ]);
}
add_action('rest_api_init', 'registerApiUserPost');
?>