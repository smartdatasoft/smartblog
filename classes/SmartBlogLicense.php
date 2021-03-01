<?php

if (!defined('_PS_VERSION_')) {
	exit;
}


class SmartBlogLicense
{

	private $product_id = 24671;
	private $store_url  = 'https://classydevs.com/';

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct($scheduled = 1)
	{
		// Configuration::updateValue('SMARTBLOG_DLINK', "");

		if ($scheduled) {
			$purchase_code = Configuration::get('SMARTBLOG_LICENSE');
			$todate        = Configuration::get('SMARTBLOG_LICENSE_DATE');
			$stable = Configuration::get('SMARTBLOG_STABLE');
			$d_link = Configuration::get('SMARTBLOG_DLINK');

			if (isset($stable) && isset($d_link)) {

				if ($stable == '' && $d_link == '') {
					$today = date('Y-m-d');
					if ( $today > $todate ) {
						Configuration::updateValue('SMARTBLOG_LICENSE_DATE', $today);
						$this->smartblog_get_update($purchase_code);
					}
				} else {

					$this->show_notification($stable, $d_link);
				}
			}
		}
	}


	public function smartblog_get_update($key, $scheduled = 1)
	{
		$api_params = array(
			'edd_action' => 'get_version',
			'item_id'    => $this->product_id,
			'license'    => $key,
			'version'    => _MODULE_SMARTBLOG_VERSION_,
			'url'        => _PS_BASE_URL_SSL_,
		);
		$url        = $this->store_url . '?' . http_build_query($api_params);

		$response = $this->wp_remote_get(
			$url,
			array(
				'timeout' => 20,
				'headers' => '',
				'header'  => false,
				'json'    => true,
			)
		);

		$responsearray = Tools::jsonDecode($response, true);
		if (version_compare($responsearray['stable_version'], _MODULE_SMARTBLOG_VERSION_, '>')) {
			$d_link = $responsearray['download_link'];
			Configuration::updateValue('SMARTBLOG_STABLE', $responsearray['stable_version']);
			Configuration::updateValue('SMARTBLOG_DLINK', $d_link);
			if ($scheduled) {
				$this->show_notification($responsearray['stable_version'], $d_link);
			} else {
				return $d_link;
			}
		}
	}

	public function smartblog_activate_license($key)
	{
		$api_params = array(
			'edd_action' => 'activate_license',
			'item_id'    => $this->product_id,
			'license'    => $key,
			'url'        => _PS_BASE_URL_SSL_,
		);

		$url = $this->store_url . '?' . http_build_query($api_params);

		$response = $this->wp_remote_get(
			$url,
			array(
				'timeout' => 20,
				'headers' => '',
				'header'  => false,
				'json'    => true,
			)
		);

		$responsearray = Tools::jsonDecode($response, true);

		if ($responsearray['success'] && $responsearray['license'] == 'valid') {
			Configuration::updateValue('SMARTBLOG_LICENSE_DATE', "");
			return true;
		} else {
			return false;
		}
	}

	public function smartblog_deactivate_license($key)
	{
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'item_id'    => $this->product_id,
			'license'    => $key,
			'url'        => _PS_BASE_URL_SSL_,
		);

		$url = $this->store_url . '?' . http_build_query($api_params);

		$response = $this->wp_remote_get(
			$url,
			array(
				'timeout' => 20,
				'headers' => '',
				'header'  => false,
				'json'    => true,
			)
		);

		$responsearray = Tools::jsonDecode($response, true);
		if ($responsearray['success'] && $responsearray['license'] == 'deactivated') {
			Configuration::updateValue('SMARTBLOG_STABLE', "");
			Configuration::updateValue('SMARTBLOG_DLINK', "");
			return true;
		} else {
			return false;
		}
	}

	private function wp_remote_get($url, $args = array())
	{
		return $this->getHttpCurl($url, $args);
	}


	private function getHttpCurl($url, $args)
	{
		global $wp_version;
		if (function_exists('curl_init')) {
			$defaults = array(
				'method'      => 'GET',
				'timeout'     => 30,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(
					'Authorization'   => 'Basic ',
					'Content-Type'    => 'application/x-www-form-urlencoded;charset=UTF-8',
					'Accept-Encoding' => 'x-gzip,gzip,deflate',
				),
				'body'        => array(),
				'cookies'     => array(),
				'user-agent'  => 'Prestashop' . $wp_version,
				'header'      => true,
				'sslverify'   => false,
				'json'        => false,
			);

			$args         = array_merge($defaults, $args);
			$curl_timeout = ceil($args['timeout']);
			$curl         = curl_init();
			if ($args['httpversion'] == '1.0') {
				curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			} else {
				curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			}
			curl_setopt($curl, CURLOPT_USERAGENT, $args['user-agent']);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $curl_timeout);
			curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, 'api=true');
			$ssl_verify = $args['sslverify'];
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, ($ssl_verify === true) ? 2 : false);
			$http_headers = array();
			if ($args['header']) {
				curl_setopt($curl, CURLOPT_HEADER, $args['header']);
				foreach ($args['headers'] as $key => $value) {
					$http_headers[] = "{$key}: {$value}";
				}
			}
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
			if (defined('CURLOPT_PROTOCOLS')) { // PHP 5.2.10 / cURL 7.19.4
				curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
			}
			if (is_array($args['body']) || is_object($args['body'])) {
				$args['body'] = http_build_query($args['body']);
			}
			$http_headers[] = 'Content-Length: ' . strlen($args['body']);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($curl);
			if ($args['json']) {
				return $response;
			}
			$header_size    = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$responseHeader = substr($response, 0, $header_size);
			$responseBody   = substr($response, $header_size);
			$error          = curl_error($curl);
			$errorcode      = curl_errno($curl);
			$info           = curl_getinfo($curl);
			curl_close($curl);
			$info_as_response            = $info;
			$info_as_response['code']    = $info['http_code'];
			$info_as_response['message'] = 'OK';
			$response                    = array(
				'body'     => $responseBody,
				'headers'  => $responseHeader,
				'info'     => $info,
				'response' => $info_as_response,
				'error'    => $error,
				'errno'    => $errorcode,
			);
			return $response;
		}
		return false;
	}

	private function show_notification($v, $d)
	{
		$msg = 'There is a new version of SmartBlog is available.';

?>
		<div class="row">
			<div class="col-lg-12">
				<div class="update-content-area">
					<div class="update-ajax-loader" style="display:none">
						<div class="lds-dual-ring"></div>
					</div>
					<div class="update-logo-and-text">
						<img src="<?php echo _MODULE_SMARTBLOG_IMAGE_URL_ . 'module_logo.png'; ?>" width="90" height="90">
						<div class="update-header-text-and-version">
							<h4 class="update_msg"><?php echo $msg; ?></h4>
							<h6 class="update_vsn"><?php echo 'New Version: ' . $v; ?></h6>
						</div>
					</div>
					<?php
					if ($d == '') {
						$validity = Configuration::get('SMARTBLOG_LICENSE_VALIDITY');
						if ($validity == 'valid') {
					?>
							<a href="javascript:void(0)" id="classy_update_bt" data-down_vs="<?php echo $v; ?>" data-down_url="<?php echo $d; ?>" class="btn btn-primary classy-update-bt"><?php echo 'Click to Generate The Update Link'; ?></a>
						<?php
						} else {
							$link      = new Link();
							$activate_link = $link->getAdminLink('AdminModules') . '&configure=smartblog';
						?>
							<a href="<?php echo $activate_link; ?>" class="btn btn-primary classy-update-bt"><?php echo 'Click to Activate Your SmartBlog to Update'; ?></a>
						<?php
						}
					} else {
						?>
						<a href="javascript:void(0)" id="classy_update_bt" data-down_vs="<?php echo $v; ?>" data-down_url="<?php echo $d; ?>" class="btn btn-primary classy-update-bt"><?php echo 'Update To <strong>Version ' . $v . '</strong>'; ?></a>
					<?php
					}
					?>
				</div>
			</div>
		</div>
<?php
	}

	public static function init()
	{
		new SmartBlogLicense();
	}
}
