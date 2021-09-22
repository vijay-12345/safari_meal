<?php

namespace App\Http\Controllers\Api;

use DB, Config, Session, Request, View, Redirect, Exception;
use App\Restaurent, App\helpers, App\Product, App\Coupon, App\City, App\Area, App\User, App\Review;
use App\Traits\CalculateDileveryCharge;
use App\Setting;

class RestaurentController extends \App\Http\Controllers\Controller 
{
	use CalculateDileveryCharge;

	public function __construct() {
		//$this->middleware('auth.api', ['except' => ['login']]);
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 * POST /v1/RestarantList 
	 *	{
	 * 		"area_name": "Baded B.O" 
	 *	}
	 */		
	public function restaurentList_V1()
	{	
		$inputs = Request::all();
		if(isset($inputs['area_name'])) {
			$areaLatLong = Area::select('*')->where('name','like','%'.$inputs['area_name'].'%')->first();	
			if(!empty($areaLatLong)) {
				$inputs = array_merge(['areaLatLong'=>$areaLatLong],$inputs);
				$restaurentData = Restaurent::restaurentList($inputs);
				$restaurentData['restaurent'] = $restaurentData['restaurent']->toArray();
				return $this->apiResponse($restaurentData);
			} else {
				return $this->apiResponse(["error"=>"Record not found!"],true);
			}
		} else {
			return $this->apiResponse(["error"=>"Field area_name required!"],true);
		}
	}

	/*
	* restaurentList
	*/
	public function restaurentList($flag)
	{
		$filterArr = $inputs = Request::all();
		try {
			if(isset($inputs['long']) && isset($inputs['lat'])) {
				//---------------------End of Default Setting----------------------------
				$restaurantidarray = array();

				if(!empty($inputs['restaurantlimit'])) $restaurantlimit = $inputs['restaurantlimit'];
				
				else $restaurantlimit = 5;

				$currenttime = date('H:i:s');
				$currenttime = __dateToTimezone('', $currenttime, 'H:i:s');

				$day_of_week        = date('l');
				$day_number_of_week = date('N');
				
				$productResIds = Restaurent::getRestaurantIdsHaveProduct();
			
				$setting = Setting::getSetting();
				if($setting) $radius = $setting->radius;
				else $radius = \Config::get('constants.search_m');
				
				$restaurant_query = Restaurent::select('timing.*','restaurant.*', \DB::raw('ROUND(SQRT(POW(69.1 * (latitude - '. $inputs['lat'] .'), 2) + POW(69.1 * ('. $inputs['long'] .' - longitude) * COS(latitude / 57.3), 2)), 3) AS distance') )
					->leftJoin('timing', 'restaurant.id', '=', 'timing.restaurant_id')
					->with('image')
					->where('timing.weekday', $day_number_of_week)
					->whereIn('restaurant.id', $productResIds)->where('restaurant.status', 1)
					->whereRaw('ROUND(SQRT(POW(69.1 * (latitude - '. $inputs['lat'] .'), 2) + POW(69.1 * ('. $inputs['long'] .' - longitude) * COS(latitude / 57.3), 2)), 3) <='.$radius);
				
				$restaurant_query->addSelect(
					DB::raw('( CASE WHEN `open` <= "'.$currenttime.'" AND `closing` >= "'.$currenttime.'" THEN 1 
		            		ELSE 0 END) AS is_open')
				);

				//filter
				if(isset($filterArr['filterRestaurants']['cuisines']) && count($filterArr['filterRestaurants']['cuisines']) >0 )
				{
					$restaurantids = \DB::table('cuisine')->select('cuisine.*','restaurant_cousine.*')->whereIn('cuisine.name',$filterArr['filterRestaurants']['cuisines'])
					 ->leftJoin('restaurant_cousine', 'restaurant_cousine.cousine_id', '=', 'cuisine.id')			 			
					->get();
					foreach($restaurantids as $restaurantid) {
						$restaurantidarray[] = $restaurantid->restaurant_id;
					}
					$restaurant_query->whereIn('restaurant.id',$restaurantidarray);
				}
				
				// Veg Filter
				if( isset( $filterArr['filterRestaurants']['veg'] ) && $filterArr['filterRestaurants']['veg'] == 'true') {
					$restaurant_query->where('is_veg', 1);
				}
				
				// Non Veg Filter
				if( isset( $filterArr['filterRestaurants']['non_veg'] ) && $filterArr['filterRestaurants']['non_veg'] == 'true') {
					$restaurant_query->where('is_nonveg', 1);
				}

				if(isset($filterArr['restauranttitle']) && !empty($filterArr['restauranttitle'])) {
					$restaurant_query->where('restaurant.name', 'like', '%'.$filterArr['restauranttitle'].'%');
				}

				if(isset($filterArr['filterRestaurants']['open']) && !empty($filterArr['filterRestaurants']['open']) && $filterArr['filterRestaurants']['open'] == "true") {						
					$restaurant_query->where('timing.open','<=', $currenttime)->where('timing.closing','>=', $currenttime);		
				}

				if(isset($filterArr['filterRestaurants']['rating']) && !empty($filterArr['filterRestaurants']['rating']) && $filterArr['filterRestaurants']['rating'] >0) {						
					$restaurant_query->where('restaurant.rating', $filterArr['filterRestaurants']['rating']);	
				}
				if(isset($filterArr['filterRestaurants']['deals']) && !empty($filterArr['filterRestaurants']['deals']) && $filterArr['filterRestaurants']['deals'] == "true") {						
					$restaurant_query->where('restaurant.deals', 1);				
				}

				if($flag == "0") {						
					$restaurant_query->where('restaurant.is_home_cooked', 0);				
				}

				if($flag == "1") {						
					$restaurant_query->where('restaurant.is_home_cooked', 1);				
				}
				//end filter
				
				$restaurant_count = $restaurant_query->count();
				
				$restaurant_query->orderBy('is_open','DESC');

				// $restaurant = $restaurant_query->orderBy('distance', 'ASC')->paginate($restaurantlimit);
				$restaurant = $restaurant_query->orderBy('restaurant.rating', 'DESC')->paginate($restaurantlimit);
				
				// Prepare location array for distance based delivery charge
				$areaLocation = [];
				
				$areaLocation['first_location'] = [
					'latitude' 	=> $inputs['lat'],
					'longitude'	=> $inputs['long']
				];
				
				if($restaurant_count == 0) {
					$message = trans('admin.not.found');
				} else {
					$message = trans('Successfully!');
					$tempRestaurant = $restaurant;

					foreach($tempRestaurant as $key => $resObj) {
						// Get menus
					//	DB::enableQueryLog(); 
						$menus = Restaurent::menuList($resObj->id);
						$restaurant[$key]['menus'] = $menus;
					//	dd(DB::getQueryLog()); 
						// Update delivery charges based on distance
						$areaLocation['second_location'] = [
							'latitude' 	=> $resObj->latitude,
							'longitude'	=> $resObj->longitude
						];

						$charge = $this->getDeliveryCharge($areaLocation);
						$restaurant[$key]->distance = $this->getDistance($areaLocation['first_location']['latitude'], $areaLocation['second_location']['latitude']);
						if ($charge) $restaurant[$key]->delivery_charge = $charge;
					}
				}
				
				$adminData = User::select('first_name','last_name','contact_number','email', 'countrycode')->where(['role_id'=>Config::get('constants.user.super_admin')])->get();
				
				return $this->apiResponse(['restrocount' => $restaurant_count, 'restaurant'=>str_replace('/?', '?', $restaurant->toArray()),'message'=>$message, 'admin' => $adminData]);	
				
			} else {
				return $this->apiResponse(["error"=>"Field long and lat are required!"], true);
			}
		} catch(\Exception $e) {
			return $this->apiResponse(['error'=>$e->getMessage()],true);
		}
	}

	


	/*
	* product list base on restaurant_id
	*/
	public function productList() {
		
		$inputs = Request::all();
		if(isset($inputs['restaurant_id']) && isset($inputs['menu_id'])) {
			$products_query = Product::lang('product')->where('restaurant_id',$inputs['restaurant_id'])
			->where('menu_id',$inputs['menu_id']);
		 	$restaurent_count = $products_query->count();			
			$products = $products_query->get();
			if($restaurent_count == 0) {
				$message = trans('admin.not.found');
			} else {
				$message = trans('Successfully!');
			}			
			return $this->apiResponse(['data'=>$products,'message'=>$message]);	
		} else {
			return $this->apiResponse(["error"=>"Field restaurant_id and menu_id are required!"],true);
		}
	}

	/*
	 * Restaurant details
	 */
	private function productAddonsForApi($id) {
		$addons = Product::productAddons($id);
		$addonsStruct = [];
		foreach($addons as $group => $items) {
			$addonsStruct[] = [
				'name' => $items[0]['name'],
				'type' => $items[0]['type'],
				'required' => $items[0]['required'],
				'data'=>$items
			];
		}
		return $addonsStruct;
	}
	
	public function restaurantDetail()
	{
		$inputs = Request::all();
		$id = $inputs['id'];
		
		
		$currenttime = date('H:i:s');
		$currenttime = __dateToTimezone('', $currenttime, 'H:i:s');
		
		
		$restaurantDetails = [];
		$restaurantDetails = Restaurent::lang()->select('timing.*','restaurant.*')
		->leftJoin('timing', 'restaurant.id', '=', 'timing.restaurant_id')->with('image')->with('area')->with('city')->with('state')->with('country')->with('openTime')->with(['review'=>function($query){
			$query->take(2);
		}])->with('reviewRating')->addSelect(
					DB::raw('( CASE WHEN `open` <= "'.$currenttime.'" AND `closing` >= "'.$currenttime.'" THEN 1 
		            		ELSE 0 END) AS is_open')
				)->find($id);
		
		if(!$restaurantDetails)			
			return $this->apiResponse(['error'=>trans('invalid.restaurant.id')],true);	
		$restaurantDetails = $restaurantDetails->toArray();
		$menusData = Restaurent::menuList($id);

		if(!$menusData) {
			$restaurantDetails['menus'] = null;
		} else {
			$restaurantDetails['menus'] = $menusData->toArray();
			foreach($restaurantDetails['menus'] as $key => $item) 
			{
				$products 	= Product::lang()->where(['menu_id'=> $item['menu_id'],'restaurant_id'=>$id])
					->with(['image' => function($query){
						$query->select('target_id', 'location');
					}])->get();

				$products = $products->toArray();	
				foreach($products as $index => $pr) {
					$products[$index]['addons'] = $this->productAddonsForApi($pr['id']);//Product::productAddons($pr['id']);
				}

				$restaurantDetails['menus'][$key]['products'] = $products;
			}	
		}
		return $this->apiResponse(['data'=>$restaurantDetails,'message'=>trans('Successfully!')]);	
	}

	/*
	* Deals API
	*/
	public function deals(){		
		try {
			$deals = Coupon::select('coupon.*')
						->where('coupon.status',1)
						->where('coupon.start_date','<=',date('Y-m-d'))
						->where('coupon.end_date','>=',date('Y-m-d'))
						//->join('restaurant','restaurant.id','=','coupon.restaurant_id')
						//->leftJoin('image','image.target_id','=','restaurant.id')
						->orderBy('id', 'DESC')
						->get();			

			if(count($deals) > 0) {
				//get restaurant obj
				foreach($deals as $key=>$v) {
					$restaurant = Restaurent::select('timing.*','restaurant.*')
						->leftJoin('timing', 'restaurant.id', '=', 'timing.restaurant_id')
						->with('image','cuisines')						
						->where('restaurant.id',$v->restaurant_id)->first();
					
					$menus = Restaurent::menuList($v->restaurant_id);
					$restaurant['menus'] = $menus;
					$deals[$key]['restaurants'] = $restaurant;					
				}
				//end of restauran obj
				return $this->apiResponse(["data"=>$deals]);	
			} else {   
				return $this->apiResponse(["data"=> [], "message"=>trans('admin.not.found')]);
			}
					
		} catch(\Exception $e) {
			return $this->apiResponse(["error"=>$e->getMessage()],true);
		}	
	}

	/*
	* coupon appy method
	*/	
	public function applyCoupon() {
		try {			
			$inputs = Request::all();
			//prd($inputs);
			$couponRes= (new Restaurent())->applyCoupon($inputs);
			if(isset($couponRes['error'])) {
				return $this->apiResponse(["error"=>$couponRes['error']],true);	
			}else if(isset($couponRes['data'])) {
				return $this->apiResponse(["data"=>$couponRes['data']]);
			} else {
				return $this->apiResponse(["message"=>$couponRes['message']],true);
			}			
		} catch(\Exception $e) {
			return $this->apiResponse(['error'=>$e->getMessage()],true);
		}
	}

	public function restaurantfulldetails()
	{
		try {
			$restaurantfull=array();
			$inputs = Request::all();
			$restaurantfull = (new Restaurent())->restaurantfulldetails($inputs['restaurant_id']);
		}
		catch(\Exception $e) {
			return $this->apiResponse(['error'=>$e->getMessage()],true);
		}
		return $this->apiResponse(['data'=>$restaurantfull,'message'=>trans('Successfully!')]);	
	}

	/* 
	* City List
	*
	*
	*/
	public function cityList() {		
		try {
			$data = City::lang()->where('status',1)->get();
			return $this->apiResponse(["data"=>$data]);
		} catch(\Exception $e) {
			return $this->apiResponse(['error'=>$e->getMessage()],true);
		}
	}

	/* 
	* Area List
	*
	*
	*/
	public function areaList()
	{
		$inputs = Request::all();
		try {
			$data = Area::lang()
				->select('id','name','city_id','lang_id','url_alias','latitude','longitude')
				->where('city_id', $inputs['city_id'])
				->get();
			
			return $this->apiResponse(["data" => $data]);
		
		} catch(\Exception $e){
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}

	// Get restaurant reviews
	public function getReviews()
	{
		$message = null;
		try {
			$inputs = Request::all();
			$offset = ($inputs['page'] - 1) * $inputs['per_page'];

			$restaurant = Restaurent::find( $inputs['restaurant_id'] );
			if( ! $restaurant ) {
				throw new Exception('Restaurant does not exist.', 1);
			}

			// Get total review count
			$reviewCount = Review::where([
				'restaurant_id' =>  $inputs['restaurant_id'],
				'status'		=> 	1
			])->count();
			
			$reviews = Review::where('review.restaurant_id', $inputs['restaurant_id'])
				->where('review.status', 1)
				->leftJoin('user as u', 'review.customer_id', '=', 'u.id')
				->select('review.id as review_id', 'u.id as user_id', 'u.first_name', 'u.last_name', 'u.profile_image', 'review.review', 'review.rating', 'review.date')
				->take( $inputs['per_page'] )
				->skip( $offset )
				->get();

			return $this->apiResponse(['data' => array(
				'total' 	=> $reviewCount,
				'page'		=> $inputs['page'],
				'per_page'	=> $inputs['per_page'],
				'reviews'	=> $reviews
			)]);

		} catch (Exception $e) {
			$message = $e->getMessage();
		}

		return $this->apiResponse(['error' => $message], true);
	}

}