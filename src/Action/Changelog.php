<?php

namespace App\Action;

use App\Presenter\ChangelogItems;

class Changelog extends \App\Classes\Action {

    public function __construct() {
        parent::__construct();
        $this->setModel(new \App\Model\Changelog());
    }

    public function execute($id = null) {
        $params = app()->request()->processGetParams([
            'thumb' => ['type' => 'string'],
        ]);

        $this->text()->getByLink('/changelog/');
        app()->breadCrumbs()->setElem($this->text()->name(), '');

        if ($id !== null) {
            $this->model()->reader()->object((int)$id);
            if ($this->model()->exists()) {
                if (!isset($_SESSION['changelog'])){
                    $_SESSION['changelog'] = [$this->model()->id() => []];
                } else if (!isset($_SESSION['changelog'][$this->model()->id()])){
                    $_SESSION['changelog'][$this->model()->id()] = [];
                }
                
                if (!isset($_SESSION['changelog'][$this->model()->id()]['thumb'])) {
                    if ($params['thumb'] == 'up') {
                        $vals = $this->model()->getVals();
                        $likes = (int)$vals['likes'];
                        $vals['likes'] = $likes+1;
                        $this->model()->update($vals);
                        $_SESSION['changelog'][$this->model()->id()] = ['thumb' => 'up'];
                    } else if ($params['thumb'] == 'down') {
                        $vals = $this->model()->getVals();
                        $dislikes = (int)$vals['likes'];
                        $vals['dislikes'] = $dislikes+1;
                        $this->model()->update($vals);
                        $_SESSION['changelog'][$this->model()->id()] = ['thumb' => 'down'];
                    }
                }
            }
            
            app()->response()->redirect('/changelog/');
        }

        $presenter = new ChangelogItems();
        $presenter->find();

        app()->metadata()->setFromModel($this->text(), null, $presenter->pagination());

        $this->text()->getByLink('/changelog/');

        $this->view()
                ->set('bread_crumbs', app()->breadCrumbs()->render())
                ->set('items', $presenter->renderItems())
                ->set('item', $this->text())
                ->set('pagination', $presenter->pagination()->render())
                ->setTemplate('index')
                ->save();
    }

    /**
     * 
     * @return \App\Model\Changelog
     */
    public function model() {
        return parent::model();
    }

}
