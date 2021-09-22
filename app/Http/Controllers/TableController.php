<?php 

namespace App\Http\Controllers;

use Session,Request,View,Redirect;
use DB;
use App\Restaurent,App\helpers,App\Review,App\Timing,App\Table;
use App\Area,App\Product,App\UserAddress, App\Menu, App\Coupon;
use \Exception;
use App\Cart, App\Order,Lang,Auth, Validator,Input, App\Cuisine, App\Setting;
use App\Traits\CalculateDileveryCharge;

class TableController extends Controller
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
		

   	public function tablebook($restaurantUrl)
	{
		try {
			$restaurantdetails	= Restaurent::lang()->where('restaurent_urlalias', $restaurantUrl)->first();

			if(!$restaurantdetails) throw new Exception("No record was found.", 404);

			$restaurantdetails	= Restaurent::restaurantfulldetailsForview($restaurantdetails->id);
			$restaurantdetails 	= array_merge($restaurantdetails, array('restaurantUrl'=>$restaurantUrl));

			// New delivery charges set according to distance
			// echo '<pre>';prd($restaurantdetails['restaurantdetails']->toArray());

			$locations = [];
			$area_location = Request::get('location');
			$restaurant = $restaurantdetails['restaurantdetails'];
			
			if ($area_location && $restaurant) {
				$temp = explode(',', $area_location);
				if(count($temp) > 1) {
					$locations['first_location'] = [
							'latitude' 	=> $temp[0],
							'longitude'	=> $temp[1]
						];
					$locations['second_location'] = [
							'latitude' 	=> $restaurant->latitude,
							'longitude'	=> $restaurant->longitude
						];
				}
			}
			
			
			//ddd($restaurantdetails);
			
			return View::make('table_book', $restaurantdetails);		
			
		} catch (\Exception $e) {
			// prd($e->getMessage());
            return redirect('/');
        }

	}


	public function add()
	{
		try {
			///prd(Request::all());
			$data = [];
			$cart = new Cart;
			$restaurantIdsHaveProduct = Restaurent::getRestaurantIdsHaveProduct();

			if(Session::get('access.role') != 'admin') {			
				$data['restaurant_data'] = Restaurent::whereIn('id',Session::get('access.restaurant_ids'))->where('status',1)->whereIn('id',$restaurantIdsHaveProduct)->lists('name','id');
			} else {
				$data['restaurant_data'] = Restaurent::where('status',1)->whereIn('id',$restaurantIdsHaveProduct)->lists('name','id');
			}
			//prd($data['restaurant_data']);
			$input = Request::all();
			
			Table::dateFormat($input);
		    $ordernum = Table::createOrder($input, New Cart());
		    	
		    if($ordernum == false) {
				return redirect()->back()->withErrors(trans('Cart don\'t have product. Please add and again try'));;
		    }
		    
			Session::put('flash_message', trans('admin.successfully.added')." Booking No.:: ".$ordernum);
			
			return redirect()->back();
	
		}
		catch(\Exception $e) {
			Session::flash('flash_message', trans("Booking not created because of ".$e->getMessage()));				     
			return redirect()->back();
			exit;		
		}	
		return view('table_book',['data' => $data]);
	}


	public function thanks($ordernum)
	{
		$order = Order::where(['order_number' => $ordernum])->first();

	    if( empty($order) ) return redirect('/');

		return View::make('thanks')->with(['ordernum'=> $ordernum, 'paymentmethod'=>Request::input('paymentmethod')]);
    }


	public function createOrder() {
		try {
			$request = Request::all();
			$cart = new Cart;
			
			// Apply coupon
			if(!empty($request['coupon_id'])) {
				$validid = Coupon::where(['id'=>$request['coupon_id']])->first();
				$cart->setcoupandetails(serialize($validid));	
			}
			
			// Set order type
			$cart->setOrderType( isset($request['order_type']) ? $request['order_type'] : null );
			
			// Create order
			$ordernum =	Order::createOrder($request, $cart);

			if(is_numeric($ordernum)) {
				$detail['restaurent_id']	=	$request['restaurant_id'];
				$detail['status']			=	Lang::get('restaurent.Order .created .successfully.');
				$detail['ordernum']			=	$ordernum;				
				return redirect('proceedtosecurecheckout/'.$ordernum)->with($detail);											
			}

			Session::put('Errormessage', Lang::get('restaurent.There .is .no .Product .in .cart.'));
			
			return View::make('cart.checkOut');

		} catch(\Exception $e) {
			print_r($e->getMessage());
			exit;		
		}

		Session::put('Errormessage', Lang::get('restaurent.There .is .no .Product .in .cart.'));
		
		return View::make('cart.checkOut');
	}
	
	public function updateTableStatus(Request $request) {
	    prd(Request::all());
		$inputs = Request::all();
		DB::table('table_booking')->where('id', $inputs['id'])->update(['status' => $inputs['status']]);
		echo json_encode(['list_data'=>'success']); exit();
		
	}
	
    
}