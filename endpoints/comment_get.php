<?php 

function apiCommentGet($request) {
  $postID = $request['id'];

  $comments = get_comments([
    'post_id' => $postID,
  ]);

  return rest_ensure_response($comments);
}

function registerApiCommentGet() {
  register_rest_route('api', '/comment/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'apiCommentGet',
  ]);
}
add_action('rest_api_init', 'registerApiCommentGet');
?>