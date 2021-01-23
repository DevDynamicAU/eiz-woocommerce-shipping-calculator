<?php
/*
Class: Eiz_Check_API
Author: DevDynamic
Original Developer: Zeta Digital
Version: 0.1.0
Author URI: https://devdynamic.com.au
*/

if (!defined('WPINC')) {
	die;
}

if (!class_exists('Eiz_Check_API')) {
	class Eiz_Check_API {
		private static $accessToken = '';
		private static $api_url = "https://app.eiz.com.au/api/auth/woocommerce/APPCheck";

		/**
	 	 * @param null $token
		 * @throws Exception
		*/
		public static function init($token = null) {
			// todo get this function from a common file, as its repeated
			if (!is_null($token)) {
				self::$accessToken = trim(esc_attr($token), " ");
			} else {
				$token_option_name = 'eiz_access_token_' . get_current_network_id();
				self::$accessToken = trim(esc_attr(get_option($token_option_name)), " ");
			}

			if (self::$accessToken == '') {
				throw new Exception('Missing Access Token!');
			}
		}

		public static function check() {
			$url = self::$api_url.'?version=0.1.0';
			$response = wp_remote_post($url, array(
				'headers' => array(
					'Authorization' => 'Bearer ' . self::$accessToken
				),
				'method' => 'GET',
				'timeout' => 25
			));
			
			if (is_wp_error($response)) {
				$error_message = $response->get_error_message();
				if ($error_message == 'fsocket timed out') {
					throw new Exception("Sorry, the couriers are currently unavailable, please refresh the page or try again later");
				} else {
					throw new Exception("Sorry, something went wrong with the couriers. If the problem persists, please contact us!");
				}
			} else {
				$body = json_decode($response['body']);
				return $body;
			}

			return array(); 
		}
	}
}