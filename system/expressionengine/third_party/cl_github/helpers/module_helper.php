<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('cl_rstr_replace') )
{
	function cl_rstr_replace($search, $replace, $subject)
	{
	    $pos = strrpos($subject, $search);

	    if($pos !== false)
	    {
	        $subject = substr_replace($subject, $replace, $pos, strlen($search));
	    }

	    return $subject;
	}
}

if ( ! function_exists('cl_rstrpos') )
{
	function cl_rstrpos($haystack, $needle) 
	{
		return (strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE);
	}
}