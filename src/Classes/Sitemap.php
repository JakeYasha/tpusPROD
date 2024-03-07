<?php

namespace App\Classes;

use SimpleXMLElement;

class Sitemap {

	protected $changefreqs = [];
	protected $dir_path = '';
	public $file_name = 'sitemap';
	protected $file_name_postfix = '-';
	public $index_file_name = 'sitemap';
	protected $loc = '';
	protected $sitemaps = [];
	protected $sitemaps_count = 0;
	protected $structure = null;
	protected $total_urls_count = 0;
	protected $urls_count = 0;
	protected $urls_count_limit = 50000;
	protected $use_gz = false;

	public function __construct() {
		$this->changefreqs = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
		$this->activateStructure();
		return true;
	}

	public function addUrl($section, $changefreq = null, $lastmod = null, $priority = null) {
		if ($this->urls_count >= $this->urls_count_limit) {
			$this->saveStructure()
					->reactivateStructure();
			$this->urls_count = 0;
		}
		$url = $this->structure->addChild('url');
		$url->addChild('loc', $this->loc . (string) $section);
		if (($changefreq !== null) && isset($this->changefreqs[(string) $changefreq])) {
			$url->addChild('changefreq', (string) $changefreq);
		}
		if (($lastmod !== null) && (preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', (string) $lastmod))) {
			$url->addChild('lastmod', (string) $lastmod);
		}
		if ($priority !== null) {
			$priority = (double) $priority;
			if (($priority >= 0) && ($priority <= 1)) {
				$url->addChild('priority', round($priority, 1));
			}
		}
		$this->urls_count++;
		return $this;
	}

	public function getStats() {
		return [
			'sitemaps_count' => $this->sitemaps_count,
			'total_urls_count' => $this->total_urls_count
		];
	}

	public function save() {
		$this->saveStructure(true)
				->deactivateStructure();
		if ($this->sitemaps) {
			$this->structure = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><sitemapindex></sitemapindex>');
			$this->structure->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			foreach ($this->sitemaps as $_sitemap) {
				$sitemap = $this->structure->addChild('sitemap');
				$sitemap->addChild('loc', $_sitemap['loc']);
				$sitemap->addChild('lastmod', $_sitemap['lastmod']);
			}
			$this->structure->asXML($this->dir_path . '/' . $this->index_file_name . '.xml');
		}
		return $this;
	}

	public function setDirPath($dir_path) {
		$this->dir_path = (string) $dir_path;
		return $this;
	}

	public function setFileName($file_name) {
		$this->file_name = (string) $file_name;
		return $this;
	}

	public function setFileNamePostfix($file_name_postfix) {
		$this->file_name_postfix = (string) $file_name_postfix;
		return $this;
	}

	public function setIndexFileName($index_file_name) {
		$this->index_file_name = (string) $index_file_name;
		return $this;
	}

	public function setLoc($loc) {
		$this->loc = (string) $loc;
		return $this;
	}

	public function setUrlsCountLimit($limit) {
		$this->urls_count_limit = (int) $limit;
		return $this;
	}

	public function setUseGz($val) {
		$this->use_gz = $val ? true : false;
		return $this;
	}

	// -------------------------------------------------------------------------

	protected function activateStructure() {
		$this->structure = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><urlset></urlset>');
		$this->structure->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		return $this;
	}

	protected function deactivateStructure() {
		$this->structure = null;
		return $this;
	}

	protected function reactivateStructure() {
		return $this->deactivateStructure()
						->activateStructure();
	}

	protected function saveStructure($mode = false) {
		$this->total_urls_count += $this->urls_count;
		if ($this->use_gz) {
			if ($mode && !$this->sitemaps_count) {
				$file = $this->dir_path . '/' . $this->file_name . '.xml.gz';
			} else {
				$this->sitemaps_count++;
				$file = $this->dir_path . '/' . $this->file_name . $this->file_name_postfix . (string) $this->sitemaps_count . '.xml.gz';
				$this->sitemaps[] = array(
					'lastmod' => date('Y-m-d'),
					'loc' => $this->loc . '/' . $this->file_name . $this->file_name_postfix . (string) $this->sitemaps_count . '.xml.gz'
				);
			}
			$gz_file_desc = gzopen($file, 'w5');
			gzwrite($gz_file_desc, $this->structure->asXML());
			gzclose($gz_file_desc);
		} else {
			if ($mode && !$this->sitemaps_count) {
				$file = $this->dir_path . '/' . $this->file_name . '.xml';
			} else {
				$this->sitemaps_count++;
				$file = $this->dir_path . '/' . $this->file_name . $this->file_name_postfix . (string) $this->sitemaps_count . '.xml';
				$this->sitemaps[] = array(
					'lastmod' => date('Y-m-d'),
					'loc' => $this->loc . '/' . $this->file_name . $this->file_name_postfix . (string) $this->sitemaps_count . '.xml'
				);
			}
			$this->structure->asXML($file);
		}
		return $this;
	}

}
