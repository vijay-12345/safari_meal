<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'App\User',
		'secret' => '',
	],
	
	/*'facebook' => [
    'client_id' => '127658067955152',
    'client_secret' => '7906afe5210266b908a3e7869e738139',
   	'redirect' => 'http://safarimeals.com/fbredirect'     
	]*/
	
	'facebook' => [
    'client_id' => '557653537935491',
    'client_secret' => '4caa3317b86d38b29211c433eb5461ef',
   	'redirect' => 'http://safarimeals.com/fbredirect'     
	]
	

];
