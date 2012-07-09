<?php

class SocialWidget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'wpfbplugin', // Base ID
			'Wordpress for Facebook Plugin', // Name
			array( 'description' => __( 'Social reading for blog posts', 'text_domain' ), ) // Args
		);
		
	}

 	public function form( $instance ) {
		// outputs the options form on admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}

	public function widget( $args, $instance ) 
	{
		global $socialPlugin;
		
		try
		{
			$user = $socialPlugin->getUser();
			//<a href="{$socialPlugin->getLogoutUrl()}">Log Out</a>
			echo <<<EOT
			<div class="wpfbwidget">
				<div><img src="http://graph.facebook.com/{$user['id']}/picture" /></div><div class="wpfbwidgetcontent">Logged in as {$user['name']}<br />Sharing is Enabled. </div>
			</div>
			<style>
			.wpfbwidget {padding:5px; background-color:#eee; padding-top:13px; border-radius:5px; width:340px; padding-left:14px; overflow:hidden; }
			.wpfbwidget>div {display: table-cell; border-collapse: collapse; vertical-align: middle; }
			.wpfbwidget>div.wpfbwidgetcontent {padding-left:10px;}
			</style>		
EOT;

			
		}
		catch (exception $e) 
		{
			echo "<a href=\"{$socialPlugin->getLoginUrl()}\"><img src=\"http://coffeetalker.com/minimax/templates/coffeetalker/images/button-sign-in-with-facebook.png\"></a>"; 
		}
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
	
			echo "<div id='xcContainer'></div>";
		
		
	}

}
