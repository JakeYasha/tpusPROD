<?php

class TelegramServiceApi {
	private $auth_key = 'f0b2774b-fb77-474d-b547-d29b39492654';
	private $api_url = 'https://xlorspace.ru/bot/';
	private $api_token = null;
	
	public function Send($message) {
		$this->GetAPIToken();
        file_get_contents($this->api_url . "?bot=TovaryplusOnlineLog&action=send&token={$this->api_token}&message=" . urlencode('[API]: ' . $message));
	}
	
	public function GetAPIToken() {
		$this->api_token = file_get_contents($this->api_url . "?format=plain&action=auth&auth_key=" . $this->auth_key);
		return $this->api_token;
	}
}

$la = $argv[2];
$t_api = new TelegramServiceApi();
$t_api->Send($la);
?>