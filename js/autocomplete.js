jQuery(document).ready(function() {

  var search_box = jQuery('.angellist_search');
  var is_typing_query = false;
  var is_searching = false
  var min_length = 3;
  var last_char_typed_at;
  var pause_before_search_ms = 1000;

  search_box.autocomplete({
    source: search,
    minLength: 3,
    select: function( event, ui ) {
      console.log( ui.item ?
		   "Selected: " + ui.item.label :
		   "Nothing selected, input was " + this.value);
    },
    render: function(a,b,c) { console.log(a,b,c); },
    open: function() {
      //$(this).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
    },
    close: function() {
      //$(this).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
    }
  });

  // hack to custom render the items
  search_box.data("autocomplete")._renderItem = function (ul, item) {
    console.log(ul, item);
    return jQuery("<li>").data("item.autocomplete", item)
      .append(jQuery('<img>')
      .appendTo(ul);
  };


  // request from local server's plugin file, which will curl-call out to the
  // angellist API. No cross-site scripting.
  function search(query, results_callback) {
    jQuery.ajax({
      'url': '/wp-content/plugins/angellist-embed/search.php',
      'data': {'query': query.term},
      'dataType': 'json',
      'method': 'GET',
      'success': function(results) { results_callback.apply(this, [results]) },
      'error': function(results) { results_callback.apply(this, [results]); },
    });
  }

  function render_results(results) {
    console.log(results);
    // plugin to tinyMCE and add the markup at the bottom?
  }


  // clear the input if the default text is in there
  search_box.focus(function() {
    if (jQuery(this).is('.inactive')) {
      jQuery(this).removeClass('inactive');
      jQuery(this).val('');
    }
  });

});