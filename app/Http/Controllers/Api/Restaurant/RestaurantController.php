<?php

namespace App\Http\Controllers\Api\Restaurant;

use DB, Config, Redirect, Exception;
use App\Restaurent;
use App\helpers;
use App\Product, App\Coupon, App\City, App\User;
use App\Setting;
use App\Order;
use Illuminate\Http\Request;

class RestaurantController extends \App\Http\Controllers\Controller 
{
    public function __construct() {

    }

    /*
     * Order History API.  
     * Required parametrs: user_id INT
     * Other parameters : limit INT, offset INT, sorting STRING, sort_field STRING, date YYYY-mm-dd, 
     * 
    */
    public function orderList(Request $request) {
        try {
            $input = $request->all();
            if( empty($input['user_id']) ){ 
                return $this->apiResponse(["error"=>"User Id can't be null"], true);
            } else{
                $restauantOwner = Restaurent::select('id','owner_id')->where('owner_id', $input['user_id'])->first();
                if($restauantOwner){
                    $limit = 50;
                    $offset = 0;
                    $filter = [
                        'sorting'=>'desc',
                        'sort_field'=>'created_at'
                    ];
                    
                    if( ! empty($input['sorting'])){ 
                        $filter['sorting'] = $input['sorting'];
                    }
                    if( ! empty($input['sort_field'])){ 
                        $filter['sort_field'] = $input['sort_field'];
                    }
                    if( ! empty($input['limit'])){ 
                        $limit = $input['limit'];
                    }
                    if( ! empty($input['offset'])){ 
                        $offset = $input['offset'];
                    }
                    
                    $order = Order::leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
                        ->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')
                        ->leftjoin('user', 'order.user_id', '=', 'user.id')		
                        ->select('order.*','restaurant.name as restaurant_name', DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'),'user.first_name as customer_first_name','user.last_name as customer_last_name');
                    
                    if( ! empty($input['status'])){ 
                        $order->where('order.status','=',$input['status']);
                    }
                    if( ! empty($input['order_id'])){ 
                        $order->where('order.id','=',$input['order_id']);
                    }
                    if( ! empty($input['date'])){ 
                        $order->whereDate('order.date','=',$input['date']);
                    }

                    $orderData = $order->orderBy($filter['sort_field'], $filter['sorting'])
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
                    
                    if($orderData->isEmpty()) {
                        return $this->apiResponse(["orders"=>null]);
                    } else{
                        return $this->apiResponse(["orders"=>$orderData->toArray()]);
                    }
                } else{
                    return $this->apiResponse(["error"=>"User is not a restaurant owner"], true);
                }
            }
        } catch(\Exception $e) {
            return $this->apiResponse(["error"=>$e->getMessage()], true);
        }

    }

    /*
     * API for update order status.  
     * Required parametrs: user_id INT
     * Other parameters : limit INT, offset INT, sorting STRING, sort_field STRING, date YYYY-mm-dd
     * 
    */
    public function updateOrderStatus(Request $request) {
        try {
            $input = $request->all();
           

            
            if( empty($input['user_id']) ){ 
                return $this->apiResponse(["error"=>"User Id can't be null"], true);
            } else{
                $restauantOwner = Restaurent::select('id','owner_id')->where('owner_id', $input['user_id'])->first();
                //return $this->apiResponse(["orders"=>$restauantOwner]);
                if($restauantOwner){
                    if( empty($input['order_id']) ){
                        return $this->apiResponse(["error"=>"Order Id can't be null"], true);
                    } else if( empty($input['status']) ){
                        return $this->apiResponse(["error"=>"Status can't be null"], true);
                    } else{
                        $order = Order::select('id')
                                ->where('id', $input['order_id'])
                                ->where('restaurant_id',$restauantOwner['id'])
                                ->first();
                        if($order){
                            $order->status = $input['status'];
                            $order->save();

                            $orders = Order::where('id','=',$order_id)->first();
                            $ordernum = $orders['order_number'];
                            $this->notifyRestaurant($ordernum);



                            return $this->apiResponse(["message"=>"Order no ".$input['order_id']." updated successfully"]);
                        } else{
                            return $this->apiResponse(["error"=>"This order not belongs to your restaurant"], true);
                        }
                    }
                } else{
                    return $this->apiResponse(["error"=>"User is not a restaurant owner"], true);
                }
            }
        } catch(\Exception $e) {
            return $this->apiResponse(["error"=>$e->getMessage()], true);
        }
    }

    /*
     * Driver List API.  
     * Required parametrs: user_id INT
     * Other parameters : limit INT, offset INT, sorting STRING, sort_field STRING, date YYYY-mm-dd
     * 
    */
    public function driverList(Request $request) {
        try {
            $input = $request->all();
            if( empty($input['user_id']) ){ 
                return $this->apiResponse(["error"=>"User Id can't be null"], true);
            } else{
                $restauantOwner = Restaurent::select('id','owner_id')->where('owner_id', $input['user_id'])->first();
                if($restauantOwner){
                    $limit = 50;
                    $offset = 0;
                    $filter = [
                        'sorting'=>'desc',
                        'sort_field'=>'created_at'
                    ];
                    $date = date('Y-m-d');
                    
                    if( ! empty($input['sorting'])){ 
                        $filter['sorting'] = $input['sorting'];
                    }
                    if( ! empty($input['sort_field'])){ 
                        $filter['sort_field'] = $input['sort_field'];
                    }
                    if( ! empty($input['limit'])){ 
                        $limit = $input['limit'];
                    }
                    if( ! empty($input['offset'])){ 
                        $offset = $input['offset'];
                    }
                    if( ! empty($input['date'])){ 
                        $date = $input['date'];
                    }
                    
                    $order = Order::leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
                        ->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')
                        ->leftjoin('user', 'order.user_id', '=', 'user.id')		
                        ->select('order.*','restaurant.name as restaurant_name', DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'),'user.first_name as customer_first_name','user.last_name as customer_last_name')
                        ->whereDate('order.date','=', $date)
                        ->orderBy($filter['sort_field'], $filter['sorting'])
                        ->limit($limit)
                        ->offset($offset)
                        ->get();
                    
                    if($order->isEmpty()) {
                        return $this->apiResponse(["orders"=>null]);
                    } else{
                        return $this->apiResponse(["orders"=>$order->toArray()]);
                    }
                } else{
                    return $this->apiResponse(["error"=>"User is not a restaurant owner"], true);
                }
            }
        } catch(\Exception $e) {
            return $this->apiResponse(["error"=>$e->getMessage()], true);
        }

    }



    public function notifyRestaurant( $orderNo )
    {

        //$order = Order::where('order_number', $orderNo)->first();
        $getOrder = Order::where('order_number', $orderNo)->first();
        if ($getOrder) $dataReturn = Order::sendOrderNotificationForRestaurant($getOrder->toArray(), $getOrder->id);
        return true;
        


    }






}