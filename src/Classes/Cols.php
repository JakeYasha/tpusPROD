<?php

namespace App\Classes;

class Cols {

	public static function getCoord() {
		return [
			'default_val' => '0.000000',
			'flags' => 'not_null',
			'type' => 'double(9,6)'
		];
	}

	public static function getFiasCode() {
		return [
			'default_val' => '',
			'flags' => 'not_null',
			'type' => 'static_string(26)'
		];
	}

	public static function getGuid() {
		return [
			'default_val' => '',
			'flags' => 'not_null',
			'type' => 'static_string(36)'
		];
	}

	public static function getGuid_primaryKey() {
		return [
			'default_val' => '',
			'flags' => 'not_null primary_key',
			'type' => 'static_string(36)'
		];
	}

	public static function getInt($length) {
		return [
			'default_val' => '0',
			'flags' => 'not_null unsigned',
			'type' => 'int_' . $length
		];
	}

	public static function getInt1() {
		return [
			'default_val' => '0',
			'flags' => 'not_null unsigned',
			'type' => 'int_1'
		];
	}

	public static function getInt8() {
		return [
			'default_val' => '0',
			'flags' => 'not_null unsigned',
			'type' => 'int_8'
		];
	}

	public static function getList($list) {
		return [
			'default_val' => '',
			'flags' => 'not_null',
			'type' => "list('" . implode("','", array_keys($list)) . "')"
		];
	}

	public static function getPrice() {
		return [
			'default_val' => '0.00',
			'flags' => 'not_null unsigned',
			'type' => 'double(10,2)'
		];
	}

	public static function getRating() {
		return [
			'default_val' => '0.0',
			'flags' => 'not_null unsigned',
			'type' => 'double(2,1)'
		];
	}

	public static function getString25() {
		return [
			'default_val' => '',
			'flags' => 'not_null',
			'type' => 'string(25)'
		];
	}

	public static function getString($length) {
		return [
			'default_val' => '',
			'flags' => 'not_null',
			'type' => 'string(' . (int) $length . ')'
		];
	}

	public static function getText2() {
		return [
			'flags' => 'not_null',
			'type' => 'text_2'
		];
	}

	public static function getMd5() {
		return [
			'default_val' => '',
			'flags' => 'not_null',
			'type' => 'static_string(32)'
		];
	}

}
