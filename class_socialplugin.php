<?php

/**
 * 
 */
class SocialPlugin 
{	
	function __construct() 
	{
		$this->config = array(
			"modal" 	=> true,
			"fromfb" 	=> true,
			"fb"		=> array(	"client" => "xxx",
									"secret" => "xxx"
								)
		);
		
		
			
	}
	
	function init()
	{
		ob_start();
		session_start();
	}
	
	function head()
	{		
		/*
		 * Check if page is a single, if yes, then do our stuff
		 */
		if(is_single())
		{
			if(strpos($_SERVER['HTTP_REFERER'],"http://www.facebook.com")!==false)
			{
				$_SESSION['code'] = $_GET['code'];
				//header("Location:http://xc.io");
				die;
			}
			
		
			$this->echoHeaders();
		}
		
	}
	
	function echoHeaders()
	{
		$post = @get_post();
		$title = get_the_title();
		$pr = get_permalink();
		$content = get_the_content();
		
	
		
		if(!strlen($post->post_excerpt))
		{
			$content = substr(trim($post->post_content), 0,100);
		
			if(strlen($content >= 100))
			{
				$content .= "...";
			}
		} 
		
		$img = "http://blog.eukhost.com/wp-content/uploads/2012/06/wordpress_logo.png";
		if(has_post_thumbnail())
		{
			$img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail');
			$img = $img[0];
		}


		$this->code = sha1(sha1($this->secret).$this->secret.$this->site.$_SERVER['REQUEST_URI'].sha1($_SERVER['REQUEST_URI']));
		
		
		$str =<<<EOT
			</head>
 		 	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">   
 		   	<meta property="fb:app_id"      content="384766668212170" /> 
	  		<meta property="og:type"        content="article" /> 
	  		<meta property="og:url"         content="$pr" /> 
	  		<meta property="og:title"       content="$title" /> 
	  		<meta property="og:image"       content="$img" /> 
	  		<meta property="og:description" content="$content"/>		
EOT;
		echo $str;
	}
	
	
	function pageUrl()
	{
		$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
		    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
		else 
		{
		    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	function foot()
	{
		if(is_single())
		{
			require_once("facebook.php");
			$facebook = new Facebook(array(
	 			'appId'  => '384766668212170',
	  			'secret' => '464c58240fae3d9439238af6d8bdea25'
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
					
				}
			}
			
		}
		
		
		
		?>
		<script type="text/javascript">
			window.onload = wpfbPlugin;
			
			function wpfbPlugin()
			{
				fbdiv = document.getElementById("fb-root");
				
				if(fbdiv)
				{
					
				}
				else
				{
					fbdiv = document.createElement("fb-root");
					fbdiv.setAttribute("id","fb-root")
					document.body.appendChild(fbdiv);
					
					
				}
				
				
				if(typeof(FB) === 'undefined')
				{
					 window.fbAsyncInit = function() {
				
					 FB.init({
					      appId      : '384766668212170', // App ID
					  status     : true, // check login status
					  cookie     : true, // enable cookies to allow the server to access the session
					  xfbml      : true  // parse XFBML
					});
					
				
				postCook();
				
				
					// Additional initialization code here
							  };
							
							 (function() {
					var e = document.createElement('script'); e.async = true;
					e.src = document.location.protocol +
					  '//connect.facebook.net/en_US/all.js';
					document.getElementById('fb-root').appendChild(e);
					  }());
					  
					  
					
				}
				
			}
			
			
			  function postCook()
  {
  	
  	fbtoken = getCookie('fbaccess');
  	
  	<?php
  	 if(isset($_SESSION['code']))
  	 {
  	 	echo "fbtoken =\"{$_SESSION['code']}\" ";
	 }?>
	 
  	if(fbtoken != null && fbtoken!="")
  	{
  		alert(fbtoken);
  		doPost(fbtoken)
  	}
  	else
  	{
  		
  			
     	
     	
  	
  		
  	
  	/*
  	 FB.login(function(response) {
   if (response.authResponse) {
     console.log('Welcome!  Fetching your information.... ');
     FB.api('/me', function(response) {
     	var access_token =   FB.getAuthResponse()['accessToken'];
     	
     	setCookie("fbaccess",access_token,3);
     	
     console.log('Access Token = '+ access_token);
       console.log('Good to see you, ' + response.name + '.');
       doPost(access_token);
     },{scope:'post_actions'});
   } else {
     console.log('User cancelled login or did not fully authorize.');
   }
 });*/


    
  	}

  }
  
  function doPost(token)
  {
  	
  	      FB.api(
        '/me/news.reads',
        'post',
        { article: window.location.href, accessToken: token},
        function(response) {
           if (!response || response.error) {
              console.log('Error occured\n' + response.error.message);
              if(response.error.code!=3501)
              {              setCookie("fbaccess","",3); 
              console.log('clear cookie');
              }
              
           } else {
              alert('Cook was successful! Action ID: ' + response.id);
           }
        });
  }
  
  
  function setCookie(c_name,value,exdays)
{
var exdate=new Date();
exdate.setDate(exdate.getDate() + exdays);
var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
document.cookie=c_name + "=" + c_value;
}

function getCookie(c_name)
{
var i,x,y,ARRcookies=document.cookie.split(";");
for (i=0;i<ARRcookies.length;i++)
{
  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  x=x.replace(/^\s+|\s+$/g,"");
  if (x==c_name)
    {
    return unescape(y);
    }
  }
}


  
  			function xcGet(strURL,obj) 
			{
			    var xmlHttpReq = false;
			    var self = this;
			    if (window.XMLHttpRequest) 
			    {
			        self.xmlHttpReq = new XMLHttpRequest();
			    }
			    else if (window.ActiveXObject) 
			    {
			        self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
			    }
			    
			    self.xmlHttpReq.open('GET', strURL, true);
			    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			    self.xmlHttpReq.onreadystatechange = function() 
			    {
			        if (self.xmlHttpReq.readyState == 4) 
			        {
			        	
			            obj(self.xmlHttpReq.responseText);
			        }
			    }
			    self.xmlHttpReq.send();
			}

			
		</script>
		<?php
	}
}
