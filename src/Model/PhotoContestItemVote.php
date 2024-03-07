<?php

/**
 * @author Dmitriy Mitrofanov <d.i.mitrofanov@gmail.com>
 * @package tovaryplus.ru
 * @category PhotoContest
 * @version 1.00
 */
namespace App\Model;
class PhotoContestItemVote extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\TimestampActionTrait,
	 Component\IpAddrTrait;

	public function add(PhotoContestItem $photo) {
		$hash = md5(app()->request()->getRemoteAddr() . app()->request()->getUserAgent() . time() . $photo->val('photo_contest_id') . $photo->val('nomination_id'));
		setcookie('photo_contest_hash', $hash, time() + 86400);
		$this->insert([
			'photo_contest_id' => $photo->val('photo_contest_id'),
			'nomination_id' => $photo->val('nomination_id'),
			'ip_addr' => app()->request()->getRemoteAddr(),
			'user_agent' => app()->request()->getUserAgent(),
			'cookie_hash' => $hash
		]);
		$photo->update(['counter_votes' => (int) $photo->val('counter_votes') + 1]);

		return $this;
	}

	public function check($photo_contest_id, $nomination_id) {
		$cond0 = ['AND', 'photo_contest_id = :photo_contest_id', 'nomination_id = :nomination_id'];
		$cond1 = ['AND', 'ip_addr = :ip', 'user_agent = :user_agent'];
		$cond2 = ['AND', 'cookie_hash = :cookie_hash'];

		$params = [
			':photo_contest_id' => $photo_contest_id,
			':nomination_id' => $nomination_id,
			':ip' => app()->request()->getRemoteAddr(),
			':user_agent' => app()->request()->getUserAgent(),
			':cookie_hash' => isset($_COOKIE['photo_contest_hash']) ? $_COOKIE['photo_contest_hash'] : ''
		];

		$this->reader()
				->setWhere(['AND', $cond0, ['OR', $cond1, $cond2]], $params)
				->objectByConds();

		return !$this->exists();
	}

	public function fields() {
		return [
			'user_agent' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'hidden_field'
			],
			'cookie_hash' => [
				'col' => \Sky4\Db\ColType::getString(32),
				'elem' => 'hidden_field'
			],
			'photo_contest_id' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'text_field',
				'label' => 'Фото конкурс'
			],
			'nomination_id' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'text_field',
				'label' => 'Номинация'
			],
		];
	}

}
