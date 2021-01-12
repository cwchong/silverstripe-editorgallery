(function () {

  var availableLangs = ['en'];
  if (jQuery.inArray(tinymce.settings.language, availableLangs) != -1) {
    tinymce.PluginManager.requireLangPack("ss_insert_inpagegallery");
  }

  var each = tinymce.each;
  tinymce.create('tinymce.plugins.InsertInPageGallery', {
    getInfo: function () {
      return {
        longname: 'Button to insert Galleries',
        author: 'Chong Chee Wai',
        authorurl: 'http://www.movingmouse.com/',
        infourl: 'https://github.com/cwchong/silverstripe-editorgallery',
        version: "1.0"
      };
    },
    init: function (ed, url) {
      // single enter exits the div and creates a paragraph
      ed.on('keydown', function (e) {
      // ed.onKeyDown.add(function (ed, e) {
        // Capture Enter without shift
        if ((e.keyCode == 13)) {
          var dom = ed.dom;

          var parents = dom.getParents(ed.selection.getNode());
          for (var i = parents.length - 1; i >= 0; i--) {
            currentNode = parents[i];
            // Insert empty paragraph at the end of the outermost blockquote tag
            if (currentNode.className == 'ss-inpagegallerywrap') {
              // dom.insertAfter doesn't work reliably
              var uniqueID = dom.uniqueId();
              // dom.uniqueId() is only guaranteed unique in session, need to find if exists in editor:
              var curEl = dom.get(uniqueID);
              while (curEl !== null) {
                uniqueID = dom.uniqueId();
                curEl = dom.get(uniqueID);
              }
              jQuery('<p id="' + uniqueID + '">&nbsp;</p>').insertAfter(currentNode);

              // Move to the new node
              var newParagraph = dom.select('p#' + uniqueID)[0];
              ed.selection.setCursorLocation(newParagraph);
              // remove generated id
              dom.setAttrib(uniqueID, 'id', '');

              // Don't create an extra paragraph
              e.preventDefault();
              break;
            }
          }
        }
        //remove wrapping div if content deleted (so no stray divs)
        if (e.keyCode == 8 || e.keyCode == 46) { //backspace and delete keycodes
          try {
            var elem = ed.selection.getNode().parentNode; //current caret node
            if (elem.classList.contains("ss-inpagegallerywrap")) {
              elem.remove();
              // parent remove should take care of child nodes
              e.preventDefault();
              return false;
            }
          } catch (error) {
            console.log('err', error);
          }
        }
      });

      var handleBtnClick = function (id, type, text) {
        var output = '<div class="ss-inpagegallerywrap"><img class="ss-inpagegallery" data-id="' + id + 
                      '" data-type="' + type + '" alt="Gallery placeholder" title="Gallery: ' + 
          text + '" src="' + url + '/img/gallery-' + type + '.png?"></div>';
        ed.execCommand('mceInsertContent', false, output);
        ed.execCommand('mceRepaint');
      }

      // define the ui btn with list of galleries to insert:
      ed.addButton('ss_insert_inpagegallery', {
        title: 'Insert Gallery',
        image: url + '/../images/gallery-16.png',
        type: 'menubutton',
        menu: (function () { 
          var options = [];
          jQuery.ajax({
            async: false,
            url: ed.documentBaseURI.toAbsolute('inpagegallery/json'),
              data: { pageid: jQuery('#Form_EditForm_ID').val() }, 
              success: function(apiResponseJson) {
                if(! Object.keys(apiResponseJson).length)
                  return alert("Sorry, no active 'Gallery' could be found!");
                jQuery.each(apiResponseJson, function(id, name) {
                  options.push({
                    text: name,
                    menu: [
                      {
                        text: 'Grid',
                        onclick: function () {
                          handleBtnClick(id, 'grid', name);
                        }
                      },
                      {
                        text: 'Carousel',
                        onclick: function () {
                          handleBtnClick(id, 'carousel', name);
                        }
                      },
                      {
                        text: 'Carousel (Wide)',
                        onclick: function () {
                          handleBtnClick(id, 'bigcarousel', name);
                        }
                      }
                    ],
                  });
                });
              },
              error: function( xhr, status ) {
                return alert("Sorry, no active 'Gallery' could be found!");
              }
            });
          return options;
        })()
      });

      // converts <img class="ss-inpagegallery" /> into shortcode [inpagegallery] on save
      ed.on('SaveContent', function (o) {
        var content = jQuery(o.content);
        content.find('.ss-inpagegallery').each(function () {
          var el = jQuery(this);
          var shortCode = '[inpagegallery id="' + el.data('id') + '" type="' + el.data('type') + '"]' + el.attr('title') + '[/inpagegallery]';
          el.replaceWith(shortCode);
        });
        o.content = jQuery('<div />').append(content).html(); // Little hack to get outerHTML string
      });

      var shortTagRegex = /(.?)\[inpagegallery(.*?)\](.+?)\[\/\s*inpagegallery\s*\](.?)/gi;

      // restores <img class="ss-inpagegallery" /> from shortcode [inpagegallery] on load
      ed.on('BeforeSetContent', function (o) {
        var matches = null, content = o.content;
        var prefix, suffix, attributes, attributeString, title;
        var attrs, attr;
        var imgEl;
        // Match various parts of the embed tag
        while ((matches = shortTagRegex.exec(content))) {
          prefix = matches[1];
          suffix = matches[4];
          if (prefix === '[' && suffix === ']') {
            continue;
          }
          attributes = {};
          // Remove quotation marks and trim.
          attributeString = matches[2].replace(/['"]/g, '').replace(/(^\s+|\s+$)/g, '');

          // Extract the attributes and values into a key-value array (or key-key if no value is set)
          attrs = attributeString.split(/\s+/);
          for (attribute in attrs) {
            attr = attrs[attribute].split('=');
            if (attr.length == 1) {
              attributes[attr[0]] = attr[0];
            } else {
              attributes[attr[0]] = attr[1];
            }
          }

          title = matches[3];

          imgEl = jQuery('<img/>').attr({
            //'src': 'code-blocks/images/codeblock-150.png' xxx this has to be with the type
            'src': url + '/img/gallery-' + (attributes.type) + '.png'
            , 'title': title
            , 'class': 'ss-inpagegallery'
            , 'width': 150
            , 'height': 50
          });

          jQuery.each(attributes, function (key, value) {
            imgEl.attr('data-' + key, value);
          });

          // visually replace the shortcode with an image
          content = content.replace(matches[0], prefix + (jQuery('<div/>').append(imgEl).html()) + suffix);
        }
        o.content = content;
      });
    }
  });
  tinymce.PluginManager.add("ss_insert_inpagegallery", tinymce.plugins.InsertInPageGallery);
})();