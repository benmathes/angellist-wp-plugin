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
 * editing a post: adding in the JS, CSS, and markup
 * we'll need for the autocomplete searches.
 */
add_action('admin_head', 'angellist_edit_tools');
function angellist_edit_tools() {
  // jquery and custom jquery UI for the autocomplete
  wp_enqueue_script('angellist_jquery', plugins_url() . '/angellist-embed/js/jquery-1.7.1.min.js', null, false, $in_footer = true);
  wp_enqueue_script('angellist_jquery_ui_custom', plugins_url() . '/angellist-embed/js/jquery-ui-1.8.18.custom.min.js', null, false, $in_footer = true);

  // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
  $autocomplete_script = 'angellist_autocomplete';
  wp_enqueue_script($autocomplete_script, plugins_url() . '/angellist-embed/js/autocomplete.js', null, false, $in_footer = true);
  global $post;
  wp_localize_script($autocomplete_script, 'AngelList_AJAX', array('url' => admin_url('admin-ajax.php'), 'post_id' => $post->ID));

  wp_enqueue_style('angellist_autocomplete_search_styles', plugins_url() . '/angellist-embed/css/autocomplete.css');
  //wp_enqueue_style('angellist_jquery_ui_search_styles', plugins_url() . '/angellist-embed/css/jquery-ui-1.8.18.custom.css');
  add_meta_box('angellist_embed_search','What Startup or Person is this Article About?', 'angellist_edit_autocomplete_box', 'post');
}
function angellist_edit_autocomplete_box() {
  include( dirname(__FILE__) . '/views/angellist_search.php');
}



/**
 * add/remove over ajax in realtime as the author edits the posts
 * with custom actions that javascript pass into /wp-admin/admin-ajax.php
 */
add_action('wp_ajax_add_angellist_widget_to_post', 'angellist_add_to_post');
function angellist_add_to_post() {
  if (!empty($_POST['post_id'])) {
    update_post_meta($_POST['post_id'], 'angellist_profile', $_POST['angellist_profile']);
  }
}
add_action('wp_ajax_remove_angellist_widget_from_post', 'angellist_remove_from_post');
function angellist_remove_from_post() {
  if (!empty($_POST['post_id'])) {
    delete_post_meta($_POST['post_id'], 'angellist_profile');
  }
}



/**
 * if a given post has an angellist embed widget, add in the js that will render it
 */
add_action('wp_head', 'load_widget_if_post_has_it');
function load_widget_if_post_has_it() {
  global $post;
  if (!empty($post)) {
    $angellist_profile = get_post_meta($post->ID, 'angellist_profile', $single = true);
    if (!empty($angellist_profile)) {

      // always queue up the JS that will load in the widget
      wp_enqueue_script('angellist_load_the_embed_widget', "${angellist_profile['url']}/embed/pandodaily.js", array('jquery'), false, $in_footer = true);

      // before editing the post, first look for previous embed
      // widget in the post body to remain idempotent.
      $embed_code = "<div id='angellist_embed'></div>";
      if (FALSE === strpos($post->post_content, $embed_code)) {
        $post->post_content .= $embed_code;
        wp_update_post($post);
      }
    }
  }
}



/**
 * callback to angellist when the post is published
 * so we can let the startup founders know they have
 * some press to check out.
 */
add_action('publish_post', 'angellist_publish_notify');
function angellist_publish_notify($post_id) {
  $angellist_profile = get_post_meta($post_id, 'angellist_profile', $single = true);
  $already_notified = get_post_meta($post_id, 'angellist_entity_notified', $single = true);
  if ($angellist_profile && !$already_notified) {
    ob_start();

    // TODO: DON'T COMMIT. CHANGE TO ANGEL.CO.
    $ping_url = "http://angel.co/embed/post_published/?"
      . "type="        . urlencode($angellist_profile['type']) 
      . "&name="       . urlencode($angellist_profile['name'])
      . "&id="         . urlencode($angellist_profile['id'])
      . "&url="        . urlencode($angellist_profile['url'])
      . "&perma_link=" . urlencode(get_permalink($post_id));

    $curl_handle = curl_init($ping_url);
    $results = curl_exec($curl_handle);
    $response = curl_getinfo($curl_handle);
    curl_close($curl_handle);

    // mark that we notified AL.    
    update_post_meta($post_id, 'angellist_entity_notified', true);
    // don't need the output.
    ob_end_clean(); 
  }
}