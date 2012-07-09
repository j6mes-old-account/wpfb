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
			echo <<<EOT
			<div id='wpfbwidget' class="wpfbwidget">
				<div><img src="http://graph.facebook.com/{$user['id']}/picture" /></div><div class="wpfbwidgetcontent">Logged in as {$user['name']}<br />Sharing is <a id='wpfbmenu' onclick='wpfbMenu();' href='javascript:void()'>Enabled</a>. </div>
				<div id='wpfbwidgetmenu'>
				<ul>
					<li><a href="http://developers.facebook.com/docs/opengraph/">What is sharing?</a></li>
					<li><a href="{$socialPlugin->getLogoutUrl()}" target="_blank">Log Out</a></li>
				</ul></div>
			</div>
			<style>
			.wpfbwidget {padding:5px; background-color:#eee; padding-top:13px; border-radius:5px; width:340px; padding-left:14px; overflow:hidden; }
			.wpfbwidget>div {display: table-cell; border-collapse: collapse; vertical-align: middle; }
			.wpfbwidget>div.wpfbwidgetcontent {padding-left:10px;}
			#wpfbwidgetmenu {display:none}
			</style>
			<script>
			function wpfbMenu(e)
			{
				wpfbwidget = document.getElementById('wpfbwidgetmenu');
				if(wpfbwidget.style.display == 'block')
				{
					wpfbwidget.style.display = 'none';
				}
				else
				{
					wpfbwidget.style.display = 'block';
				}
				
			}
			</script>		
EOT;

			
		}
		catch (exception $e) 
		{
			$plurl = plugins_url();
			echo "<a href=\"{$socialPlugin->getLoginUrl()}\"><img src=\"$plurl/wpfb/img_button.png\"></a>"; 
		}
			
		
		
	}

}
