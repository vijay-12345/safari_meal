<?php 

namespace App\Http\Controllers;

use Session,Request,View,Redirect;
use DB;
use App\Restaurent,App\helpers,App\Review,App\Timing;
use App\Area,App\Product,App\UserAddress, App\Menu, App\Coupon;
use \Exception;
use App\Cart,App\Order,Lang,Auth,Validator,Input,App\Cuisine;
use App\Traits\CalculateDileveryCharge;

class RestaurentController extends Controller
{
	use CalculateDileveryCharge;

	/*
	|--------------------------------------------------------------------------
	| Restaurent Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
		
	public function restaurentList($restaurantUrl="")
	{		
		
			(new Cart())->clearcart();
			$cousinlimit=5;		
			$inputs = Request::all();	
			//prd($inputs);
			//echo json_encode($inputs);die;			
			if(!empty($inputs['cousinlimit'])){			
				$cousinlimit=$inputs['cousinlimit'];
			}

			$restaurantidarray =array();				
			// $cousine = DB::table('cuisine')->paginate($cousinlimit);	
                        $cousine = Cuisine::lang()->take($cousinlimit)->get();
			$totalCuisines = DB::table('cuisine')->count();		
			if(isset($inputs['rating'])){
				$filter['restaurant.rating'] = $inputs['rating'];
			}			
			$currenttime = date('H:m:s');				
			$day_of_week = date('l');
			$day_number_of_week = date('N');
			$areaLatLong = Area::select('*')->where('url_alias',$restaurantUrl)->first();

			$restaurent_query = Restaurent::lang()
			->select('timing.*','restaurant.*')
			->leftJoin('timing', 'restaurant.id', '=', 'timing.restaurant_id')
			->with('image')
			->where('timing.weekday',$day_number_of_week)
			->where('restaurant.status', 1);
			
			if($areaLatLong->latitude && $areaLatLong->longitude)
			{
				$restaurent_query->addSelect(DB::raw('SQRT(POW(69.1 * (latitude - '. $areaLatLong->latitude .'), 2) + POW(69.1 * ('. $areaLatLong->longitude .' - longitude) * COS(latitude / 57.3), 2)) AS distance'))->whereraw('SQRT(POW(69.1 * (latitude - '. $areaLatLong->latitude .'), 2) + POW(69.1 * ('. $areaLatLong->longitude .' - longitude) * COS(latitude / 57.3), 2))<='.\Config::get('constants.search_m'));

			}
			// prd($restaurent_query->toSql());
			
			if(isset($inputs['cuisines']) && count($inputs['cuisines'])>0){				
				$restaurantids = Cuisine::select('cuisine.*','restaurant_cousine.*')->whereIn('cuisine.name',$inputs['cuisines'])
					->leftJoin('restaurant_cousine', 'restaurant_cousine.cousine_id', '=', 'cuisine.id')			 			
					->get();				
				foreach($restaurantids as $restaurantid){
					$restaurantidarray[] = $restaurantid->restaurant_id;
				}
				$restaurent_query->whereIn('restaurant.id',$restaurantidarray);	
			}
			
			if(isset($inputs['restauranttitle']) && !empty($inputs['restauranttitle'])){						
				$restaurent_query->where('restaurant.name', 'like', '%'.$inputs['restauranttitle'].'%');
			}

			if(isset($inputs['filterRestaurants']['open'])){						
				$restaurent_query->where('timing.open','<=',$currenttime)->where('timing.closing','>=',$currenttime);		
			}

			if(isset($inputs['filterRestaurants']['deals'])){						
				$restaurent_query->where('restaurant.deals',1);				
			}	

			if(isset($inputs['rating'])) {
				$restaurent_query->where('restaurant.rating', '<=' , $inputs['rating']);
			}

			if( isset( $inputs['veg'] ) && $inputs['veg'] == 1 ) {
				$restaurent_query->where('restaurant.is_veg', 1);
			}

			if( isset( $inputs['non_veg'] ) && $inputs['non_veg'] == 1 ) {
				$restaurent_query->where('restaurant.is_nonveg', 1);
			}

			$restaurent_count = $restaurent_query->count();
						
			if(isset($inputs['rating'])) {
				$restaurent = $restaurent_query->orderBy('restaurant.rating',  'DESC')->paginate(10);			
			} elseif($areaLatLong->latitude && $areaLatLong->longitude) {
				$restaurent = $restaurent_query->orderBy('distance','DESC')->paginate(10);		
			} else {
				$restaurent = $restaurent_query->paginate(10);
			}

			$data=array();
			foreach($restaurent as $val){
				$data[$val->id]=$val;									
			}
							
			$restaurent_count=count($data);				
			//prd($restaurent);
			return View::make('home.restaurent',['restaurentUrl'=>'restaurentlist/'.$restaurantUrl,'totalCuisines'=>$totalCuisines,'areaLatLong'=>$areaLatLong,'cousinlimit'=>$cousinlimit,'cousine'=>$cousine,'restrocount'=>$restaurent_count,'restaurent'=>$restaurent,'restaurantUrl'=>$restaurantUrl]);
		
	}

	public function morecuisines(){
		$inputs = Request::all();
		$addcousinehtml = '';
		$isdata = 0;
		$adddivhtml = array();		
		$limit = $inputs['limit'];	
		$cousine = DB::table('cuisine')
		->where('id','>',$limit)
		->where('id','<=',$limit+2)
		->get();		
		if(count($cousine)>0){
			$isdata = 1;
			foreach($cousine as $cousineDetail){			
				$addcousinehtml .= '<div class="checkbox-cont">								
					<input type="checkbox" class="css-checkbox" id="checkboxcG'.$cousineDetail->id.'" value="'.ucfirst($cousineDetail->name).'" name="cuisines[]">
					<label class="css-label" for="checkboxcG'.$cousineDetail->id.'">'.ucfirst($cousineDetail->name).'</label>
				</div>';
			}
			$addcousinehtml .= '**%&%**'.$isdata;		
			$addcousinehtml .= '**%&%**'.($limit+2);
		}				
		echo  $addcousinehtml;
		die;
	}

	protected function output()
	{
		$this->data = empty($this->data) ? null : $this->data;
		return array(
			'status' => $this->status, 			
			'data' => $this->data
		);
	}

	public function updateCheckout()
	{
		$status = (new Cart)->addtocart(Request::all());		
		$cart = new Cart;
		return View::make('cart.checkoutCart',['cart'=>$cart]);
	}

	public function addtocart()
	{
		$cart = new Cart;
		$status = $cart->addtocart(Request::all());
		
		return View::make('cart.cart')->with('cart', $cart);
	}
	
	public function restaurentdetail($restaurantUrl)
	{
		//Cart::clearcart();
		$restaurantdetails	= 	Restaurent::lang()->where('restaurent_urlalias',$restaurantUrl)->first();
		$restaurantdetails	=	Restaurent::restaurantfulldetailsForview($restaurantdetails->id);
		$restaurantdetails = array_merge($restaurantdetails, array('restaurantUrl'=>$restaurantUrl));

		// New delivery charges set according to distance

		$locations = [];
		$area_location = Request::get('location');
		$restaurant = $restaurantdetails['restaurantdetails'];


		// $charge = $this->getDeliveryCharge([
		// 	'first_location' => [
		// 		'latitude' 	=> '29.207443',
		// 		'longitude'	=> '79.496536'
		// 	],
		// 	'second_location' => [
		// 		'latitude' 	=> '29.228905',
		// 		'longitude'	=> '79.491066'	
		// 	]
		// ]);

		if ($area_location && $restaurant) {
			$temp = explode(',', $area_location);

			$locations['first_location'] = [
					'latitude' 	=> $temp[0],
					'longitude'	=> $temp[1]
				];

			$locations['second_location'] = [
					'latitude' 	=> $restaurant->latitude,
					'longitude'	=> $restaurant->longitude
				];
		}

		$charge = $this->getDeliveryCharge($locations);
		// New delivery charges set according to distance

		$cart = new Cart;
		$cart->clearcartOthercoupan();
		
		$cart->setdeliveryCharges($charge);
		// $cart->setdeliveryCharges($restaurantdetails['restaurantdetails']->delivery_charge);
		$cart->setPackagingFees($restaurantdetails['restaurantdetails']->packaging_fees);
		$cart->setCGST($restaurantdetails['restaurantdetails']->cgst);
		$cart->setSGST($restaurantdetails['restaurantdetails']->sgst);
		
		return View::make('gotToRestaurant',$restaurantdetails);		
	
		//	return View::make('gotToRestaurant',['resauranttimes'=>$resauranttimes,'rewiewdetails'=>$rewiewdetails,'restaurantUrl'=>$restaurantUrl,'restaurantMenu'=>$restaurantMenu,'restaurantproduct'=>$restaurantproduct,'restaurantdetails'=>$restaurantdetails]);	
	}
   
	public function proceedToCheckout($restaurantUrl)
	{
		try
		{
			if( Auth::user() )
			{
				$dayOfWeek = date('N');
				$restaurantdetails = Restaurent::select('timing.*', 'restaurant.*')
					->leftJoin('timing', 'restaurant.id', '=', 'timing.restaurant_id')
					->where('timing.weekday', $dayOfWeek)
					->where('restaurent_urlalias',$restaurantUrl)
					->first();

				$detail = array();
				$detail['cart'] = (new Cart);
				$detail['restaurantUrl'] = $restaurantUrl;		
				$detail['restaurantdetails'] = $restaurantdetails;		
				$detail['user'] = Auth::user();	
				
				if(empty($detail['cart']->getData())){
					Session::put('Errormessage', trans("home.no.product.in.cart"));
					return back();
				}

				return View::make('cart.checkOut',$detail);					
				
			} else {
				return back();
			}
		}		
		catch(\Exception $e){
			print_r($e->getMessage());
			exit;		
		}
	}	

	public function checkcoupanvalid()
	{
		$request = 	Request::all();
			$coupanResponse =	Coupon::couponisvalid($request['coupancode']);
		if(is_numeric($coupanResponse))
			Session::put('Successmessage', "Coupon code successfully applied");
		else
		{
			Session::put('Errormessage',$coupanResponse);
			$cart = new Cart;
			$cart->clearcartOthercoupan();
		}
		return back();
	}
	
	public function createOrder()
	{
		try
		{
			$request = Request::all();
			$cart = new Cart;
			
			// Apply coupon
			if(!empty($request['coupon_id']))
			{
				$validid = Coupon::where(['id'=>$request['coupon_id']])->first();
				$cart->setcoupandetails(serialize($validid));	
			}

			// Set order type
			$cart->setOrderType( isset($request['order_type']) ? $request['order_type'] : null );
			
			// Create order
			$ordernum =	Order::createOrder($request, $cart);
			if(is_numeric($ordernum))
			{	
				$detail['restaurent_id']	=	$request['restaurant_id'];
				$detail['status']			=	Lang::get('restaurent.Order .created .successfully.');
				$detail['ordernum']			=	$ordernum;				
				return redirect('proceedtosecurecheckout/'.$ordernum)->with($detail);											
			}

			Session::put('Errormessage', Lang::get('restaurent.There .is .no .Product .in .cart.'));
			
			return View::make('cart.checkOut');

		} catch(\Exception $e){
			print_r($e->getMessage());
			exit;		
		}

		Session::put('Errormessage', Lang::get('restaurent.There .is .no .Product .in .cart.'));
		
		return View::make('cart.checkOut');
	}
	
	public function proceedToSecureCheckout($ordernumber='')
	{
		$input=Request::all();
		if(!empty($input['ordernum'])){
			$ordernumber=$input['ordernum'];
		}

		$cart = new Cart();	
		$coupan=$cart->getcoupandetails();
		if(!empty($coupan))
		{
			$coupanResponse =	Coupon::couponisvalid($coupan->coupon_code);
			if(!is_numeric($coupanResponse))
			{	
				Session::put('Errormessage',$coupanResponse);
				$cart->clearcartOthercoupan();
				return back();
			}
		}

		$order	= Order::where('order_number',$ordernumber)->first();			
		if($order->status != 0)
		{
			Session::put('Successmessage', "Your Order already created with Order number:: ".$ordernumber);
			return redirect('thanks/'.$ordernumber);
		}

		$useraddresses = UserAddress::where('user_id',Auth::user()->id)->where('country_id', '<>', 0)->get();		
		//print_r($useraddresses->toArray()); exit;						
		$restaurentid = array_column($cart->getData(), 'restaurant_id');		
		$restaurantdetails = Restaurent::select('timing.*','restaurant.*')
			->leftJoin('timing', 'restaurant.id', '=', 'timing.restaurant_id')
			->where('restaurant.id',$restaurentid[0])
			->first();
		
		$detail['user'] = Auth::user();
		$detail['restaurantdetails']=$restaurantdetails;		
		$detail['cart'] = (new Cart());
		
		if($order->order_type =='pickup'){			
			$detail['cart']->setdeliveryCharges(0);
		}
		
		$orderproduct = DB::table('order_item')->where('id',$order->id)->get();	
		$detail['order'] = $order;		
		$detail['orderproduct'] = $orderproduct;				
		$detail['addressflag'] = count($useraddresses);						
		$detail['ordernumber'] = $ordernumber;
		$detail['order_type'] = $order->order_type;
		$detail['useraddresses'] = "N/A";
		
		if(count($useraddresses) > 0) {
			$detail['useraddresses'] = Auth::user()->userAddressDetail();
		}

		if (Request::getMethod() == 'POST')
        {
        	$subtotal = $detail['cart']->getSubTotal();
            $order->cgst = $detail['cart']->getCGST()/100 * $subtotal;
            $order->sgst = $detail['cart']->getSGST()/100 * $subtotal;
            $order->packaging_fees = $detail['cart']->getPackagingFees();
            $order->save();

            $rules = ['captcha' => 'required|captcha'];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails())
            {
				Session::put('Errormessage', "Please Enter valid Captcha");
				return redirect()->back();
			}

        	if($order->status == 0)
			{	
				if(empty($input['area'])){
					Session::put('Errormessage', "Please select shipping address from  address book");
					return redirect()->back();
				}

				Order::setCustomerAdderss($input['area'], $order->id);
				Session::put('Successmessage', "Your Order is successfully created with Order number:: ".$ordernumber);

				$order->delivery_address_id = $input['area'];

				// Notify driver
				Order::sendOrderNotification($order->toArray(), $order->id);

				return redirect('thanks/'.$detail['ordernumber'].'?paymentmethod='.Request::input('paymentmethod'));
			}
		}

        return View::make('cart.secureCheckout',$detail);				
	}

	public function thanks($ordernum)
	{
		return View::make('thanks')->with(['ordernum'=>$ordernum, 'paymentmethod'=>Request::input('paymentmethod')]);
    }

    /* ======review post from front site ========== */
    public function reviewpost(){
    	$input=Request::all();
 		$input['customer_id'] = Auth::user()->id;
	    $input['date'] = date('Y-m-d H:i:s');	 
	    $user = Review::where(['customer_id'=>$input['customer_id'],'restaurant_id'=>$input['restaurant_id']])->first(); 
	    if(!empty($user)){					    	
	    	return redirect()->back()->withErrors([trans('home.you.have.already.post.review')]);	
	    }   	 					
        $validator = Validator::make(Input::all(),[
	        'review' => 'required',
	        'rating' =>'required'		        
	    ]);
        if ($validator->fails())
        {			
			$messages = $validator->messages();
			return redirect()->back()->withErrors($messages);
		}	    
	    //prd($input);
	    Review::create($input);		   							    
	    Session::flash('flash_message', trans('home.successfully.posted'));		    	
	    return redirect()->back();	        
		
		  	
    }
}
