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
  wp_enqueue_script('auto_at_mention', plugins_url() . '/angellist-embed/js/auto_at_mention.js', array('jquery'));
}