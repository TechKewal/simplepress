/**
 * Simple Press plugin
 */

tinymce.PluginManager.add('sphelp', function(editor, url) {
	editor.addCommand('sp_help', function() {
		editor.windowManager.open({
			file : url + '/sp-help.php',
			width : 450,
			height : 420,
			inline : 1,
			popup_css : false
		}, {
			plugin_url : url, // Plugin absolute URL
		});
	});

	editor.addButton('sphelp', {
		cmd : 'sp_help',
		icon : 'help',
		tooltip : 'Help'
	});

});
