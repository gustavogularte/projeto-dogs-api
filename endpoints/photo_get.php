<?php 

function photoData($post) {
  $postMeta = get_post_meta($post->ID);
  $src = wp_get_attachment_image_src($postMeta['img'][0], 'large')[0];
  $user = get_userdata($post->post_author);
  $totalComments = get_comments_number($post->ID);

  return [
    'id' => $post->ID,
    'author' => $user->user_login,
    'title' => $post->post_title,
    'date' => $post->post_date,
    'src' => $src,
    'peso' => $postMeta['peso'][0],
    'idade' => $postMeta['idade'][0],
    'acessos' => $postMeta['acessos'][0],
    'total_comments' => $totalComments,
  ];
}

function apiPhotoGet($request) {
  $postID = $request['id'];
  $post = get_post($postID);

  if (!isset($post) || empty($postID)) {
    $response = new WP_Error('error', 'Post não encontrado', ['status' => 404]);
    return rest_ensure_response($response);
  }

  $photo = photoData($post);
  $photo['acessos'] = (int) $photo['acessos'] + 1;
  update_post_meta($postID, 'acessos', $photo['acessos']);

  $comments = get_comments([
    'post_id' => $postID,
    'order' => 'ASC',
  ]);

  $response = [
    'photo' => $photo,
    'comments' => $comments,
  ];

  return rest_ensure_response($response);
}

function registerApiPhotoGet() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'apiPhotoGet',
  ]);
}
add_action('rest_api_init', 'registerApiPhotoGet');

function apiPhotosGet($request) {
  $_total = sanitize_text_field($request['_total']) ?: 6;
  $_page = sanitize_text_field($request['_page']) ?: 1;
  $_user = sanitize_text_field($request['_user']) ?: 0;

  if (!is_numeric($_user)) {
    $user = get_user_by('login', $_user);
    if (!$user) {
      $response = new WP_Error('error', 'Usuário não encontrado', ['status' => 404]);
      return rest_ensure_response($response);
    }
    $_user = $user->ID;
  }

  $args = [
    'post_type' => 'post',
    'author' => $_user,
    'post_per_page' => $_total,
    'paged' => $_page,
  ];

  $query = new WP_Query($args);
  $posts = $query->posts;

  $photos = [];
  if($posts) {
    foreach ($posts as $post) {
      $photos[] = photoData($post);
    }
  }

  return rest_ensure_response($photos);
}

function registerApiPhotosGet() {
  register_rest_route('api', '/photo', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'apiPhotosGet',
  ]);
}
add_action('rest_api_init', 'registerApiPhotosGet');
?>