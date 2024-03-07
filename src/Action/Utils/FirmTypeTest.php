<?php

namespace App\Action\Utils;

use Foolz\SphinxQL\SphinxQL;
use App\Model\Firm;
use function app;

class FirmTypeTest extends \App\Action\Utils {
    public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
            exit();
        }
    }

	public function execute() {
        $sphinx = SphinxQL::create(app()->getSphinxConnection());
        $sphinx->reset();
        $words_list = explode(' ', preg_replace('~ {2,}~', ' ', trim('кондитер'))); 
        $search_expression = '"'.implode(' ', $words_list).'" ='.implode('<<=', $words_list);
        $result = $sphinx->select('*')
                ->from(SPHINX_FIRM_INDEX)
                ->match('(company_name,company_activity)', $search_expression, true)
                ->limit(0, SPHINX_MAX_INT)
                ->option('ranker', 'none')
                ->option('max_matches', SPHINX_MAX_INT)
                ->execute();

        $firm_ids = [];
        foreach ($result as $row) {
            $firm_ids[] = $row['id'];
        }

        if ($firm_ids) {
            $id_where_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($firm_ids);
            $firm = new Firm();
            $out_array = $firm->reader()->setSelect(['id', 'id_city', 'flag_is_active'])->setWhere($id_where_conds['where'], $id_where_conds['params'])->rows();
            foreach ($out_array as $row) {
                $firm = new Firm($row['id']);
                echo $firm->id() . ' (' . $firm->getCity() . '): ' . $firm->name() . ' | ' . $firm->activity() . '<br/><br/>';
            }
        }            
        
		exit();
	}

}
