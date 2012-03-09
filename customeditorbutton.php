<?php
/*
Plugin Name: Content Editor Button
Plugin URI: http://themergency.com
Description: Adds a custom button to the content editor
Version: 0.1
Author: Brad Vincent
Author Email: bradvin@gmail.com
License:

  Copyright 2011 Brad Vincent (bradvin@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

class ContentEditorButton {

  protected $name = 'Content Editor Button';
  protected $slug = 'content-editor-button';

  /**
   * Constructor
   */
  function __construct() {
    //Hook up to the init action
    add_action( 'init', array( &$this, 'init_content_editor_button' ) );
  }
  
  /**
   * Runs when the plugin is initialized
   */
  function init_content_editor_button() {
    
    //add a button to the content editor, next to the media button
    //this button will show a popup that contains inline content
    add_action('media_buttons_context', array( &$this, 'add_inline_button'));
    
    //add another button
    //this button will show a popup that loads an external page in an iframe
    add_action('media_buttons_context', array( &$this, 'add_iframe_button'));    
    
    //add the content to the bottom of the page that will be shown in the inline modal
    add_action('admin_footer',  array( &$this, 'add_inline_popup_content'));
    
    //output the content of the external file into the iframe
    add_action('parse_request', array(&$this, 'output_iframe_page'));
  }
  
  
  //action to add a custom button to the content editor
  function add_inline_button($context) {
    
    //path to my icon
    $img = plugins_url( 'penguin.png' , __FILE__ );
    
    //the id of the container I want to popup when the button is clicked
    $container_id = 'popup_container';
    
    //our popup's title
    $title = 'An Inline Popup!';

    //append the icon
    $context .= "<a href='#TB_inline?width=400&inlineId={$container_id}' 
      class='thickbox' title='{$title}'><img src='{$img}' /></a>";
    
    return $context;
  }
  
  function add_iframe_button($context) {
    
    //path to my icon
    $img = plugins_url( 'bug.png' , __FILE__ );
    
    //the url to use. 
    //we use this URL so our plugin picks up the request 
    //we handle the request within the method: output_iframe_page
    $url = get_bloginfo( 'url' ) . '/?editor-popup-iframe=' . $this->slug;
    
    //our popup's title
    $title = 'An Iframe Popup!';

    //append the icon
    $context .= "<a href='{$url}&TB_iframe=1' class='thickbox' 
      onclick='return false;' title='{$title}'><img src='{$img}' />";
    
    return $context;
  }
  
  function add_inline_popup_content() {
?>
<script>
  
    jQuery(function($) {
      $('.insert_html').click(function() {
        window.send_to_editor('<h1>Inserted from my popup!</h1>');
      });
      
      $('.cancel_popup').click(function(e) {
        tb_remove();
        e.preventDefault();
      });
    });
  
</script>
<div id="popup_container" style="display:none;">
  <h2>Hello from my custom button!</h2>
  <input class="insert_html" type="button" value="insert some html" />
  <input class="cancel_popup" type="button" value="cancel" />
</div>
<?php
  }
  

  function output_iframe_page() {
    $handle = 'editor-popup-iframe';

    //check that we are handling the correct request
    if ( !empty( $_GET[$handle] ) && $_GET[$handle] == $this->slug ) {
      //include our external file
      //this external file now has access to the WordPress runtime!
      require_once 'popup.php';
      exit;
    }
    
  }
  
} // end class
new ContentEditorButton();

?>