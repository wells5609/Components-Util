<?php

namespace Phpf\Util;

class Html {
	
	/**
	 * Parses a string of html attributes into a (potentially) nested array.
	 * 
	 * @param string $attrStr HTML attribute string to parse
	 * @return array Single or multi-dimensional assoc. array.
	 */
	function parseAttrs( $attrStr ){
		
		if ( ! is_string($attrStr) )
			return $attrStr;
		
		// only 1 attribute => prefix with space
		if ( false === strpos($attrStr, '" ') )
			$attrStr = ' ' . $attrStr;
		
		// split at double quote followed by whitespace
		$attrArray = explode('" ', $attrStr);
		
		$return = array();
		foreach ( $attrArray as $attr ) {
			
			// split name and value
			$keyVals = explode('="', $attr);
			
			$key = trim(array_shift($keyVals));
			
			// $keyVals is now just values
			foreach ( $keyVals as $val ) {
				
				// remove quotes from value
				$val = str_replace('"', '', $val);
				
				// if spaces, multiple values (e.g. class)
				if ( false !== strpos($val, ' ') )
					$val = explode(' ', $val);
				
				$return[ $key ] = $val;
			}
		}
		
		return $return;	
	}
	
	/**
	 * Returns an attribute name/value pair as a string.
	 * 
	 * @param string $attr The attribute name (e.g. 'class')
	 * @param string|array $value The attr value. If array, delimited by spaces.
	 * @return string The attribute string with a leading space.
	 */
	public static function attrStr( $attr, $value ){
		if ( is_array( $value ) )
			$value = implode( ' ', $value );
		return ' ' . $attr . '="' . htmlspecialchars(Str::esc($value), ENT_COMPAT) . '"';
	}
		
	/**
	 * Returns multiple attribute name/value pairs as a single string.
	 * 
	 * @param array $attributes Assoc. array of name/value pairs.
	 * @param array $exclude Indexed array of attr names to exclude from the returned string.
	 * @return string The attributes string with a leading space.
	 */
	public static function attrsStr( array $attributes, array $exclude = array() ){
		$s = '';
		foreach( $attributes as $attr => $value ){
			if ( !empty($exclude) && in_array($attr, $exclude) )
				continue;
			$s .= self::attrStr($attr, $value);	
		}
		return $s;
	}
	
	/**
	 * Returns attribute(s) as a string.
	 * 
	 * @param string|array $args The attributes as a string or assoc. array.
	 * @return string The attribute string with a leading space.
	 */
	public static function parseAttrsToStr( $args ){
		return self::attrsStr( self::parseAttrs($args) );
	}
		
	/**
	 * Returns an opening HTML tag, (possibly) with attributes.
	 * 
	 * @param string $tag The HTML tag (default: 'div')
	 * @param array $attributes The as an assoc. array. (optional)
	 * @return string The opening HTML tag string.
	 */
	public static function tagOpen( $tag, $attributes = array() ){
		return '<' . $tag . ( empty($attributes) ? '' : self::attrsStr(self::parseAttrs($attributes)) ) . '>';
	}
		
	/**
	 * Returns a closing HTML tag.
	 * 
	 * @param string $tag The HTML tag (default: 'div')
	 * @return string The closing HTML tag string.
	 */
	public static function tagClose( $tag ){
		return '</' . $tag . '>';	
	}

	/**
	 * Returns an HTML tag with given content.
	 * 
	 * @param string $tag The HTML tag (default: 'div')
	 * @param array $attributes The as an assoc. array. (optional)
	 * @param string $content The content to place inside the tag.
	 * @return string The HTML tag wrapped around the given content.
	 */
	public static function tag( $tag, $attributes = array(), $content = '' ){
		return self::tagOpen($tag, $attributes) . $content . '</' . $tag . '>';
	}
	
	/**
	 * Returns a <script> tag
	 */
	public static function script( $url, array $attrs = array() ){
		$attrs = !empty($attrs) ? self::attrsStr($attrs) : '';
		return '<script src="' . $url . '"' . $attrs . '></script>';
	}
	
	/**
	 * Returns a <link> tag
	 */
	public static function link( $url, array $attrs = array() ){
		$default = array('rel' => 'stylesheet', 'type' => 'text/css');
		$attrs = array_merge($default, $attrs);
		return '<link href="' . $url . '"' . self::attrsStr($attrs) . '>';
	}
	
	/**
	 * Returns a <a> tag
	 */
	public static function a( $content, $href, array $attributes = array() ){
		return '<a href="' . $href . '"' . self::attrsStr($attributes) . '>' . $content . '</a>';
	}
	
	/**
	 * Returns a panel using Bootstrap 3
	 */
	public static function panel( $body, $heading = '', $panel_attributes = array(), $after_body = null ){
		
		$attrs = array_merge(array('class' => 'panel-default'), $panel_attributes);
		$attrs['class'] .= ' panel';
		
		$s = '<div' . self::attrsStr($attrs) . '>';
		
		if ( !empty($heading) )
			$s .= '<div class="panel-heading">' . $heading . '</div>';
		
		if ( !empty($body) )
			$s .= '<div class="panel-body">' . $body . '</div>';
		
		if ( !empty($after_body) )
			$s .= $after_body;
		
		$s .= '</div>';
		
		return $s;
	}
}
