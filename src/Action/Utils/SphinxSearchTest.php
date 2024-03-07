<?php

namespace App\Action\Utils;
use \Foolz\SphinxQL\SphinxQL;

class SphinxSearchTest extends \App\Action\Utils {
	protected $query = null;

    public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
            exit();
        }
    }

    public function execute() {
        $sphinx = SphinxQL::create(app()->getSphinxConnection());
        $search_limit = 1050;
        $limit = 10;
        
        if (false) {
            ini_set('max_execution_time', -1);
            $sphinx->query('TRUNCATE RTINDEX `' . SPHINX_PRICE_CATALOG_INDEX . '`');

            $pc = new \App\Model\PriceCatalog();
            $items = $pc->reader()->objects();

            $i = 0;
            foreach ($items as $item) {
                $item->updateRtIndex($sphinx);
                echo ++$i . "\r";
            }
            
            exit();
        }
        
        $params = app()->request()->processGetParams([
            'q' => ['type' => 'string']
        ]);
        
        $query = \App\Classes\Search::prepareSearchQuery($params['q']);
        $this->setQuery($query, true);
        
        var_dump($this->query);

        $result = $sphinx
				->select('*', SphinxQL::expr('WEIGHT() as `weight`'))
				->limit(0, $search_limit)
				->from([SPHINX_PRICE_CATALOG_INDEX])
				->where('node_level', '=', 2)
				->match('(subgroup_name)', SphinxQL::expr($query))
				->orderBy('weight', 'DESC')
				->orderBy('node_level', 'ASC')
				->option('ranker', 'sph04')
				//->groupBy('id_parent')
				->enqueue()
				->select('*', SphinxQL::expr('WEIGHT() as `weight`'))
				->limit(0, $search_limit)
				->from([SPHINX_PRICE_CATALOG_INDEX])
				->match('(name,web_name,web_many_name)', SphinxQL::expr($query))
				->where('node_level', '!=', 2)
				->orderBy('weight', 'DESC')
				->orderBy('node_level', 'ASC')
				->option('ranker', 'sph04')
				->groupBy('id_catalog')
				->enqueue()
				->select('*', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, $limit)
				->from([SPHINX_FIRM_INDEX])
				->where('id', 'IN', app()->location()->getFirmIds())
				->match('(company_name,company_activity,company_address)', SphinxQL::expr($query))
				->orderBy('weight', 'DESC')
				->orderBy('rating', 'DESC')
				->option('field_weights', ['company_name' => 10, 'company_activity' => 1])
				->option('ranker', 'sph04')
				->enqueue()
				->select('*', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, $limit)
				->from([SPHINX_FIRM_CATALOG_INDEX])
				->where('node_level', '<', (int)3)
				->match('(name)', SphinxQL::expr($query))
				->orderBy('weight', 'DESC')
				->option('ranker', 'sph04')
				->executeBatch();


        var_dump($result);
        
        exit();
    }
    
    public function setQuery($query, $use_synonims = false) {
		$query = str()->toLower(trim(\App\Classes\Search::clearQuery($query)));

		if (str()->length($query) === 0) {
			app()->response()->redirect(app()->link('/search/empty/'));
		}

		$we = new \App\Model\WordException();
		$exceptions = $we->reader()->rowsWithKey('name');
		$words = explode(' ', $query);
		$result_words = [];
		$_query = $query;
		foreach ($words as $word) {
			$word = trim($word);
			if (!isset($exceptions[$word])) {
				$result_words[] = $word;
			}
		}

		$query = implode(' ', $result_words);
		$result_words[] = $_query;

		if ($use_synonims) {
			//синонимы
			$replace = [];
			$syn = new \App\Model\Synonym();
			foreach ($result_words as $k => $s) {
				$synonims = $syn->reader()
						->setWhere(['AND', "`search` = :search"], [':search' => $s])
						->rows();

				foreach ($synonims as $kk => $r) {
					$replace[$k][$kk] = $r['replace'];
				}
			}

			$words_with_synonims = [];
			if ($replace) {
				foreach ($replace as $k => $synonims) {
					foreach ($synonims as $kk => $rep) {
						$words_with_synonims[] = '('.str()->replace($query, $result_words[$k], $replace[$k][$kk]).')';
					}
				}
			}

			$words_with_synonims = array_unique($words_with_synonims);
			if ($words_with_synonims) {
				$query = '('.$query.')|'.implode('|', array_reverse($words_with_synonims));
			}
		}

		$this->query = $query;

		return $this->query;
	}

}
