<?php

namespace App\Model;

class MaterialFile extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\EmbeddedFileTrait,
        Component\TimestampActionTrait;

    public function fields() {
        return [
            'type' => [
                'col' => [
                    'flags' => 'not_null',
                    'type' => "list('image','file')"
                ],
                'elem' => 'radio_buttons',
                'label' => 'Вид данных',
                'options' => ['image' => 'Изображение', 'file' => 'Файл']
            ],
            'flag_is_temp' => [
                'col' => \Sky4\Db\ColType::getInt(1),
                'elem' => 'check_box',
                'label' => 'Временное изображение',
            ]
        ];
    }

    public function path($postfix = '') {
        return '/file/' . $this->val('file_subdir_name') . '/' . $this->val('file_name') . $postfix . '.' . $this->val('file_extension');
    }

    public function iconLink($postfix = '') {
        return '/file/' . $this->val('file_subdir_name') . '/' . $this->val('file_name') . $postfix . '.' . $this->val('file_extension');
    }

    public function thumb($postfix = '') {
        if ($this->embededFileComponent()->isImage()) {
            return '/file/' . $this->val('file_subdir_name') . '/' . $this->val('file_name') . '-330x200.' . $this->val('file_extension');
        }

        $file = new \Sky4\FileSystem\File(APP_DIR_PATH . '/public/img/' . $this->val('file_extension') . '.png');
        if ($file->exists()) {
            return '/img/' . $this->val('file_extension') . $postfix . '.png';
        }

        return '/img/default-icon.png';
    }

    public function delete() {
        $this->embededFileComponent()->setSubDirName('file');
        $file1 = new \Sky4\FileSystem\File($this->embededFileComponent()->path());
        $file2 = new \Sky4\FileSystem\File($this->embededFileComponent()->path('-thumb'));

        $file1->remove();
        $file2->remove();

        return parent::delete();
    }

    public function name() {
        return $this->val('file_raw_name');
    }

    public function link() {
        return $this->path();
    }

    /*public static function getImagesByFirm(Firm $firm) {
        $ff = new FirmFile();
        return $ff->reader()
                        ->setWhere(['AND', 'id_firm = :id_firm', 'type = :type'], [':id_firm' => $firm->id(), ':type' => 'image'])
                        ->setOrderBy('timestamp_inserting DESC')
                        ->objects();
    }*/

    /*public static function getFilesByFirm(Firm $firm) {
        $ff = new FirmFile();
        return $ff->reader()
                        ->setWhere(['AND', 'id_firm = :id_firm', 'type = :type'], [':id_firm' => $firm->id(), ':type' => 'file'])
                        ->setOrderBy('timestamp_inserting DESC')
                        ->objects();
    }*/

    public function getFormatSize($unit = "", $decimals = 2) {
        $bytes = $this->val('file_dimension_size');
        $units = array('б' => 0, 'Кб' => 1, 'Мб' => 2, 'Гб' => 3, 'Тб' => 4,
            'Пб' => 5, 'Эб' => 6, 'Зб' => 7, 'YB' => 8);

        $value = 0;
        if ($bytes > 0) {
            if (!array_key_exists($unit, $units)) {
                $pow = floor(log($bytes) / log(1024));
                $unit = array_search($pow, $units);
            }

            $value = ($bytes / pow(1024, floor($units[$unit])));
        }

        if (!is_numeric($decimals) || $decimals < 0) {
            $decimals = 2;
        }

        return sprintf('%.' . $decimals . 'f ' . $unit, $value);
    }

}
