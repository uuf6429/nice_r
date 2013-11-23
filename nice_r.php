<?php

	/**
	 * Nicely prints human-readable information about a value.
	 * Note: This funciton is provided for compatibility reasons with earlier version.
	 * You don't need this file if you plan on using the class directly.
	 * @param mixed $value The value to print.
	 * @param bool $return (Optional) Return printed HTML instead of writing it out (default is false).
	 * @return string If $return is true, the rendered HTML otherwise null.
	 */
	function nice_r($value, $return = false){
		$n = new Nicer($value);
		return $return ? $n->generate() : $n->render();
	}
	