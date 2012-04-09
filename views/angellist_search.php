<?
  global $post;
  $angellist_profile = get_post_meta($post->ID, 'angellist_profile', $single = true);
?>

<div class="angellist">

  <? if (!empty($angellist_profile)) : ?>
    <input type="hidden" name="angellist_profile" value='<?= htmlspecialchars(json_encode($angellist_profile)) ?>'>
  <? endif; ?>

  <input type="text" 
	 name="angellist_embed_search" 
	 class="angellist_search inactive"
	 value="startup or user name..."
	 start_val="startup or user name..."
	 >

  <span class="currently_selected"></span>
     
  <a href="javascript:" class="expand_angellist_preview button">
    <span class="expand_angellist_button_description">show</span>
    preview 
    <span class="expand_angellist_preview_arrow">⇣</span>
  </a>

  <div class="preview_explanation">
    If this preview looks bad or emtpy, AngelList just got an email and we'll fix it.
  </div>

  <div class="angellist_preview">
  </div>

</div>
