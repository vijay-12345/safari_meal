<?php 
namespace App\Http\Controllers\Api;
use DB, Config, Redirect, Exception;
use App\Restaurent;
use App\helpers;
use App\Product, App\Coupon, App\City, App\User, App\Menu;
use App\Setting;
use App\Order;
use Illuminate\Http\Request;


use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateorderController extends \App\Http\Controllers\Controller {


	public function updateOrderStatus(Request $request) {
        try {
            $input = $request->all();
            // if( empty($input['user_id']) ){ 
            //     return $this->apiResponse(["error"=>"User Id can't be null"], true);
            // } else{
                
            if( empty($input['order_id']) ){
                return $this->apiResponse(["error"=>"Order Id can't be null"], true);
            } else if( empty($input['status']) ){
                return $this->apiResponse(["error"=>"Status can't be null"], true);
            } else{
                $order = Order::select('id')
                        ->where('id', $input['order_id'])->first();
                
                        
                if($order){
                    $order->status = $input['status'];
                    $order->save();


                    $orders = Order::where('id','=',$input['order_id'])->first();
                    $ordernum = $orders['order_number'];
                    $getOrder = Order::where('order_number', $ordernum)->first();

                    if($order->status == 7)
                    {
                        // if ($getOrder) $dataReturn = Order::sendOrderNotificationForRestaurant($getOrder->toArray(), $getOrder->id);
                        if ($getOrder) $dataReturn = Order::sendOrderNotificationForRestaurant2($getOrder->toArray(), $getOrder->id);
                    }
                    if($order->status == 3)
                    {
                        // if ($getOrder) $dataReturn = Order::notifyUserAboutOrderStatusDriverAccepted($getOrder->toArray(), $getOrder->id);
                        if ($getOrder) $dataReturn = Order::notifyUserAboutOrderStatusDriverAcceptedForRestaurant($getOrder->toArray(), $getOrder->id);

                    }
                    if($order->status == 6)
                    {
                        // if ($getOrder) $dataReturn = Order::notifyUserAboutOrderStatusCancelled($getOrder->toArray(), $getOrder->id);
                        if ($getOrder) $dataReturn = Order::notifyUserAboutOrderStatusCancelledForRestaurant($getOrder->toArray(), $getOrder->id);
                        if ($getOrder) $dataReturn = Order::notifyUserAboutOrderStatusCancelledByRestaurantToUser($getOrder->toArray(), $getOrder->id);


                    }
                    if($order->status == 5)
                    {
                        // if ($getOrder) $dataReturn = Order::notifyUserAboutOrderStatusDelivered($getOrder->toArray(), $getOrder->id);
                        if ($getOrder) $dataReturn = Order::notifyUserAboutOrderStatusDeliveredForRestaurant($getOrder->toArray(), $getOrder->id);
                    }



                    //$this->notifyRestaurant($ordernum);





                    return $this->apiResponse(["message"=>"Order no ".$input['order_id']." updated successfully"]);
                } else{
                    return $this->apiResponse(["error"=>"This order not belongs to your restaurant"], true);
                }
            }
               
            // }
        } catch(\Exception $e) {
            return $this->apiResponse(["error"=>$e->getMessage()], true);
        }

    }


    // public function notifyRestaurant( $orderNo )
    // {

    //     //$order = Order::where('order_number', $orderNo)->first();
    //     $getOrder = Order::where('order_number', $orderNo)->first();
    //     if ($getOrder) $dataReturn = Order::sendOrderNotificationForRestaurant($getOrder->toArray(), $getOrder->id);
    //     return true;
    // }




    public  function sendOrderNotificationForRestaurant(Array $order, $orderId)
    {
        try {

            
            self::prepareNotificationMessageForRestaurant( $order );
            
            // echo "good";
            // die;

            $user = new User;

            $usernotify = Order::where('id','=',$orderId)->first();


            $usertoken = User::where('id','=',$usernotify['user_id'])->first();

            $deviceTokenToDrivers = $usertoken['device_token'];
            
            
            

            StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

            // StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
            //StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
            
            // Notify drivers for the created orders
            if ($deviceTokenToDrivers) {
             
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
                StaticPushNotify::setReceivers($final);
            
                
                $notificationResponse = StaticPushNotify::notify(true);
                return $notificationResponse;
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
        return;
    }

    public  function notifyUserAboutOrderStatusDriverAccepted(Array $order, $orderId)
    {
        try{
        self::prepareNotificationMessageForDriverAccepted( $order );
            
            

            $user = new User;

            $usernotify = Order::where('id','=',$orderId)->first();


            $usertoken = User::where('id','=',$usernotify['user_id'])->first();

            $deviceTokenToDrivers = $usertoken['device_token'];
            
            
            

            StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

            // StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
            //StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
            
            // Notify drivers for the created orders
            if ($deviceTokenToDrivers) {
             
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
                StaticPushNotify::setReceivers($final);
            
                
                $notificationResponse = StaticPushNotify::notify(true);
                return $notificationResponse;
            }
        }
        catch (Exception $e) {
            dd($e->getMessage());
        }
        return;
        
    }

    public  function notifyUserAboutOrderStatusCancelled(Array $order, $orderId)
    {
        try{
        self::prepareNotificationMessageForCancelled( $order );
            
            // echo "good";
            // die;

            $user = new User;

            $usernotify = Order::where('id','=',$orderId)->first();


            $usertoken = User::where('id','=',$usernotify['user_id'])->first();

            $deviceTokenToDrivers = $usertoken['device_token'];
            
            
            

            StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

            // StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
            //StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
            
            // Notify drivers for the created orders
            if ($deviceTokenToDrivers) {
             
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
                StaticPushNotify::setReceivers($final);
            
            
                $notificationResponse = StaticPushNotify::notify(true);
                return $notificationResponse;
            }
        }
        catch (Exception $e) {
            dd($e->getMessage());
        }
        return;
        
    }

    public  function notifyUserAboutOrderStatusDelivered(Array $order, $orderId)
    {
        try{
            // echo "good3";
            // die;
            
            self::prepareNotificationMessageForDelivered( $order );
            // echo "good4";
            // die;
            

            $user = new User;

            $usernotify = Order::where('id','=',$orderId)->first();


            $usertoken = User::where('id','=',$usernotify['user_id'])->first();

            $deviceTokenToDrivers = $usertoken['device_token'];
            
            
            

            StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

            // StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
            //StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
            
            // Notify drivers for the created orders
            if ($deviceTokenToDrivers) {
             
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
                StaticPushNotify::setReceivers($final);
            
                
                $notificationResponse = StaticPushNotify::notify(true);

                // print_r($deviceTokenToDrivers);
                // die;
                return $notificationResponse;
            }
        }
        catch (Exception $e) {
            dd($e->getMessage());
        }
        return;
        
    }


	
					
}
