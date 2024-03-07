<?php

namespace App\Action\Utils;

class CheckFirmEmbedImage extends \App\Action\Utils {

	public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
			exit();
		}
	}

	public function execute() {
        ini_set('display_errors', 1);
        ini_set("log_errors", 0);
        set_time_limit(300);
        $dump_file = new \Sky4\FileSystem\File('/var/www/sites/tovaryplus.ru/update/utf8/sts_price_xml.txt');
		if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
            $i = 0;
            $cur_firm_id = 0;
            $cur_firm_id_has_embed_images = 0;
            $firm_ids_has_embed_images = [];
            echo 'Start<br>';
			while (($buffer = fgets($handle)) !== false) {
				$params = [];
                $i ++;
                if ($i % 100 == 0) {
					echo 'обработано записей: ' . $i . '<br>';
				}

				$row = explode('	', str_replace('\\	', ' ', $buffer));
				if (count($row) !== 30) {
					echo $this->file_name.' проблемы в строке №'.$i.' ('.count($row).') <br>';
					continue;
				}

				$source = new \App\Model\Component\Source();
				$source_options = $source->fields()['source']['options'];

				if ( ! array_key_exists($row[24], $source_options)) {
					echo $this->file_name.' неправильный source в строке №'.$i.' ('.count($row).') <br>';
					continue;
				}
                
                $embedded_image = '';
                if (preg_match('~img\-data="([^"]+)"~', trim($row[2]), $matches)) {
                    $embedded_image = '/var/www/sites/tovaryplus.ru/update/' . str_replace([
                        '/var/www/sites/tovaryplus.ru/update/',
                        '/usr/share/nginx/html/tovaryplus.new/tovaryplus.ru/app/cron'
                    ],'',$matches[1]);
                    $embedded_image = str_replace('//', '/', $embedded_image);
                    $row[2] = preg_replace('~(<img style[^>]+>)~', '', trim($row[2]));
                    if ($cur_firm_id_has_embed_images != (int)$row[14] && !in_array((int)$row[14], $firm_ids_has_embed_images)) {
                        $cur_firm_id_has_embed_images = (int)$row[14];
                        $firm_ids_has_embed_images []= (int)$row[14];
                        $firm = new \App\Model\Firm();
                        $firm->getByIdFirmAndIdService((int)$row[14], (int)$row[16]);
                        if ($firm->exists()) {
                            if ($firm->id() != $cur_firm_id) {
                                $cur_firm_id = $firm->id();
                                echo '<h2>' . $firm->name() . ' [#' . $firm->id() . ']' . '</h2>';
                            }
                            echo 'Найдены embedded_image для: ' . $firm->name() . ' [' . $firm->id() . ']' . '<br>';
                        }
                    }
                }

				$vals = [
					'country_of_origin' => trim($row[0]),
					'currency' => trim($row[1]),
					'description' => trim($row[2]),
					'flag_is_active' => (int)$row[3],
					'flag_is_available' => (int)$row[4],
					'flag_is_delivery' => (int)$row[5],
					'flag_is_image_exists' => 0,
					'flag_is_referral' => 0,
					'flag_is_retail' => (int)$row[7],
					'flag_is_wholesale' => (int)$row[8],
					'id_external' => (int)$row[9],
					'id_firm' => null,
					'id_group' => (int)$row[11],
					'id_subgroup' => (int)$row[12],
					'legacy_id_city' => (int)$row[13],
					'legacy_id_firm' => (int)$row[14],
					'legacy_id_price' => (int)$row[15],
					'legacy_id_service' => (int)$row[16],
					'name' => trim($row[17]),
					'name_external' => trim($row[18]),
					'params' => trim($row[19]),
					'price' => (double)$row[20],
					'price_old' => (double)$row[21],
					'price_wholesale' => (double)$row[22],
					'price_wholesale_old' => (double)$row[23],
					'source' => $row[24],
					'timestamp_inserting' => \Sky4\Helper\DateTime::now()->format(),
					'timestamp_last_updating' => $row[26],
					'unit' => trim(str()->toLower($row[27])),
					'url' => trim($row[28]),
					'vendor' => trim($row[29]) === 'НЕ УКАЗАНО' ? '' : trim($row[29])
				];

				$price_object = new \App\Model\Price();
				$price_object->reader()->setSelect(['id', 'id_firm'])
						->setWhere(['AND', 'legacy_id_price = :id_price', 'legacy_id_service = :id_service', 'legacy_id_firm = :id_firm'], [':id_price' => $vals['legacy_id_price'], ':id_service' => $vals['legacy_id_service'], ':id_firm' => $vals['legacy_id_firm']])
						->objectByConds();

				if ($price_object->exists()) {
                    if (in_array((int)$row[14], $firm_ids_has_embed_images)) {
                        if ($embedded_image && file_exists($embedded_image) && is_file($embedded_image)) {
                            $firm = new \App\Model\Firm();
                            $firm->getByIdFirmAndIdService($vals['legacy_id_firm'], $vals['legacy_id_service']);
                            if ($firm->exists() && $firm->id() == -100) {
                                echo 'Для файла: ' . $embedded_image . '<br>';
                                echo 'Условие 3 выполняется: ' . (is_file($embedded_image) ? 'Да' : 'Нет') . '<br>';
                        
                                /*echo '<div style="height: 50px; display: flex; flex-direction: row; justify-content: space around;">';
                                echo '<div><img src="' . str_replace('/var/www/sites/tovaryplus.ru/update','../..',$embedded_image) . '"></div>';
                                echo '<div>' . $price_object->name() . ' [' . $price_object->id() . ']' . '</div>';
                                echo '</div>';*/
                                $this->setEmbeddedImage($price_object, $embedded_image);
                            }
                        }
                    }
					
				}
            }
            echo 'Finish<br>';
        } else {
            echo 'Smth bad with file<br>';
        }
        
		exit();
    }
    
    private function setEmbeddedImage(\App\Model\Price $price, $embedded_image) {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
        
		$file_extension = array_reverse(explode('.', $embedded_image))[0];
		$image = [
			'file_name' => \Sky4\Helper\Random::uniqueId(),
			'id_firm' => $price->id_firm(),
			'id_price' => $price->id(),
			'file_extension' => $file_extension,
			'path' => $embedded_image,
			'source' => 'auto'
		];

		$where = ['AND', 'id_firm = :id_firm', 'id_price = :id_price', 'source = :source'];
		$params = [':id_firm' => (int)$image['id_firm'], ':id_price' => (int)$image['id_price'], ':source' => 'auto'];

		$im = new \App\Model\Image();
		$im->reader()
				->setWhere($where, $params)
                ->setOrderBy('timestamp_inserting DESC')
				->objectByConds();

		//копирование и подготовка изображения
		$target_file_name = $image['file_name'];
		$target_dir = new \Sky4\FileSystem\Dir(APP_DIR_PATH.'/public/image/');
		$target_dir->setPath($target_dir->path().str()->sub($target_file_name, 0, 1).'/');
		if ( ! $target_dir->exists()) {
			$target_dir->create();
		}

		$target_dir->setPath($target_dir->path().str()->sub($target_file_name, 1, 1).'/');
		if ( ! $target_dir->exists()) {
			$target_dir->create();
		}

		$image['file_subdir_name'] = str()->sub($target_file_name, 0, 1).'/'.str()->sub($target_file_name, 1, 1);
		copy($image['path'], $target_dir->path().$target_file_name.'.'.$image['file_extension']);
		unset($image['path']);

		if ($im->exists()) {
			$file = new \Sky4\FileSystem\File(APP_DIR_PATH.'/public/image/'.$im->val('file_subdir_name').'/'.$im->val('file_name').'.'.$im->val('file_extension'));
			$file->remove();
			$im->update($image);
		} else {
			$im->insert($image);
		}
	}

}
