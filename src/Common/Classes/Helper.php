<?php

namespace App\Classes;

use CDateTime;
use CMonth;
use CString;

class Helper {

	public static function formatDate($date_time) {
		$timestamp = CDateTime::toTimestamp($date_time);
		$date = array();
		$date[] = '<span>' . date('d', $timestamp) . '</span>';
		$date[] = '<em>';
		$date[] = CString::sub(CMonth::name(date('m', $timestamp)), 0, 3);
		$date[] = ' ‘';
		$date[] = date('y', $timestamp);
		$date[] = '</em>';
		return implode('', $date);
	}

	public static function formatDate_page($date_time) {
		$timestamp = CDateTime::toTimestamp($date_time);
		$date = array();
		$date[] = date('d ', $timestamp);
		$date[] = CString::sub(CMonth::name(date('m', $timestamp)), 0, 3);
		$date[] = date(' Y', $timestamp);
		return implode('', $date);
	}

	public static function closeTags($html) {
		$openedtags = array();
		$result = array();
		preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
		$openedtags = $result[1];

		preg_match_all('#</([a-z]+)>#iU', $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		if (count($closedtags) == $len_opened) {
			return $html;
		}

		$openedtags = array_reverse($openedtags);
		for ($i = 0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags)) {
				$html .= '</' . $openedtags[$i] . '>';
			} else {
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}

		return $html;
	}

}
