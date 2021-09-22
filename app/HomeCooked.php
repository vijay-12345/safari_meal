<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB, App\Product, App\Coupon;
use App\Order;
use App\Setting;

class HomeCooked extends AppModel
{
	use SoftDeletes;

	protected $table = 'home_cooked';
	
	protected $distance;
	
	protected $count;
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	protected $hidden = [];		
    
    protected $dates = ['deleted_at'];

	public $fillable = ["id","is_home_cooked","parent_id" ,"area_id","city_id","state_id","country_id","owner_id", "lang_id","deals", "name" ,"rating" ,"min_order","delivery_charge","delivery_time", "longitude" ,"latitude" ,"tax_per_order" ,"floor","street" ,"company" ,"status" ,"created_at" ,"updated_at","restaurent_urlalias","featured", 'admin_commission', 'is_veg', 'is_nonveg', 'phone', 'packaging_fees', 'cgst', 'sgst', "delivery_applicable", 'flat_delivery_time', 'confirm_order' ];
	
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
		
	public function image(){		
		return $this->hasOne('App\Image','target_id','id')->where('type', 'restaurant');
	}	
   	
   	/**
     * The restaurant that belong to the cuisine.
     */
    public function cuisines()
    {
        return $this->belongsToMany('App\Cuisine','restaurant_cousine','restaurant_id','cousine_id')->where('cuisine.lang_id',\Config::get('app.locale_prefix'));
    }
   	
   	/**
     * The restaurant that belong to the timing.
     */
    public function openTime()
    {
        return $this->hasMany('App\Timing','restaurant_id','id')->orderBy('weekday');
    }
   	
   	/**
     * The restaurant that belong to the review.
     */
    public function review()
    {
        return $this->hasMany('App\Review','restaurant_id','id')->orderBy('date','desc')->with('customer');
    } 
   	
   	/**
     * The restaurant that belong to the reviewRating.
     */
    public function reviewRating()
    {
        return $this->hasMany('App\Review','restaurant_id','id')->select('id','restaurant_id', DB::raw('SUM(rating) as total_rating'), \DB::raw('count(*) as total_reviews'), DB::raw('ROUND(SUM(rating)/count(*), 1) as average'));
    } 
   	
   	/**
     * The restaurant that belong to the coupon.
     */
    public function coupon()
    {
        return $this->hasMany('App\Coupon','restaurant_id','id');
    }                  	
    
    /*
	* Return ResaurantList
    */
	public static function restaurentList($filterArr)
	{	
		//---------------------Default Setting----------------------------

		if(empty($filterArr['areaLatLong'])) {			
 			$filterArr['areaLatLong'] = ["latitude"=>"28.459497","longitude"=>"77.026638"];
		}			
		if(!empty($filterArr['cousinlimit'])) $cousinlimit=$filterArr['cousinlimit'];
		else $cousinlimit = 2;

		if(!empty($filterArr['restaurantlimit'])) $restaurantlimit=$filterArr['restaurantlimit'];

		else $restaurantlimit = 10;
		//---------------------End of Default Setting----------------------------

		$restaurantidarray = array();				
		$cousine = \DB::table('cuisine')->paginate($cousinlimit);

		$totalCuisines = \DB::table('cuisine')->count();		
		if(isset($filterArr['rating'])) $filter['restaurant.rating'] = $filterArr['rating'];

		$currenttime = date('H:m:s');				
		$day_of_week = date('l');
		$day_number_of_week = date('N');
		//$areaLatLong = Area::select('*')->where('url_alias',$restaurantUrl)->first();
		//$areaLatLong = Area::select('*')->where('name','like','%'.$filterArr['area_name'].'%')->first();	

		$restaurent_query = Restaurent::select('timing.*','restaurant.*', \DB::raw('POW(69.1 * (latitude - '. $filterArr['areaLatLong']->latitude .'), 2) + POW(69.1 * ('. $filterArr['areaLatLong']->longitude .' - longitude) * COS(latitude / 57.3), 2) AS distance'))
			->leftJoin('timing', 'restaurant.id', '=', 'timing.restaurant_id')
			->with('image')
			->where('timing.weekday',$day_number_of_week);						

		if(isset($filterArr['cuisines']) && count($filterArr['cuisines'])>0) {				
			$restaurantids = \DB::table('cuisine')->select('cuisine.*','restaurant_cousine.*')->whereIn('cuisine.name',$filterArr['cuisines'])
			 	->leftJoin('restaurant_cousine', 'restaurant_cousine.cousine_id', '=', 'cuisine.id')			 			
				->get();			
			foreach($restaurantids as $restaurantid) {
				$restaurantidarray[] = $restaurantid->restaurant_id;
			}
			$restaurent_query->whereIn('restaurant.id', $restaurantidarray);	
		}

		if(isset($filterArr['restauranttitle']) && !empty($filterArr['restauranttitle'])) {
			$restaurent_query->where('restaurant.name', 'like', '%'.$filterArr['restauranttitle'].'%');
		}

		if(isset($filterArr['filterRestaurants']['open']) && !empty($filterArr['filterRestaurants']['open'])) {						
			$restaurent_query->where('timing.open','<=', $currenttime)->where('timing.closing','>=', $currenttime);		
		}

		if(isset($filterArr['rating'])) {						
			$restaurent_query->where('restaurant.rating', $filterArr['rating']);	
		}

		if(isset($filterArr['filterRestaurants']['deals']) && !empty($filterArr['filterRestaurants']['deals'])) {						
			$restaurent_query->where('restaurant.deals', 1);				
		}

		$restaurent_count = $restaurent_query->count();

		$restaurent = $restaurent_query->orderBy('restaurant.rating', 'DESC')->paginate($restaurantlimit);
		// $restaurent = $restaurent_query->orderBy('distance',  'ASC')->paginate($restaurantlimit);

		//print_r($restaurent);die;
		return ['totalCuisines'=>$totalCuisines,'areaLatLong'=>$filterArr['areaLatLong'],'cousinlimit'=>$cousinlimit,'cousine'=>$cousine,'restrocount'=>$restaurent_count,'restaurent'=>$restaurent];	
		//return ['totalCuisines'=>$totalCuisines,'areaLatLong'=>$filterArr['areaLatLong'],'cousinlimit'=>$cousinlimit,'cousine'=>$cousine,'restrocount'=>$restaurent_count,'restaurent'=>$restaurent->toArray()];
	}

	public static function menuList($restaurant_id = null) {
		
		if($restaurant_id !=null) {			
			$data = Product::lang('product')
				->join('menu','product.menu_id','=','menu.id')
				->groupBy('product.menu_id')
				->where('product.restaurant_id','=',$restaurant_id)		
				->select('menu.name as menu_name','menu.id as menu_id')->get();	
			return $data;
		} else {
			return false;
		}
	}
	
	/*
	* return restaurant ids that have product
	*/ 
	public static function getRestaurantIdsHaveProduct() {
		$restaurantids = Product::lang()->select('restaurant_id')->get();
		$restaurantidarray = [];
		if(count($restaurantids) > 0) {
			foreach($restaurantids as $restaurantid) {
				$restaurantidarray[] = $restaurantid->restaurant_id;
			}
		}
		return $restaurantidarray;
	}

	public function restaurantfulldetails($resid)
	{
		$returnArray = array();
		$returnArray['restaurantdetails'] = Restaurent::lang()->select('restaurant.*')->where('id',$resid)->first();

		$returnArray['rewiewdetails']= 	Review::select('review.*','user.first_name','user.last_name')
				->leftJoin('user', 'user.id', '=', 'review.customer_id')
				->where('restaurant_id',$resid)->get();

		$returnArray['resauranttimes'] = Timing::where('restaurant_id',$resid)->orderBy('weekday', 'ASC')->groupBy('weekday')->get();

		$returnArray['restaurantproduct'] = Product::lang()->where('restaurant_id',$resid)->get();

		$returnArray['restaurantMenu'] = Menu::select('menu.id','menu.name')
						->leftJoin('product', 'product.menu_id', '=', 'menu.id')
						->where('product.restaurant_id',$resid)->groupBy('menu.name')->get();
		return $returnArray;
	}


	public static function restaurantfulldetailsForview($resid)
	{
		$returnArray = array();
		$dayOfWeek   = date('N');
		
		$returnArray['restaurantdetails'] = Restaurent::lang()->select('timing.*','restaurant.*')
				->leftJoin('timing', 'restaurant.id', '=', 'timing.restaurant_id')
				->with('image')
				->with('coupon')
				->where('timing.weekday', $dayOfWeek)
				->where('restaurant.id', $resid)
				->first();
		
		$restaurant_id = $returnArray['restaurantdetails']->id;
		
		$returnArray['rewiewdetails'] = Review::select('review.*','user.first_name')
					->leftJoin('user', 'user.id', '=', 'review.customer_id')
					->where('restaurant_id', $restaurant_id)
					->paginate(10);
		
		$returnArray['resauranttimes'] = Timing::where('restaurant_id',$restaurant_id)
					->orderBy('weekday', 'ASC')
					->groupBy('weekday')
					->get();
		
		$returnArray['restaurantproduct'] = Product::lang()
					->with('image')
					->where('restaurant_id',$restaurant_id)
					->orderBy('menu_id','asc')
					->get();
		
		$returnArray['restaurantMenu'] = Menu::lang('menu')
					->select('menu.id', 'menu.name')
					->leftJoin('product', 'product.menu_id', '=', 'menu.id')
					->where('product.restaurant_id',$restaurant_id)->groupBy('menu.name')
					->orderBy('menu.id','asc')
					->get();
		
		return $returnArray;
	}
	
	/*
	* update lat and long of restaurant
	*/
	public static function updateLatLong($restaurant_id)
	{
		$restObj = self::findOrFail($restaurant_id);

		$address = '';
		$fulladdress = '';
		$stateCountry = '';

		if( !empty($restObj->area->name) ) {
			$fulladdress .= $restObj->area->name.', ';
		}

		if( !empty($restObj->city->name) ) {
			$address .= $restObj->city->name.', ';
			$fulladdress .= $restObj->city->name.', ';
		}

		if( !empty($restObj->state->state_name) )
		{
			$address .= $restObj->state->state_name.', ';
			$stateCountry .= $restObj->state->state_name.', ';
			$fulladdress .= $restObj->state->state_name.', ';
		}

		if( !empty($restObj->country->country_name) )
		{
			$address .= $restObj->country->country_name.', ';
			$stateCountry .= $restObj->country->country_name.', ';
			$fulladdress .= $restObj->country->country_name.', ';
		}

		$address = trim($address, ', ');
		$stateCountry = trim($stateCountry, ', ');
		$fulladdress = trim($fulladdress, ', ');
		
		// Get lat long
		$latLongArr = getLatLong($fulladdress);
		if( ! $latLongArr ) {
			$latLongArr = getLatLong($address);
			if( ! $latLongArr ) {
				$latLongArr = getLatLong($stateCountry);
			}
		}

		$restObj->fill([
			'longitude' => $latLongArr['long'],
			'latitude'  => $latLongArr['lat']
		])->save();
	}


	/*
	* apply coupon code
	*/
	public function applyCoupon($data) {
		try {
			$couponApplyData = Coupon::where(['coupon_code'=>$data['coupon_code'],'restaurant_id'=>$data['restaurant_id']])
					->where('start_date','<=',date('Y-m-d H:i:s'))
					->where('end_date','>=',date('Y-m-d H:i:s'))
					->first();
			if(count($couponApplyData) > 0) {
				return ['data'=>$couponApplyData];
			} else {
				return ['message'=>trans('Invalid Coupon')];	
			}
		} catch(\Exception $e) {
			return ['error'=>$e->getMessage()];
		}
	}

	/* get  children restaurant */
	public static function getChildrenRestaurantIds($parent_ids) {
		return self::whereIn('parent_id',$parent_ids)->lists('id');
	}	
	
}

?>