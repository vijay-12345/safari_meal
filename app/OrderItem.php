<?php namespace App;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends AppModel
{
	protected $table = 'order_item';	
	//protected $primaryKey = 'id';
	//public $timestamps = false;
	//public $fillable = [];
	protected $hidden = [];
    public function getAddonsListAttribute($value)
    {
    	if($value){
	 		if((@unserialize($value) !== false)){
				$uvalue= unserialize($value);
			}else{
				$uvalue= json_decode($value);
			}
			foreach ($uvalue as $key => $v) {
				$uvalue[$key] = (object)$v;
			}
			return $uvalue;   		
    	}else{
    		return $value;
    	}
    }	
     /**
     * Get the image record associated with the user.
     */
	public function orderitemlist($orderid)
	{
		return self::where('order_id',$orderid)->get();
	}

	public function image(){		
		return $this->hasOne('App\Image','target_id','product_id')->where('type', 'product');
	}



	// public function restaurant() {
	// 	return $this->belongsTo('App\Restaurent','restaurant_id','id')->with('imageForItem');
	// }
}

