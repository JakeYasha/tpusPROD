<?php

namespace App\Action\Utils;

use App\Model\PriceCatalog;
use App\Model\YmlCategory;
use Foolz\SphinxQL\SphinxQL;
use function app;

class YmlRubricator extends \App\Action\Utils {

	public function execute() {
		$yc = new YmlCategory();
		$categories = $yc->reader()->setWhere(['AND', 'id_firm_user = :id_firm_user', 'parent_node != :nil'], [':id_firm_user' => 154, ':nil' => 0])
				->setOrderBy('parent_node ASC, id_yml_category ASC')
				->objects();

		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$res = [];
		foreach ($categories as $cat) {
			$name = $cat->name();
            $name = preg_replace('~[^a-zA-Zа-яА-Я0-9- ]~u', '', $cat->name());
			$data = $sphinx->select(['id_catalog', 'node_level', SphinxQL::expr('WEIGHT() AS weight')])
					->from(SPHINX_PRICE_CATALOG_INDEX)
					->where('id_group', '!=', 44)
					->option('max_matches', SPHINX_MAX_INT)
					->limit(0, SPHINX_MAX_INT)
					->groupby('id_catalog')
					->orderby('weight', 'DESC')
					->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"))
					->match('web_many_name', SphinxQL::expr(str_replace([')', '(', '/'], ' ', $name)))
					->execute();

			$parent = new YmlCategory();
			$parent->reader()->setWhere(['AND', 'id_yml_category = :node'], [':node' => $cat->val('parent_node')])
					->objectByConds();
			$res[$cat->id()] = [
				'name' => $parent->name() . '/' . $cat->name()
			];

			$cats = [];
			$_cats = [];
			if ($data) {
				$min_level = 10;
				foreach ($data as $dt) {
					$pcat = new PriceCatalog($dt['id_catalog']);
					$current_level = (int) $pcat->val('node_level');
					$min_level = $current_level < $min_level ? $current_level : $min_level;
					$cats[] = [
						'level' => $current_level,
						'name' => $pcat->name(),
						'id' => $pcat->id(),
						'parent_node' => $pcat->val('parent_node')
					];
				}

				foreach ($cats as $pkey => $pcat) {
					if ($pcat['level'] > $min_level) {
						unset($cats[$pkey]);
					}
				}

				if (count($cats) > 1) {
					$parent = current($_cats)['parent_node'];
					$ncat = new PriceCatalog($parent);
					$cats = [];
					$cats[] = [
						'level' => $ncat->val('node_level'),
						'name' => $ncat->name(),
						'id' => $ncat->id(),
						'parent_node' => $ncat->val('parent_node')
					];
				}

				$_cats = [];
				foreach ($cats as $pkey => $pcat) {
					$_cats[$pcat['id']] = $pcat['name'];
					if (trim($pcat['name']) === trim($name)) {
						$_cats = [];
						$_cats[$pcat['id']] = $name;
						break;
					}
				}
			}

			$res[$cat->id()]['catalog_name'] = current($_cats);
		}

		echo '<table border="1" cellpadding="5">';
		foreach ($res as $r) {
			echo '<tr><td>' . $r['name'] . '</td><td>' . $r['catalog_name'] . '</td></tr>';
		}
		echo '</table>';
		exit();
	}

}
