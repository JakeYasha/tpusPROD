<?php

namespace App\Action\Firm;

use App\Model\StatObject;
use Sky4\Exception;
use Sky4\Helper\DeprecatedDateTime;
use App\Model\AdvText;
use Sky4\Model;
use function app;
use function str;

class Show extends \App\Action\Firm {

	public function execute($id_firm, $id_service, $id_firm_branch = null, $_args_check = null) {
		app()->frontController()->layout()->setTemplate('firm');
		$this->findModelObject($id_firm, $id_service);
		if ($this->model()->isBlocked() && strtotime($this->model()->val('timestamp_ratiss_updating')) < strtotime(DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, date('m') - 1))) || $_args_check !== null) {
			throw new Exception(Exception::TYPE_BAD_URL);
		}
        if ($this->model()->hasWebPartner()) {
            app()->metadata()->noIndex(true);
        }
        
		$this->checkUrl('/firm/show/[0-9]+/[0-9]+/', $this->model()->link());
        
        $_vals = $this->model()->getVals();
        unset($_vals['id']);
        
        if ((int)$id_firm_branch > 0) {
            $_fb = new \App\Model\FirmBranch();
            $firm_branch = $_fb->reader()
                    ->setWhere([
                            'AND',
                            'id_firm = :id_firm',
                            'id_service = :id_service',
                            'id = :id_firm_branch'
                        ],[
                            ':id_firm' => (int)$id_firm, 
                            ':id_service' => (int)$id_service, 
                            ':id_firm_branch' => (int)$id_firm_branch
                        ])
                    ->objectByConds();
            
            if ($firm_branch->exists()){
                $vals = $firm_branch->getVals();
                $this->model()->branch_id = $vals['id'];
                $this->model()->flag_is_price_attached = $vals['flag_is_price_attached'];
                unset($vals['id']);
                $this->model()->setVals($vals);
                app()->metadata()->noIndex(true);
            } else {
                throw new Exception(Exception::TYPE_BAD_URL);                
            }
        }

		$this->params = app()->request()->processGetParams([
			'mode' => ['type' => 'string'],
			'sorting' => ['type' => 'string'],
			'q' => ['type' => 'string'],
			'id_catalog' => ['type' => 'int'],
			'display_mode' => ['type' => 'string'],
			'id_promo' => ['type' => 'int'],
			//garbage
			'map' => ['type' => 'string'],
			'photo' => ['type' => 'string'],
			'tmp' => ['type' => 'string'],
		]);
        
        if (isset($this->params['q']) && $this->params['q'] !== null) {
            $this->params['q'] = \App\Classes\Search::clearYo($this->params['q']);
            $this->params['q'] = str()->toLower(trim(\App\Classes\Search::clearQuery($this->params['q'])));
        }

		if ($this->params['map'] !== null || $this->params['photo'] !== null) {
			app()->response()->redirect($this->model()->link(), 301);
		}

		app()->breadCrumbs()
				->setElem('Каталог фирм', app()->link('/firm/catalog/'))
				->setElem($this->model()->name(), $this->model()->linkItem());

		$this->setTabs($this->params);

		$this->view()
				->set('url', app()->uri(), $this->params)
				->set('filters', $this->params);

		app()->metadata()->setFromModel($this->model());
		$types = $this->model()->getTypes();

		foreach ($types as $type) {
			app()->adv()
					->setAdvertRestrictions($type->val('advert_restrictions'))
					->setIdFirmType($type->id());

			/*if ($type->val('node_level') == 1 || $type->val('node_level') == 2) {
				//app()->adv()->addKeyword(str()->toLower(trim($type->val('name'))));
			}*/
		}

		$this->view()->set('short_display_mode', TRUE);
		$branches = $this->model()->getBranches();
		$branch_names = [];
		foreach ($branches as $branch) {
			if (!isset($branch_names[$branch->name()])) {
				$branch_names[$branch->name()] = [];
			}
			$branch_names[$branch->name()][] = $branch;
		}
        
		$firm_branch_names = [];
        $firm_branches = [];

        $_firm_branches = $this->model()->getFirmBranches();
        if ($_firm_branches) {
            foreach ($_firm_branches as $id_city => $_items) {
                foreach($_items as $firm_branch) {
                    if($this->model()->branch_id == $firm_branch->id()) {
                        $firm_branch->setVals($_vals);
                    }
                    if (!isset($firm_branch_names[$firm_branch->name()])) {
                        $firm_branch_names[$firm_branch->name()] = [];
                    }
                    $firm_branch_names[$firm_branch->name()][] = $firm_branch;
                    if (!isset($firm_branches[$firm_branch->val('id_city')])){
                        $firm_branches[$firm_branch->val('id_city')] = [];
                    }
                    $firm_branches[$firm_branch->val('id_city')] []= $firm_branch;
                }
            }
        }
        
        $full_branch_names = ['branches' => $branch_names, 'firm_branches' => $firm_branch_names];
        $full_branches = ['branches' => $branches, 'firm_branches' => $firm_branches];
        
		$this->view()
				->set('branches', $full_branches)
				->set('branches_names', $full_branch_names)
                ->set('short_full_contacts', FALSE);
        
		switch ($this->params['mode']) {
			case '' :
			default :
				//app()->metadata()->setCanonicalUrl(app()->link('/firm/show/' . $this->model()->id_firm() . '/' . $this->model()->id_service() . '/'));
				app()->stat()->addObject(StatObject::FIRM_SHOW, $this->model());
				for ($kolvo = 0; $kolvo <= KOLVO_STAT_ADD; $kolvo++) {
					app()->stat()->addObject(StatObject::FIRM_SHOW, $this->model());
				}
				$this->view()->set('short_display_mode', FALSE);
                $this->view()->set('short_full_contacts', TRUE);
				$bottom_block = $this->renderCommonInfoBlock($types, $full_branches, $full_branch_names);
				break;
			case 'price' :
				$bottom_block = $this->renderPriceListBlock();
				break;
			case 'video' :
				$bottom_block = $this->renderVideoBlock();
				break;
			case 'review' :
				$bottom_block = $this->renderReviewBlock();
				break;
			case 'promo' :
				$bottom_block = $this->renderPromoBlock();
				break;
			case 'firm-branch' :
				$bottom_block = $this->renderFirmBranchBlock();
				break;
		}
        
        app()->metadata()->setOgMetatag('og:type', 'article');
        app()->metadata()->setOgMetatag('og:locale', 'ru_RU');
        app()->metadata()->setOgMetatag('og:image', APP_URL . $this->model()->logoPath() );
        app()->metadata()->setOgMetatag('og:image:secure_url', APP_URL . $this->model()->logoPath() );
        
                $adv_text = new AdvText();
                
		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('item', $this->model())
				->set('bottom_block', $bottom_block)
				->set('mode', $this->params['mode'])
                                ->set('position', $adv_text->getByUrl(app()->location()->linkPrefix() . app()->request()->getRequestUri()))
				->setTemplate('show')
				->save();
	}

}
