<?php

class Smartpromotion{
	public function checkPromotion($module = '',$page = ''){
		$this->checkUpdate();
		$url = 'http://promo.smartdatasoft.net/promotion/promotion.php?module='.$module.'&page='.$page;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		curl_close($curl);
		$html = htmlentities($result);
		$result = array(
			'content' => $html
		);
		$serialize_result = serialize($result);
		return $serialize_result;
	}

	public function checkUpdate(){
		$installed_version = '3.0.0';
		$timeout = Configuration::get('smartblog_update_timeout', false);
		$now = time();
		if($now > (int)$timeout) {
			$timelimit = 30 * 60*60;
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => 'http://updates.smartdatasoft.net/check_for_updates.php',
				CURLOPT_USERAGENT => 'Smartdatasoft cURL Request',
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => array(
					'purchase_key' => '',
					'operation' => 'check_update',
					'domain' => $_SERVER['HTTP_HOST'],
					'module' =>  'smartblog',
					'version' => $installed_version,
					'theme_name' => basename(_THEME_DIR_),
				)
			));

			$resp = curl_exec($curl);
			curl_close($curl);

			$respAarray = (array) Tools::jsonDecode($resp);

		}
	}

}