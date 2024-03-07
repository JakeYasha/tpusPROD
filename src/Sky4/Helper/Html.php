<?php

namespace Sky4\Helper;

class Html {

	/**
	 * @param StringHelper $text
	 * @return StringHelper
	 */
	public static function cdata($text) {
		return '<![CDATA[' . (string) $text . ']]>';
	}

	/**
	 * @param StringHelper $val
	 * @return StringHelper
	 */
	public static function charset($val = 'utf-8') {
		return self::tag('meta', ['charset' => (string) $val]);
	}

	/**
	 * @param StringHelper $name
	 * @return StringHelper
	 */
	public static function closeTag($name) {
		return '</' . (string) $name . '>';
	}

	/**
	 * @param StringHelper $text
	 * @param StringHelper $media
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function css($text, $media = null, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		$attrs['type'] = 'text/css';
		if ($media !== null) {
			$attrs['media'] = (string) $media;
		}
		return self::openTag('style', $attrs) . (string) $text . self::closeTag('style');
	}

	/**
	 * @param StringHelper $url
	 * @param StringHelper $media
	 * @return StringHelper
	 */
	public static function cssFile($url, $media = null) {
		return self::linkTag('stylesheet', 'text/css', $url, $media);
	}

	/**
	 * @param StringHelper $text
	 * @param StringHelper $media
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function cssWithCdata($text, $media = null, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		$attrs['type'] = 'text/css';
		if ($media !== null) {
			$attrs['media'] = (string) $media;
		}
		return self::openTag('style', $attrs) . self::cdata($text) . self::closeTag('style');
	}

	/**
	 * @param StringHelper $string
	 * @return StringHelper
	 */
	public static function encode($string) {
		return htmlspecialchars((string) $string);
	}

	/**
	 * @param array $array
	 * @return array
	 */
	public static function encodeArray($array) {
		$result = [];
		foreach ((array) $array as $key => $value) {
			if (is_string($key)) {
				$key = htmlspecialchars($key);
			}
			if (is_string($value)) {
				$value = htmlspecialchars($value);
			} elseif (is_array($value)) {
				$value = self::encodeArray($value);
			}
			$result[$key] = $value;
		}
		return $result;
	}

	/**
	 * @param StringHelper $name
	 * @param StringHelper $content
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function httpEquiv($name, $content, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		$attrs['http-equiv'] = (string) $name;
		$attrs['content'] = (string) $content;
		return self::tag('meta', $attrs);
	}

	/**
	 * @param StringHelper $url
	 * @param StringHelper $title
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function image($url, $title = null, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		$attrs['src'] = (string) $url;
		if ($title !== null) {
			$attrs['alt'] = self::encode($title);
		}
		return self::tag('img', $attrs);
	}

	/**
	 * @param StringHelper $text
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function js($text, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		$attrs['type'] = 'text/javascript';
		return self::openTag('script', $attrs) . (string) $text . self::closeTag('script');
	}

	/**
	 * @param StringHelper $url
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function jsFile($url, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		$attrs['type'] = 'text/javascript';
		$attrs['src'] = (string) $url;
        
		return self::tag('script', $attrs, '', true);
	}

	/**
	 * @param StringHelper $text
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function jsWithCdata($text, $attrs = []) {
		if (is_array($attrs)) {
			$attrs = [];
		}
		$attrs['type'] = 'text/javascript';
		return self::openTag('script', $attrs) . self::cdata($text) . self::closeTag('script');
	}

	/**
	 * @param StringHelper $string
	 * @param StringHelper $url
	 * @param array $attrs
	 * @param bool $is_absolute
	 * @return StringHelper
	 */
	public static function link($string, $url = '#', $attrs = [], $is_absolute = false) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		if ($url !== null) {
			$url = (string) $url;
			if ($is_absolute && ($url != '#') && !preg_match('#^(http|https)\://#', $url)) {
				$attrs['href'] = 'http://' . $url;
			} else {
				$attrs['href'] = $url;
			}
		}
		return self::tag('a', $attrs, $string);
	}

	/**
	 * @param StringHelper $relation
	 * @param StringHelper $type
	 * @param StringHelper $url
	 * @param StringHelper $media
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function linkTag($relation = null, $type = null, $url = null, $media = null, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		if ($relation !== null) {
			$attrs['rel'] = (string) $relation;
		}
		if ($type !== null) {
			$attrs['type'] = (string) $type;
		}
		if ($url !== null) {
			$attrs['href'] = (string) $url;
		}
		if ($media !== null) {
			$attrs['media'] = (string) $media;
		}
		return self::tag('link', $attrs);
	}

	/**
	 * @param StringHelper $string
	 * @param StringHelper $email
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function mailTo($string, $email = null, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
		if ($email !== null) {
			$attrs['href'] = 'mailto:' . (string) $email;
		}
		return self::tag('a', $attrs, $string);
	}

	/**
	 * @param StringHelper $name
	 * @param StringHelper $content
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function metatag($name, $content, $attrs = []) {
		if (!is_array($attrs)) {
			$attrs = [];
		}
        if ((string) $name) {
            $attrs['name'] = (string) $name;
        } 
        if ((string) $content) {
            $attrs['content'] = (string) $content;
        }
		return self::tag('meta', $attrs);
	}

	/**
	 * @param StringHelper $name
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function openTag($name, $attrs = []) {
		return '<' . (string) $name . self::renderAttrs($attrs) . '>';
	}

	/**
	 * @param StringHelper $string
	 * @return StringHelper
	 */
	public static function strip($string) {
		return strip_tags((string) $string);
	}

	/**
	 * @param StringHelper $name
	 * @param array $attrs
	 * @param StringHelper $content
	 * @param bool $close
	 * @return StringHelper
	 */
	public static function tag($name, $attrs = [], $content = null, $close = true) {
		$result = '<' . (string) $name . self::renderAttrs($attrs);
		if ($content === null) {
			return $result . ' />';
		}
		return $close ? $result . '>' . self::encode($content) . '</' . (string) $name . '>' : $result . ' />' . self::encode($content);
	}

	/**
	 * @param StringHelper $string
	 * @return StringHelper
	 */
	public static function titleTag($string) {
		return self::tag('title', [], $string);
	}

	// -------------------------------------------------------------------------

	/**
	 * @param array $attrs
	 * @return StringHelper
	 */
	public static function renderAttrs($attrs) {
		$result = '';
		foreach ($attrs as $name => $value) {
			$result .= ' ' . (string) $name . '="' . self::encode($value) . '"';
		}
		return $result;
	}

}
