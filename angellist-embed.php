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

add_action('admin_head', 'angellist_edit_tools');

function angellist_edit_tools() {
  wp_enqueue_script('angellist_autocomplete_search', plugins_url() . '/angellist-embed/js/autocomplete.js', array('jquery'));
  wp_enqueue_script('angellist_jquery_ui', plugins_url() . '/angellist-embed/js/jquery-1.7.1.min.js', array('jquery'));
  wp_enqueue_script('angellist_jquery_ui_custom', plugins_url() . '/angellist-embed/js/jquery-ui-1.8.18.custom.min.js', array('jquery'));
  wp_enqueue_style('angellist_autocomplete_search_styles', plugins_url() . '/angellist-embed/css/autocomplete.css');
  wp_enqueue_style('angellist_autocomplete_search_styles', plugins_url() . '/angellist-embed/css/jquery-ui-1.8.18.custom.css');
  add_meta_box('angellist_embed_search','What Company/Person is this Article About?', 'angellist_edit_autocomplete_box', 'post');

}


function angellist_edit_autocomplete_box() {
  include( dirname(__FILE__) . '/views/angellist_search.html');
}


