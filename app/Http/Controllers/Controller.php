<?php 

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Auth;
use Session;
use Illuminate\Routing\Route;
use App\UserRestaurant;

abstract class Controller extends BaseController 
{
	use DispatchesCommands, ValidatesRequests;

	public $addressjson = ['house_no', 'floor_unit', 'street', 'area_name', 'city', 'state', 'state_id'];
	
	public function apiResponse($data=[], $error = false)
	{
		$defaultResponseArr = [];
		
		switch($error)
		{
			case true:
			case '1':
			case 'true':
				$defaultResponseArr['success'] = false;				
				break;
			default:
				$defaultResponseArr['success'] = true;
		}

		return \Response::json( array_merge($defaultResponseArr, $data) );
	}

	/*
	* Added by sandeep singh 
	* Date : 16-5-2017
	* convert address to json object of customer delivery address
	*/
	public function convertAddressToJson($address)
	{
		$address['address_json'] = Null;

		if(count($address) > 0)
		{
			foreach($address as $key=>$addv)
			{
				if(in_array($key, $this->addressjson))
				{
					$address['address_json'][$key] = $addv;
					unset($address[$key]);
				}
			}
		}

		$address['address_json'] = json_encode($address['address_json']);
		
		return $address;
	}

	// Get error string thrown by validator
	protected function getError( $validator )
	{
		$messages = [];
		if( $validator->errors() )
		{
			foreach($validator->errors()->getMessages() as $key => $val) {
				$messages[] = implode(' and ', $val);
			}
			
			$message = implode(' and ', $messages);			
			return $message ? $message.'.' : null;
		}

		return null;
	}
}
