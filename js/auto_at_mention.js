jQuery(document).ready(function() {

  // detecting @ means shift+2
  var lastKey = null;
  var search_string = '';
  var is_typing_query = false;
  var is_searching = false
  var min_length = 3;
  var last_char_typed_at;
  var pause_before_search_ms = 1000;

  function try_search(query, position) {
    // pretend we're searching immediately
    render_results({'name': 'searching...'});

    setTimeout(function() {
      // but wait before actually firing off a query until they've stopped typing for a second
      if ( (new Date()).getTime() - last_char_typed_at >= pause_before_search_ms) {
	search(query, position);
      }
    }, pause_before_search_ms);
  }

  function get_character(event) {
    var ignore_keys = {
      16: 'shift',
      17: 'ctrl',
      18: 'alt',      
    }
    if (!ignore_keys[event.which]) {
      return String.fromCharCode(event.which == null ? event.keyCode : event.which);
    } else {
      return null;
    }
  }

  function search(query, position) {
    // request from local server's plugin file, which will curl-call out to the
    // angellist API. No cross-site scripting.
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
  }

  function get_search_string(editor) {
    // tinyMCE gives us no good way to get non-HTML content
    rawContent = editor.getContent({format : 'raw'});

    // regex for \s+@(\w+\s+\w+). what if more than one? Remove after search (ensure 1)?
    var search_string = rawContent;
  }

  // wire up the tinyMCE editor to search using @mentions
  if (typeof tinyMCE !== 'undefined') {
    tinyMCE.onAddEditor.add(function(mgr,editor) {
      editor.onKeyUp.add(function(editor, event) {
	console.log(event.which);
	var character = get_character(event);
	last_char_typed_at = (new Date()).getTime();

	search_string = get_search_string(editor);
	console.log(search_string);

	if (search_string.length >= min_length) {
	  try_search(search_string, {'x':event.pageX, 'y':event.pageY});
	}

	if (event.which === 50) {
	  is_typing_query = true;
	}
      });
    });
  }



});