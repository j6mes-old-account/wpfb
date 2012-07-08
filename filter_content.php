<?php


function filter_the_content($content)
{
	if(is_single())
	{
		return "<div id='addtotimeline'>x</div>".$content;
	}
	
	return $content;
}
