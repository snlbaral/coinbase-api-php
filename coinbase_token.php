<?php
session_start();
($config = @include 'config.php') or die('Configuration file not found');
$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
if(isset($_GET['code'])) {
	$code = $_GET['code'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.coinbase.com/oauth/token");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
	            "grant_type=authorization_code&code=".$code."&client_id=".$config['COINBASE_CLIENT_ID']."&client_secret=".$config['COINBASE_CLIENT_SECRET']."&redirect_uri=".$redirect);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	curl_close ($ch);
	$data = json_decode($server_output,TRUE);
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