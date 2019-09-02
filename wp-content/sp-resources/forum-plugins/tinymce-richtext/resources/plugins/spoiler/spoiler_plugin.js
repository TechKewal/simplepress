/**
 *
 * @author Yellow Swordfish
 * @copyright https://simple-press.com.
 */

(function() {
	tinymce.create('tinymce.plugins.SpoilerPlugin', {

		init : function(ed) {
			ed.addCommand('mceSpoiler', function(ui,v) {
				selText = ed.selection.getContent();
				if (selText){
					html = '[spoiler]'+selText+'[/spoiler]';
					ed.execCommand("mceInsertContent", false, html);
					ed.execCommand('mceRepaint');
				}
			});

			ed.addButton('spoiler', {
				icon: 'spoiler',
				tooltip: 'Spoiler',
				cmd: 'mceSpoiler',

				onPostRender: function() {
					var ctrl = this;

					ed.on('NodeChange', function(e) {
						if (ed.selection.getContent()) {
							ctrl.disabled(false);
						} else {
							ctrl.disabled(true);
						}
					});
				}
			});
		},

	});

	// Register plugin
	tinymce.PluginManager.add('spoiler', tinymce.plugins.SpoilerPlugin);
})();
