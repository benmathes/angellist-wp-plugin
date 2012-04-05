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
  wp_enqueue_script('angellist_jquery', plugins_url() . '/angellist-embed/js/jquery-1.7.1.min.js');
  wp_enqueue_script('angellist_jquery_ui_custom', plugins_url() . '/angellist-embed/js/jquery-ui-1.8.18.custom.min.js');
  wp_enqueue_script('angellist_autocomplete', plugins_url() . '/angellist-embed/js/autocomplete.js');
  wp_enqueue_style('angellist_autocomplete_search_styles', plugins_url() . '/angellist-embed/css/autocomplete.css');
  wp_enqueue_style('angellist_jquery_ui_search_styles', plugins_url() . '/angellist-embed/css/jquery-ui-1.8.18.custom.css');
  add_meta_box('angellist_embed_search','What Startup or Person is this Article About?', 'angellist_edit_autocomplete_box', 'post');
}
function angellist_edit_autocomplete_box() {
  // TODO: if a post has a URL associated, display on page load
  // ideally plugin to JS by firing events?
  include( dirname(__FILE__) . '/views/angellist_search.html');

  echo "<hr>" . get_post_meta($post_id, 'angellist_profile_url', $single = true);
}


/**
 * APPEND THE WIDGET ON SAVE
 */
add_action('save_post', 'angellist_add_widget');
function angellist_add_widget($post_id) {
  // check for post_meta
  $profile_url = get_post_meta($post_id, 'angellist_profile_url');
  if ($profile_url) {
    $post = get_post($post_id);

    // look for previous embed widget in the post body:
    $pattern_start = "<!-- BEGIN ANGELLIST EMBED WIDGET -->";
    $pattern_end = "<!-- END ANGELLIST EMBED WIDGET -->";
    $embed_code = "<div id='angellist_embed'></div><script src='${$profile_url}/embed/pandodaily.js' type='text/javascript'></script>";
    if (preg_match("/${pattern_start}.*${pattern_end}/", $post->post_content)) {
      preg_replace("/${$pattern_start}.*${$pattern_end}/$pattern_start $embed_code $pattern_end/", $post->post_content);
    }
    else {
      $post['post_content'] .= "$pattern_start $embed_code $pattern_end";
    }

    // unhook this function so it doesn't loop infinitely
    remove_action('save_post', 'angellist_add_widget');
    // update the post, which calls save_post again
    wp_update_post($post);
    // re-hook this function
    add_action('save_post', 'angellist_add_widget');
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