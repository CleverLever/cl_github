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

if ( ! function_exists('cl_prefix_array_keys') )
{
	/**
	 * Prefix array keys.
	 *
	 * @param string $prefix 
	 * @param array $array 
	 * @return void
	 * @author Chris LeBlanc
	 */
	function cl_prefix_array_keys($prefix, array $array)
	{
		$data = array();
		
		if (cl_is_map($array))
		{
			foreach($array as $key => $value)
			{
				$data[$prefix.$key] = (!is_array($value)) ? $value : cl_prefix_array_keys($prefix, $value);
			}
		}
		else
		{
			foreach($array as $value)
			{
				$data[] = cl_prefix_array_keys($prefix, $value);
			}
		}

		return $data;
	}
}

if ( ! function_exists('cl_convert_maps_to_sequences') )
{
	
	/**
	 * cl_convert_hashmaps_to_sequences()
	 * 
	 * Converts array values which might have been defined as a map to a 
	 * sequence based on some assumptions that define it as a map. This is useful 
	 * for insuring that EE can create tag pairs for what is commonly 
	 * incorrectly referred to as an associative array.
	 *
	 * @param string 	$input 
	 * @param string 	$parent_is_sequence 
	 * @return $data
	 * @author Chris LeBlanc
	 */
	function cl_convert_hashmaps_to_sequences($input, $parent_is_sequence = FALSE)
	{
		$data = array();

		if (is_array($input)) 
		{ 
			foreach ($input as $key => $value)
			{
				if (cl_is_map($value) && !$parent_is_sequence) $data[$key] = array(cl_convert_hashmaps_to_sequences($value));
				else $data[$key] = cl_convert_hashmaps_to_sequences($value, true);
			}
		}
		else
		{
			$data = $input;
		}
		
		return $data;
	}
}

if ( ! function_exists('cl_is_map') )
{
	function cl_is_map($array) 
	{
		if (!is_array($array)) return FALSE;
		return (bool) !(array_values($array) == $array);
	}
}