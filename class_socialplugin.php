<?php

/**
 * 
 */
class SocialPlugin 
{
		
	function __construct() 
	{
		$this->fb =array(	"client" => "",
					  		"secret" => ""	
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
	 * get User... gets a facebook user
	 *
	 * @return array the facebook user
	 * @author james@thorne.bz 
	 */ 
	 function getUser()
	 {
	 	if(is_array($this->user))
		{
			return $this->user;
		}
		else
		{
			throw new Exception("User not authed",101); 
		}
	 }
	
	/**
	 * get log out url
	 *
	 * @return string the url
	 * @author james@thorne.bz 
	 */ 
	 function getLogoutUrl()
	 {
	 	if(strlen($this->lou))
		{
			return $this->lou;
		}
		else
		{
			throw new Exception("User not authed",101); 
		}
	 }
	 
	 /**
	 * get log in url
	 *
	 * @return string the url
	 * @author james@thorne.bz 
	 */ 
	 function getLoginUrl()
	 {
	 	if(strlen($this->liu))
		{
			return $this->liu;
		}
		
	 }
	 
	/**
	 * Posts the news read article to a facebook wall
	 *
	 * @return void
	 * @author james@thorne.bz 
	 */ 
	function postFb($try = false)
	{
		if(is_single())
		{
			require_once("class_facebook.php");
			$facebook = new Facebook(array(
	 			'appId'  => $this->fb['client'],
	  			'secret' => $this->fb['secret']
			));
			

			$this->liu = $facebook->getLoginUrl(array("scope"=>"publish_actions"));

			if($facebook->getUser()>0)
			{
				
				try
				{
					$this->lou = $facebook->getLogoutUrl();
					$this->user = $facebook->api("/me",'get',array("access_token"=>$facebook->getAccessToken()));
					$facebook->api("/me/news.reads?article={$this->pageUrl()}",'post',array("access_token"=>$facebook->getAccessToken()));
				
				}
				catch (FacebookApiException $e)
				{
					/*
					 * XXX: TODO: Gracefully handle? or Ignore, its up to you
					 */
					 
					 print_r($e);
				}
				
				
				
			}
			else
			{
				/*
				 * If we've just come from facebook, check for an authenticated referal
				 */
				if(strpos($_SERVER['HTTP_REFERER'],"http://www.facebook.com")!==false)
				{
					if(isset($_GET['code']) and @strlen($_GET['code']))
					{
						/*
						 * If we have a code then we need to force update the access token
						 */
						if(isset($_GET['code']))
						{
							$token_url = "https://graph.facebook.com/oauth/access_token?"
							. "client_id=" .$this->fb['client']. "&redirect_uri=" . urlencode($this->pageUrl())
							. "&client_secret=" . $this->fb['secret'] . "&code=" . $_GET['code'];
							
							$response = @file_get_contents($token_url);
							
							if(strlen($response))
							{
						
								$eq = explode("&",$response);
								$eq = explode("=",$eq[0]);
								$facebook->setAccessToken($eq[1]);
								unset($_SESSION['code']);
							}
						}
						if(!$try)
						{
							$this->postFb(true);
						}
					}
					else
					{
						/*
						 * Go to a canvas page for log in
						 */	
						header("Location:http://www.facebook.com/dialog/oauth/?
					    client_id={$this->fb['client']}
					    &redirect_uri=". urlencode($this->pageUrl()) ."
					    &scope=publish_actions");
						die;	 
					}
					
					
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
		$img = plugins_url()."/wpfb/img_wordpress.png"; //default
		
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
						  	xfbml      : false // parse XFBML
						});
						
						FB.getLoginStatus(function(response) {
							/*
							 * 	If we're connected then hide the login box
							 */
							if(response.status == "connected")
							{
								hideAll("wpfbBox");
							}
							else
							{
								wpfbBox = document.getElementById('wpfbBox');
								FB.XFBML.parse(wpfbBox);	
							}
						});
						
						FB.Event.subscribe("auth.login", function(r)
						{
							location.reload();
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
			
			function hideAll(className) 
			{
				els= getElementsByClassName(document, className),
       			cnt = els.length;
   				for (var i = 0; i < cnt; i++) 
   				{
     				var e = els[i];
					e.style.display = 'none';
				}
  
			}
			
			function getElementsByClassName(node,classname) 
			{
				//Dustin Diaz method
  				if (node.getElementsByClassName) 
  				{ 
    				return node.getElementsByClassName(classname);
				}
  				else 
  				{
    				return (function getElementsByClass(searchClass,node) 
    				{
        				if ( node == null )
						{
          					node = document;
						}
        				var classElements = [],
            			els = node.getElementsByTagName("*"),
            			elsLen = els.length,
            			pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)"), i, j;

				        for (i = 0, j = 0; i < elsLen; i++) 
				        {
          					if ( pattern.test(els[i].className) ) 
          					{
              					classElements[j] = els[i];
              					j++;
          					}
        				}
        				return classElements;
    				})(classname, node);
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
