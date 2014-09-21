<?php
$ulContent = '';

foreach ($elements as $element)
{
	$attributes = array();
	
	
	$ulContent .= html_tag($config['innerElement']['tag'], $attributes = $config['innerElement']['attributes'], $element);
}
	
echo html_tag($config['wrapperElement']['tag'], $config['wrapperElement']['attributes'], $ulContent);
		
		
