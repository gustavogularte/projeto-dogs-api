<?php 

function apiUserPost($request) {
  $username = sanitize_text_field($request['username']);
  $password = $request['password'];
  $email = sanitize_email($request['email']);

  if (empty($username) || empty($password) || empty($email)) {
    $response = new WP_Error('error', 'Dados incompletos', ['status' => 406]);
    return rest_ensure_response($response);
  }

  if (username_exists($username)) {
    $response = new WP_Error('error', 'Nome de usuário já existe', ['status' => 403]);
    return rest_ensure_response($response);
  }

  if (email_exists($email)) {
    $response = new WP_Error('error', 'Email já cadastrado', ['status' => 403]);
    return rest_ensure_response($response);
  }

  $response = wp_insert_user([
    'user_login' => $username,
    'user_pass' => $password,
    'user_email' => $email,
    'role' => 'subscriber',
  ]);
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