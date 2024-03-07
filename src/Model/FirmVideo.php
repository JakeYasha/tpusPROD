<?php

namespace App\Model;

use App\Model\Component\ActiveTrait;
use App\Model\Component\IdFirmTrait;
use App\Model\Component\IdTrait;
use App\Model\Component\ImageTrait;
use App\Model\Component\NameTrait;
use App\Model\Component\NewStateTrait;
use App\Model\Component\TextTrait;
use App\Model\Component\TimestampActionTrait;
use DateInterval;
use function app;

class FirmVideo extends \Sky4\Model\Composite {

	use IdTrait,
	 ActiveTrait,
	 IdFirmTrait,
	 NameTrait,
	 NewStateTrait,
	 ImageTrait,
	 TextTrait,
	 TimestampActionTrait;

	public function cols() {
		$cols = [
			'name' => ['label' => 'Название'],
			'id_firm' => ['label' => 'Фирма']
		];

		$cols = $cols + $this->timestampActionComponent()->cols('timestamp_inserting') + $this->newComponent()->cols() + $this->activeComponent()->cols();

		return $cols;
	}

	public function defaultOrder() {
		return ['timestamp_inserting' => 'desc'];
	}

	public function defaultEyeEnabled() {
		return true;
	}

	public function fields() {
		return [
			'video_length' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'video_length',
					'type' => 'string(10)',
				],
				'elem' => 'text_field',
				'label' => 'Длина видео'
			],
			'video_youtube' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'video_youtube',
					'type' => 'string(1000)',
				],
				'elem' => 'text_field',
				'label' => 'Ссылка на youtube-видео',
				'params' => [
					'rules' => [
						'required',
						'reg_exp' => [
							'^https?://(?:www\.)?youtu(?:be\.com/watch\?v=|\.be/)([\w\-]+)(&(amp;)?[\w\?=]*)?$',
							'',
							'В этом поле может быть только ссылка на youtube-видео'
						]
					]
				]
			],
			'video_code' => [
				'attrs' => ['rows' => '10'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'text_area',
				'label' => 'или код видео',
				'params' => [
					'parser' => false
				]
			],
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'hidden_field',
				'label' => 'ID города'
			],
			'total_views' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_8'
				],
				'elem' => 'hidden_field',
				'label' => 'Количество просмотров'
			],
		];
	}

	public function filterFields() {
		return [
			'id_firm' => [
				'elem' => 'drop_down_list',
				'label' => 'Фирма',
				'options' => $this->idFirmComponent()->getFirmNamesForFilter(),
				'cond' => '=',
				'field_name' => 'id_firm'
			],
			'flag_is_active' => [
				'elem' => 'single_check_box',
				'label' => 'Только активные',
				'cond' => 'flag',
				'field_name' => 'flag_is_active',
				'val' => 1,
				'default_val' => 1
			],
			'flag_is_new' => [
				'elem' => 'single_check_box',
				'label' => 'Только новые',
				'cond' => 'flag',
				'field_name' => 'flag_is_new'
			]
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'field', 'name' => 'flag_is_active'],
			['type' => 'field', 'name' => 'flag_is_new'],
				//['type' => 'component', 'name' => 'TimestampInterval'],
				//['type' => 'field', 'name' => 'timestamp_ending']
		];
	}

	public function imageResolutions() {
		return [
			'image' => ['width' => 150, 'height' => 200]
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Видео';
	}

	public function isYoutube() {
		return $this->val('video_youtube') && !$this->val('video_code');
	}

	public function getYoutubeHash() {
		$result = '';

		if ($this->isYoutube()) {
			if (preg_match("~youtube.com~", $this->val('video_youtube'))) {
				preg_match_all("~v=([a-zA-Z0-9-_]*)~", $this->val('video_youtube'), $matches);
				$result = isset($matches[1][0]) ? $matches[1][0] : FALSE;
			} else if (preg_match("~youtu.be~", $this->val('video_youtube'))) {
				preg_match_all("~be/([a-zA-Z0-9-_]*)~", $this->val('video_youtube'), $matches);
				$result = isset($matches[1][0]) ? $matches[1][0] : FALSE;
			}
		} else {
			$result = FALSE;
		}

		return $result;
	}

	public function getThumbnailSrc() {
		$result = '/css/img/no-img-video.png';

		if ($this->isYoutube()) {
			$hash = $this->getYoutubeHash();
			if ($hash !== false) {
				$result = 'https://img.youtube.com/vi/' . $this->getYoutubeHash() . '/mqdefault.jpg';
			}
		}

		return $result;
	}

	public function getYoutubeVideoDuration() {
		$result = false;
		$hash = $this->getYoutubeHash();

		$key = 'AIzaSyCxNWz2CWqA2zt86lvJhIEhfmgsBLSk0vM';
		$part = 'contentDetails';

		$url = 'https://www.googleapis.com/youtube/v3/videos'
				. '?id=' . $hash
				. '&key=' . $key
				. '&part=' . $part;


		$data = file_get_contents($url);
		if ($data) {
			$result_array = json_decode($data, true);
		}
		if (isset($result_array['items'][0]['contentDetails']['duration'])) {
			$duration = $result_array['items'][0]['contentDetails']['duration'];
			$interval = new DateInterval($duration);

			$result = date($interval->h > 0 ? 'H:i:s' : 'i:s', strtotime($interval->h . ":" . $interval->i . ":" . $interval->s));
		}

		return $result;
	}

	public function formStructure() {
		return [
			['type' => 'component', 'name' => 'Name'],
			['type' => 'tab', 'name' => 'about_tab', 'label' => 'Описание видео'],
			['type' => 'component', 'name' => 'Text', 'tab_name' => 'about_tab'],
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'field', 'name' => 'video_youtube'],
			['type' => 'field', 'name' => 'video_code'],
			['type' => 'field', 'name' => 'video_length'],
			['type' => 'field', 'name' => 'flag_is_active'],
			['type' => 'component', 'name' => 'Image'],
			['type' => 'label', 'text' => 'При указании ссылки на youtube, можно не добавлять картинку'],
		];
	}

	public function linkAjax() {
		return '/app-ajax/get-video/' . $this->id() . '/';
	}

	public function orderableFieldsNames() {
		$cols = $this->cols();
		return array_keys($cols);
	}

	public function link() {
		return $this->firm()->linkItem() . '?id=' . $this->id() . '&mode=video';
	}

	public static function prepare(FirmVideo $item, $image = null, $big_image = null) {
		$firm = $item->firm();

		return [
			'id' => $item->id(),
			'name' => $item->val('name'),
			'text' => $item->val('brief_text'),
			'image' => $image,
			'big_image' => $big_image,
			'link' => app()->linkFilter($firm->link(), ['id' => $item->id(), 'mode' => 'video']),
			'firm' => $firm,
			'is_active' => true,
			'total_views' => (int) $item->val('total_views')
		];
	}

	public function beforeInsert(&$vals, $parent_object = null) {
		$vals['flag_is_new'] = '1';
		if (!$this->val('video_length') && $this->isYoutube()) {
			$vals['video_length'] = $this->getYoutubeVideoDuration();
		}
		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		if (count($vals) !== 1) {
			$vals['flag_is_new'] = '0';
			if (!$this->val('video_length') && $this->isYoutube()) {
				$vals['video_length'] = $this->getYoutubeVideoDuration();
			}
		}
		return parent::beforeUpdate($vals);
	}

}
