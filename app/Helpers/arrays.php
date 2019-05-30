<?php

if (!function_exists('arr_pairs')) {
	function arr_pairs($arr, $index_key, $value_key) {
		if (empty($arr)) return $arr;
		$pairs = [];
		foreach($arr as $val) {
			if ($k = array_get((array)$val, $index_key))
				$pairs[$k] = array_get((array)$val, $value_key);
		}
		return $pairs;
	}
}

if (!function_exists('arr_lget')) {
	function arr_lget($arr, $key) {
		if (empty($arr)) return [];
		foreach($arr as $val) 
			if ($val && array_has((array)$val, $key))
				$values[] = array_get((array)$val, $key); 
		return isset($values) ? $values : [];
	}
}

if (!function_exists('arr_lfind')) {
	function arr_lfind($arr, $key, $value, $all=false) {
		$matches = [];
		foreach($arr as $val) {
			if ($value == array_get((array)$val, $key)) {
				if (!$all) return $val;
				$matches[] = $val;
			}
		}
		return $matches;
	}
}

if (!function_exists('arr_lkey')) {
	function arr_lkey($arr, $key, $value) {
		foreach($arr as $k => $v) 
			if ($value == array_get((array)$v, $key))
				return $k;
		return false;
	}
}

if (!function_exists('arr_usort')) {
	function arr_usort(&$arr, $field, $desc=false) {

		usort($arr, function($a, $b) use ($field, $desc) {
			return array_get((array)$a, $field) > array_get((array)$b, $field) ? 
					($desc ? -1 : 1) : ($desc ? 1 : -1);
						 
		});
	}
}