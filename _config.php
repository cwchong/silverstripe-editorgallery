<?php

/**
 * Fetches the name of the current module folder name.
 *
 * @return string
**/
define('INPAGEGALLERY_DIR', basename(dirname(__FILE__)));
/**
 * Prefix for db field names to prevent clash
 *
 * @return string
**/
define('INPAGEGALLERY_PREFIX', 'IPG');

/**
 * overloaded module for timeline and accordion here as well
 *
 */


/***
 *
similar to codeblock; but we are not using extra d/o, but the page's own fields
however, for ajax to get the correct html to render, we can either do at runtime (modifying the Content)
or do as per CodeBlock via another controller

for a test, we can attach gallery to page; photo carousel; modify the codeblock html to pull 
json from our own module source; as a manner of allowing flexibility, instead of passing no param, 
we will pass the page id, and the js should show a list of carousel available from this page

for other resource types (eg form) it should be another module/button/js)
for extendability, the attachment of carousel should be part of the module instead of in mysite
ie. dataobject decorators (refer to sifa blog > blogmember for data extension)

extension: create basic single tier has many field; prevent name clash by prefixing with our prefix


editor_plugin: pass page id [v] using jquery selecting page id from hidden form
inpagegallery.html: use page id passed, not hardcode [v]

>> instead of having gallery content in a text box, it should be formed via the nested children photo elements

>> "other" types of embeddable content?
    >> highlight div: image, image align, text, bg color (customisable but with default palette)
    >> grid, 3 col, default image > text; 2, 3, 4, w/o bg; 2, 3 with bg
    >> youtube embed (full width); preserve aspect?
        >> need to override the default style by tinymce, in editor.css
        >> need to add in the play-button styling overlay
    >> quote widget, (except normal widgets cannot position in b/w content), site-wide quotes, random
    >> map widget? site wide as well since we dun see every page needing such, so can be quite cumbersome to keep the tab

 *
 **/