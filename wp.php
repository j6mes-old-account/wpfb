<?php

/*
Plugin Name: WordPress <3 Facebook
Plugin URI: http://lolblog.net/
Description: A plugin that shares user's reads on facebook
Version: 1.0
Author: James
Author URI: http://lolblog.net
License: GPLv3
*/



/*
 * 		Import all the social core files
 */
require_once("socialplugin.php");

//require_once("core/socialwidget.php");  

//require_once("filters/the_content.php");  

$socialPlugin = new SocialPlugin(); 

/*
 * 		Add all the wordpress hooks
 */ 
add_action('init',			array($socialPlugin,"init"));	//Init - starts session and output buffer
add_action("wp_head",		array($socialPlugin,"head"));	//Head - does everything we need in the page
//add_action('widgets_init', 	create_function('', 'return register_widget("SocialWidget");'));
add_filter('the_content', 	'filter_the_content' );








