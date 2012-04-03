jQuery(document).ready(function() {

  var search_box = jQuery('.angellist_search');
  var is_typing_query = false;
  var is_searching = false
  var min_length = 3;
  var last_char_typed_at;
  var pause_before_search_ms = 1000;


  // pretend we're searching immediately
  // but wait before actually firing off a query until they've stopped typing for a second
  function try_search(query, position) {
    render_results({'name': 'searching...'});
    setTimeout(function() {
      if ( (new Date()).getTime() - last_char_typed_at >= pause_before_search_ms) {
	search(query, position);
      }
    }, pause_before_search_ms);
  }


  // request from local server's plugin file, which will curl-call out to the
  // angellist API. No cross-site scripting.
  function search(query, position) {
    jQuery.ajax({
      'url': '/wp-content/plugins/angellist-embed/search.php',
      'data': {'query': query},
      'dataType': 'json',
      'method': 'GET',
      'success': function(results) { render_results(results, position); },
      'error': function(results) { render_results(results, position); },
    });
  }

  function render_results(results) {
    console.log(results);

    // plugin to tinyMCE and add the 
  }


  function 


  // clear the input if the default text is in there
  search_box.focus(function() {
    if ($(this).val() === $(this).attr('start_val')) {
      $(this).val('');
    }
  });


  // check on each keypress if the autocomplete search should be added
  search_box.keyup(function() {
    last_char_typed_at = (new Date()).getTime();
    if (search_box.val().length >= min_length) {
      try_search(serach_box.val());
    }
  });

  


}