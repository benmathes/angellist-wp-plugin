jQuery(document).ready(function() {

  if (tinyMCE) {
    console.log(tinyMCE);
    tinyMCE.onAddEditor.add(function(mgr,editor) {
      console.debug('A new editor is available' + editor.id);
      editor.onKeyUp.add(function(editor, event) {
	console.log(editor, event.which);
      });
    });
  }
});