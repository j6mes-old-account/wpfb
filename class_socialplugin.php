<?php

/**
 * 
 */
class SocialPlugin 
{	
	function __construct() 
	{
		$this->fb =array(	"client" => "384766668212170",
					  		"secret" => "464c58240fae3d9439238af6d8bdea25"	
		);
		
		
					
	}
	
	/**
	 * Called from wp_init hook, just start sessions
	 *
	 * @return void
	 * @author james@thorne.bz 
	 */ 
	function init()
	{
		ob_start();
		session_start();
	}
	
	/**
	 * Called from wp_head hook
	 *
	 * @return void
	 * @author james@thorne.bz 
	 */ 
	function head()
	{		
		/*
		 * Check if page is a single, if yes, then do our stuff
		 */
		if(is_single())
		{
			
			/*
			 * If we've just come from facebook, check for an authenticated referal
			 */
			if(strpos($_SERVER['HTTP_REFERER'],"http://www.facebook.com")!==false)
			{
				if(isset($_GET['code']) and @strlen($_GET['code']))
				{
					/*
					 * Set Session  = the code
					 */
					$_SESSION['code'] = $_GET['code'];	
				}
				else
				{
					/*
					 * Go to a canvas page for log in
					 */	
					header("Location:http://xc.io");	 
				}
				
				die;
			}
			
		
			$this->echoHeaders();
			
			/*
			 * If the hit isn't from facebook, publish this to wall
			 */
			if(strpos($_SERVER['HTTP_USER_AGENT'],"facebook")===false)
			{
				$this->postFb();
			}
		}
		
	}
	
	
	/**
	 * Posts the news read article to a facebook wall
	 *
	 * @return void
	 * @author james@thorne.bz 
	 */ 
	function postFb()
	{
		if(is_single())
		{
			require_once("facebook.php");
			$facebook = new Facebook(array(
	 			'appId'  => $this->fb['client'],
	  			'secret' => $this->fb['secret']
			));
			
			$facebook->setAccessToken($_SESSION['code']);
			
			if(strpos($_SERVER['HTTP_USER_AGENT'],"facebook")===false)
			{
				try
				{
					$facebook->api("/me/news.reads?article={$this->pageUrl()}",'post',array("access_token"=>$facebook->getAccessToken()));
				}
				catch (FacebookApiException $e)
				{
					/*
					 * XXX: TODO: Gracefully handle? or Ignore, its up to you
					 */
				}
			}
			
		}

		
	}
	
	
	/**
	 * Echo The Facebook meta data in the page header
	 *
	 * @return void
	 * @author james@thorne.bz 
	 */
	function echoHeaders()
	{
		/*
		 * Collect parameters for post meta data
		 */
		$post = @get_post();
		$title = get_the_title();
		$pr = get_permalink();

	
		/*
		 * Truncate post content to create an exceprt if we don't have one
		 */
		if(!strlen($post->post_excerpt))
		{
			$content = substr(trim($post->post_content), 0,100);
		
			if(strlen($content >= 100))
			{
				$content .= "...";
			}
		}
		else
		{
			$content = $post->post_excerpt; 	
		} 
		
		
		/*
		 * Set an image for it.
		 */
		$img = "http://blog.eukhost.com/wp-content/uploads/2012/06/wordpress_logo.png";		//default
		
		/*
		 * And override it if we have a featured image
		 */
		if(has_post_thumbnail())
		{
			$img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail');
			$img = $img[0];
		}

		
		
		
		/*
		 * Cosntruct head
		 */
		$str =<<<EOT
			</head>
 		 	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">   
 		   	<meta property="fb:app_id"      content="{$this->fb['client']}" /> 
	  		<meta property="og:type"        content="article" /> 
	  		<meta property="og:url"         content="{$pr}" /> 
	  		<meta property="og:title"       content="{$title}" /> 
	  		<meta property="og:image"       content="{$img}" /> 
	  		<meta property="og:description" content="{$content}"/>		
	  		
	  		
	  		<script type="text/javascript">
	  		//<![CDATA[
	  		/*
	  		 *	Facebook Sharing Plugin for Wordpress by James Thorne
	  		 *	Released under GPLv3 License
	  		 */
			window.onload = wpfbPlugin;	
			function wpfbPlugin()
			{
				fbdiv = document.getElementById("fb-root");
				if(!fbdiv)
				{
					fbdiv = document.createElement("fb-root");
					fbdiv.setAttribute("id","fb-root")
					document.body.appendChild(fbdiv);					
				}
				
			
				if(typeof(FB) === 'undefined')
				{
					window.fbAsyncInit = function() 
					{
						FB.init({
							appId      : '{$this->fb['client']}', // App ID
					  		status     : true, // check login status
						  	cookie     : true, // enable cookies to allow the server to access the session
						  	xfbml      : true  // parse XFBML
						});
					};
							
					(function() {
						var e = document.createElement('script'); e.async = true;
						e.src = document.location.protocol +
					  	'//connect.facebook.net/en_US/all.js';
						document.getElementById('fb-root').appendChild(e);
					}());					
				}
				
			}
			
			//]]>
			
		</script>
EOT;
		echo $str;
	}
	
	
	/**
	 * Returns the URL of the current page
	 *
	 * @return string the urk of the current page
	 * @author Chacha102@stackoverflow.com 
	 */
	function pageUrl()
	{
		$url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		
		if ($_SERVER["SERVER_PORT"] == "80")
		{
			$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		} 
		else 
		{
		     $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		return $url;
	}
}
