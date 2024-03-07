<?php

namespace App\Classes;

class Search {

	public static function clearQuery($query) {
        $rez_qw = preg_replace('~[^ 0-9а-яА-Яa-zA-Z-./,]~u', '', $query);
        $rez_qw = preg_replace('~[-/,]~u', ' ', $rez_qw);
        // оставляем буквы-цифры, тире точки запятые. Потом тире, запятые - заменяем на пробелы
		return $rez_qw;
		//return preg_replace('~[^ 0-9а-яА-Яa-zA-Z-]~u', '', $query);
	}
    
    public static function clearYo($query) {
		return preg_replace('~[ёЁ]~u', 'е', $query);
	}

	// Обработка поискового запроса
	public static function prepareSearchQuery($search_query) {
		$pattern = '~[/=-]+~';
		$replacement = ' ';
		$search_query = preg_replace($pattern, $replacement, $search_query);
		$search_query = self::clearQuery($search_query);
        $search_query = self::clearYo($search_query);

		return trim($search_query);
	}

}
