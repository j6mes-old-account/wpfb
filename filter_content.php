<?php


function filter_the_content($content)
{
	if(is_single())
	{
		$button = <<<EOT
		<fb:login-button show-faces="true" width="200" max-rows="1" scope="publish_actions">
  		</fb:login-button>
EOT;

		return "<div id='addtotimeline'>{$button}</div>".$content;
	}
	
	return $content;
}
