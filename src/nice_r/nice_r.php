<?php

	/**
	 * Nicely prints human-readable information about a value.
	 * @param mixed $value The value to print.
	 * @param bool $return (Optional) Return printed HTML instead of writing it out (default is false).
	 */
	function nice_r($value, $return = false){
		if($return)ob_start();
		_nice_r_v($value, 'nice_r');
		if($return)return ob_get_clean();
	}

	function _nice_r_v($var, $class='', $id=''){
		static $BEENTHERE = '__NICE_R_INFINITE_RECURSION_PROTECT__';
		$class .= ' nice_r_t_'.gettype($var);
		?><div id="<?php echo $id; ?>" class="<?php echo $class; ?>"><?php
			if(is_array($var)){
				if(isset($var[$BEENTHERE])){
					?><span class="nice_r_ir">Infinite Recursion Detected!</span><?php
				}else{
					$var[$BEENTHERE] = true;
					foreach($var as $k=>$v)
						if($k!==$BEENTHERE)
							_nice_r_kv($k, $v);
					unset($var[$BEENTHERE]);
				}
			}elseif(is_object($var)){
				if(isset($var->$BEENTHERE)){
					?><span class="nice_r_ir">Infinite Recursion Detected!</span><?php
				}else{
					$var->$BEENTHERE = true;
					foreach((array)$var as $k=>$v)
						if($k!==$BEENTHERE)
							_nice_r_kv($k, $v);
					unset($var->$BEENTHERE);
				}
			}else{
				_nice_r_kv('', $var);
			}
		?></div><?php
	}

	function _nice_r_h($text){
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}

	function _nice_r_kv($key, $val){
		static $id = 0; $id++;
		$p = ''; $d = ''; $t = gettype($val);
		$is_hash = ($t=='array') || ($t=='object');
		switch($t){
			case 'boolean':
				$p = $val ? 'TRUE' : 'FALSE';
				break;
			case 'integer':
			case 'double':
				$p = (string)$val;
				break;
			case 'string':
				$d .= ', '.strlen($val).' characters';
				$p = $val;
				break;
			case 'resource':
				$d .= ', '.get_resource_type($val).' type';
				$p = (string)$val;
				break;
			case 'array':
				$d .= ', '.count($val).' elements';
				break;
			case 'object':
				$d .= ', '.get_class($val).', '.count(get_object_vars($val)).' properties';
				break;
		}
		?><a href="javascript:;" onclick="nice_r_toggle('<?php echo $id; ?>');">
			<span class="nice_r_a<?php if(!$is_hash)echo ' nice_r_ad'; ?>" id="nice_r_a<?php echo $id; ?>">&#9658;</span>
			<span class="nice_r_k"><?php echo _nice_r_h($key); ?></span>
			<span class="nice_r_d">(<?php echo '<span>'.ucwords($t).'</span>'.$d; ?>)</span>
			<span class="nice_r_p nice_r_t_<?php echo $t; ?>"><?php echo _nice_r_h($p); ?></span>
		</a><?php
		if($is_hash){
			_nice_r_v($val, 'nice_r_v', 'nice_r_v'.$id);
		}
	}