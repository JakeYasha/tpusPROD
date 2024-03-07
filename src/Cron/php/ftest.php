<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/').'/../../../config/config_app.php';
\Sky4\App::init();

ini_set('display_errors', 1);
ini_set("log_errors", 0);

$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
$firm_type = new App\Model\FirmType();
$types = $firm_type->reader()->setWhere(['AND', '`node_level` = :3', 'parent_node = :parent_node'], [':3' => 2, ':parent_node' => 400])->rows();
$firm = new App\Model\Firm(140731);
//print_r($firm->getVals());

foreach ($types as $type) {
    if ($type['id'] != 2040) continue;
	$words_list = explode(' ', preg_replace('~ {2,}~', ' ', trim($type['name']))); 
	$search_expression = '"'.implode(' ', $words_list).'" ='.implode(' =', $words_list);
	print_r($search_expression);
//exit();

	//$search_expression = '"^ =автомобилей =импортного =производства $"';
	$result = $sphinx->select('*')
			->from(SPHINX_FIRM_INDEX)
			->where('id', '=', 140731)
			->match('(company_name,company_activity)', $search_expression, true)
			->limit(0, SPHINX_MAX_INT)
			->option('ranker', 'none')
			->option('max_matches', SPHINX_MAX_INT)
			->execute();
	if ($result) {
		print_r($type['name']);
		print_r($search_expression);
		exit();
	}
}

