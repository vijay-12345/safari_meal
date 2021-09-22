<?php 
namespace App\Http\Controllers\Api;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB, Config, Redirect, Exception;
use App\Restaurent;
use App\helpers;
use App\Product, App\Coupon, App\City, App\User;
use App\Setting;
use App\Order;
use Illuminate\Http\Request;

class OrderlistController extends \App\Http\Controllers\Controller {
	
	public function orderList(Request $request) {
		$inputs = $request->all();			
		try {

			if(!empty($inputs['offset'])) {			
				$offset=$inputs['offset'];
			} else {
				$offset=0;
			}
			if(!empty($inputs['sorting'])) {			
				$sorting=$inputs['sorting'];
			} else {
				$sorting="desc";
			}
			if(!empty($inputs['sort_field'])) {			
				$sort_field=$inputs['sort_field'];
			} else {
				$sort_field="date";
			}
			if(!empty($inputs['from'])) {			
				$start_date=$inputs['from'];
			} else {
				$st=Order::orderBy('date','ASC')->first();
				$start_date = $st['date'];
			
			}
			if(!empty($inputs['to'])) {			
				$end_date=$inputs['to'];
			} else {
				$end_date=$inputs['date'];
			}

			if(!empty($inputs['limit'])) {			
				$orderlimit=$inputs['limit'];
			} else {
				$order = Order::where('id','=',$inputs['order_id'])
			                ->orWhere(['user_id'=>$inputs['user_id']])
							->orWhere('status','=',$inputs['status'])
							->orWhere('date','=',$inputs['date'])
							->orWhereBetween('date',[$start_date,$end_date])
							->get();
				$orderlimit=count($order);
			}
			if(!isset($inputs['user_id'])) {
				return $this->apiResponse(["error"=>'Please provide user_id'],true);
			}
			$order = Order::where(['restaurant_id'=>$inputs['restaurant_id']])
		                    ->where(function($query) use($inputs,$start_date,$end_date){
		                    $query->whereBetween('date',[$start_date,$end_date])
		                    ->orWhere('date','=',$inputs['date'])
    		                  ->orWhere('id','=',$inputs['order_id'])
    						  ->orWhere('status','=',$inputs['status']);
		                })
		                       //where('id','=',$inputs['order_id'])
			    //             ->orWhere(['user_id'=>$inputs['user_id']])
							// ->orWhere('status','=',$inputs['status'])
							// ->orWhere('date','=',$inputs['date'])
							// ->orWhereBetween('date',[$start_date,$end_date]) 
							
							->with([

								'items',
								'customer',
								'driver',
								'restaurant'

							])
							->orderby($sort_field,$sorting)
							//->offset($offset)
							//->limit($orderlimit)
							//->orderby($inputs['sorting'])
							//->orderby('time','desc')
							->paginate($orderlimit);
			if(!$order->isEmpty()) {
				$data = $order->toArray();
			} else {
				$data = null;
			}
			return $this->apiResponse(["data"=>$data]);
		} catch(\Exception $e) {
			return $this->apiResponse(["error"=>$e->getMessage()],true);
		}			
	}
					
}
