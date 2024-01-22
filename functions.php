<?php
//remove_action('rest_api_init', 'create_initial_rest_routes', 99);

$dirbase = get_template_directory();
require_once $dirbase . '/endpoints/user_post.php';
require_once $dirbase . '/endpoints/user_get.php';

require_once $dirbase . '/endpoints/photo_post.php';
require_once $dirbase . '/endpoints/photo_delete.php';

require_once $dirbase . '/endpoints/comment_post.php';
require_once $dirbase . '/endpoints/comment_get.php';

update_option('large_size_w', 1000);
update_option('large_size_h', 1000);
update_option('large_crop', 1);

function changeApi() {
  return 'json';
}
add_filter('rest_url_prefix', 'changeApi');

function expirarToken() {
  return time() + (1000 * 1000 * 1000);
}
add_action('jwt_auth_expire', 'expirarToken');
?>