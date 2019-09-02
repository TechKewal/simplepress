/* Syntax Highlighter plugin */
/* Simple:Press */

(function() {
	tinymce.create('tinymce.plugins.SyntaxPlugin', {

		init : function(ed) {

            var brushes = new Array();
			var list = tinyMCE.activeEditor.getParam("brushes");

            /* make sure we have some brushes to work with */
            if (!list) return;

    		var langs = list.split(',');
    		var x = langs.length;

    		for (i=0; i<x; i++) {
    			switch(langs[i]) {
    				case 'apache':
    					brushes.push({text: 'apache', onclick : function() { processCodeLang('apache'); }});
                        break;
    				case 'applescript':
    					brushes.push({text: 'applescript', onclick : function() { processCodeLang('applescript'); }});
                        break;
    				case 'asm':
    					brushes.push({text: 'asm', onclick : function() { processCodeLang('asm'); }});
                        break;
    				case 'bash-script':
    					brushes.push({text: 'bash-script', onclick : function() { processCodeLang('bash-script'); }});
                        break;
    				case 'bash':
    					brushes.push({text: 'bash', onclick : function() { processCodeLang('bash'); }});
                        break;
    				case 'basic':
    					brushes.push({text: 'basic', onclick : function() { processCodeLang('basic'); }});
                        break;
    				case 'clang':
    					brushes.push({text: 'clang', onclick : function() { processCodeLang('clang'); }});
                        break;
    				case 'css':
    					brushes.push({text: 'css', onclick : function() { processCodeLang('css'); }});
                        break;
    				case 'diff':
    					brushes.push({text: 'diff', onclick : function() { processCodeLang('diff'); }});
                        break;
    				case 'html':
    					brushes.push({text: 'html', onclick : function() { processCodeLang('html'); }});
                        break;
    				case 'java':
    					brushes.push({text: 'java', onclick : function() { processCodeLang('java'); }});
                        break;
    				case 'javascript':
    					brushes.push({text: 'javascript', onclick : function() { processCodeLang('javascript'); }});
                        break;
    				case 'lisp':
    					brushes.push({text: 'lisp', onclick : function() { processCodeLang('lisp'); }});
                        break;
    				case 'ooc':
    					brushes.push({text: 'ooc', onclick : function() { processCodeLang('ooc'); }});
                        break;
    				case 'php':
    					brushes.push({text: 'php', onclick : function() { processCodeLang('php'); }});
                        break;
    				case 'python':
    					brushes.push({text: 'python', onclick : function() { processCodeLang('python'); }});
                        break;
    				case 'ruby':
    					brushes.push({text: 'ruby', onclick : function() { processCodeLang('ruby'); }});
                        break;
    				case 'sql':
    					brushes.push({text: 'sql', onclick : function() { processCodeLang('sql'); }});
                        break;
    				case 'sql':
    					brushes.push({text: 'yaml', onclick : function() { processCodeLang('yaml'); }});
                        break;
    			}
            }

			ed.addButton('syntax', {
				type: 'splitbutton',
				text: '',
				icon: 'syntax',
				tooltip: 'Syntax Highlighter',
				onclick: function() {
					//ed.insertContent('Main button');
				},
				menu: brushes,
				onPostRender: function() {
					var ctrl = this;
					ed.on('NodeChange', function(e) {
						if(ed.selection.getContent()) {
							ctrl.disabled(false);
						} else {
							ctrl.disabled(true);
						}
					});
				}
			});

			function processCodeLang(codeLang) {
				selText = tinyMCE.activeEditor.selection.getContent();
				selText = selText.replace(/<p[^>]*>/g, '').replace(/<\/p>/g, '<br /><br />');
				html = '<div class="sfcode"><pre class="brush-'+codeLang+' syntax">'+selText+'</pre></div>';
				tinyMCE.activeEditor.execCommand("mceInsertContent", false, html);
				tinyMCE.activeEditor.execCommand('mceRepaint');
				return;
			}

		},
	});

	tinymce.PluginManager.add('syntax', tinymce.plugins.SyntaxPlugin);
})();