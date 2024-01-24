<?php 

function apiPasswordLost($request) {
  $email = $request['email'];
  $url = $request['url'];

  if (empty($email)) {
    $response = new WP_Error('error', 'Informe seu email', ['status' => 406]);
    return rest_ensure_response($response);
  }
  $user = get_user_by('email', $email);
  if (!$user) {
    $response = new WP_Error('error', 'Email não cadastrado', ['status' => 401]);
    return rest_ensure_response($response);
  }

  $key = get_password_reset_key($user);
  $login = $user->user_login;

  $message = "Link para resetar senha: \r\n";
  $body = $message . "$url/key=$key&login=$login";

  wp_mail($email, 'Password Reset', $body);

  return rest_ensure_response('Email enviado');
}

function registerApiPasswordLost() {
  register_rest_route('api', '/password/lost', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'apiPasswordLost',
  ]);
}
add_action('rest_api_init', 'registerApiPasswordLost');
?>