<?php

namespace App\Classes;

class RssFeed extends Controller {

	public $file_path;
	public $url_path;
	public $update_interval;
	public $items_to_render_count;
	public $text;
	protected $view = null;

	public function __construct($url_path, $file_path, $update_interval = 1, $items_to_render_count = 10) {
		$this->url_path = $url_path;
		$this->file_path = $file_path;
		$this->update_interval = $update_interval;
		$this->items_to_render_count = $items_to_render_count;
	}

	public function getRss() {
		if ($this->updateRss()) {
			$this->text = file($this->file_path);
			$this->text = implode("", $this->text);
		}
	}

	public function updateRss() {
		if (!file_exists($this->file_path) || (filemtime($this->file_path) + $this->update_interval * 60 * 60 < time())) {
			if (@!copy($this->url_path, $this->file_path)) return false;

			$this->text = file($this->file_path);
			$this->text = implode("", $this->text);

			if (preg_match('/<?xml[^>]+encoding[\s]*=[\s]*("|\')windows-1251("|\')[^>]+?>/i', $this->text)) {
				$this->text = iconv("cp1251", "utf-8", $this->text);
				file_put_contents($this->file_path, $this->text);
			}
		}

		return true;
	}

	public function render() {
		$html = "";
		if ($this->text) {

			$item_matches = array();
			preg_match_all("#<item>.*?</item>#is", $this->text, $item_matches);

			$item = array();

			$items_counter = 0;
			if (sizeof($item_matches) > 0) {
				foreach ($item_matches[0] as $item) {
					$date = "";
					$items_counter++;
					$title_matches = array();
					$link_matches = array();
					$description_matches = array();
					$title_count = preg_match("#<title>(.*?)</title>#is", $item, $title_matches);
					$link_count = preg_match("#<link>(.*?)</link>#is", $item, $link_matches);
					$description_count = preg_match("#<description>(.*?)</description>#is", $item, $description_matches);

					$date_matches = array();
					$date_count = preg_match("#<pubDate>(.*?)</pubDate>#is", $item, $date_matches);

					if ($title_count && $link_count) {
						$title_matches[1] = preg_replace("#<\!\[CDATA\[(.*?)\]\]>#is", "$1", $title_matches[1]);
						$link_matches[1] = preg_replace("#<\!\[CDATA\[(.*?)\]\]>#is", "$1", $link_matches[1]);
						$description_matches[1] = preg_replace("#<\!\[CDATA\[(.*?)\]\]>#is", "$1", $description_matches[1]);

						$image = "";
						$image_matches = array();
						$image_count = preg_match("#<img src=\"([^\"]+)\"#is", $description_matches[1], $image_matches);
						if ($image_count) {
							$image = $image_matches[1];
						}

						if ($date_count) $date = strtotime($date_matches[1]);

						$full_text_matches = array();
						$full_text_count = preg_match("#<div class=\"K2FeedFullText\">(.*?)</div>#eis", $description_matches[1], $full_text_matches);
                        $text = isset($full_text_matches[1]) ? $full_text_matches[1] : '';
						$text = preg_replace("#(<iframe(.*?)</iframe>)#is", "", $text);
						$text = preg_replace("#<([a-z][a-z0-9]*)[^>]*?(\/?)>#is", "<$1$2>", $text);
						$text = preg_replace(array("#([\r\n]{0,})#is", "#(<p>.{0,4}</p>)#is", "#(</{0,1}a>)#is", "#(</{0,1}div>)#is"), "", $text);

						$html .= "<div class=\"news_feed_block\" style=\"border-top: 1px solid #e8e8e8;padding: 15px 0 0 0;margin: 15px 0 0;color: #444444;
                                                    font-size: 12px;line-height: 16px;\"><a rel='nofollow' href='".$link_matches[1]."' target='_blank'>".$title_matches[1]."</a><br/><br/>"
								."<div class=\"clearfix\">"
								."<img src=\"$image\" alt=\"$title_matches[1]\" width=\"20%\" style=\"float: left;padding: 0 10px;\"/>"
								.$text
								.($date ? "<p style=\"text-align: right; color: #df2645; padding-right: 50px;\">".date("d.n.Y", $date)."</p>" : "")
								."</div>"
								."</div>";
					}

					if ($items_counter >= $this->items_to_render_count) break;
				}
			}
		}

		return $this->view()
						->set('elems', $html)
						->setTemplate('rss_feed', 'elem')
						->render();
	}

}
