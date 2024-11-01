(function() {
   tinymce.create('tinymce.plugins.copysafepdf', {
      init : function(ed, url) {
         ed.addButton('copysafepdf', {
            title : 'CopySafe PDF',
            image : url+'/images/copysafepdfbutton.png',
            onclick : function() {
                var name = prompt("Name of the class file", "");
                if (name != null && name != '')
                    ed.execCommand('mceInsertContent', false, '[copysafepdf name="'+name+'"][/copysafepdf]');
            }
         });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : "CopySafe PDF",
            author : 'ArtistScope',
            authorurl : 'http://www.artistscope.com/',
            infourl : 'http://www.artistscope.com/copysafe_pdf_protection_wordpress_plugin.asp',
            version : "0.1"
         };
      }
   });
   tinymce.PluginManager.add('copysafepdf', tinymce.plugins.copysafepdf);
})();