<?php 

namespace App;
use Illuminate\Database\Eloquent\Model;

class Coupon extends AppModel
{		
	protected $table = 'coupon';

	protected $primaryKey = 'id';

	public $timestamps = false;

	public $fillable = ['coupon_code','description','coupon_value','type','number_of_items','restaurant_id','coupon_can_use','combo_offer','product_can_use','start_date','end_date','terms','status'];
	
	protected $hidden = [];
	//validation rule
	public static $rules = [
	    'create' => [
		        'restaurant_id'		=> 'required',        
		        'coupon_code'		=> 'required',        
		        'type' 				=> 'required',
		       	'coupon_value'		=> 'required|numeric',
		        'start_date'   		=> 'required',
		        'end_date'   		=> 'required'
	        ],
	    'edit'   => [
		    	'restaurant_id'		=> 'required',
		        'coupon_code'		=> 'required',
		        'type' 				=> 'required',
		        'coupon_value'		=> 'required|numeric',
		        'start_date'   		=> 'required',
		        'end_date'   		=> 'required'
	        ]
 	];	

    public function restaurants()
    {
        return $this->belongsTo('App\Restaurent','restaurant_id','id');
    }

    /*===date format======*/
    public static function dateFormat(&$input,$format = 'Y-m-d'){
 		if(isset($input['start_date'])){
			$date=date_create($input['start_date']);
			$input['start_date'] = date_format($date,$format);
 		} 
  		if(isset($input['end_date'])){
			$date=date_create($input['end_date']);
			$input['end_date'] = date_format($date,$format);
 		} 		
    }
  	
    public static function couponisvalid($coupon_code)
    {
		$cart =	new Cart();
		$user =	\Auth::user();
		$productids = array();
		$amount = 0;
		// prd($cart->getData());
		
		foreach($cart->getData() as $key=>$val) {
			$productids[] = $val['prodid'];
			$restaurantid = $val['restaurant_id'];
			$amount +=	$val['totalCost'];
		}
		
		if(count($productids) <= 0) return trans('validation.coupon_make_any_order');
		
		$validcoupon  =	self::where(['coupon_code'=>$coupon_code,'restaurant_id'=>$restaurantid, 'status'=>1])->first();
		
		if(count($validcoupon) <= 0) return trans('validation.coupon_not_exist');
		
		if(((!empty($validcoupon->start_date) && $validcoupon->start_date!="0000-00-00 00:00:00") && (strtotime(date('Y-m-d')) < strtotime($validcoupon->start_date) )) || ((!empty($validcoupon->end_date) && $validcoupon->end_date!="0000-00-00 00:00:00") && (strtotime(date('Y-m-d')) > strtotime($validcoupon->end_date))))	
			return trans("validation.coupon_not_valid");
		
		if($validcoupon->combo_offer == 1 && count($productids) <= 1 )
			return trans("validation.coupon__min_product");
		
		if($amount < $validcoupon->minimum_price)
			return trans("validation.coupon__min_order ").format($validcoupon->minimum_price).' to use this coupon';
		
		$usercanuse		=	explode(",", $validcoupon->coupon_can_use);
		$productcanuse	=	explode(",", $validcoupon->product_can_use);
		
		if(!in_array($user->id, $usercanuse))
			return trans("validation.coupon__user");	
		
		foreach($productids as $productid)
			if(in_array($productid, $productcanuse)) {
				$cart->setcoupandetails(serialize($validcoupon));
				return $validcoupon->id;
			}
		return trans("validation.coupon_valid_product");
	}

}

?>