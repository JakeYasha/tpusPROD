<?php

namespace App\Classes;

use CFileCache;
use DateTime;
use SimpleXMLElement;

class YandexMetrika {

    private $token = null;
    private $counter = null;
    private $date_beginning = null;
    private $date_ending = null;

    public function __construct() {
        $this->token = YANDEX_METRIKA_TOKEN;
        $this->counter = YANDEX_METRIKA_COUNTER;
    }

    //setters
    public function setToken($token) {
        $this->token = (string) $token;
        return $this;
    }

    public function setCounter($counter) {
        $this->counter = (string) $counter;
        return $this;
    }

    public function setDateBeginning($datetime) {
        $this->date_beginning = self::getDate($datetime);
        return $this;
    }

    public function setDateEnding($datetime) {
        $this->date_ending = self::getDate($datetime);
        return $this;
    }

    public function getPopularPages($count = 25) {
        $_date_beginning = $this->date_beginning->format('Ymd');
        $_date_ending = $this->date_ending->format('Ymd');

        $xml_doc = simplexml_load_file($this->getHost() . '/stat/content/popular.xml?id=' . $this->getCounter() . '&oauth_token=' . $this->getToken() . '&date1=' . (string) $_date_beginning . '&date2=' . (string) $_date_ending . '&per_page=' . $count);
        $pages = [];
        if (is_object($xml_doc) && property_exists($xml_doc, 'data') && ($xml_doc->data instanceof SimpleXMLElement)) {
            if (is_object($xml_doc->data->row) && ($xml_doc->data->row instanceof SimpleXMLElement)) {
                foreach ($xml_doc->data->row as $link) {
                    $pages[] = [
                        'page_views' => (int) $link->page_views,
                        'url' => (string) $link->url
                    ];
                }
            }
        }

        $xml_doc = simplexml_load_file($this->getHost() . '/stat/content/titles.xml?id=' . $this->getCounter() . '&oauth_token=' . $this->getToken() . '&date1=' . (string) $_date_beginning . '&date2=' . (string) $_date_ending . '&per_page=' . ($count + 5));
        if (is_object($xml_doc) && property_exists($xml_doc, 'data') && ($xml_doc->data instanceof SimpleXMLElement)) {
            if (is_object($xml_doc->data->row) && ($xml_doc->data->row instanceof SimpleXMLElement)) {
                $i = -1;
                foreach ($xml_doc->data->row as $link) {
                    if ((string) $link->name === 'Информация не найдена') {
                        continue;
                    }
                    $i++;
                    $pages[$i]['name'] = (string) $link->name;
                }
            }
        }

        print_r($pages);
        exit();
    }

    public function getVisitors() {
        $_date_beginning = $this->date_beginning->format('Ymd');
        $_date_ending = $this->date_ending->format('Ymd');

        $counters = [];
        $xml_doc = simplexml_load_file($this->getHost() . '/counter/' . $this->getCounter() . '.xml?oauth_token=' . (string) $this->getToken());
        if (is_object($xml_doc) && property_exists($xml_doc, 'counter') && ($xml_doc->counter instanceof SimpleXMLElement)) {
            if (is_object($xml_doc->counter) && property_exists($xml_doc->counter, 'code_options') && ($xml_doc->counter instanceof SimpleXMLElement)) {
                $i = -1;
                foreach ($xml_doc->counter as $counter) {
                    $i++;
                    $counters[$i] = [
                        'id' => '',
                        'permission' => '',
                        'site' => '',
                        'status' => '',
                        'views' => 0,
                        'visitors' => 0,
                        'visits' => 0
                    ];
                    if (property_exists($counter, 'id')) {
                        $counters[$i]['id'] = (string) $counter->id;
                    }
                    if (property_exists($counter, 'permission')) {
                        $counters[$i]['permission'] = (string) $counter->permission;
                    }
                    if (property_exists($counter, 'site')) {
                        $counters[$i]['site'] = (string) $counter->site;
                    }
                    if (property_exists($counter, 'code_status')) {
                        $counters[$i]['status'] = (string) $counter->code_status;
                    }
                }
            }
        }

        foreach ($counters as $counter_id => $counter_data) {
            $xml_doc = simplexml_load_file($this->_urlTrafficSummaryByCounter($counter_data['id'], $_date_beginning, $_date_ending));
            if (is_object($xml_doc) && property_exists($xml_doc, 'totals') && ($xml_doc->totals instanceof SimpleXMLElement)) {
                if (property_exists($xml_doc->totals, 'page_views')) {
                    $counters[$counter_id]['views'] = (int) $xml_doc->totals->page_views;
                }
                if (property_exists($xml_doc->totals, 'visitors')) {
                    $counters[$counter_id]['visitors'] = (int) $xml_doc->totals->visitors;
                }
                if (property_exists($xml_doc->totals, 'visits')) {
                    $counters[$counter_id]['visits'] = (int) $xml_doc->totals->visits;
                }
            }
        }

        $result = [];
        foreach ($counters as $counter) {
            $result = $counter;
        }

        return $result;
    }

    public function getCurrentDay() {
        $date = date('Ymd');
        return $this->_get($date, $date);
    }

    public function getCurrentMonth() {
        $date_beginning = date('Ym01', time() - 24 * 60 * 60);
        $date_ending = date('Ymd', time() - 24 * 60 * 60);
        return $this->_get($date_beginning, $date_ending);
    }

    public function getPeriod($date_beginning = '', $date_ending = '') {
        self::checkDate($date_beginning);
        self::checkDate($date_ending);
        return $this->_get($date_beginning, $date_ending);
    }

    /**
      @deprecated
      delete
     */
    public function getTotalVisitors($date_beginning = '', $date_ending = '') {
        self::checkDate($date_beginning);
        self::checkDate($date_ending);
        return $this->_getTotalVisitors($date_beginning, $date_ending);
    }
    
    public function getTotalVisitorsWithOAuth() {
        $url = 'https://api-metrika.yandex.ru/stat/v1/data';
        $headers = [
            'Content-Type: application/x-yametrika+json',
            'Authorization: OAuth ' . $this->token
        ];

        $params = array(
            'ids' => $this->counter,
            'metrics' => 'ym:s:visits',
            'date1' => $this->date_beginning->format('Y-m-d'),
            'date2' => $this->date_ending->format('Y-m-d')
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $xml_doc = json_decode($data, true);

        if ($xml_doc) {
            return $xml_doc['data'][0]['metrics'][0] ?? 0;
        }

        return false;
    }

    public function getPrevDay() {
        $date = date('Ymd', time() - 24 * 60 * 60);
        return $this->_get($date, $date);
    }

    public static function checkDate($date) {
        if (!preg_match('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', (string) $date)) {
            throw new EngineException();
        }
        return true;
    }

    /**
     * @return DateTime
     */
    public static function getDate($date) {
        $date = strtotime($date);
        $datetime = date('Ymd', $date);

        if (preg_match('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', (string) $datetime, $matches)) {
            $day = (int) $matches[3];
            $month = (int) $matches[2];
            $year = (int) $matches[1];
            $date = new DateTime();
            $date->setDate($year, $month, $day);
            return $date;
        }
    }

    public function getHost() {
        return 'https://api-metrika.yandex.ru';
    }

    /**
      @deprecated
      delete
     */
    private function _getTotalVisitors($date_beginning = '', $date_ending = '') {
        ini_set('max_execution_time', 360);

        $date_beginning = self::getDate($date_beginning);
        $date_ending = self::getDate($date_ending);
        $date_interval = $date_beginning->diff($date_ending);

        $_date_beginning = $date_beginning->format('Ymd');
        $_date_ending = $date_ending->format('Ymd');
        $_days = 0;
        $_title = '';
        if ((int) $date_interval->days) {
            $_days = (int) $date_interval->days;
            $_title = $date_beginning->format('d.m.Y') . ' - ' . $date_ending->format('d.m.Y');
        } else {
            $_title = $date_beginning->format('d.m.Y');
        }

        $url = $this->getHost() . "/stat/v1/data?ids=" . $this->counter
                . "&oauth_token=" . $this->token
                . "&metrics=ym:s:visits"
                . "&date1=" . $date_beginning->format('Y-m-d')
                . "&date2=" . $date_ending->format('Y-m-d');

        $counters = [];
        $xml_doc = json_decode(file_get_contents($url), true);

        if ($xml_doc) {
            return $xml_doc['data'][0]['metrics'][0] ?? 0;
        }

        return false;
    }
    
    private function _get($date_beginning = '', $date_ending = '') {
        ini_set('max_execution_time', 360);

        $date_beginning = self::getDate($date_beginning);
        $date_ending = self::getDate($date_ending);
        $date_interval = $date_beginning->diff($date_ending);

        $_date_beginning = $date_beginning->format('Ymd');
        $_date_ending = $date_ending->format('Ymd');
        $_days = 0;
        $_title = '';
        if ((int) $date_interval->days) {
            $_days = (int) $date_interval->days;
            $_title = $date_beginning->format('d.m.Y') . ' - ' . $date_ending->format('d.m.Y');
        } else {
            $_title = $date_beginning->format('d.m.Y');
        }

        $url = $this->getHost() . "/stat/v1/data?ids=" . $this->counter
                . "&oauth_token=" . $this->token
                . "&metrics=ym:s:visits"
                . "&dimensions=ym:s:date"
                . "&date1=" . $date_beginning->format('Y-m-d')
                . "&date2=" . $date_ending->format('Y-m-d')
                . "&sort=ym:s:date";

//		$counters = [];
//		$xml_doc = json_decode(file_get_contents($url));
//
//		
//		if($xml_doc) {
//			
//		}


        if (is_object($xml_doc) && property_exists($xml_doc, 'counter') && ($xml_doc->counter instanceof SimpleXMLElement)) {
            if (is_object($xml_doc->counter) && property_exists($xml_doc->counter, 'code_options') && ($xml_doc->counter instanceof SimpleXMLElement)) {
                $i = -1;
                foreach ($xml_doc->counter as $counter) {
                    $i++;
                    $counters[$i] = [
                        'id' => '',
                        'permission' => '',
                        'site' => '',
                        'status' => '',
                        'views' => 0,
                        'visitors' => 0,
                        'visits' => 0
                    ];
                    if (property_exists($counter, 'id')) {
                        $counters[$i]['id'] = (string) $counter->id;
                    }
                    if (property_exists($counter, 'permission')) {
                        $counters[$i]['permission'] = (string) $counter->permission;
                    }
                    if (property_exists($counter, 'site')) {
                        $counters[$i]['site'] = (string) $counter->site;
                    }
                    if (property_exists($counter, 'code_status')) {
                        $counters[$i]['status'] = (string) $counter->code_status;
                    }
                }
            }
        }

        foreach ($counters as $counter_id => $counter_data) {
            $xml_doc = simplexml_load_file($this->_urlTrafficSummaryByCounter($counter_data['id'], $_date_beginning, $_date_ending));
            if (is_object($xml_doc) && property_exists($xml_doc, 'totals') && ($xml_doc->totals instanceof SimpleXMLElement)) {
                if (property_exists($xml_doc->totals, 'page_views')) {
                    $counters[$counter_id]['views'] = (int) $xml_doc->totals->page_views;
                }
                if (property_exists($xml_doc->totals, 'visitors')) {
                    $counters[$counter_id]['visitors'] = (int) $xml_doc->totals->visitors;
                }
                if (property_exists($xml_doc->totals, 'visits')) {
                    $counters[$counter_id]['visits'] = (int) $xml_doc->totals->visits;
                }
            }
        }

        $_counters = [];
        foreach ($counters as $counter) {
            $_counters[$counter['site']] = $counter;
        }
        ksort($_counters);
        reset($_counters);
        $total_views = 0;
        $total_views_visits = 0.0;
        $total_visitors = 0;
        $total_visits = 0;
        foreach ($_counters as $_counter) {
            $total_views += $_counter['views'];
            $total_visitors += $_counter['visitors'];
            $total_visits += $_counter['visits'];
        }

        return $_counters;
    }

    /**
      @deprecated
      delete
     */
    public function getSummaryByDays() {
        $fc = new CFileCache();
        $data = '';
        $cache = $fc->get('metrika-summary-days');
        if ($cache !== false) {
            $data = $cache;
        } else {
            //$url = "http://api-metrika.yandex.ru/stat/traffic/summary.json?id=" . $this->counter . "&oauth_token=" . $this->token . "&date1=" . $this->date_beginning->format('Ymd') . "&date2=" . $this->date_ending->format('Ymd');
            $url = $this->getHost() . "/stat/v1/data?ids=" . $this->counter
                    . "&oauth_token=" . $this->token
                    . "&metrics=ym:s:visits,ym:s:pageviews,ym:s:users,ym:s:avgVisitDurationSeconds"
                    . "&dimensions=ym:s:date"
                    . "&date1=" . $this->date_beginning->format('Y-m-d')
                    . "&date2=" . $this->date_ending->format('Y-m-d')
                    . "&sort=ym:s:date";

            $data = file_get_contents($url);
            $fc->setTimeout(60 * 60 * 2)
                    ->add('metrika-summary-days', $data);
        }

        return $data;
    }

    public function getSummaryByDaysWithOAuth() {
        $fc = new CFileCache();
        $data = '';
        $cache = $fc->get('metrika-summary-days');
        if ($cache !== false) {
            $data = $cache;
        } else {
            $url = 'https://api-metrika.yandex.ru/stat/v1/data';
            $headers = [
                'Content-Type: application/x-yametrika+json',
                'Authorization: OAuth ' . $this->token
            ];

            $params = array(
                'ids' => $this->counter,
                'metrics' => 'ym:s:visits,ym:s:pageviews,ym:s:users,ym:s:avgVisitDurationSeconds',
                'dimensions' => 'ym:s:date',
                'date1' => $this->date_beginning->format('Y-m-d'),
                'date2' => $this->date_ending->format('Y-m-d'),
                'sort' => 'ym:s:date'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $data = curl_exec($ch);
            curl_close($ch);
            $fc->setTimeout(60 * 60 * 2)
                    ->add('metrika-summary-days', $data);
        }

        return $data;
    }

    /**
      @deprecated
      delete
     */
    public function getSummaryByMonths() {
        $fc = new CFileCache();
        $data = '';
        $cache = $fc->get('metrika-summary-months');
        if ($cache !== false) {
            $data = $cache;
        } else {
            $url = $this->getHost() . "/stat/v1/data?ids=" . $this->counter
                    . "&oauth_token=" . $this->token
                    . "&metrics=ym:s:visits,ym:s:pageviews,ym:s:users,ym:s:avgVisitDurationSeconds"
                    . "&dimensions=ym:s:datePeriod<group>"
                    . "&date1=" . $this->date_beginning->format('Y-m-d')
                    . "&group=month"
                    . "&date2=" . $this->date_ending->format('Y-m-d')
                    . "&sort=ym:s:datePeriod<group>";

            $data = file_get_contents($url);
            $fc->setTimeout(60 * 60 * 24)
                    ->add('metrika-summary-months', $data);
        }

        return $data;
    }

    public function getSummaryByMonthsWithOAuth() {
        $fc = new CFileCache();
        $data = '';
        $cache = $fc->get('metrika-summary-months');
        if ($cache !== false) {
            $data = $cache;
        } else {
            $url = 'https://api-metrika.yandex.ru/stat/v1/data';
            $headers = [
                'Content-Type: application/x-yametrika+json',
                'Authorization: OAuth ' . $this->token
            ];

            $params = array(
                'ids' => $this->counter,
                'metrics' => 'ym:s:visits,ym:s:pageviews,ym:s:users,ym:s:avgVisitDurationSeconds',
                'dimensions' => 'ym:s:datePeriod<group>',
                'group' => 'month',
                'date1' => $this->date_beginning->format('Y-m-d'),
                'date2' => $this->date_ending->format('Y-m-d'),
                'sort' => 'ym:s:datePeriod<group>'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $data = curl_exec($ch);
            curl_close($ch);

            $fc->setTimeout(60 * 60 * 24)
                    ->add('metrika-summary-months', $data);
        }

        return $data;
    }

    private function getToken() {
        return $this->token;
    }

    private function getCounter() {
        return $this->counter;
    }

    private function _urlCounter() {
        return $this->getHost() . '/counter/' . $this->getCounter() . '.xml?oauth_token=' . (string) $this->getToken();
    }

    private function _urlTrafficSummaryByCounter($id, $date_beginning, $date_ending) {
        return $this->getHost() . '/stat/traffic/summary.xml?id=' . (string) $id . '&oauth_token=' . (string) $this->getToken() . '&date1=' . (string) $date_beginning . '&date2=' . (string) $date_ending;
    }

    private function _urlPopularPages($id, $date_beginning, $date_ending, $per_page = 25) {
        return $this->getHost() . '/stat/content/popular.xml?id=' . (string) $id . '&oauth_token=' . (string) $this->getToken() . '&date1=' . (string) $date_beginning . '&date2=' . (string) $date_ending . '&per_page=' . $per_page;
    }

}
