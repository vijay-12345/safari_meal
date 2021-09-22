<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends AppModel
{
	protected $table = 'setting';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['country_id','country_code','timezone', 'radius', 'order_status'];
	
	public static function getSetting($id = 1) {
		
		return self::where('id', $id)->first();
	}

}

?>