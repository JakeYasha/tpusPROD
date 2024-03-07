<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8" /></head><body>

<?
        
	if(isset($_GET['link'])) {
		$duration = getYoutubeVideDurationByLink($_GET['link']);
		// PT1H32M10S
		$date = $duration;
		$interval = new DateInterval($date);

		$time = $interval->h > 0 ? $interval->h . ':' : '00:';
		$time .= $interval->i > 0 ? $interval->i . ':' : '00:';
		$time .= $interval->s > 0 ? $interval->s : '00';
		
		echo date($interval->h > 0 ? 'H:i:s' : 'i:s', strtotime($interval->h . ":" . $interval->i . ":" . $interval->s));
	}
		
	function getYoutubeVideoUrlFromLink($link) {
		$pattern = '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/';
		preg_match($pattern, $link, $matches);
		
		$id_match = count($matches) - 1;

		if (isset($matches[$id_match]) && !empty($matches[$id_match]))
			return $matches[$id_match];
		
		return '';
	}

	function getYoutubeVideDurationByLink($link) {
		$key = 'AIzaSyAbRZxC7BgS2fEqqnyi-ph0l6KPacAegMc';
		$part = 'contentDetails';
		
		$id = getYoutubeVideoUrlFromLink($link);
		
		if (empty($id))
			die('No data');
		
		$url = 'https://www.googleapis.com/youtube/v3/videos'
			 . '?id=' . $id 
			 . '&key=' . $key
			 . '&part=' . $part;
		
		$data = '';
		try	{
			$data = file_get_contents($url);
			if (empty($data)) die('No data');
		} catch(Exception $e) {
			die('No data');
		}	
		
		$json = json_decode($data, 1);
		
		if (isset($json['items']) && isset($json['items'][0])){
			$videoDetails = $json['items'][0];
			
			if (isset($videoDetails['contentDetails']) && isset($videoDetails['contentDetails']['duration']))
				return $videoDetails['contentDetails']['duration'];
		}
		die('No data');
	}
?>
    </body>
</html>