<?php

namespace App\Model\Issue;

use App\Model\Issue;
use CDate;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model;
use Sky4\Model\Utils;


use App\Action\FirmManager;
use App\Model\MaterialRubric;
use App\Model\Rubric;
use App\Model\Material as MaterialModel;
use App\Classes\Pagination;
use App\Model\IssueMaterial as IssueMaterial;

use App\Presenter\MaterialItems;



class ManagerForm extends \Sky4\Model\Form {
	public function __construct(Model $model = null, $params = null) {
		if (!($this->model() instanceof Issue)) {
			$this->setModel(new Issue());
		}
		parent::__construct($model, $params);
	}
    
    public function editableFieldsNames() {
		return array_keys($this->fields());
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => $this->model()->exists() ? 'Сохранить' : 'Добавить',
				'attrs' => [
					'class' => 'send js-send btn btn_primary',
					'type' => 'submit'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/issue/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
        $fields = $this->model()->getFields();

		$fields['name']['label'] = 'Название';
		$fields['name']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];
        $fields['name']['attrs']['class'] = 'form__control form__control_modal';

		if (!isset($fields['name']['attrs'])) {
			$fields['name']['attrs'] = [];
		}
		$fields['name']['attrs']['data-validate-on-focus-out'] = 'true';

		$fields['number']['label'] = 'Номер выпуска';
		$fields['number']['params']['rules'] = ['required'];
        $fields['number']['attrs']['class'] = 'form__control form__control_modal';

		$fields['short_text']['label'] = 'Описание выпуска';
		$fields['short_text']['params']['rules'] = ['length' => ['max' => 2000, 'min' => 10], 'required'];
        $fields['short_text']['attrs']['class'] = 'form__control form__control_modal';
		if (!isset($fields['short_text']['attrs'])) {
			$fields['short_text']['attrs'] = [];
		}
		$fields['short_text']['attrs']['data-validate-on-focus-out'] = 'true';
        
        $fields['id']['elem'] = 'hidden_field';
        $fields['id_service']['elem'] = 'hidden_field';
        $fields['id_city']['elem'] = 'hidden_field';

		return [
            'name' => $fields['name'],
            'number' => $fields['number'],
            'short_text' => $fields['short_text'],
            'id_service' => $fields['id_service'],
            'id_city' => $fields['id_city'],
            'id' => $fields['id'],
            'image' => ['elem' => 'hidden_field', 'attrs' => ['class' => 'js-upload-id-holder issue-image']],
			'full_image' => ['elem' => 'hidden_field', 'attrs' => ['class' => 'js-upload-id-holder issue-full-image']],
        ];
	}

	// -------------------------------------------------------------------------

	public function render() {
        if ($this->model()->exists()) {
			$images = Utils::getObjectsByIds($this->model()->val('image'));
			$full_images = Utils::getObjectsByIds($this->model()->val('full_image'));
			$edit_row = [
                'image' => isset($images[$this->model()->val('image')]) ? $images[$this->model()->val('image')]->iconLink('-thumb') : null,
                'full_image' => isset($full_images[$this->model()->val('full_image')]) ? $full_images[$this->model()->val('full_image')]->embededFileComponent()->setSubDirName('service/' . app()->FirmManager()->id_service() . '/issue/file')->iconLink('-thumb') : null
            ];
			$image_url = $edit_row['image'] ? $edit_row['image'] : '/img/no_img.png';
			$full_image_url = $edit_row['full_image'] ? $edit_row['full_image'] : '/img/no_img.png';
		} else {
			$edit_row = [];
			$image_url = '/img/no_img.png';
			$full_image_url = '/img/no_img.png';
		}
                
        $filters = app()->request()->processGetParams([
            'page' => ['type' => 'int'],
            'sorting' => ['type' => 'string'],
            'query' => ['type' => 'string'],
        ]);

        $presenter = new MaterialItems();
        $presenter->page_material_items();
        $presenter->find($filters);

        $presenter_choose = new MaterialItems();
        $presenter_choose->page_use_material_items();              
        $issue = new IssueMaterial();
        $_material_ids = array_keys($issue->reader()->setWhere(['AND', 'id_issue = :id_issue'], [':id_issue' => $this->model()->id()])->rowsWithKey('id_material'));
        if ($_material_ids) {
            $where_conds = Utils::prepareWhereCondsFromArray($_material_ids, 'id');

            $_materials = new MaterialModel();
            $_materials = $_materials->reader()
                    ->setWhere($where_conds['where'], $where_conds['params'])
                    ->objects();

            $presenter_choose->setItems($_materials);
        }

        app()->tabs()->setSortOptions(self::materialSortingOptions());
        //var_dump($presenter_choose);
        $view_edit = $this->view();
        if ($this->model()->exists() && $this->model()->id()){
            $view_edit->set('filters', $filters)
                ->set('have_items', $presenter_choose->renderItems())
                ->set('items', $presenter->renderItems())
                ->set('items_count', $presenter->pagination()->getTotalRecords())
                ->set('pagination', $presenter->pagination()->render(true))
                ->set('sorting', app()->tabs()
                                ->setDisplayMode(false)
                                ->setActiveSortOption($filters['sorting'])
                                ->renderSorting(true));
        }
        
		return $view_edit
						->set('attrs', $this->getAttrs())
                        ->set('mode', $this->model()->exists() ? 'edit' : 'add')
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', '')
						->set('sub_heading', '')
						->set('image_url', $image_url)
						->set('full_image_url', $full_image_url)
                        ->set('model_id', $this->model()->exists() ? $this->model()->id() : 0)
						->setTemplate('issue_add_form', 'forms')
						->render();
                
	}
    public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}
        public static function materialSortingOptions() {
		return [
			'default' => ['name' => 'по дате &darr;', 'expression' => 'timestamp_inserting DESC'],
			'name-desc' => ['name' => 'по алфавиту &darr;', 'expression' => 'name DESC'],
			'default-asc' => ['name' => 'по дате &uarr;', 'expression' => 'timestamp_inserting ASC'],
			'name-asc' => ['name' => 'по алфавиту &uarr;', 'expression' => 'name ASC'],
		];
	}
}
