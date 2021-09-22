<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;

trait PushNotify {

	/**
	* @var Object
	*/
	private $receivers = [];

	/**
	* @var Object
	*/
	private $headers;

	/**
	* @var Object
	*/
	private $apiAccessKey = null;

	/**
	* @var Object
	*/
	private $notificationResponse = ['success' => false, 'message' => null];

	/**
	* @var Object
	*/
	//private $apiUrl = 'https://android.googleapis.com/gcm/send';

	private $apiUrl = 'https://fcm.googleapis.com/fcm/send';

	/**
	* @var Object
	*/
	private $message = [
		'message' 		=> 'This is message',
		'title'			=> 'This is title',
		'subtitle'		=> 'This is subtitle.',
		'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
		'vibrate'		=> 1,
		'sound'			=> 1,
		'largeIcon'		=> 'large_icon',
		'smallIcon'		=> 'small_icon'
	];

	// Constructor..
	function __construct()
	{
		// API access key from Google API's Console
		// define( 'API_ACCESS_KEY', 'AAAA6LvVRuE:APA91bG6XAfJZxEgkzO_7eRp_UxRO32iF4u_fWz_7gGRRhglACsj2KKabXNV1G3XuD11IaJa1iOeAPvf2yEy8CZFFHDUqGs-Y7Qlt-nV6WT1KhNSwMWuAz1b8vatpi7NVLt-T2zfuqq1' );
	}


	public function getHeaders()
	{
		return [
			'Authorization: key=' . $this->apiAccessKey,
			'Content-Type: application/json'
		];
	}

	public function setAndroidApiAccessKey($key)
	{
		$this->apiAccessKey = $key;
	}

	public function getAndroidApiAccessKey()
	{
		return $this->apiAccessKey;
	}

	// Modify the push message
	public function setReceivers(Array $receivers)
	{
		$this->receivers = $receivers;
	}

	// Get receivers list
	public function getReceivers()
	{
		return $this->receivers;
	}

	// Modify the push message
	public function setMessage(Array $message)
	{
		$this->message = $message;
	}

	// Get final message to be sent
	public function getMessage()
	{
		return $this->message;
	}

	// Send notification to users
	public function notify()
	{
		try 
		{
			if( is_null( $this->getAndroidApiAccessKey() ) ) 
				throw new Exception('You need to set Api key to send push notifications', 1);

			// Get receivers list
			$receivers = $this->getReceivers();

			if( empty( $receivers ) ) throw new Exception('At least one receiver required to send notification.', 1);

			$data = [
				'registration_ids' 	=> $receivers,
				'data'				=> $this->getMessage()
			];

			$ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, self::getHeaders() );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $data ) );
            // $result = curl_exec($ch );

			// $ch = curl_init();
			// curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
			// curl_setopt($ch, CURLOPT_POST, true );
			// curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders() );
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
			// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
			$response = curl_exec($ch);

			$this->notificationResponse['success'] = true;
			$this->notificationResponse['message'] = $response;
			curl_close( $ch );

		} catch (Exception $e) {
			$this->notificationResponse['message'] = $e->getMessage();
		}

		return $this->notificationResponse;
	}

}