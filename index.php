<?php

require 'vendor/autoload.php';

$b = new Coinbase();

try {
	$t = $b->getAddress('1d5b727757654f4f4b1c6adea995ab39212450e204f9142c895711251d71b7a8');
	print_r($t);
	//$t = json_decode($t,true);
	//echo $t['data']['address'];
} catch (Exception $e) {
	print_r($b->getAccessToken());
}