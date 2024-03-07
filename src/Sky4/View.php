<?php

namespace Sky4;

use Sky4\Exception,
	Sky4\FileSystem\Dir,
	Sky4\FileSystem\File,
	Sky4\OutputBuffer;

class View {

	protected $basic_subdir_name = '';
	protected $dir = null;
	protected $dir_path = '';
	protected $file = null;
	protected $file_extension = '';
	protected $file_name = '';
	protected $saved_templates = [];
	protected $strip = false;
	protected $subdir_name = '';
	protected $vars = [];
	protected $theme_name = null;// тип темы

	
	public function __construct() {
		if (!defined('APP_SUB_SYSTEM_NAME')) {
			throw new Exception('Отсутствует базовая константа APP_SUB_SYSTEM_NAME');
		} elseif (!defined(APP_SUB_SYSTEM_NAME . '_VIEWS_DIR_PATH')) {
			throw new Exception('Отсутствует базовая константа ' . APP_SUB_SYSTEM_NAME . '_VIEWS_DIR_PATH');
		}
		$this->setDirPath(constant(APP_SUB_SYSTEM_NAME . '_VIEWS_DIR_PATH'));
	}

	/**
	 * @return Dir
	 */
	public function dir() {
        $this->setThemeDir();
		if ($this->dir === null) {
			$this->dir = new Dir($this->dir_path);
			$this->dir->create();
			if ($this->subdir_name) {
				$subdirs_names = explode('/', $this->subdir_name);
				foreach ($subdirs_names as $subdir_name) {
					$this->dir->setPath($this->dir->path() . $subdir_name . '/')
							->create();
				}
			} elseif ($this->basic_subdir_name) {
				$subdirs_names = explode('/', $this->basic_subdir_name);
				foreach ($subdirs_names as $subdir_name) {
					$this->dir->setPath($this->dir->path() . $subdir_name . '/')
							->create();
				}
			}
        }
        
		return $this->dir;
	}

	/**
	 * @return File
	 */
	public function file() {
		if ($this->file === null) {
			if (!$this->dir()->exists()) {
				throw new Exception('Директория шаблона отсутствует [' . $this->dir()->path() . ']');
			}
			if (!$this->file_name || !$this->file_extension) {
				throw new Exception('Имя и расширение файла шаблона отсутствует');
			}
			$this->file = new File($this->dir()->path() . $this->file_name . '.' . $this->file_extension);
			if (!$this->file->exists()) {
				$this->file->putData('');
			}
		}
		return $this->file;
	}

	public function getTemplate() {
		return $this->file_name;
	}

	/**
	 * @return View
	 */
	public function set($param_1, $param_2 = null) {
		if (is_array($param_1)) {
			return $this->setVars($param_1);
		}
		return $this->setVar($param_1, $param_2);
	}

	/**
	 * @return View
	 */
	public function setBasicSubdirName($subdir_name) {
		$this->basic_subdir_name = (string) $subdir_name;
		$this->dir = null;
		return $this;
	}

	/**
	 * @return View
	 */
	public function setDirPath($dir_path) {
		$this->dir = null;
		$this->dir_path = (string) $dir_path;
		return $this;
	}

	/**
	 * @return View
	 */
	public function setStrip($flag) {
		$this->strip = (bool) $flag;
		return $this;
	}

	/**
	 * @return View
	 */
	public function setSubdirName($subdir_name) {
		$this->dir = null;
		$this->subdir_name = (string) $subdir_name;
		return $this;
	}

	/**
	 * @return View
	 */
	public function setTemplate($file_name, $subdir_name = '') {
		$this->file = null;
		$this->file_extension = 'tpl';
		$this->file_name = (string) $file_name;
		if ($this->subdir_name !== (string) $subdir_name) {
			$this->setSubdirName($subdir_name);
		}
		return $this;
	}

	// устанавливаем тему извне
	/**
	 * @return View
	 */
	public function setTheme($theme_name) {
		$this->theme_name = $theme_name;
		return $this;
	}

	/**
	 * @return View
	 */
	public function setVar($name, $val) {
		$this->vars[(string) $name] = $val;
		return $this;
	}

	/**
	 * @return View
	 */
	public function setVars($vars) {
		if (is_array($vars)) {
			foreach ($vars as $name => $val) {
				$this->setVar($name, $val);
			}
		}
		return $this;
	}

	// -------------------------------------------------------------------------

	// получем тему
	/**
	 * @return string
	 */
	public function getTheme() {
		if ($this->theme_name === null) {
			return \App\Classes\App::stsService()->val('theme_name');
		}
		
		return $this->theme_name;
	}

	public function getSavedTemplate($name, $default_val = null) {
		return isset($this->saved_templates[(string) $name]) ? $this->saved_templates[(string) $name] : $default_val;
	}

	public function getSavedTemplates() {
		return $this->saved_templates;
	}

	public function render() {
		return $this->renderTemplate();
	}

	public function renderJson() {
		return json_encode($this->renderTemplate());
	}

	public function renderTemplate() {
		if (!$this->file()->exists()) {
			throw new Exception('Шаблон отсутствует [' . $this->file()->path() . ']');
		}
		$output_buffer = new OutputBuffer();
		$output_buffer->open();
		extract($this->vars);
		require $this->file()->path();
		if ($this->strip === true) {
			$data = $this->stripHtml($output_buffer->getData());
		} else {
			$data = $output_buffer->getData();
		}
		$output_buffer->close();
		return $data;
	}

	/**
	 * @return View
	 */
	public function save($name = 'content', $content = null) {
		return $this->saveTemplate($name, $content);
	}

	/**
	 * @return View
	 */
	public function saveTemplate($name = 'content', $content = null) {
		$this->saved_templates[(string) $name] = ($content !== null) ? (string) $content : $this->render();
		return $this;
	}

	// -------------------------------------------------------------------------

	public static function stripHtml($data) {
		return str_replace(["\t", "\n", "\n\r", "\r\n", "  "], ['', '', '', '', ''], $data);
	}
	

	/* Проверка на существование альтернативы в новом стиле. Если есть, меняется dir_patch подгрузки */
    public function setThemeDir() {
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'tovaryplus.ru') !== false){
            $_theme_name = isset($_COOKIE['theme_name']) 
                    ? $_COOKIE['theme_name'] 
                    : \App\Classes\App::stsService()->val('theme_name');
        }
        
		if (isset($_theme_name)){
			$this->dir_path = str_replace('/views_', '/views2', $this->dir_path);
			switch ($_theme_name){
				case 'telemagic':
					$this->dir_path = str_replace('/views2', '/views3', $this->dir_path);
					break;
				default:break;
			}
		} else {
			if (\App\Classes\App::stsService()->val('theme_name') == 'telemagic'){
				$this->dir_path = str_replace('/views_', '/views2', $this->dir_path);
				$this->dir_path = str_replace('/views2', '/views3', $this->dir_path);
			} else {
				$this->dir_path = str_replace('/views_', '/views2', $this->dir_path);
			}
		}

        return $this;
    }
}
