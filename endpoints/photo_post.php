<?php 

function apiPhotoPost($request) {
  $user = wp_get_current_user();
  $userID = $user->ID;

  if ($userID === 0) {
    $response = new WP_Error('error', 'Usúario não possui permissão', ['status' => 401]);
    return rest_ensure_response($response);
  }

  $nome = sanitize_text_field($request['nome']);
  $peso = sanitize_text_field($request['peso']);
  $idade = sanitize_text_field($request['idade']);
  $files = $request->get_file_params();

  if (empty($nome) || empty($peso) || empty($idade) || empty($files)) {
    $response = new WP_Error('error', 'Dados incompletos', ['status' => 422]);
    return rest_ensure_response($response);
  }

  $response = [
    'post_author' => $userID,
    'post_type' => 'post',
    'post_status' => 'publish',
    'post_title' => $nome,
    'post_content' => $nome,
    'files' => $files,
    'meta_input' => [
      'peso' => $peso,
      'idade' => $idade,
      'acessos' => 0,
    ],
  ];
  $postID = wp_insert_post($response);

  require_once ABSPATH . 'wp-admin/includes/image.php';
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';

  $photoID = media_handle_upload('img', $postID);
  update_post_meta($postID, 'img', $photoID);

  return rest_ensure_response($response);
}

function registerApiPhotoPost() {
  register_rest_route('api', '/photo', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'apiPhotoPost',
  ]);
}
add_action('rest_api_init', 'registerApiPhotoPost');
?>