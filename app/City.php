<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends AppModel
{
	protected $table = 'city';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','name','state_id','lang_id','status'];
	
	protected $hidden = [];	
	
	public static function getCityById($id)
	{
		return $city	= City::where('id',$id)->first();	
	}
	public static function insertGetId($data){
		$data = array_merge(['lang_id'=>\Config::get('app.locale_prefix'),'status'=>1],$data);
		self::create($data);
		return \DB::getPdo()->lastInsertId();
	}
}
