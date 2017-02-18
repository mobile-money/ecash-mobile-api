<?php

namespace Mukete;

use Mukete\CurlAgent as CurlAgent;

class ECashMobileAPI {

	private $clientId;

	private $clientSecret;

	private $username;

	private $password;

	private $URL_TOKEN = "http://api.ecashmobile.com/oauth/v2/token";

	private $URL_PAYMENT = "http://api.ecashmobile.com/api/mtn/withdraw";

	private $URL_TEST = "http://api.ecashmobile.com/api/articles";

	private $curlAgent;

	private $accessToken;

	private $refreshToken;

	private $accessTokenFetched;

	private $tokenDate;

	public static $instance = NULL;

	public static function getInstance($clientId, $clientSecret, $username, $password) {
		if (static::$instance === NULL) {
			static::$instance = new ECashMobileAPI();

			static::$instance->clientId = $clientId;
			static::$instance->clientSecret = $clientSecret;
			static::$instance->username = $username;
			static::$instance->password = $password;

			static::$instance->accessToken = null;
			static::$instance->refreshToken = null;
			static::$instance->accessTokenFetched = false;
			static::$instance->tokenDate = time();

			static::$instance->curlAgent = new CurlAgent(static::$instance->URL_TOKEN, static::$instance->URL_PAYMENT, static::$instance->URL_TEST);
		}

		return static::$instance;
	}

	protected function __construct() {
	}

	public function oauthAuthenticate() {
		$currentDate = time();

		if ($this->accessTokenFetched == true) {
			if (static::$instance->tokenDate <= $currentDate) {

				return static::$instance->accessToken;
			} else {

				$instance->curlAgent->setOauthData(Array("client_id" => static::$instance->clientId, "client_secret" => static::$instance->clientSecret, "refresh_token" => static::$instance->refreshToken, "grant_type" => "refresh_token"));

				$response = $this->curlAgent->authenticate();
				$jsonResponse = json_decode($response);

				$developerAccessToken = $jsonResponse->access_token;
				static::$instance->tokenDate = time() + 3600;

				$this->accessTokenFetched = true;
				static::$instance->accessToken = $developerAccessToken;
				static::$instance->refreshToken = $jsonResponse->refresh_token;

				return static::$instance->accessToken;
			}
		} else {
			static::$instance->curlAgent->setOauthData(Array("client_id" => static::$instance->clientId, "client_secret" => static::$instance->clientSecret, "username" => static::$instance->username, "password" => static::$instance->password, "grant_type" => "password"));

			$responseTwo = $this->curlAgent->authenticate();
			$jsonResponseTwo = json_decode($responseTwo);

			$developerAccessToken = $jsonResponseTwo->access_token;
			static::$instance->tokenDate = time() + 3600;

			$this->accessTokenFetched = true;
			static::$instance->accessToken = $developerAccessToken;
			static::$instance->refreshToken = $jsonResponseTwo->refresh_token;

			return static::$instance->accessToken;
		}
	}

	/*
		* Demande de payment adréssé à un client possédant un numéro de téléphone.
		*/
	public function requestPayment($amountToDebited, $customerPhoneNumber) {
		// echo "ACCESS TOKEN IS:" . static::$instance->accessToken;
		static::$instance->curlAgent->setPaymentData(Array("access_token" => static::$instance->accessToken, "amount" => $amountToDebited, "phoneNumber" => $customerPhoneNumber));

		$response = static::$instance->curlAgent->requestPayment(); // Demande de payment.
		$jsonResponse = json_decode($response); // Décodage du json renvoyé.

		return $response;
	}

}
