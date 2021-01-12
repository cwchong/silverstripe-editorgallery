(function () {

  var availableLangs = ['en'];
  if (jQuery.inArray(tinymce.settings.language, availableLangs) != -1) {
    tinymce.PluginManager.requireLangPack("ss_insert_inpagetimeline");
  }

  var each = tinymce.each;
  tinymce.create('tinymce.plugins.InsertInPageTimeline', {
    getInfo: function () {
      return {
        longname: 'Button to insert Timelines',
        author: 'Chong Chee Wai',
        authorurl: 'http://www.movingmouse.com/',
        infourl: 'https://github.com/cwchong/silverstripe-editorgallery',
        version: "1.0"
      };
    },
    init: function (ed, url) {
      // single enter exits the div and creates a paragraph
      ed.on('keydown', function (e) {
        var dom = ed.dom;
        // Capture Enter without shift
        if ((e.keyCode == 13)) {
          var parents = dom.getParents(ed.selection.getNode());
          for (var i = parents.length - 1; i >= 0; i--) {
            currentNode = parents[i];
            // Insert empty paragraph at the end of the outermost blockquote tag
            if (currentNode.className == 'ss-inpagetimelinewrap') {
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
            if (elem.classList.contains("ss-inpagetimelinewrap")) {
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
        var output = '<div class="ss-inpagetimelinewrap"><img class="ss-inpagetimeline" data-id="' + id + 
          '" data-type="' + type + '" alt="Timeline placeholder" title="Timeline: ' + 
          text + '" src="'+ url +'/img/timeline-' + type + '.png?"></div>';
        ed.execCommand('mceInsertContent', false, output);
        ed.execCommand('mceRepaint');
      }

      // define the ui btn with list of galleries to insert:
      ed.addButton('ss_insert_inpagetimeline', {
        title: 'Insert Timeline',
        image: url + '/../../images/timeline-20.png',
        type: 'menubutton',
        menu: (function () { 
          var options = [];
          jQuery.ajax({
            async: false,
            url: ed.documentBaseURI.toAbsolute('inpagetimeline/json'),
              data: { pageid: jQuery('#Form_EditForm_ID').val() }, 
              success: function(apiResponseJson) {
                if(! Object.keys(apiResponseJson).length)
                  return alert("Sorry, no active 'Timeline' could be found!");
                jQuery.each(apiResponseJson, function(id, name) {
                  options.push({
                    text: name,
                    menu: [
                      {
                        text: 'Horizontal',
                        onclick: function () {
                          handleBtnClick(id, 'horizontal', name);
                        }
                      },
                      {
                        text: 'Accordion',
                        onclick: function () {
                          handleBtnClick(id, 'accordion', name);
                        }
                      },
                      {
                        text: 'Vertical',
                        onclick: function () {
                          handleBtnClick(id, 'vertical', name);
                        }
                      }
                    ],
                  });
                });
              },
              error: function( xhr, status ) {
                return alert("Sorry, no active 'Timeline' could be found!");
              }
            });
          return options;
        })()
      });

      // converts <img class="ss-inpagetimeline" /> into shortcode [inpagetimeline] on save
      ed.on('SaveContent', function (o) {
        var content = jQuery(o.content);
        content.find('.ss-inpagetimeline').each(function () {
          var el = jQuery(this);
          var shortCode = '[inpagetimeline id="' + el.data('id') + '" type="' + el.data('type') + '"]' + el.attr('title') + '[/inpagetimeline]';
          el.replaceWith(shortCode);
        });
        o.content = jQuery('<div />').append(content).html(); // Little hack to get outerHTML string
      });

      var shortTagRegex = /(.?)\[inpagetimeline(.*?)\](.+?)\[\/\s*inpagetimeline\s*\](.?)/gi;

      // restores <img class="ss-inpagetimeline" /> from shortcode [inpagetimeline] on load
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
            'src': url + '/img/timeline-' + (attributes.type) + '.png'
            , 'title': title
            , 'class': 'ss-inpagetimeline'
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
  tinymce.PluginManager.add("ss_insert_inpagetimeline", tinymce.plugins.InsertInPageTimeline);
})();