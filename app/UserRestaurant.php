<?php namespace App;
use Illuminate\Database\Eloquent\Model;
class UserRestaurant extends AppModel
{
		
	protected $table = 'user_restaurant';
	protected $primaryKey = 'id';
	public $timestamps = false;
	public $fillable = ['cousine_id','restaurant_id','lang_id'];
	
	protected $hidden = [];	  
    public static function insertData($data,$user_id){
    	$restaurantInsertArr =  [];

    	if(count($data) >0){    		
	    	foreach($data as $key=>$v){	    		
	    		$restaurantInsertArr[] = ['restaurant_id'=>$v,'user_id'=>$user_id,'lang_id'=>\Config::get('app.locale_prefix')] ;
	    		
	    	}	    	
	    	self::insert($restaurantInsertArr);
	    	
    	}

    }
    public static function updateData($data,$user_id){
    	$restaurantInsertArr =  [];
    	self::lang()->where('user_id',$user_id)->delete();
    	if(count($data) >0){    		
	    	foreach($data as $key=>$v){	    		
	    		$restaurantInsertArr[] = ['restaurant_id'=>$v,'user_id'=>$user_id,'lang_id'=>\Config::get('app.locale_prefix')] ;
	    		
	    	}	    	
	    	self::insert($restaurantInsertArr);
	    	
    	}

    } 
    public static function getRestaurantIds($user_id){	    	
	    $data = self::lang()->where('user_id',$user_id)->get();
	    $restaurantIdArr = [];
	    if(count($data) > 0){
	    	foreach($data as $key=>$v){	    		
	    		$restaurantIdArr[] = $v->restaurant_id;
	    		
	    	}	
	    }
	    return $restaurantIdArr;
    }    
}
?>
