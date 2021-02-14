# coinbase-api-php

[![Build Status](https://travis-ci.org/snlbaral/coinbase-api-php.svg)](https://travis-ci.org/snlbaral/coinbase-api-php)
[![Total Downloads](https://poser.pugx.org/snlbaral/coinbase-api-php/d/total.svg)](https://packagist.org/packages/snlbaral/coinbase-api-php)
[![Code Coverage](https://codecov.io/gh/snlbaral/coinbase-api-php/badge.svg)](https://codecov.io/gh/snlbaral/coinbase-api-php)
[![License](https://poser.pugx.org/snlbaral/coinbase-api-php/license)](https://packagist.org/packages/snlbaral/coinbase-api-php)

This is an open source library that allows PHP applications to interact programmatically with the <a href="https://developers.coinbase.com/docs/wallet/coinbase-connect">COINBASE CONNECT (OAUTH2)</a>.

Requirements
------------

Using this library for PHP requires the following:

* [Composer][composer] or a manual install of the dependencies mentioned in
  `composer.json`.


Installation
------------

The recommended way to install it PHP is to install it using
[Composer][composer]:

```sh
composer require snlbaral/coinbase-api-php
```


Quick start
-----------

Create an OAuth2 Application. Once created, your application is assigned with ***Client ID***, ***Client secret***.
Once you have a *Redirect URI*, a *Client ID*, and a *Client Secret*, your web application can start using this library by following these steps.
**Warning: *Client Secrets* are similar to passwords or private keys by allowing an application to identify as yours: therefore, *Client Secrets* should be kept private.**

### Step 1: create your configuration

#### `config.php`

```php
<?php

return [
    /**
     * Your coinbase client ID.
     */
    'COINBASE_CLIENT_ID' => '<COINBASE_CLIENT_ID>',

    /**
     * Your coinbase client secret.
     */
    'COINBASE_CLIENT_SECRET' => '<COINBASE_CLIENT_SECRET>',

    /**
     * Your coinbase account id/address.
     */
    'COINBASE_ACCOUNT_ID' => '<COINBASE_ACCOUNT_ID>',
];
```

### Step 2: Create `coinbase_token.php` and add this uri to your application's redirect uri(s).

```php
<?php
session_start();
require 'vendor/autoload.php';
($config = @include 'config.php') or die('Configuration file not found');
$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$client = new GuzzleHttp\Client();

if(isset($_GET['code'])) {
	$code = $_GET['code'];
	$response = $client->Request(
		'POST',
		'https://api.coinbase.com/oauth/token',
		[
			'form_params' => [
				'grant_type' => 'authorization_code',
				'code' => $code,
				'client_id' => $config['COINBASE_CLIENT_ID'],
				'client_secret' => $config['COINBASE_CLIENT_SECRET'],
				'redirect_uri' => $redirect,
			],
		]
	);
	$data = json_decode($response->getBody()->getContents(),TRUE);
	$access_token = $data['access_token'];
	$refresh_token = $data['refresh_token'];
	file_put_contents('refreshToken.txt', $refresh_token);
	$_SESSION['coinbase_token'] = $access_token;
	header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
	return;
}

if(isset($_REQUEST['logout'])) {
	unset($_SESSION['coinbase_token']);
}

if(!isset($_SESSION['coinbase_token'])) {
	echo '<a href="https://www.coinbase.com/oauth/authorize?client_id='.$config['COINBASE_CLIENT_ID'].'&redirect_uri='.$redirect.'&response_type=code&scope=wallet%3Aaddresses%3Acreate,wallet%3Aaddresses%3Aread,wallet%3Anotifications%3Aread" target="_blank">Authorize Me</a>';
} else {
	echo '<h2>Save this access token for later use.</h2>';
	echo 'Access Token: '.$_SESSION['coinbase_token'];
	echo '<br/><a href="?logout">Logout</a>';
}
```

### Step 3: get an OAuth access token

After creating `coinbase_token.php` file and adding this uri to your applications redirect uri(s). Open `coinbase_token.php` and authorize the application.
It will return an access token, save this token to use it in the application.


Usages
----------

**Init**
```php
require 'vendor/autoload.php';
use Snlbaral\Coinbase\Coinbase;

$client = new Coinbase();
$accessToken = "<Access Token from Step 3>";
```

**Get Address**
```php
$address = $client->getAddress($accessToken);
```

**List transactions**
```php
$transactions = $client->listTransactions($accessToken);
```

**Warning: Access Token is only valid for 2 hours. After 2 hours it will return a 401 error. You'll need to get new access token to continue using the service through OAuth2**
```php
$refreshToken = file_get_contents('refreshToken.txt');
$accessToken = $client->getAccessToken($refreshToken); //returns new access token valid for next 2 hours.
```


**Example.php**

```php
try {
	$address = $client->getAddress($accessToken);
	print_r($address);
	//$address = json_decode($address,true);
	//echo $address['data']['address'];
} catch (Exception $e) {

	if($e->getCode()==401) {
		$refreshToken = file_get_contents('refreshToken.txt');
		$accessToken = $client->getAccessToken($refreshToken);
		//Save/Update the this new access token to use it for next 2 hours
		
		//Get Address using new access token
		$address = $client->getAddress($accessToken);
		print_r($address);		
	} else {
		print_r($e->getMessage());
	}

}
```

License
-------

This library for PHP is licensed under the [3-Clause
BSD License][bsd-3-clause].

Credits
-------

This library for PHP is developed and maintained by Sunil Baral.
