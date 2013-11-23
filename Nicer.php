<?php

	/**
	 * Inspects and prints out PHP values as HTML in a nicer way than print_r().
	 * @author Christian Sciberras <christian@sciberras.me>
	 * @copyright (c) 2013, Christian Sciberras
	 * @license https://raw.github.com/uuf6429/nice_r/master/LICENSE MIT License
	 * @link https://github.com/uuf6429/nice_r GitHub Repository
	 * @version 2.0
	 * @since 2.0
	 */
	class Nicer {
		protected $value;
		
		/**
		 * Allows modification of CSS class prefix.
		 * @var string
		 */
		public $css_class = 'nice_r';
		
		/**
		 * Allows modification of HTML id prefix.
		 * @var string
		 */
		public $html_id = 'nice_r_v';
		
		/**
		 * Allows modification of JS function used to toggle sections.
		 * @var string
		 */
		public $js_func = 'nice_r_toggle';
		
		/**
		 * Since PHP does not support private constants, we'll have to settle for private static fields.
		 * @var string
		 */
		protected static $BEEN_THERE = '__NICE_R_INFINITE_RECURSION_PROTECT__';
		
		/**
		 * Constructs new renderer instance.
		 * @param mixed $value The value to inspect and render.
		 */
		public function __construct($value){
			$this->value = $value;
		}
		
		/**
		 * Generates the inspector HTML and returns it as a string.
		 * @return string Generated HTML.
		 */
		public function generate(){
			ob_start();
			$this->render();
			return ob_get_clean();
		}
		
		/**
		 * Renders the inspector HTML directly to the browser.
		 */
		public function render(){
			$this->_render_value($this->value, $this->css_class);
		}
		
		/**
		 * Render a single particular value.
		 * @param mixed $var The value to render
		 * @param string $class Parent CSS class.
		 * @param string $id Item HTML id.
		 */
		protected function _render_value($var, $class = '', $id = ''){
			$BEENTHERE = self::$BEEN_THERE;
			$class .= ' '.$this->css_class.'_t_'.gettype($var);
			
			?><div id="<?php echo $id; ?>" class="<?php echo $class; ?>"><?php
			
				switch(true){
					
					// handle arrays
					case is_array($var):
						if(isset($var[$BEENTHERE])){
							?><span class="<?php echo $this->css_class; ?>_ir">Infinite Recursion Detected!</span><?php
						}else{
							$var[$BEENTHERE] = true;
							$has_subitems = false;
							foreach($var as $k=>$v){
								if($k!==$BEENTHERE){
									$this->_render_keyvalue($k, $v);
									$has_subitems = true;
								}
							}
							if(!$has_subitems){
								?><span class="<?php echo $this->css_class; ?>_ni">Empty Array</span><?php
							}
							unset($var[$BEENTHERE]);
						}
						break;
					
					// handle objects
					case is_object($var):
						if(isset($var->$BEENTHERE)){
							?><span class="<?php echo $this->css_class; ?>_ir">Infinite Recursion Detected!</span><?php
						}else{
							$var->$BEENTHERE = true;
							$has_subitems = false;
							foreach((array)$var as $k=>$v){
								if($k!==$BEENTHERE){
									$this->_render_keyvalue($k, $v);
									$has_subitems = true;
								}
							}
							if(!$has_subitems){
								?><span class="<?php echo $this->css_class; ?>_ni">No Properties</span><?php
							}
							unset($var->$BEENTHERE);
						}
						break;
					
					// handle simple types
					default:
						$this->_render_keyvalue('', $var);
						break;
				}
				
			?></div><?php
			
		}

		/**
		 * Converts a string to HTML, encoding any special characters.
		 * @param string $text The original string.
		 * @return string The string as HTML.
		 */
		protected function _esc_html($text){
			return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
		}
		
		/**
		 * Render a key-value pair.
		 * @staticvar int $id Specifies element id.
		 * @param string $key Key name.
		 * @param mixed $val Key value.
		 */
		function _render_keyvalue($key, $val){
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
			
			$cls = $this->css_class;
			$xcls = !$is_hash ? $cls.'_ad' : '';
			?><a href="javascript:;" onclick="<?php echo $this->js_func; ?>('<?php echo $this->html_id; ?>','<?php echo $id; ?>');">
				<span class="<?php echo "{$cls}_a $xcls"; ?>" id="<?php echo "{$this->html_id}_a$id"; ?>">&#9658;</span>
				<span class="<?php echo "{$cls}_k"; ?>"><?php echo $this->_esc_html($key); ?></span>
				<span class="<?php echo "{$cls}_d"; ?>">(<?php echo '<span>'.ucwords($t).'</span>'.$d; ?>)</span>
				<span class="<?php echo "{$cls}_p {$cls}_t_$t"; ?>"><?php echo $this->_esc_html($p); ?></span>
			</a><?php
			
			if($is_hash){
				$this->_render_value($val, $cls.'_v', $this->html_id.'_v'.$id);
			}
			
		}
	}
