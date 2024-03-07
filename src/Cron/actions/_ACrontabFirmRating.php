<?php

class ACrontabFirmRating extends ACrontabAction {

	public function run() {
		$this->log('построение рейтинга фирм');
		$i = 0;
		$counter = 0;
		$limit = 10000;

		$this->createTempTable('firm_rank');
		$this->db->query()
				->setUpdate('firm')
				->setSet(['rating' => 0])
				->setWhere(['AND', '`flag_is_active` = :nil'], [':nil' => 0])
				->update();

		while (1) {
			$firm = new Firm();
			$firms = $firm
					->setWhere(['AND', '`flag_is_active` = :one'], [':one' => 1])
					->setLimits($limit, $i * $limit)
					->getAll();

			if (!$firms) {
				break;
			}

			foreach ($firms as $frm) {
				$counter++;
				$this->addFirmRank($frm);
			}
			$i++;
			$this->log(' -> обработано ' . $i * $limit . ' записей');
		}

		$this->flipTable('firm_rank');

		$this->log('добавлено ' . $counter . ' рейтингов');
	}

	private function rankUsers(Firm $firm) {
		$firm_review = new App\Model\FirmReview();
		$user_review_score = $firm_review->reader()
				->setSelect(['AVG(`score`) as `score`, COUNT(`id`) as `count`'])
				->setWhere(['AND', '`flag_is_active` = :flag_is_active', '`flag_is_considered` = :flag_is_considered', '`id_firm` = :id_firm', '`id_service` = :id_service'], [':flag_is_active' => 1, ':flag_is_considered' => 1, ':id_firm' => $firm->id_firm(), ':id_service' => $firm->id_service()])
				->rowByConds();

		return [
			'score' => (double) $user_review_score['score'],
			'count' => $user_review_score['count']
		];
	}

	private function addFirmRank(Firm $firm) {
		$firm_rank = new FirmRank();
		$firm_rank->setTable('tmp_firm_rank');
		$rank_kegeles = $this->rankKegeles($firm);

		$user_review_score = $this->rankUsers($firm);
		$rank_users = $user_review_score['score'];
		$rank_users_count = $user_review_score['count'];
		$full_rank = $this->fullRank($rank_users, $rank_kegeles, $rank_users_count);

		$firm_rank->insert([
			'id_firm' => $firm->id_firm(),
			'id_service' => $firm->id_service(),
			'rank_kegeles' => $rank_kegeles,
			'rank_users' => $rank_users,
			'rank_users_count' => $rank_users_count,
			'rank' => $full_rank
		]);

		$firm->update(['rating' => 5 * $full_rank]);

		return $rank_kegeles;
	}

	private function rankKegeles(Firm $firm) {
		return
				$this->rankerYears($firm) +
				$this->rankerGoods($firm) +
				$this->rankerInfo($firm) +
				$this->rankerPromo($firm) +
				$this->rankerBanners($firm) +
				$this->rankerPriotity($firm) +
				$this->rankerQuality($firm);
	}

	private function rankerPromo(Firm $firm) {
		$result = 0;

		$promo = new FirmPromo();
		$count = $promo->reader()
				->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service', 'flag_is_active = :flag_is_active', '`timestamp_beginning` < :datetime', '`timestamp_ending` > :datetime'], [':id_firm' => $firm->id_firm(), ':id_service' => $firm->id_service(), ':flag_is_active' => 1, ':datetime' => \Sky4\Helper\DeprecatedDateTime::now()])
				->count();

		if ($count > 0) {
			$result = $count * 0.05;
			if ($result > 0.5) {
				$result = 0.5;
			}
		}

		return $result;
	}

	private function rankerBanners(Firm $firm) {
		$result = 0;

		$banner = new Banner();
		$count = $banner->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service', 'flag_is_active = :flag_is_active', '`timestamp_beginning` < :datetime', '`timestamp_ending` > :datetime'], [':id_firm' => $firm->id_firm(), ':id_service' => $firm->id_service(), ':flag_is_active' => 1, ':datetime' => \Sky4\Helper\DeprecatedDateTime::now()])
				->count();

		if ($count > 0) {
			$result = $count * 0.05;
			if ($result > 0.5) {
				$result = 0.5;
			}
		}

		return $result;
	}

	private function rankerPriotity(Firm $firm) {
		$result = 0;
		$priority = (int) $firm->priority();

		if ($priority > 0) {
			if ($priority < 10) {
				$result = 0.025;
			} elseif ($priority < 20) {
				$result = 0.05;
			} elseif ($priority < 30) {
				$result = 0.1;
			} elseif ($priority >= 30) {
				$result = 0.15;
			}
		}

		return $result;
	}

	private function rankerYears(Firm $firm) {
		$result = 0;
		$diff = abs(\Sky4\Helper\DeprecatedDateTime::diff(\Sky4\Helper\DeprecatedDateTime::now(), $firm->val('timestamp_inserting'), 'm'));

		switch ($diff) {
			case $diff > 4 * 12 : $result = 0.15;
				break;
			case $diff > 2 * 12 : $result = 0.1;
				break;
			case ($diff < 2 * 12 && $diff > 12) : $result = 0.05;
				break;
			case ($diff > 1) : $result = 0.1;
				break;
			default : $result = 0;
		}

		return $result;
	}

	private function rankerGoods(Firm $firm) {
		$result_rank_image = 0;
		$result_rank_text = 0;

		$sts_price = new StsPrice();
		$all_count = $sts_price->setWhere(['AND', '`id_firm` = :id_firm', '`id_service` = :id_service', '`blocked` = :nil'], [':id_firm' => $firm->val('id_firm'), ':id_service' => $firm->val('id_service'), ':nil' => 0])
				->count();

		if ($all_count > 0) {
			$count_with_image = $sts_price->setWhere(['AND', '`id_firm` = :id_firm', '`id_service` = :id_service', '`blocked` = :nil', '`exist_image` != :nil'], [':id_firm' => $firm->val('id_firm'), ':id_service' => $firm->val('id_service'), ':nil' => 0])->count();
			$count_with_text = $sts_price->setWhere(['AND', '`id_firm` = :id_firm', '`id_service` = :id_service', '`blocked` = :nil', '`info` != :empty'], [':id_firm' => $firm->val('id_firm'), ':id_service' => $firm->val('id_service'), ':nil' => 0, ':empty' => ""])->count();

			$result_rank_image = 0;
			$result_rank_text = 0;
			$image = $count_with_image / $all_count * 100;
			$text = $count_with_text / $all_count * 100;

			if ($image >= 80 && $all_count >= 100) $result_rank_image = 0.2;
			elseif ($image >= 50 && $all_count >= 100) $result_rank_image = 0.1;
			elseif ($image >= 20 && $all_count >= 100) $result_rank_image = 0.05;
			elseif ($image >= 80 && $all_count < 100) $result_rank_image = 0.1;
			elseif ($image >= 50 && $all_count < 100) $result_rank_image = 0.05;
			elseif ($image >= 20 && $all_count < 100) $result_rank_image = 0.03;
			elseif ($image > 0) $result_rank_image = 0.01;

			if ($text >= 80 && $all_count >= 100) $result_rank_text = 0.2;
			elseif ($text >= 50 && $all_count >= 100) $result_rank_text = 0.1;
			elseif ($text >= 20 && $all_count >= 100) $result_rank_text = 0.05;
			elseif ($text >= 80 && $all_count < 100) $result_rank_text = 0.1;
			elseif ($text >= 50 && $all_count < 100) $result_rank_text = 0.05;
			elseif ($text >= 20 && $all_count < 100) $result_rank_text = 0.03;
			elseif ($text > 0) $result_rank_text = 0.01;
		}

		return $result_rank_image + $result_rank_text;
	}

	private function rankerInfo(Firm $firm) {
		$rank = 0;

		$logo = trim($firm->val('file_logo'));
		if ($logo) {
			$rank += 0.05;
		}

		$description = new FirmDescription();
		$description->getByFirm($firm);

		if ($description->exists()) {
			$rank += 0.05;
		}

		return $rank;
	}

	private function rankerQuality(Firm $firm) {
		$last_check_date = \Sky4\Helper\DeprecatedDateTime::now(60 * 60 * 24 - 180);
		$frq = new FirmRankQuality();
		$all_count = $frq->setWhere(['AND', '`id_firm` = :id'], [':id' => $firm->id()])->count();

		if ($all_count === 0) return 0;

		$good_count = $frq->setWhere(['AND', '`id_firm` = :id', '`quality_check_result` = :one'], [':id' => $firm->id(), ':one' => 1])->count();
		$percent = $good_count / $all_count * 100;

		if ($good_count == 0 and $all_count < 3) return -0.1;

		if ($percent >= 80) return 0.3;
		if ($percent >= 60) return 0.2;
		if ($percent >= 40) return 0.1;

		return -0.3;
	}

	private function fullRank($rank_users, $rank_kegeles, $rank_users_count) {
		return $rank_users - ($rank_users - $rank_kegeles) / pow(($rank_users_count + 1), (($rank_users_count * 0.02) / ($rank_users + 0.1)));
	}

	private function checkFirm(Firm $firm) {
		$fr = new FirmRank();
		return $fr
						->setWhere('`id_firm` = :id', [':id' => $firm->id()])
						->getByConds()
						->exists();
	}

}
