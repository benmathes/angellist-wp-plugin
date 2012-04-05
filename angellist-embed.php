<?php
/*
Plugin Name: Angellist Embed
Plugin URI: http://angel.co
Description: helps you add angellist embed widgets into your wordpress blog
Version: 0.1
Author: AngelList
Author URI: http://angel.co
License: GPL2

Copyright 2012  AngelList  (email : hackers@angel.co)
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2, as 
   published by the Free Software Foundation.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * EDITING A POST
 */
add_action('admin_head', 'angellist_edit_tools');
function angellist_edit_tools() {
  wp_enqueue_script('angellist_jquery', plugins_url() . '/angellist-embed/js/jquery-1.7.1.min.js', null, false, $in_footer = true);
  wp_enqueue_script('angellist_jquery_ui_custom', plugins_url() . '/angellist-embed/js/jquery-ui-1.8.18.custom.min.js', null, false, $in_footer = true);

  // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
  $autocomplete_script = 'angellist_autocomplete';
  wp_enqueue_script($autocomplete_script, plugins_url() . '/angellist-embed/js/autocomplete.js', null, false, $in_footer = true);
  global $post;
  wp_localize_script($autocomplete_script, 'AngelList_AJAX', array('url' => admin_url('admin-ajax.php'), 'post_id' => $post->ID));

  wp_enqueue_style('angellist_autocomplete_search_styles', plugins_url() . '/angellist-embed/css/autocomplete.css');
  wp_enqueue_style('angellist_jquery_ui_search_styles', plugins_url() . '/angellist-embed/css/jquery-ui-1.8.18.custom.css');
  add_meta_box('angellist_embed_search','What Startup or Person is this Article About?', 'angellist_edit_autocomplete_box', 'post');
}
function angellist_edit_autocomplete_box() {
  // TODO: if a post has a URL associated, display on page load
  // ideally plugin to JS by firing events?
  include( dirname(__FILE__) . '/views/angellist_search.html');
}



/**
 * ADD/REMOVE OVER AJAX IN REALTIME AS THE AUTHOR EDITS THE POSTS
 * WITH CUSTOM ACTIONS THAT JAVASCRIPT PASS INTO /wp-admin/admin-ajax.php
 */
add_action('wp_ajax_add_angellist_widget_to_post', 'angellist_add_to_post');
function angellist_add_to_post() {
  if (!empty($_POST['post_id'])) {
    update_post_meta($_POST['post_id'], 'angellist_profile_url', $_POST['profile_url']);
  }
}
add_action('wp_ajax_remove_angellist_widget_from_post', 'angellist_remove_from_post');
function angellist_remove_from_post() {
  if (!empty($_POST['post_id'])) {
    delete_post_meta($_POST['post_id'], 'angellist_profile_url');
  }
}

/**
 * INCLUDE JS THAT WILL RENDER THE ANGELLIST WIDGET IF THE POST HAS ONE ATTACHED
 */
add_action('wp_head', 'load_widget_if_post_has_it');
function load_widget_if_post_has_it() {
  global $post;
  $profile_url = get_post_meta($post->ID, 'angellist_profile_url', $single = true);
  if (!empty($profile_url)) {
    wp_enqueue_script('angellist_load_the_embed_widget', "${profile_url}/embed/pandodaily.js", array('jquery'), false, $in_footer = true);
    // look for previous embed widget in the post body:
    // important for performance that this does NOT call update_post on each page load.
    $embed_code = "<div id='angellist_embed'></div>";
    if (FALSE === strpos($post->post_content, $embed_code)) {
      $post->post_content .= $embed_code;
      wp_update_post($post);
    }
  }
}


/**
 * PING US WHEN THE POST IS PUBLISHED
 */
add_action('publish_post', 'angellist_publish_notify');
function angellist_publish_notify($post_id) {
  $profile_url = get_post_meta($post_id, 'angellist_profile_url');
  if ($profile_url) {
    $post = get_post($post_id);
    $ping_url = "https://localhost:3000/embed/post_published/?profile_url=" . urlencode($profile_url) . "&perma_link=" . urlencode(get_permalink($post_id));
    $ch = curl_init($ping_url);
    $results = curl_exec($ch);
    $response = curl_getinfo($ch);
    curl_close($ch);
  }
}