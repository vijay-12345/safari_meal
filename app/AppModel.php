<?php 

namespace App;

use Illuminate\Database\Eloquent\Model, Session;

class AppModel extends Model
{	
	
	public function scopeLang($query,$modelName=null) {
		// print_r($query);
		// die ;

		if($modelName !=null) {
			return $query->where([$modelName.".lang_id"=>trim(\Config::get('app.locale_prefix'))]);
		}

		return $query->where(["lang_id"=>trim(\Config::get('app.locale_prefix'))]);
	}

	public function scopeAccess($query,$modelName=null) {			

		if(Session::get('access.role') == 'admin') {
			return $query;
		} elseif(!empty(Session::get('access.restaurant_ids'))) {
			
			if($modelName !=null) {
				return $query->whereIn($modelName.".restaurant_id",Session::get('access.restaurant_ids'));
			}

			return $query->whereIn("restaurant_id",Session::get('access.restaurant_ids'));
		
		} else {
			if($modelName !=null) {
				return $query->whereIn($modelName.".restaurant_id",[0]);
			}
			return $query->whereIn("restaurant_id",[0]);
		}

	}		
}

?>
