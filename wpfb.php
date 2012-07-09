<?php

/*
Plugin Name: Facebook/Wordpress Social Reader 
Plugin URI: http://lolblog.net/
Description: Facebook, the world's most popular social network, offers the Open Graph API a way of representing actions, objects and friendships online.  Readers who chose to do so will publish their reading activity on your blog to their Facebook timeline, a public log of activity, visible to all their friends.  If you haven't already done so, <a href="http://lolblog.net/creating-a-facebook-app"><strong>create an app on facebook</strong></a>.  With the App ID and App Secret they provide you, fill out this <a href="options-general.php?page=wpfb-setfbclientinfo"><strong>form</strong></a> and then you'll be ready to go.  This app provides a <strong>Widget</strong> that should be included on the individual post reading page. If you don't have a sidebar on this page, then intermediate users can include the dynamic sidebar at a desired location 'wpfbplugin' by editing your template.     
Version: 0.0.1
Author: James Thorne
Author URI: http://lolblog.net
License: GPLv3
*/



/*
 * 		Import all the social core files
 */
require_once("class_socialplugin.php");
require_once("class_socialwidget.php");  
require_once("function_registersidebars.php");
require_once("filter_content.php");
   

$socialPlugin = new SocialPlugin(); 




/*
 * 		Add all the wordpress hooks
 */ 
add_action('init',			array($socialPlugin,"init"));	//Init - starts session and output buffer
add_action("wp_head",		array($socialPlugin,"head"));	//Head - does everything we need in the page
add_action('widgets_init', 	'registerSidebars');
add_action('widgets_init', 	create_function('', 'return register_widget("SocialWidget");'));
add_filter('the_content', 	'filter_the_content' );
add_action('admin_init', 'wpfb_init' );
add_action( 'admin_menu', 'wpfbaddtomenu' );



/*
 * Admin Page
 */
function wpfbaddtomenu() 
{
	add_options_page( 'Facebook Options', 'Facebook Social Reader', 'manage_options', 'wpfb-setfbclientinfo', 'wpfb_options_page' );
}

function wpfb_options_page() 
{
	if ( !current_user_can( 'manage_options' ) )  
	{
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Facebook Social Reader</h2>
		<p>You must first visit Facebook and create an application. Please follow the set-up guide <a href="http://lolblog.net/creating-a-facebook-app">here</a> to find out how to this.  Facebook will give you a Client or Application ID and a Secret that you can enter below.</p>

		<form method="post" action="options.php">
			<?php settings_fields('wpfb_plugin_options'); ?>
			<?php $options = get_option('wpfb_options'); ?>
			<h3>Facebook App Information:</h3>
			<table class="form-table">
				<!-- Textbox Control -->
				<tr>
					<th scope="row">App ID / Client ID</th>
					<td>
						<input type="text" size="57" name="wpfb_options[client]" value="<?php echo $options['client']; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">App Secret</th>
					<td>
						<input type="text" size="57" name="wpfb_options[secret]" value="<?php echo $options['secret']; ?>" />
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
			
			
		</form>
	</div>
		
				
	<?php
	
}

function wpfb_validate_options($input) 
{
	 // strip html from textboxes
	$input['client'] =  intval($input['client']); // Sanitize textbox input (strip html tags, and escape characters)
	$input['secret'] =  wp_filter_nohtml_kses($input['secret']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

function wpfb_init()
{
	register_setting( 'wpfb_plugin_options', 'wpfb_options', 'wpfb_validate_options' );
}












