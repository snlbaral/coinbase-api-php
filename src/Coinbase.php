<?php

namespace Snlbaral\Coinbase;

use GuzzleHttp\Client as GuzzleHttpClient;


class Coinbase
{

	private $client;
	private $config;

	function __construct()
	{
		($this->config = @include 'config.php') or die('Configuration file not found');
		$this->client = new GuzzleHttpClient();
	}

	public function getAccessToken()
	{
		$refreshToken = file_get_contents('refreshToken.txt');
		$response = $this->client->Request(
			'POST',
			'https://api.coinbase.com/oauth/token',
			[
				'form_params' => [
					'grant_type' => 'refresh_token',
					'client_id' => $this->config['COINBASE_CLIENT_ID'],
					'client_secret' => $this->config['COINBASE_CLIENT_SECRET'],
					'refresh_token' => $refreshToken,
				],
			]
		);
		$body = $response->getBody()->getContents();
		$body = json_decode($body, true);
		$refreshToken = $body['refresh_token'];
		file_put_contents("refreshToken.txt", $refreshToken);
		return $body['access_token'];
	}

	public function getUser($accessToken)
	{
		$response = $this->client->Request(
			'GET',
			'https://api.coinbase.com/v2/user',
			[
				'headers' => [
					"Authorization" => "Bearer ".$accessToken,
				],
			]			
		);
		return $response->getBody()->getContents();
	}

	public function listAccounts($accessToken)
	{
		$response = $this->client->Request(
			'GET',
			'https://api.coinbase.com/v2/accounts',
			[
				'headers' => [
					"Authorization" => "Bearer ".$accessToken,
				],
			]			
		);
		return $response->getBody()->getContents();		
	}

	public function getAccount($accessToken, $account_id=NULL)
	{
		if($account_id==NULL) {
			$account_id = $this->config["COINBASE_ACCOUNT_ID"];
		}
		$response = $this->client->Request(
			'GET',
			'https://api.coinbase.com/v2/accounts/'.$account_id,
			[
				'headers' => [
					"Authorization" => "Bearer ".$accessToken,
				],
			]			
		);
		return $response->getBody()->getContents();
	}

	public function listAddresses($accessToken)
	{
		$response = $this->client->Request(
			'GET',
			'https://api.coinbase.com/v2/accounts/'.$this->config["COINBASE_ACCOUNT_ID"].'/addresses',
			[
				'headers' => [
					"Authorization" => "Bearer ".$accessToken,
				],
			]			
		);
		return $response->getBody()->getContents();
	}

	public function getAddress($accessToken)
	{
		$response = $this->client->Request(
			'POST',
			'https://api.coinbase.com/v2/accounts/'.$this->config["COINBASE_ACCOUNT_ID"].'/addresses',
			[
				'headers' => [
					"Authorization" => "Bearer ".$accessToken,
				],
			]
		);
		return $response->getBody()->getContents();
	}

	public function listTransactions($accessToken)
	{
		$response = $this->client->Request(
			'GET',
			'https://api.coinbase.com/v2/accounts/'.$this->config["COINBASE_ACCOUNT_ID"].'/transactions',
			[
				'headers' => [
					"Authorization" => "Bearer ".$accessToken,
				],
			]			
		);
		return $response->getBody()->getContents();
	}

	public function getTransaction($accessToken,$transaction_id)
	{
		$response = $this->client->Request(
			'GET',
			'https://api.coinbase.com/v2/accounts/'.$this->config["COINBASE_ACCOUNT_ID"].'/transactions/'.$transaction_id,
			[
				'headers' => [
					"Authorization" => "Bearer ".$accessToken,
				],
			]			
		);
		return $response->getBody()->getContents();
	}

}