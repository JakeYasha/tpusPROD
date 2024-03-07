<?php

class Cache extends CAppController {

	public function actionCache($method_alias = '') {
		$method_name = 'render' . str()->replace(str()->firstCharsOfWordsToUpper(str()->replace(str()->toLower($method_alias), '-', ' ')), ' ', '');
		if (!$method_name || !method_exists($this, $method_name)) {
			throw new CException();
		}

		$result = '';
		$caching_rules = $this->cachingRules();
		if (isset($caching_rules[$method_name])) {
			$cache = new CFileCache();
			$cache->setTimeout(3);
			$id = md5(CObject::className($this) . $method_name);
			$result = $cache->get($id);
			if ($result === false) {
				echo "Cached!<br />\n";
				$result = CObject::execute($this, $method_name);
				$cache->add($id, $result);
			} else {
				echo "Cache!<br />\n";
			}
		} else {
			echo "Without caching!<br />\n";
			$result = CObject::execute($this, $method_name);
		}
		echo $result;

		exit();
	}

	public function cachingRules() {
		return array(
			'renderModelsList' => array(
				'cache_class_name' => 'CFileCache',
				'enabled' => true,
				'timeout' => 10,
				'type' => 'array'
			)
		);
	}

	public function renderModelsList() {
		$cmodel = new \App\Controller\Model();
		return $this->view()
						->set('models', $cmodel->getModels())
						->setTemplate('models_list', 'model')
						->render();
	}

}
