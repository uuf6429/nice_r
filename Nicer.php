<?php

/**
 * Inspects and prints out PHP values as HTML in a nicer way than print_r().
 * @author Christian Sciberras <christian@sciberras.me>
 * @copyright (c) 2015, Christian Sciberras
 * @license https://raw.github.com/uuf6429/nice_r/master/LICENSE MIT License
 * @link https://github.com/uuf6429/nice_r GitHub Repository
 * @version 3.0
 * @since 2.0
 */
class Nicer
{
    #region Properties & Constants
	
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
     * Whether to inspect and output methods for objects or not.
     * @var boolean
     */
    public $inspect_methods = false;
	
    /**
     * Since PHP does not support private constants, we'll have to settle for private static fields.
     * @var string
     */
    protected static $BEEN_THERE = '__NICE_R_INFINITE_RECURSION_PROTECT__';
	
    /**
     * Is reflection supported in this PHP install?
     * @var bool
     */
    protected $_has_reflection = null;
	
    /**
     * Displayable method modifiers.
     * @var string[]
     */
    protected $_visible_mods = array('abstract', 'final', 'private', 'protected', 'public', 'static');
	
    #endregion
	
    #region String properties
	
    public $STR_EMPTY_ARRAY = 'Empty Array';
    public $STR_NO_PROPERTIES = 'No Properties';
    public $STR_NO_METHODS = 'No Methods';
    public $STR_INFINITE_RECURSION_WARNING = 'Infinite Recursion Detected!';
    public $STR_STR_DESC = '%d characters';
    public $STR_RES_DESC = '%s type';
    public $STR_ARR_DESC = '%d elements';
    public $STR_OBJ_DESC = '%d properties';
	
    #endregion
	
    #region Constructors & Entry Points
	
    /**
     * Constructs new renderer instance.
     * @param mixed $value The value to inspect and render.
     * @param boolean $inspectMethods Whether to inspect and output methods for objects or not.
     */
    public function __construct($value, $inspectMethods = false)
    {
        $this->value = $value;
        $this->inspect_methods = $inspectMethods;
		
        if (is_null($this->_has_reflection))
            $this->_has_reflection = class_exists('ReflectionClass');
    }
	
    #endregion
	
    #region Public methods
	
    /**
     * Generates the inspector HTML and returns it as a string.
     * @return string Generated HTML.
     */
    public function generate()
    {
        return $this->_generate_value($this->value, $this->css_class);
    }
	
    /**
     * Renders the inspector HTML directly to the browser.
     */
    public function render()
    {
        echo $this->generate();
    }
	
    #endregion
	
    #region Internal Methods

    /**
     * Converts a string to HTML, encoding any special characters.
     * @param string $text The original string.
     * @return string The string as HTML.
     */
    protected function _esc_html($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    protected function _generate_keyvalues($array, &$html)
    {
        $has_subitems = false;
		
        foreach ($array as $k => $v)
        {
            if ($k !== self::$BEEN_THERE)
            {
                $html .= $this->_generate_keyvalue($k, $v);
                $has_subitems = true;
            }
        }

        return $has_subitems;
    }
	
    protected function _inspect_array(&$html, &$var)
    {
        // render items
        if (!$this->_generate_keyvalues($var, $html))
        {
            $html .= '<span class="' . $this->css_class . '_ni">' . $this->STR_EMPTY_ARRAY . '</span>';
        }
    }
	
    protected function _inspect_object(&$html, &$var)
    {
        // render properties
        if (!$this->_generate_keyvalues((array) $var, $html))
        {
            $html .= '<span class="' . $this->css_class . '_ni">' . $this->STR_NO_PROPERTIES . '</span>';
        }

        // render methods (if enabled)
        if ($this->inspect_methods)
        {
            $has_subitems = false;
			
            foreach ((array) get_class_methods($var) as $method)
            {
                $html .= $this->_generate_callable($var, $method);
                $has_subitems = true;
            }
			
            if (!$has_subitems)
            {
                $html .= '<span class="' . $this->css_class . '_ni">' . $this->STR_NO_METHODS . '</span>';
            }
        }
    }
	
    /**
     * Render a single particular value.
     * @param mixed $var The value to render
     * @param string $class Parent CSS class.
     * @param string $id Item HTML id.
     */
    protected function _generate_value($var, $class = '', $id = '')
    {
        $BEENTHERE = self::$BEEN_THERE;
        $class .= ' ' . $this->css_class . '_t_' . gettype($var);
		
        $html = '<div id="' . $id . '" class="' . $class . '">';
		
        switch (true)
        {
            // handle arrays
            case is_array($var):
                if (isset($var[$BEENTHERE]))
                {
                    $html .= '<span class="' . $this->css_class . '_ir">' . $this->STR_INFINITE_RECURSION_WARNING . '</span>';
                }
                else
                {
                    $var[$BEENTHERE] = true;
                    $this->_inspect_array($html, $var);
                    unset($var[$BEENTHERE]);
                }
                break;

            // handle objects
            case is_object($var):
                if (isset($var->$BEENTHERE))
                {
                    $html .= '<span class="' . $this->css_class . '_ir">' . $this->STR_INFINITE_RECURSION_WARNING . '</span>';
                }
                else
                {
                    $var->$BEENTHERE = true;
                    $this->_inspect_object($html, $var);
                    unset($var->$BEENTHERE);
                }
                break;

            // handle simple types
            default:
                $html .= $this->_generate_keyvalue('', $var);
                break;
        }
		
        return $html . '</div>';
    }
	
    /**
     * Generates a new unique ID for tagging elements.
     * @staticvar int $id
     * @return integer An ID unique per request.
     */
    protected function _generate_dropid()
    {
        static $id = 0;
        return ++$id;
    }
	
    /**
     * Render a key-value pair.
     * @staticvar int $id Specifies element id.
     * @param string $key Key name.
     * @param mixed $val Key value.
     */
    protected function _generate_keyvalue($key, $val)
    {
        $id = $this->_generate_dropid();
        $p = ''; // preview
        $d = ''; // description
        $t = gettype($val); // get data type 
        $is_hash = ($t == 'array') || ($t == 'object');
		
        switch ($t)
        {
            case 'boolean':
                $p = $val ? 'TRUE' : 'FALSE';
                break;
			
            case 'integer':
            case 'double':
                $p = (string) $val;
                break;
			
            case 'string':
                $d .= ', ' . sprintf($this->STR_STR_DESC, strlen($val));
                $p = $val;
                break;
			
            case 'resource':
                $d .= ', ' . sprintf($this->STR_RES_DESC, get_resource_type($val));
                $p = (string) $val;
                break;
			
            case 'array':
                $d .= ', ' . sprintf($this->STR_ARR_DESC, count($val));
                break;
			
            case 'object':
                $d .= ', ' . get_class($val) . ', ' . sprintf($this->STR_OBJ_DESC, count(get_object_vars($val)));
                break;
        }
		
        $cls = $this->css_class;
        $xcls = !$is_hash ? (' ' . $cls . '_ad') : '';
        $html  = '<a class="' . $cls . '_c ' . $xcls . '" ' . ($is_hash ? 'href="javascript:;"' : '') . ' onclick="' . $this->js_func . '(\'' . $this->html_id . '\',\'' . $id . '\');">';
        $html .= '	<span class="' . $cls . '_a" id="' . $this->html_id . '_a' . $id . '">&#9658;</span>';
        $html .= '	<span class="' . $cls . '_k">' . $this->_esc_html($key) . '</span>';
        $html .= '	<span class="' . $cls . '_d">(<span>' . ucwords($t) . '</span>' . $d . ')</span>';
        $html .= '	<span class="' . $cls . '_p ' . $cls . '_t_' . $t . '">' . $this->_esc_html($p) . '</span>';
        $html .= '</a>';
		
        if ($is_hash)
        {
            $html .= $this->_generate_value($val, $cls . '_v', $this->html_id . '_v' . $id);
        }
		
        return $html;
    }
	
    protected function _format_phpdoc($doc)
    {
        $doc = $this->_esc_html($doc);
		
        // fix indentation
        $doc = preg_replace('/(\\n)\\s+?\\*([\\s\\/])/', '$1 *$2', $doc);
		
        // doc attribs
        $doc = preg_replace('/(\\s)(@\\w+)/', '$1<b>$2</b>', $doc);
		
        // simple formatting
        $doc = nl2br(str_replace(' ', '&nbsp;', $doc));
		
        // linkify...links
        $doc = preg_replace('/(((f|ht){1}tp:\\/\\/)[-a-zA-Z0-9@:%_\\+.~#?&\\/\\/=]+)/', '<a href="$1">$1</a>', $doc);
        $doc = preg_replace('/(www\\.[-a-zA-Z0-9@:%_\\+.~#?&\\/=]+)/', '<a href="http://$1">$1</a>', $doc);
        $doc = preg_replace('/([_\\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\\.)+[a-z]{2,3})/', '<a href="mailto:$1">$1</a>', $doc);
		
        return $doc;
    }
	
    protected function _generate_callable($context, $callback)
    {
        $id = $this->_generate_dropid();
        $ref = null;
        $name = 'Anonymous';
        $cls = $this->css_class;
        $mods = array();
		
        if ($this->_has_reflection)
        {
            if (is_null($context))
            {
                $ref = new ReflectionFunction($callback);
            }
            else
            {
                $ref = new ReflectionMethod($context, $callback);
				
                foreach (array(
                    'abstract'      => $ref->isAbstract(),
                    'constructor'   => $ref->isConstructor(),
                    'deprecated'    => $ref->isDeprecated(),
                    'destructor'    => $ref->isDestructor(),
                    'final'         => $ref->isFinal(),
                    'internal'      => $ref->isInternal(),
                    'private'       => $ref->isPrivate(),
                    'protected'     => $ref->isProtected(),
                    'public'        => $ref->isPublic(),
                    'static'        => $ref->isStatic(),
                    'magic'         => substr($ref->name, 0, 2) === '__',
                    'returnsRef'    => $ref->returnsReference(),
                    'inherited'     => get_class($context) !== $ref->getDeclaringClass()->name,
                    // TODO Figure out whether this method is overriding a parent or not
                ) as $name => $cond)
                    if ($cond)
                        $mods[] = $name;
            }
			
            $name = $ref->getName();
        }
        elseif (is_string($callback))
        {
            $name = $callback;
        }
		
        if (!is_null($ref))
        {
            $doc = $ref->getDocComment();
            $prms = array();
            foreach ($ref->getParameters() as $p) {
                $prms[] = '$' . $p->getName() . (
                    $p->isDefaultValueAvailable()
                    ? (
                            ' = <span class="' . $cls . '_mv">' . (
                                $p->isDefaultValueConstant()
                                    ? $p->getDefaultValueConstantName()
                                    : var_export($p->getDefaultValue(), true)
                                ) . '</span>'
                            )
                        : ''
                    );
            }
        }
        else
        {
            $doc = null;
            $prms = array('???');
        }
		
        $xcls = !$doc ? (' ' . $cls . '_ad') : '';
		
        $hmod = implode(' ', array_intersect($mods, $this->_visible_mods));
        foreach ($mods as $mod) $xcls .= ' nice_r_m_' . $mod;
        if ($hmod != '') $hmod = '<span class="nice_r_mo">' . $hmod . '</span> ';
		
        $html  = '<a class="' . $cls . '_c ' . $cls . '_m' . $xcls . '" ' . ($doc ? 'href="javascript:;"' : '') . ' onclick="' . $this->js_func . '(\'' . $this->html_id . '\',\'' . $id . '\');">';
        $html .= '	<span class="' . $cls . '_a" id="' . $this->html_id . '_a' . $id . '">&#9658;</span>';
        $html .= '	<span class="' . $cls . '_k">' . $hmod . $this->_esc_html($name) . '<span class="' . $cls . '_ma">(<span>' . implode(', ', $prms) . '</span>)</span></span>';
        $html .= '</a>';
		
        if ($doc)
        {
            $html .= '<div id="' . $this->html_id . '_v' . $id . '" class="nice_r_v ' . $this->css_class . '_t_comment">';
            $html .= $this->_format_phpdoc($doc);
            $html .= '</div>';
        }
		
        return $html;
    }
	
    #endregion
}
