<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Area; 
use App\City;
use App\State;
use App\Country;

class UserAddress extends AppModel
{
	protected $table = 'user_address';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	protected $hidden = [];

	protected $fillable = ['user_id', 'first_address', 'second_address', 'city_id', 'state_id', 'zip', 'country_id', 'area_id', 'landmark', 'address_json', 'longitude', 'latitude'];
	
	public function area(){
		return $this->belongsTo('App\Area','area_id','id');
	}

	public function city(){
		return $this->belongsTo('App\City','city_id','id');
	}

	public function state(){
		return $this->belongsTo('App\State','state_id','state_id');
	}

	public function country(){
		return $this->belongsTo('App\Country','country_id','country_id');
	}

	public function user(){
		return $this->belongsTo('App\User','user_id','id');
	}		

     /**
     * Get the city record associated with the address.
     */
    public static function getCityById($id)
	{
		return $city	= City::where('id',$id)->first();	
	}

	/*
	* update lat and long of restaurant
	*/
	public static function updateLatLong($addressid)
	{
		$addObj = self::findOrFail($addressid);
		$data = [];
		$data['street'] = $addObj->first_address;		
		$address = str_replace(",", " ", $data['street']);		
		
		if(!empty($addObj->area->name)){
			$data['area_name'] = $addObj->area->name;
			$address .= ', '.$data['area_name'];
		}

		if(!empty($addObj->city->name)){
			$data['city_name'] = $addObj->city->name;
			$address .= ', '.$data['city_name'];
		}

		if(!empty($addObj->state->state_name)){
			$data['state_name'] = $addObj->state->state_name;
			$address .= ', '.$data['state_name'];
		}

		if(!empty($addObj->country->country_name)){
			$data['country_name']  = $addObj->country->country_name;
			$address .= ', '.$data['country_name'];
		}

		$latLongArr = getLatLong($address);	
		$addObj->fill(['longitude'=>$latLongArr['long'],'latitude'=>$latLongArr['lat']])->save();
	}
	
	public static function getJsonAddr($data) {
	
		$tmp = [];
		$tmp['area_name'] = Area::find($data['area_id'])->name;
		$tmp['city'] = City::find($data['city_id'])->name;	
		$data['floor_unit'] = $tmp['house_no'] = $data['second_address'];	
		$tmp['state_id'] = State::find($data['state_id'])->state_name;		
		$tmp['street'] = $data['zip'];			
		
		return json_encode($tmp);	
	}
}

