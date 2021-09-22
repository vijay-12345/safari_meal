<?php
	
	use App\Setting;

	function pr($arr) {
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
	}

	function prd($arr) {
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
		die;
	}

	// Print array and exit
	function ddd( $arguments = null, $first_param = null )
	{
	    echo '<pre>';

	    if ($first_param) print($first_param);
	    echo '<br>';
	    if (is_string($arguments) || is_float($arguments) || is_integer($arguments) || is_null($arguments)) print($arguments);
	    else if (is_array($arguments)) print_r($arguments);
	    else print_r($arguments->toArray());
	    die;
	}

	function format($amount) {
		if($amount<=0) {
			return 'Free';
		} else {
			return Config::get('settings.currency').$amount;
		}	
	}

	/*
	* return data for Order monthly Chart
	*/
	function getMonthlyChartData($data){
		$valueArr = [0,0,0,0,0,0,0,0,0,0,0,0];
		foreach($data as $key=>$v) {
			$valueArr[$v->month] = $v->amount;
		}	
		return json_encode($valueArr);
	}

	/*
	* return data for Order status  pie Chart

	* '1'=>'Pending' 			//Dark Grey
	'2'=>'Open for drivers' 	// Grey
	'3'=>'Accepted by driver', //Yellow
	'4'=>'Dispatched', 		//Green
	'5'=>'Delivered', 		//Dark Green
	'6'=>'Cancelled' 		//Red
	    
	*/

	function getPieChartData($data) {
		$pieData = [0,0,0,0,0,0];
		foreach($data as $key=>$v) {
			if($v->status == 0 || empty($v->status)) continue;
			$pieData[$v->status-1] = $v->numberOfOrders;
		}
		//prd(json_encode($pieData));	
		return json_encode($pieData);
	}

	function getLatLong($address) {
	    $address = str_replace(" ", "+", $address);
	    $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
	    $json = json_decode($json);   
	    if($json->{'status'} == 'OK'){
		    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
		    return ['lat'=>$lat,'long'=>$long];   	
	    } else {
	    	return false;
	    }
	}

	/* when order type is pick up then label */
	function orderTypeLabel($type = 'delivery') {
		$orderStatusArr = \Config::get('constants.order_status_label.admin');
		if($type !='delivery') {
			unset($orderStatusArr[2]);
			unset($orderStatusArr[3]);
			unset($orderStatusArr[4]);
			$orderStatusArr[5] = 'Picked Up';
		}
		return $orderStatusArr;
	}

	function __getTimeZoneByLatLong($lat,$lng){ 

        $api_key = config('constants.google_timezone_api_key');

        $url = "https://maps.googleapis.com/maps/api/timezone/json?location=$lat,$lng&timestamp=1331161200&key=$api_key";

        $url = str_replace('&amp;','&',$url); 

        $chin = curl_init(); 

        curl_setopt($chin, CURLOPT_URL, $url); 
        curl_setopt ($chin, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($chin, CURLOPT_SSL_VERIFYPEER, false);
        $json_output = curl_exec ($chin); 
        curl_close ($chin);
        $timez = json_decode($json_output);  
        return $timez->timeZoneId;
    }
    
    function __getTimeZoneByIP()
    {
        $ipInfo = file_get_contents('http://ip-api.com/json/' . $_SERVER['REMOTE_ADDR']);
        
        $ipInfo = json_decode($ipInfo);

        if($ipInfo->status !== 'fail') return $ipInfo->timezone;
        
        else return 'Europe/Paris';
    }
    
  	
    // __dateToTimezone('Asia/Calcutta', '2019-03-26 11:15:00','Y-m-d H:i:s');
    function __dateToTimezone($timeZone, $dateTime = null, $dateFormat = 'Y-m-d H:i:s')
    {
        // if(empty($timeZone)) $timeZone = 'UTC';
        
        $setting = Setting::select('timezone')->first();
        
        if($setting) $timeZone = $setting->timezone;
        
        else $timeZone = 'Asia/Kolkata';
        
        $dateTime = $dateTime ? $dateTime : date("Y-m-d H:i:s");
        
        $date = new DateTime($dateTime, new \DateTimeZone(config('app.timezone')));
        // $date = new DateTime($dateTime, new \DateTimeZone("Europe/Paris"));
        
        $date->setTimeZone(new \DateTimeZone($timeZone));
        
        if($dateFormat == 'iso') return $date->format('c');
        
        else return $date->format($dateFormat);
    }

    function getSecondsFromHMS($time) {
        $timeExploded = explode(':', $time);
        if (isset($timeExploded[2])) {
            return $timeExploded[0] * 3600 + $timeExploded[1] * 60 + $timeExploded[2];
        }
        return $timeExploded[0] * 3600 + $timeExploded[1] * 60;
    }

    function __toDecimalRates( $value )
    {
        $response = '';
        if ( $value ) $response = number_format($value, 2, '.', '');
        return $response;
    }


?>