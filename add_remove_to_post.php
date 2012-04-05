<?
if (!empty($_POST['post_id']) && !empty($_POST['action'])) {
  $action = $_POST['action'];
  $post_id = $_POST['post_id'];
  $key = 'angellist_profile_url';

  if (!empty($_POST['url']) && $action == 'add') {
    $url = $_POST['url'];
    update_post_meta($post_id, $key, $url);
  }
  elseif ($action == 'remove') {
    delete_post_meta($post_id, $key);
  }
}