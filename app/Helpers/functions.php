<?php

if (!function_exists('pre')) 
{
	function pre($var, $die=false) 
	{
		print "<pre>" . print_r($var, 1) . "</pre>";
		if ($die) 
			die();
	}
}
function app_name()
{
	return config('app.name', 'Listings Manager');
}

/**
*
* @note make sure $str_date match the format of $from
*/
function format_str_date($str_date, $from="d/m/Y", $to="Y-m-d H:i:s")
{
	return $str_date ? Carbon::createFromFormat($str_date, $from)->format($to) : $str_date;
}

/**
* Use only if $date is a ISO format
*/
function format_date($date, $format="m/d/Y") 
{
	return $date ? Carbon::parse($date)->format($format) : $date;
}