// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('WPSC');
	 
	tinymce.create('tinymce.plugins.WPSC', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('WPSC', function() {
				ed.windowManager.open({
					file : url + '/window.php',
					width : 360 + ed.getLang('WPSC.delta_width', 0),
					height : 210 + ed.getLang('WPSC.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('WPSC', {
				title : 'WPSC.desc',
				cmd : 'WPSC',
				image : url + '/cart.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('WPSC', n.nodeName == 'IMG');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
					longname  : 'WPSC',
					author 	  : 'Allen Han',
					authorurl : 'http://www.allensplash.com',
					infourl   : 'http://www.instinct.co.nz',
					version   : "2.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('WPSC', tinymce.plugins.WPSC);
	
	
	
	
	
	
 // add image for products page short tag	
 
 	tinymce.create('tinymce.plugins.productspage_image', {
		init : function(ed, url) {
			var pb = '<img src="' + url + '/productspage.gif" class="productspage_image mceItemNoResize" />', cls = 'productspage_image', sep = ed.getParam('productspage_image', '[productspage]'), pbRE;

			pbRE = new RegExp(sep.replace(/[\?\.\*\[\]\(\)\{\}\+\^\$\:]/g, function(a) {return '\\' + a;}), 'g');
			
			// Register commands
			ed.addCommand('productspage_image', function() {
				ed.execCommand('mceInsertContent', 0, pb);
			});

			// Register buttons
			//ed.addButton('subscribe2', {title : 'Insert Products Page Token', image : url + '/productspage.gif', cmd : cls});

			ed.onInit.add(function() {
				//ed.dom.loadCSS(url + "/css/content.css");
				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG' && ed.dom.hasClass(o.node, cls))
							o.name = 'productspage_image';
					});
				}
			});

			ed.onClick.add(function(ed, e) {
				e = e.target;

				if (e.nodeName === 'IMG' && ed.dom.hasClass(e, cls))
					ed.selection.select(e);
			});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('productspage_image', n.nodeName === 'IMG' && ed.dom.hasClass(n, cls));
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(pbRE, pb);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="productspage_image') !== -1)
							im = sep;

						return im;
					});
			});
		},

		getInfo : function() {
			return {
				longname : 'Insert productspage Image',
				author : 'Instinct Entertainment',
				authorurl : 'http://instinct.co.nz',
				infourl : 'http://instinct.co.nz',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});
	
	
	
	tinymce.PluginManager.add('productspage_image', tinymce.plugins.productspage_image);
	
	
})();


