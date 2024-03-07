<?php

namespace App\Classes;

class Capcha {

	public function isValid($response) {
		$res = false;
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = [
			'secret' => '6LcIGQcTAAAAAHvlHCPucLRWXR-d8WcKCIm3u7vJ',
			'response' => (string) $response,
			'remoteip' => app()->request()->getRemoteAddr()
		];

		$options = [
			'http' => [
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'POST',
				'content' => http_build_query($data),
			],
		];

		$context = stream_context_create($options);
		$result = json_decode(file_get_contents($url, false, $context));

		if (isset($result->success) && $result->success) {
			$res = true;
		}

		return $res;
	}

	public function render() {

		return "<script>
			$(document).ready(function() {
				grecaptcha.render('recapcha', {
					'sitekey': '6LcIGQcTAAAAAACH0lEII2K4AOdepy7ngyGzteEr'
				});
			});
		</script><div class=\"g-recaptcha\" id=\"recapcha\" data-sitekey=\"6LcIGQcTAAAAAACH0lEII2K4AOdepy7ngyGzteEr\"></div>";
	}

}
