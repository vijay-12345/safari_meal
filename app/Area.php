<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Area extends AppModel
{
	protected $table = 'area';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['id','city_id','url_alias','name','lang_id','latitude','longitude'];
	
	protected $hidden = [];		
	
	public function city()
	{
		return $this->hasOne('App\City','id','city_id');
	}
	public static function getAreaById($id)
	{
		return $city	= Area::where('id',$id)->first();	
	}
	/*
	* update lat and long of restaurant
	*/
	public static function updateLatLong($areaid){
		$areaObj = self::findOrFail($areaid);
		//pr($areaObj);		
		$data = [];
		$data['street'] = $areaObj->name;		
		$address = str_replace(",", " ", $data['street']);	
		if(!empty($areaObj->city->name)){
			$data['city_name'] = $areaObj->city->name;
			$address .= ', '.$data['city_name'];
		}					
		$latLongArr = getLatLong($address);			
		if(isset($latLongArr['long']) && isset($latLongArr['lat'])){
			$areaObj->fill(['longitude'=>$latLongArr['long'],'latitude'=>$latLongArr['lat']])->save();	
			return true;
		}else{
			$areaObj->delete();	
			return false;					
		}
		

	}
	/* insert area */
	public static function insertGetId($data){
		$data = array_merge(['lang_id'=>\Config::get('app.locale_prefix')],$data);
		self::create($data);
		$area_id =  \DB::getPdo()->lastInsertId();
		$slug_name = str_slug($data['name'], "-");
		self::where('id',$area_id)->update(['url_alias'=>$slug_name.'-'.$area_id]);
		self::updateLatLong($area_id);
		return $area_id;
	}	
}
?>
