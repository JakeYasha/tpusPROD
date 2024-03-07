<?php

namespace App\Action\PhotoContest;

use App\Model\PhotoContestItem;
use App\Model\PhotoContestItemVote;
use function app;

class Vote extends \App\Action\PhotoContest {

	public function execute() {
		$result = ['success' => false];

		$data = app()->request()->processPostParams([
			'id' => ['type' => 'int']
		]);

		$photo = new PhotoContestItem($data['id']);
		if ($photo->exists()) {
			$vote = new PhotoContestItemVote();
			if ($vote->check($photo->val('photo_contest_id'), $photo->val('nomination_id'))) {
				$vote->add($photo);
				$result = ['success' => true, 'html' => ($photo->val('counter_votes')) . ' ' . \Sky4\Helper\Word::ending(($photo->val('counter_votes')), ['голос', 'голоса', 'голосов'])];
			}
		}

		die(json_encode($result));
	}

}
