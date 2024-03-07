<?php

namespace App\Controller;

class Rating extends \App\Classes\Controller {

	public function renderStars($score, $only_stars = false, $firm = null) {
		if ($score instanceof \App\Model\Firm) {
			$tmp = $score;
			$score = $tmp->val('rating');
			$firm = $tmp;
		}

		if ($firm instanceof \App\Model\Firm) {
			$firm_rank = new \App\Model\FirmRank();
			$firm_rank->getByFirm($firm);

			if ($firm_rank->exists()) {
				$score = $firm_rank->val('rank_users');
			} else {
				$score = 0;
			}
		}

		return $this->view()
						->set('score', $score)
						->set('only_stars', $only_stars)
						->set('count', $firm instanceof \App\Model\Firm ? $firm->getReviewsCount() : 0)
						->set('url', $firm instanceof \App\Model\Firm ? app()->linkFilter($firm->linkItem(), ['mode' => 'review']) : '')
						->set('firm', ($firm != null && ($firm instanceof \App\Model\Firm)) ? $firm : null)
						->setTemplate('stars')
						->render();
	}
    
    public function renderOnlyButton($score, $only_stars = false, $firm = null) {
		if ($score instanceof \App\Model\Firm) {
			$tmp = $score;
			$score = $tmp->val('rating');
			$firm = $tmp;
		}

		if ($firm instanceof \App\Model\Firm) {
			$firm_rank = new \App\Model\FirmRank();
			$firm_rank->getByFirm($firm);

			if ($firm_rank->exists()) {
				$score = $firm_rank->val('rank_users');
			} else {
				$score = 0;
			}
		}

		return $this->view()
						->set('score', $score)
						->set('only_stars', $only_stars)
						->set('count', $firm instanceof \App\Model\Firm ? $firm->getReviewsCount() : 0)
						->set('url', $firm instanceof \App\Model\Firm ? app()->linkFilter($firm->linkItem(), ['mode' => 'review']) : '')
						->set('firm', ($firm != null && ($firm instanceof \App\Model\Firm)) ? $firm : null)
						->setTemplate('only_button')
						->render();
	}

}
