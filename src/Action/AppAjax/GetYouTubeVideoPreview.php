<?php

namespace App\Action\AppAjax;

class GetYouTubeVideoPreview extends \App\Action\AppAjax {
    public $video_url;

	public function execute() {
        if (app()->firmManager()) {
            $params = app()->request()->processGetParams([
                'url' => ['url' => 'string']
            ]);

            if ($this->isYouTubeVideo($params['url'])) {
                $this->video_url = $params['url'];
                die($this->getThumbnailSrc());
            }
        }
        die('');
	}
    
    private function isYouTubeVideo($url) {
        $url_parsed_arr = parse_url($url);
		return isset($url_parsed_arr['host']) && in_array($url_parsed_arr['host'], ['www.youtube.com', 'youtube.com', 'www.youtu.be', 'youtu.be']);
	}
    
    private function getYoutubeHash() {
		$result = FALSE;

        if (preg_match("~youtube.com~", $this->video_url)) {
            preg_match_all("~v=([a-zA-Z0-9-_]*)~", $this->video_url, $matches);
            $result = isset($matches[1][0]) ? $matches[1][0] : FALSE;
        } else if (preg_match("~youtu.be~", $this->video_url)) {
            preg_match_all("~be/([a-zA-Z0-9-_]*)~", $this->video_url, $matches);
            $result = isset($matches[1][0]) ? $matches[1][0] : FALSE;
        }

		return $result;
	}
    
    private function getThumbnailSrc() {
		echo $this->getYoutubeHash() !== false 
                ? 'https://img.youtube.com/vi/' . $this->getYoutubeHash() . '/mqdefault.jpg'
                : '/css/img/no-img-video.png';
	}

}
