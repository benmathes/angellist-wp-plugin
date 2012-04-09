jQuery(document).ready(function() {
  // namespace everything so we don't clash with other WP plugins
  var search_box = jQuery('.angellist .angellist_search');

  // only run when the AngelList embed builder is included.
  search_box.each(function() {
    var currently_selected = jQuery('.angellist .currently_selected');
    var preview = jQuery('.angellist .angellist_preview');
    var embed_preview = jQuery('.angellist #angellist_embed');
    var arrow = jQuery('.angellist .expand_angellist_preview_arrow');
    var expand_preview_button = jQuery('.angellist .expand_angellist_preview');
    var preview_explanation = jQuery('.angellist .preview_explanation');
    var expand_angellist_button_description = jQuery('.angellist .expand_angellist_button_description');

    // if we have profile, pre-fill it
    var prefill_profile = jQuery('.angellist input[name="angellist_profile"]');
    if (prefill_profile.length > 0) {
      prefill_profile = jQuery.parseJSON(prefill_profile.val()); // it's json_encode()'d 
      select_result(prefill_profile, true);
    }

    expand_preview_button.click(toggle_preview);

    search_box.autocomplete({
      'source': search,
      'minLength': 3,
      'autoFocus': true,
      'select': function(event, ui) {
        select_result(ui.item);
      }
    });

    // clear the input if the default text is in there
    search_box.focus(function() {
      if (jQuery(this).is('.inactive')) {
        jQuery(this).removeClass('inactive');
        jQuery(this).val('');
      }
    });

    // hack to custom render the items.
    // IMPORTANT: the structure (e.g. which HTML nodes are used and what their classes are) is
    // quite delicate. This is monkey-patching a function internal to jquery UI's autocomplete plugin.
    search_box.data("autocomplete")._renderItem = function(results_list, item) {
      var result_li = jQuery("<li>").addClass('angellist_search_result_list_item').data("item.autocomplete", item);
      var click_anchor = jQuery('<a>').addClass('angellist_search_result');
      var img = jQuery('<div>').addClass('img_holder').append(jQuery('<img>').attr('src', item.pic));
      var name = jQuery('<div>').addClass('name').text(item.name)
      result_li.append(click_anchor.append(img).append(name));
      results_list.append(result_li);
    };

    // request from local server's plugin file, which will curl-call out to the
    // angellist API. No cross-site scripting.
    function search(query, results_callback) {
      jQuery.ajax({
        'url': '/wp-content/plugins/angellist-embed/search.php',
        'data': {'query': query.term},
        'dataType': 'json',
        'type': 'GET',
        'success': results_callback,
        'error': results_callback
      });
    }


    function select_result(result, prefill) {
      // link to view profile in new window. 
      // and an 'x' to remove the item.
      var selected_link = jQuery('<a>').attr({
        'class': 'selected_link',
        'target': '_blank',
        'href': result.url,
        'title': 'open AngelList profile in a new window ⇗'
      });
      selected_link.html(result.name);
      var remove_x = jQuery('<a>').attr({
        'class': 'remove',
        'href': 'javascript:',
        'title': 'remove from this post',
      }).text('x');
      remove_x.click(remove_selected_result)
      selected_link.append(remove_x);
      currently_selected.empty().append(selected_link);
      load_preview(result.url)

      if (!prefill) {
        add_to_post(result);
      }
    }

    function load_preview(slug_url) {
      preview.empty().append("<div id='angellist_embed'></div><script src='" + slug_url + "/embed/pandodaily.js' type='text/javascript'></script>");
      expand_preview_button.fadeIn();
    }

    function toggle_preview(force) {
      if (preview.is('.expanded') || force == 'hide') {
        preview.removeClass('expanded');
        preview.slideUp();
        preview_explanation.fadeOut();
        expand_angellist_button_description.text('show');
        arrow.text('⇣');
      }
      else if (!preview.is('.expanded') || force == 'show') {
        preview.addClass('expanded');
        preview.slideDown();
        preview_explanation.fadeIn();
        expand_angellist_button_description.text('hide');
        arrow.text('⇡');      
      }
    }

    function remove_selected_result() {
      currently_selected.empty();
      toggle_preview('hide');
      expand_preview_button.fadeOut();
      remove_from_post();
    }

    function add_to_post(profile) {
      jQuery.ajax({
        'url': AngelList_AJAX.url,
        'data': {
          'angellist_profile': profile, 
          'post_id': AngelList_AJAX.post_id, 
          'action': 'add_angellist_widget_to_post' 
        },
        'dataType': 'json',
        'type': 'POST',
        'success': function() {},
        'error': function() {}
      });
    }

    function remove_from_post(profile) {
      jQuery.ajax({
        'url': AngelList_AJAX.url,
        'data': {
          'post_id': AngelList_AJAX.post_id, 
          'action': 'remove_angellist_widget_from_post' 
        },
        'dataType': 'json',
        'type': 'POST',
        'success': function() {},
        'error': function() {}
      });
    }
  });
});