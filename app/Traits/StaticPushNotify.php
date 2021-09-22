<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;

trait StaticPushNotify {

	/**
	* @var Object
	*/
	private static $receivers = [];

	/**
	* @var Object
	*/
	private static $headers;

	/**
	* @var Object
	*/
	private static $apiAccessKey = null;

	/**
	* @var Object
	*/
	private static $notificationResponse = ['success' => false, 'message' => null];

	/**
	* @var Object
	*/
	public static $driverMessages = [];

	/**
	* @var Object
	*/
	public static $customerMessages = [];

	/**
	* @var Object
	*/
	//private static $apiUrl = 'https://android.googleapis.com/gcm/send';

	private static $apiUrl = 'https://fcm.googleapis.com/fcm/send';

	// Constructor..
	function __construct()
	{
		// API access key from Google API's Console
		// define( 'API_ACCESS_KEY', 'AAAA6LvVRuE:APA91bG6XAfJZxEgkzO_7eRp_UxRO32iF4u_fWz_7gGRRhglACsj2KKabXNV1G3XuD11IaJa1iOeAPvf2yEy8CZFFHDUqGs-Y7Qlt-nV6WT1KhNSwMWuAz1b8vatpi7NVLt-T2zfuqq1' );
	}


	public static function getHeaders()
	{
		return [
			'Authorization: key=' . self::$apiAccessKey,
			'Content-Type: application/json'
		];
	}

	public static function setAndroidApiAccessKey($key)
	{
		self::$apiAccessKey = $key;
	}

	public static function getAndroidApiAccessKey()
	{
		return self::$apiAccessKey;
	}

	// Modify the push message
	public static function setReceivers(Array $receivers)
	{
		self::$receivers = $receivers;
	}

	// Get receivers list
	public static function getReceivers()
	{
		return self::$receivers;
	}

	// Get final message to be sent
	public static function getDriverMessage()
	{
	/*	return [
				'title'				=> 'New order created.',
				'message' 			=> 'Pickup from rest-area and drop at delivery-location.',
				'subtitle'			=> 'Subtitle: An order has created. Accept request?',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'driver',
				'notificationID'	=> uniqid()
		];*/
	}

	// Get final message to be sent
	public static function getCustomerMessage()
	{
		/*return [
				'message' 			=> 'Customer Message',
				'title'				=> 'Title: An order has created. Accept request?.',
				'subtitle'			=> 'Subtitle: An order has created. Accept request?',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'customer',
				'notificationID'	=> uniqid()
		];*/
	}

	// Send notification   // vijayanand

	
	public static function notify($messageType = false)
	{
		try 
		{
			if ($messageType) $message = self::$driverMessages;
			else $message = self::$customerMessages;

			if( is_null( self::getAndroidApiAccessKey() ) ) 
				throw new Exception('You need to set Api key to send push notifications', 1);

			// Get receivers list
			$receivers = self::getReceivers();
			// $token = array('fiAizpKjxAg:APA91bEv1_UT2fGPSXMF0ycWsQtwFRurAXrgNq6AREappwgEVsZItn0JgHXMjWTVtAJad-IUK-70QmYc7PS7ECByPCVm7n7w0EIv3WyTBY3oDGA6hGHNJE23YLXXlakunhuwbkVmT_Vw');
			
			// $receivers=$token;
			// print_r($receivers);die;
			if( empty( $receivers ) ) throw new Exception('At least one receiver required to send notification.', 1);


			if( is_array($receivers) ) {
                $data = array(
                	'registration_ids' => $receivers, 
                	'notification' => $message,
                	'data' => $message
                );
            } else {
                $data = array(
                	'to' => $receivers, 
                	'notification' => $message,
                	'data' => $message
                );
            }

            // $headers = [
            //     'Authorization: key=AIzaSyAXAaRO2yVIUpKwj12gveHRuoqIq435kpQ',
            //     'Content-Type: application/json'
            // ];

			// $ch = curl_init();
			// curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
			// curl_setopt($ch, CURLOPT_POST, true );
			// curl_setopt($ch, CURLOPT_HTTPHEADER, self::getHeaders() );
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
			// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
			// $response = curl_exec($ch);
			// self::$notificationResponse['success'] = true;
			// self::$notificationResponse['message'] = $response;
			// curl_close( $ch );
			// return $response;

			$ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, self::getHeaders() );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $data ) );
            $response = curl_exec($ch );
            
            self::$notificationResponse['success'] = true;
			self::$notificationResponse['message'] = $response;
            curl_close( $ch );

            return $response;


		} catch (Exception $e) {
			self::$notificationResponse['message'] = $e->getMessage();
		}

		return self::$notificationResponse;
	}

}