<?php

namespace App\Action\Utils;

class UpdateAllFirmVideoLength extends \App\Action\Utils {

	public function __construct() {
		parent::__construct();
		if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
			exit();
		}
	}

	public function execute() {
        $firm_video = new \App\Model\FirmVideo();
        
        $firm_videos = $firm_video->reader()
                ->objects();
        
        foreach ($firm_videos as $firm_video){
            if ($firm_video->val('video_length')) {
                echo $firm_video->id() . ': ' . $firm_video->val('video_length') . '<br/>';
            } else {
                echo $firm_video->id() . ': ' . $firm_video->val('video_length') . '<br/>';
                $duration = $firm_video->isYoutube() ? $firm_video->getYoutubeVideoDuration() :'';
                $firm_video->setVal('video_length', $duration);
                $firm_video->update($firm_video->getVals());
                //var_dump($firm_video->getVals());
                echo $duration . '<br/>';
                //break;
            }
        }
        
		exit();
	}

}
