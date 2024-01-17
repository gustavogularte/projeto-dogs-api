<?php 

function apiUserGet($request) {

  $user = wp_get_current_user();
  $userID = $user->ID;

  if ($userID === 0) {
    $response = new WP_Error('error', 'Usúario não possui permissão', ['status' => 401]);
    return rest_ensure_response($response);
  }

  $response = [
    'id' => $userID,
    'username' => $user->user_login,
    'nome' => $user->display_name,
    'email' => $user->user_email,
  ];

  return rest_ensure_response($response);
}

function registerApiUserGet() {
  register_rest_route('api', '/user', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'apiUserGet',
  ]);
}
add_action('rest_api_init', 'registerApiUserGet');
?>