<?php

namespace App\Action\Utils;

use \Foolz\SphinxQL\SphinxQL;

class StatRequestChecker extends \App\Action\Utils {

    public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
            exit();
        }
    }

    public function execute() {
        $requests = new \App\Model\StatRequest727373();
        $i = 23;
        $limit = 10000;
        while (true) {
            $items = $requests->reader()
                    ->setWhere(['AND', '`request_text` = :request_text'], [':request_text' => ''])
                    ->setOffset($i * $limit)
                    ->setLimit($limit)
                    ->setOrderBy('timestamp_inserting')
                    ->objects();
            
            if (!$items) break;
            echo $i . '<br/>';
            foreach($items as $item) {
                $question_id = explode('/', str_replace('//', '/', $item->val('request_url')));
                $question_id = isset($question_id[2]) ? trim($question_id[2]) : 0;
                if (preg_match('~^[0-9]+$~', $question_id) && $question_id > 0) {
                    echo $question_id . ': ' . $item->val('request_url') . '<br/>';
                    $vals = $item->getVals();
                    $vals['request_text'] = $question_id;
                    $item->update($vals);
                    echo $item->id() . ' updated (' . $item->val('request_text') . ')' . '<br/>';
                } else {
                    //echo '------: ' . $item->val('request_url') . '<br/>';
                }
            }
            $i++;
        }

        exit();
    }

}
