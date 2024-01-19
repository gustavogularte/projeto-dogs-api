<?php 

function apiPhotoDelete($request) {
  $user = wp_get_current_user();
  $userID = (int) $user->ID;
  $postID = $request['id'];
  $post = get_post($postID);
  $authorID = (int) $post->post_author;

  if ($userID !== $authorID || !isset($post)) {
    $response = new WP_Error('error', 'Sem permissão', ['status' => 401]);
    return rest_ensure_response($response);
  }

  $attachmentID = get_post_meta($postID, 'img', true);
  wp_delete_attachment( $attachmentID, true);
  wp_delete_post($postID, true);

  return rest_ensure_response('Post deletado');
}

function registerApiPhotoDelete() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::DELETABLE,
    'callback' => 'apiPhotoDelete',
  ]);
}
add_action('rest_api_init', 'registerApiPhotoDelete');
?>