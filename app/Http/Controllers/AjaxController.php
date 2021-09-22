<?php  

namespace App\Http\Controllers;

use Auth, Session;
use Request, Input, DB, App\City, App\UserAddress, App\Coupon, App\NewsLetter;
use App\Area, App\State, App\User, App\Restaurent, App\Timing, App\Product, App\Review, App\Menu, App\Cart,App\Order,App\Table;
use \Mail;

class AjaxController extends Controller {

	public function saveareas() {
		try {
			$input = Input::all();
			$latLongArr = ['lat'=>28.459497,'long'=>77.026638]; 
			$data = [
				'name'=>$input['new_area'],
				'city_id'=>$input['cityId'],
				'lang_id'=>\Config::get('app.locale_prefix')
			];
			Area::create($data);
			$area_id = DB::getPdo()->lastInsertId();
			
			$slug_name = str_slug($data['name'], "-");

			Area::where('id',$area_id)->update(['url_alias'=>$slug_name.'-'.$area_id]);
			
			$res = Area::updateLatLong($area_id);
			if($res) {
				echo json_encode(array('success'=>true));
			} else {
				echo json_encode(array('success'=>false,'error'=>'Area is invalid. Please again try'));	
			}			
		} catch(\Exception $e) {
			echo json_encode(array('success'=>false,'error'=>$e->getMessage()));			
		}
 	}	
 	
	public function getareas() {
		$input = Input::all();
		$Areas = Area::lang()->select('url_alias', 'name','id')->where('city_id',$input['cityId'])->whereNotIn('url_alias',[''])->get();	

		echo json_encode(array('html'=>$Areas->toArray()));
 	}

 	public function getareasnonjsn() {
		$input = Input::all();
		$result = '';
		$Areas = Area::lang()->select('*')->where('city_id',$input['cityId'])->get();

		if(count($Areas)>0) {
			$result .= '<option value="">'.\Lang::get('home.area').'</option>';
			foreach($Areas as $area){
				$result .= '<option data-option="'.$area['id'].'" value="'.$area['id'].'">'.ucfirst($area['name']).'</option>';			
			}
		} else {
			$result .= 'Notfound';
		}					
		return $result;	
 	}


 	public function getStates(){
 		$request = Request::all();
		 $states = State::select('*')->where('country_id',$request['countryId'])->get();
		 $result = '';
		 $result .= '<option value="">'.\Lang::get('home.state').'</option>';
		 foreach($states as $state){
		 	$result .= '<option data-option="'.$state->state_id.'" value="'.$state->state_id.'">'.ucfirst($state->state_name).'</option>';
		 }		 
		return $result;
 	}


 	public function getCities() {
		$request = Request::all();
		$cities = City::lang()->select('*')->where('state_id',$request['stateId'])->get();
		$result = '';
		$result .= '<option value="">'.\Lang::get('home.city').'</option>';
		foreach($cities as $city) {
			$result .= '<option data-option="'.$city['id'].'" value="'.$city['id'].'">'.ucfirst($city['name']).'</option>';			
		}		 
		return $result;
 	}


 	/* request 
	 * customer number
 	*/
 	public function getCustomer() {
 		try {
			$request = Request::all();
			$request['contactNumber'] = substr($request['contactNumber'], -10);
			$customer = User::select('user.id','user.first_name','user.last_name','user.contact_number')
						->Where('user.contact_number', 'like', '%' . trim($request['contactNumber']) . '%')->first();
			$html = view('admin.order.ajaxcustomer',['customer' => $customer]);	
			echo $html;
			die;
		}
		catch(\Exception $e) {
			print_r($e->getMessage());
			exit;		
		}	
 	}


 	public function optDeliveryAddress() {
 		$request = Request::all();  
 		/*$useraddresses = UserAddress::where('user_id',$request['userId'])->get();
 		if(count($useraddresses)>0){
 			$addressDetail =  User::where('id',$request['userId'])->first()->ajaxUserAddressDetail($request['addressId']); 		 		 		 			
 		}else{
 			$addressDetail =  'noaddress'; 		 		 		 			
 		}
 		$addressDetail =  'noaddress'; */
 		$addressDetail =  User::where('id',$request['userId'])->first()->ajaxUserAddressDetail($request['addressId']); 		 		 		 			
 		return $addressDetail; 		 		
 	}


  	/* request 
	 * restaurant id
 	*/
 	public function getRestaurant() {
		$request = Request::all();
		$cart = new Cart;
		$dayOfWeek = date('N');
 		$cart->clearcart();
 		$cart->clearcartOthercoupan();
 		// ddd($cart->getData());
		try {
			if(!empty($request['restaurant_id'])) {
				$restaurantdetails = Restaurent::restaurantfulldetailsForview($request['restaurant_id']);
				if(!empty($restaurantdetails['restaurantdetails']->restaurent_urlalias))
				{
					$restaurantdetails = array_merge($restaurantdetails, array('success'=>true, 'restaurantUrl'=>$restaurantdetails['restaurantdetails']->restaurent_urlalias));
					
					$delivery_charge = $restaurantdetails['restaurantdetails']->delivery_charge;
	 				$delivery_applicable = $restaurantdetails['restaurantdetails']->delivery_applicable;
	 				
					// $cart->setdeliveryCharges($restaurantdetails['restaurantdetails']->delivery_charge);
					$cart->setdeliveryCharges($delivery_charge, $delivery_applicable);
					
					$cart->setPackagingFees($restaurantdetails['restaurantdetails']->packaging_fees);
					$cart->setCGST($restaurantdetails['restaurantdetails']->cgst);
					$cart->setSGST($restaurantdetails['restaurantdetails']->sgst);
					
					// echo $cart->deliveryCharges();die;
					// ddd($cart->getData());
					
					$html = view('admin.order.ajaxrestaurantsearch', $restaurantdetails);					
				}
			} else {
				$html = view('admin.order.ajaxrestaurantsearch',['success'=>false,'message'=>trans('admin.not.found')]);
			}
		}
		catch(\Exception $e) {
			$html = view('admin.order.ajaxrestaurantsearch',['success'=>false,'message'=>trans('admin.not.found')]);
		}
		echo $html->render();
 	}
 	

 	public function getProductByRestaurantId() {
 		$request = Request::all(); 
 		try {
 			$products = Product::lang()->select('name','id')->where(['restaurant_id'=>$request['restaurantId'],'status'=>1])->get();	
 			echo json_encode(array('success'=>true,'products'=>$products));			
 		} catch(\Exception $e) {
			echo json_encode(array('success'=>false,'error'=>$e->getMessage()));			
		}
 	}
 	
 	public function subscribe() {
 		$input = Request::all(); 
 		try { 
 			$data = User::where(['status'=>1,'email'=>trim($input['email'])])->first();
			if(empty($data)) {
				$check = NewsLetter::where('email',$input['email'])->first();
				if(empty($check)){
					NewsLetter::create(['email'=>$input['email']]);
				}
			} else {
				$data->fill(['newsletter'=>1])->save();		
			}

			Mail::send('emails.subscribe', ['data'=> $data], function($message)  use ($input) {
				$message->from(\Config::get('constants.administrator.email'), \Config::get('constants.site_name'));
				$message->to($input['email'], 'Welcome')->subject( trans('email.thanks_for_sub_full') );
			});

 			echo json_encode(array('success'=>true,'message'=> trans('email.thanks_for_sub_full')));

 		} catch(\Exception $e) {
			echo json_encode(array('success'=>false,'error'=>$e->getMessage()));			
		}		
 	}
 	
 	public function section1Filter() {
 		$request = Request::all(); 
 		try {
 			$data = []; 
 			if(empty($request['from'])) {
 				$from = date('Y').'-01-01';
 			} else {
 				$from = $request['from'];
 			}
 			if(empty($request['to'])) {
 				$to = date('Y-m-d');
 			} else { 				
 				$to = $request['to']; 				
 			}
 			// widget value

 			$data['order_pending'] = Order::access('order')->where('status',\Config::get('constants.order.pending'))->whereBetween('date',[$from, $to])->get()->count();
 			$data['order_count'] = Order::access('order')->where('status','!=',\Config::get('constants.order.noconfirm'))->whereBetween('date',[$from, $to])->get()->count();
 			$data['sales_amount'] = Order::access('order')->whereNotIn('status',[\Config::get('constants.order.noconfirm'),\Config::get('constants.order.cancelled')])->whereBetween('date',[$from, $to])->get()->sum('amount');
 			$data['reviews_count'] = Review::access('review')->whereBetween('date',[$from, $to])->get()->count();
 			$data['tablebook_count'] = Table::access('table_booking')->whereBetween('date',[$from, $to])->get()->count();

 			// echo $data['order_pending'];die;
 			//pie chart value
 			$delivered_count = Order::access('order')->where('status',\Config::get('constants.order.delivered'))->whereBetween('date',[$from, $to])->get()->count();
 			
 			$open_for_drivers_count = Order::access('order')->where('status',\Config::get('constants.order.open_for_drivers'))->whereBetween('date',[$from,$to])->get()->count();
 			$dispatched_count = Order::access('order')->where('status',\Config::get('constants.order.dispatched'))->whereBetween('date',[$from, $to])->get()->count();
 			$data['pie_data'] = [$data['order_count'],$delivered_count,$data['order_pending'],$open_for_drivers_count,$dispatched_count];

 			//Line char value
 			$fromYear = date('Y',strtotime($from));
 			$toYear = date('Y',strtotime($to));
 			$search_text = 'Sales('.html_entity_decode(\Config::get('constants.currency')).')'; 			 		
 			$yLabel = [];			  
 			$lineChartHeading = '';
 			$fromMonth = date('n',strtotime($from));
 			$toMonth = date('n',strtotime($to));

 			if($fromMonth != $toMonth) {
 				$lineChartHeading .='Monthly wise sales in ';
 				$xLabel = [];
	 			for(;$fromMonth<=$toMonth;$fromMonth++) {
	 				$xLabel[] = date('M',strtotime($fromYear.'-'.$fromMonth.'-01'));
	 				$yLabel[] = 0;
	 			}
	 			$groupByMonthlySales = Order::access('order')->select('date','amount',DB::raw('SUM(amount) as total_amount'))->whereNotIn('status',[\Config::get('constants.order.noconfirm'),\Config::get('constants.order.cancelled')])->whereBetween('date',[$from,$to])->groupBy(DB::raw('YEAR(date)'))->groupBy(DB::raw('MONTH(date)'))->orderBy('date')->get();
 				if(!empty($groupByMonthlySales)) {
	 				//prd($groupByMonthlySales->toArray());
	 				foreach($groupByMonthlySales as $sales) {
	 					$m = date('M',strtotime($sales->date));
	 					$key = array_search($m, $xLabel);
	 					$yLabel[$key] = $sales->total_amount;
	 				}
 				}
 			} else {
 				$lineChartHeading .= 'Daily wise sales in ';
	 			$fromDays = date('j',strtotime($from));
	 			$toDays = date('j',strtotime($to));
 				$xLabel = [];
	 			for(;$fromDays<=$toDays;$fromDays++) {
	 				$xLabel[] = $fromDays;
	 				$yLabel[] = 0;
	 			}
	 			$groupByDateSales = Order::access('order')->select('date','amount',DB::raw('SUM(amount) as total_amount'))->whereNotIn('status',[\Config::get('constants.order.noconfirm'),\Config::get('constants.order.cancelled')])->whereBetween('date',[$from, $to])->groupBy('date')->orderBy('date')->get();
 				if(!empty($groupByDateSales)) {
	 				foreach($groupByDateSales as $sales) {
	 					$d = date('j',strtotime($sales->date));
	 					$key = array_search($d, $xLabel);
	 					$yLabel[$key] = $sales->total_amount;	 					
	 				} 					
 				}				 			 				
 			}

 			$lineChartHeading .= html_entity_decode(\Config::get('constants.currency'));
 			$data['line_data'] = ['heading'=>$lineChartHeading,'x_label'=>$xLabel,'y_label'=>$yLabel,'search_text'=>$search_text];

 			echo json_encode(array('success'=>true,'data'=>$data));

 		} catch(\Exception $e) {
			echo json_encode(array('success'=>false,'error'=>$e->getMessage()));			
		}
 	}

 	public function countCartProduct() {
 		$request = Request::all(); 
 		$cart = new Cart;
 		$data = $cart->getData();
 		
 		if( count($data) > 0 ) {
 			$restaurantId = array_column($data, 'restaurant_id')[0];
			if( $request['restId'] != $restaurantId ) {
				$cart->clearcartOthercoupan();
				$cart->clearcart();
			}
 		}
 		$data = $cart->getData();

 		return count($data);
 	}

 	public function getmenulist() {
 		$request = Request::all();
		$menus = Menu::select('*')->where('restaurant_id',$request['restaurant_id'])->get();
		$result = '';

		foreach($menus as $menu){
		 	$result .= '<option data-option="'.$menu->id.'" value="'.$menu->id.'" ';
		 	if(isset($request['menu_id'])){
			 	if($menu->id == $request['menu_id'])
			 	{
			 		$result .= 'selected';
			 	}
			 }	
		 	$result .= '>'.ucfirst($menu->name).'</option>';
		 }		 
		return $result;
 	}


}/*end of class*/