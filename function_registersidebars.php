<?php

function registerSidebars()
{
		register_sidebar(array(
		  'name' => __( 'Social Sidebar' ),
		  'id' => 'wpfbplugin',
		  'description' => __( 'A sidebar that can containe the user for social plugin.' ),
		  'before_title' => '<div style="display:none;">',
		  'after_title' => '</div>',
		  'before_widget' => '<div>',
		'after_widget' => "</div>\n"
		));
		
		
}