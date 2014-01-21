<?php
namespace Gekosale;

class Arr
{

	public static function merge (Array $array1, Array $array2, $recursive = TRUE)
	{
		if (TRUE === $recursive){
			return array_merge_recursive($array1, $array2);
		}
		return array_merge($array1, $array2);
	}

	public static function debug ($array, $terminate = TRUE)
	{
		echo "<pre>";
		print_r($array);
		echo "</pre>";
		if (TRUE === $terminate)
			die();
	}
}