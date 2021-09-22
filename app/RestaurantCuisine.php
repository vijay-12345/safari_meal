<?php namespace App;
use Illuminate\Database\Eloquent\Model;
class RestaurantCuisine extends AppModel
{
		
	protected $table = 'restaurant_cousine';
	protected $primaryKey = 'id';
	public $timestamps = false;
	public $fillable = ['cousine_id','restaurant_id','lang_id'];
	
	protected $hidden = [];	  
    public static function insertData($data,$restaurant_id){
    	$cuisineInsertArr =  [];

    	if(count($data) >0){    		
	    	foreach($data as $key=>$v){	    		
	    		$cuisineInsertArr[] = ['cousine_id'=>$v,'restaurant_id'=>$restaurant_id,'lang_id'=>\Config::get('app.locale_prefix')] ;
	    		
	    	}	    	
	    	self::insert($cuisineInsertArr);
	    	
    	}

    }
    public static function updateData($data,$restaurant_id){
    	$cuisineInsertArr =  [];
    	self::lang()->where('restaurant_id',$restaurant_id)->delete();
    	if(count($data) >0){    		
	    	foreach($data as $key=>$v){	    		
	    		$cuisineInsertArr[] = ['cousine_id'=>$v,'restaurant_id'=>$restaurant_id,'lang_id'=>\Config::get('app.locale_prefix')] ;
	    		
	    	}	    	
	    	self::insert($cuisineInsertArr);
	    	
    	}

    } 
    public static function getCuisineIds($restaurant_id){	    	
	    $data = self::where('restaurant_id',$restaurant_id)->get();
	    $cuisineIdArr = [];
	    if(count($data) > 0){
	    	foreach($data as $key=>$v){	    		
	    		$cuisineIdArr[] = $v->cousine_id;
	    		
	    	}	
	    }
	    return $cuisineIdArr;	
    	

    }    
}
?>
