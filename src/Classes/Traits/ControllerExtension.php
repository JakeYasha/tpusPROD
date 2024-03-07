<?php

namespace App\Classes\Traits;

trait ControllerExtension {

	public function findModelObject($id) {
		if (is_numeric($id)) {
			$this->model()->reader()->object($id);
			if (!$this->model()->exists()) {
				throw new \CException(\CApp::config()->get('text.page_not_found', 'Страница не найдена'));
			}
			if ($this->model() instanceof \CExtendedModel) {
				if ($this->model()->componentExists('Active') && !$this->model()->activeComponent()->isActive()) {
					throw new \CException(\CApp::config()->get('text.page_not_found', 'Страница не найдена'));
				} elseif ($this->model()->componentExists('State') && ($this->model()->val('state') !== 'published')) {
					throw new \CException(\CApp::config()->get('text.page_not_found', 'Страница не найдена'));
				}
			}
			if ($this->model()->val('name_in_url')) {
				\CApp::response()->redirect($this->model()->link(), 301);
			}
		} else {
			if ($this->model()->fieldExists('name_in_url')) {
				$this->model()
						->reader()
						->setWhere('`name_in_url` = :name_in_url', [':name_in_url' => $id])
						->objectByConds();
			} else {
				throw new \CException(\CApp::config()->get('text.page_not_found', 'Страница не найдена'));
			}
		}
		return $this;
	}

	public function getPage() {
		return (isset($this->options['page']) && ((int) $this->options['page'] >= 1)) ? $this->options['page'] : 1;
	}

	public function setMetadataFromModel(\Sky4\Model $object) {
		$title = $object->val('metadata_title') ? $object->val('metadata_title') : $object->title();
		$key_words = $object->val('metadata_key_words') ? $object->val('metadata_key_words') : '';
		$description = $object->val('metadata_description') ? $object->val('metadata_description') : '';
		\CApp::metadata()
				->setTitle($title)
				->setMetatag('description', $description)
				->setMetatag('keywords', $key_words);
		return $this;
	}

}
