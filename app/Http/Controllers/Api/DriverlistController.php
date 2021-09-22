<?php 
namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Auth;
use \Validator;
use Hash;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use DB, Config, Redirect, Exception;
use App\Restaurent;
use App\helpers;
use App\Product, App\Coupon, App\City, App\User, App\Menu;
use App\Setting;
use App\Order;
use App\Image;
use App\DriverDecline;
use App\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Session, View;
use App\OrderStatus;
use App\Traits\StaticPushNotify;



class DriverlistController extends \App\Http\Controllers\Controller {

    // public function testNotification(Request $request)
    // {
    //     try {

    //         StaticPushNotify::$driverMessages = [
    //             'title'             => 'New order created.',
    //             'message'           => 'Hi Vijay Anand',
    //             'subtitle'          => 'New order created.',
    //             'tickerText'        => 'Ticker text here...Ticker text here...Ticker text here',
    //             'vibrate'           => 1,
    //             'sound'             => 1,
    //             'largeIcon'         => 'large_icon',
    //             'smallIcon'         => 'small_icon',
    //             'type'              => 'driver',
    //             'notificationID'    => uniqid()
    //         ];


            

    //         //$dataReturn = Order::notifyUserAboutOrderStatusDelivered($getOrder->toArray(), $getOrder->id);



    //         StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');
    //         //StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
            
    //         // $token = array('fAGD22Q2UL4:APA91bGunyFYlKrP8bz1O519fk5uWBKNB99kHYB3yvgQJ24efBfMdhnLAROuiDls5Ror59GzVRawYBRVgEwqM5lVcPoDi1MCHsIbVIKbbbREZJhJIM7u8fc76DOUcUWs1pcL6VIgA-wx');

    //         $token = array('foVB6z3l2ak:APA91bGN_FPoEpP1-2B9GszX9eFghUEKoBVehXlXyrQfXFW8bwRO4fjeeWMXHNdx4_A4ettTuhp0xmN3O6yTXpwdRulK390GmorWQRxplSlUStMVMSGaGd_BVgv73qrMqxbPBi0tp3q3');
    //         // Notify drivers for the created orders
            
    //         StaticPushNotify::setReceivers($token);

    //         //$notificationResponse = StaticPushNotify::notify(true);
    //         $body = "hi this mesage";
    //         $title = "this is title";

    //         $notificationResponse = StaticPushNotify::notify(true);


    //         print_r($notificationResponse);

    //     }
    //     catch(Exception $e)
    //     {
    //         return $this->apiResponse(["message"=>$e->getMessage()],true);

    //     }

    // }

    public function changePassword(Request $request) 
    {
        try
        {
            $inputs = $request->all();
            $validatedData = Validator::make(
            $request->all(),[
                'id' => 'required',
                'old_password' => 'required|min:6|max:20',
                'new_password' => 'required|min:6|max:20',
                ]
            );
            
            $rules = [
                'id' => 'required',
                'old_password' => 'required|min:6|max:20',
                'new_password' => 'required|min:6|max:20'
                ]; 

            if ($validatedData->fails()){
               $out_error = [];
               foreach($rules as $key => $value) {
                    $errors = $validatedData->errors();
                    if($errors->has($key)) { 
                        $out_error[]= $errors->first($key);
                        $var = $errors->first($key);
                        break;
                    }
                }           
                return $this->apiResponse(['error' => $var ,'message' => $var], true);
            }
            $user = User::where('id','=',$inputs['id'])->first();
            if($user->role_id != 4)
            {
                return $this->apiResponse(['message' => 'User is not driver'], true);
            }
            // $password2 = ($user['password']);
            // echo $password2;
            // echo "good";
            $hashedPassword = $user['password'];
            if ( Hash::check($request->old_password, $hashedPassword) ){
                // $user->password = Hash::make($request->new_password);
                $user->password = bcrypt($request->new_password);
                $user->save();
                return $this->apiResponse(['success' => 'true','message' => 'Successfully change password', 'data'=>$user, 'token' => $token]);
            }
            return $this->apiResponse(['message' => 'Old password doesnot match'], true);

        } catch(\Exception $e) {
            return $this->apiResponse(['error' => $e->getMessage()],true);
        }
    }


    public function signout(Request $request) 
    {
        try
        {
            $inputs = $request->all();
            if(!isset($inputs['driver_id'])  || empty($inputs['driver_id']))
            {
                return $this->apiResponse(['message' => 'driver id  not be null'], true);
            }
            else
            {
                $data = User::where('id','=',$inputs['driver_id'])->first();
                if( !$data )
                {
                    return $this->apiResponse(['message' => 'Invalid driver_id  credentials'], true);
                }
                else
                {
                    if ($data->role_id != 4) {
                        return $this->apiResponse(['message' => 'User is not a driver.', 'data'=>null, 'token' => null], true);
                    }
                    $data->token = null;
                    $data->device_token = null;
                    $data->save();
                    return $this->apiResponse(['success' => 'true' ,'message' => 'successfull logout']);
                }
            }

        } catch(\Exception $e) {
            return $this->apiResponse(['error' => $e->getMessage()],true);
        }
    }


    public function update( Request $request)
    {
        try{
            $input = $request->all();
            $user = User::findOrFail($input['id']);
            // $this->validate($request, [
            //     'id' => 'required',
            //     'first_name' => 'required',
            //     'last_name' => 'required',
            //     'contact_number'=>'required|unique:user,contact_number,'.$user->id,
            //     'email'=>'required|email|unique:user,email,'.$user->id,         
            //     'image'=>'mimes:jpeg,bmp,png|max:1000',
            //     'countrycode' => 'required',

            // ]);
            $validatedData =  Validator::make($request->all(),[
                'email' => 'required|email',
                'id'     => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'contact_number'=>'required|numeric|digits_between:9,10',                  
                'countrycode' => 'required',
            ]);

            $rules = ['email' => 'required|email',
                'id'     => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'contact_number'=>'required|numeric|digits_between:9,10',                  
                'countrycode' => 'required']; 
            //$v = Validator::make($input, $rules);

            if ($validatedData->fails()){
               $out_error = [];
               foreach($rules as $key => $value) {
                    $errors = $validatedData->errors();
                    if($errors->has($key)) { 
                        $out_error[]= $errors->first($key);
                        $var = $errors->first($key);
                        break;
                    }
                }           
                //return response()->json(['message' => $out_error,'status'=>'error']);
                return $this->apiResponse(['error' => $var ,'message' => $var], true);
            }

            $data = User::where('contact_number','=',$request->contact_number)->first();
            if($data)
                if($data->id != $request->id)
                    return $this->apiResponse(['message' => 'Contact number is already exist'], true);
            
            if($request->file('image')){            
                $type = \Config::get('constants.image.for.user');
                Image::deleteImage($request->input('old_image'),$type);
                $input['profile_image'] = Image::imageUpload($request->file('image'),$type);        
            }
            $user->fill($input)->save();

            // if(Auth::attempt($request->only(['email', 'password']), $request->input('remember'))) {
            //         $user = Auth::user();
            //         $token = md5(uniqid($user->email, true));
            //         $user->token = $token;

            //         $user->save();

            //         return $this->apiResponse(['message' => 'Successfully Autheticated', 'data'=>$user, 'token' => $token]);
            //     }
            
            //Session::flash('flash_message', trans('admin.successfully.updated'));
            //return redirect()->back();
            return $this->apiResponse(['message' => 'Successfully driver  updated','data' => $user]);
        }
        catch(\Exception $e) {
            return $this->apiResponse(['error' => $e->getMessage()],true);
        }
    }


    public function register(Request $request) 
    {
        try
        {
            $inputs = $request->all();
            $validatedData = Validator::make(
            $request->all(),[
                //'email' => 'required|email'
                'email' => 'required|email',
                'password' => 'required|min:6|max:20',
                'contact_number' => 'required|numeric|digits_between:9,10',
                'first_name' => 'required',
                'last_name' => 'required',
                'countrycode' => 'required',
                //'otp' => 'required|numeric',
                ]
            );
            
            $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6|max:20',
            'first_name' => 'required',
            'last_name' => 'required',
            'contact_number'=>'required|numeric|digits_between:9,10',                  
            'countrycode' => 'required'
            ]; 
            //$v = Validator::make($input, $rules);

            if ($validatedData->fails()){
               $out_error = [];
               foreach($rules as $key => $value) {
                    $errors = $validatedData->errors();
                    if($errors->has($key)) { 
                        $out_error[]= $errors->first($key);
                        $var = $errors->first($key);
                        break;
                    }
                }           
                //return response()->json(['message' => $out_error,'status'=>'error']);
                return $this->apiResponse(['error' => $var ,'message' => $var], true);
            }
            $data = User::where('contact_number','=',$request->contact_number)->first();
            if($data)
                return $this->apiResponse(['message' => 'Contact number is already exist'], true);
            $user1 = User::create(['email'=>$request->email, 'password' => bcrypt($request->password),
                                    'contact_number'=>$request->contact_number, 'first_name' => $request->first_name,
                                    'last_name'=>$request->last_name, 'countrycode'=>$request->countrycode]);
            
            $id = \DB::getPdo()->lastInsertId();
            $user = User::findOrFail($id);
            $user->status = 1;
            $user->verified = 0;
            $user->role_id = '4';
            $user->save();

            //try logging in the user
            if(Auth::attempt($request->only(['email', 'password']), $request->input('remember'))) {
                $user = Auth::user();
                $token = md5(uniqid($user->email, true));
                $user->token = $token;
                $user->save();
                return $this->apiResponse(['message' => 'Successfully Autheticated', 'data'=>$user, 'token' => $token]);
            }
            return $this->apiResponse(['message' => 'Successfully driver  Registered','data' => $user]);

        } catch(\Exception $e) {
            return $this->apiResponse(['error' => $e->getMessage()],true);
        }
    }


	public function driverList(Request $request) {
        try {
            $input = $request->all();
            if( empty($input['user_id']) ){ 
                return $this->apiResponse(["error"=>"User Id can't be null"], true);
            } else{
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
                        return $this->apiResponse(["orders"=>json_decode($order)]);
                    }       
            }
        } catch(\Exception $e) {
            return $this->apiResponse(["error"=>$e->getMessage()], true);
        }
    }
				
}
