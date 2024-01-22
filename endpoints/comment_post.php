<?php 

function apiCommentPost($request) {
  $user = wp_get_current_user();
  $userID = (int) $user->ID;
  
  if ($userID === 0) {
    $response = new WP_Error('error', 'Sem permissão', ['status' => 401]);
    return rest_ensure_response($response);
  }

  $comment = sanitize_text_field($request['comment']);
  $postID = $request['id'];

  if (empty($comment)) {
    $response = new WP_Error('error', 'Dados incompletos', ['status' => 422]);
    return rest_ensure_response($response);
  }

  $response = [
    'comment_author' => $user->user_login,
    'comment_content' => $comment,
    'comment_post_ID' => $postID,
    'user_id' => $userID,
  ];
  $commentID = wp_insert_comment($response);
  $comment = get_comment($commentID);

  return rest_ensure_response($comment);
}

function registerApiCommentPost() {
  register_rest_route('api', '/comment/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'apiCommentPost',
  ]);
}
add_action('rest_api_init', 'registerApiCommentPost');
?>