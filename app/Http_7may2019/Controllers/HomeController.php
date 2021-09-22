<?php 

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\User;
use Validator;
use Auth;
use DB;
use App\Country,App\UserAddress;
use Illuminate\Contracts\Auth\Registrar;
use Input,App\City;
use App\Area,App\State;
use App\Order;
use App\OrderItem;

class HomeController extends Controller 
{
	/*
	|--------------------------------------------------------------------------
	| Home Controller
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
	public function __construct(Registrar $registrar)
	{					
		$this->registrar = $registrar;		
	}

	public function index()
	{	
		return view('home.home');
	}

	public function registrationSuccessful()
	{
		return view('auth/registration_successful');
	}

	public function updateProfile(Request $request)
	{	
		if ( !Auth::check()){
			return redirect('/');		    
		}	

		$rules = User::rulesUpdateWeb($request->input('id'));						
		$this->validate($request, $rules);		
		$currentUser = Auth::user();
		$currentUser->fill([$request->except(array(
				'contact_number','newsletter','first_name','last_name','countrycode','email'
				)),
				'contact_number' =>$request->input('contact_number'),
				'first_name' =>$request->input('first_name'),
				'last_name' =>$request->input('last_name'),
				'countrycode' =>$request->input('countrycode'),
				'email' =>$request->input('email'),
				'newsletter' => ($request->input('newsletter') ? 1 : 0)])->save();				
		return redirect('editprofile')->with(['status'=>'Profile edited successfully.' ]);
	}

	public function editProfile(){	
		if ( !Auth::check()){
			return redirect('/');		    
		}		
		$currentUser = Auth::user();		
		return view('editprofile')->with(['currentUser'=>$currentUser]);
	}

	public function changePassword(){		
		if ( !Auth::check()){
			return redirect('/');		    
		}
		$currentUser = Auth::user();
		return view('changePassword')->with(['currentUser'=>$currentUser]);
	}

	public function updatepassword(Request $request){				
		if ( !Auth::check()){
			return redirect('/');	    
		}		
		$currentUser = Auth::user();		
		$rules = User::rulesUpdatePassword($currentUser->id);							
		$this->validate($request, $rules);							
		$currentUser->update(['password' => bcrypt($request->input('new_password'))]);				
		return redirect('editprofile')->with(['status'=>'Password edited successfully.' ]);	    	   
	}

	public function addressBook(){

		if ( !Auth::check()){
			return redirect('/');	    
		}
		$detail = array();			
		$userid = Auth::user()->id;
		$userAddress = UserAddress::where('user_id',$userid)->get();			
		$detail['areas']	=Area::select('*')->get();
		$detail['states']	=State::select('*')->get();
		$detail['cities']	=City::select('*')->get();	
		$detail['useraddress']	=$userAddress;			
		return view('addressBook')->with($detail);				
	}

	public function updateAddressBook(Request $request)
	{
		if ( !Auth::check()){
			return redirect('/');	    
		}
		
		$userAddress=UserAddress::where('id',$request->input('id'))->first();
		$country = Country::select('country_id')->where('country_code',$request->input('country'))->first();							
		$userAddress->user_id			= $userAddress->user_id;
		$userAddress->first_address 	= $request->input('first_address');
		$userAddress->second_address 	= $request->input('second_address');
		$userAddress->country_id 		= $country->country_id;
		$userAddress->state_id 			= $request->input('state');
		$userAddress->city_id 			= $request->input('city');
		$userAddress->area_id			= $request->input('area');
		$userAddress->zip 				= $request->input('zip');
		$detail['countries']			= Country::select('*')->get();
		$detail['areas']				= Area::select('*')->get();
		$detail['states']				= State::select('*')->get();
		$detail['cities']				= City::select('*')->get();	
		$validator 						= $this->registrar->addnewaddressvalidator($request->all());	
		if ($validator->fails()){	
			$detail['errors'] = $validator->errors();				
			return view('cart.updateaddress')->with($detail);				
		}
		if(!is_numeric($request->input('state'))){				
			$userAddress->state_id  = State::insertGetId(['state_name'=>$request->input('state'),'country_id'=>$userAddress->country_id]);			
			
		}else{
			$userAddress->state_id = $request->input('state');
		}		
		
		if(!is_numeric($request->input('city'))){												
			$userAddress->city_id = City::insertGetId(['name'=>$request->input('city'),'state_id'=>$userAddress->state_id]);			
			
		}else{
			$userAddress->city_id = $request->input('city');
		}
		if(!is_numeric($request->input('area'))){															
			$userAddress->area_id = Area::insertGetId(['name'=>$request->input('area'),'city_id'=>$userAddress->city_id]);			
		
		}else{
			$userAddress->area_id = $request->input('area');
		}
		$userAddress->save();
		return view('cart.updateaddrsuccess')->with('status','Address edited Successfully.');	    			    	   
	}

	public function deleteAddress()
	{
		if ( !Auth::check()){
			return redirect('/');
		}

		$input = \Request::all();			
		$deleteMessage = DB::table('user_address')->where('id', $input['addrid'])->where('user_id', $input['uid'])->delete();		
		if($deleteMessage){
			//return back()->with(['status'=>'Address deleted successfully.' ]);	    	   	
			return 1;
		}else{
			return 0;
		}				
	}

	public function loadNewAddressBook(Request $request)
	{		
		$countries = Country::get();
		return view('cart.addnewaddress', ['countries' => $countries]);
	}

	public function loaduUpdatedAddressBook(Request $request)
	{
		$addressid = $request->input('addr');		
		$userAddress = UserAddress::where('id',$addressid)->first();
		$Area = Area::select('name')->where('city_id',$userAddress->city_id)->first();					
		$state = State::select('state_name')->where('state_id',$userAddress->state_id)->first();
		$city = City::select('name')->where('id',$userAddress->city_id)->first();			
		$detail =array();		
		$detail['addressid'] =$userAddress->id;
		$detail['user_id'] =$userAddress->user_id;
		$detail['first_address'] =$userAddress->first_address;
		$detail['second_address'] =$userAddress->second_address;
		$country = Country::select('country_code')->where('country_id',$userAddress->country_id)->first();							
		
		$detail['country'] =$country->country_code;
		$detail['stateId'] =$userAddress->state_id;
		$detail['cityId'] =$userAddress->city_id;
		$detail['areaId'] =$userAddress->area_id;
		$detail['state'] =$state->state_name;
		$detail['city'] =$city['name'];
		$detail['area'] =$Area['name'];
		$detail['zip'] =$userAddress->zip;
		$detail['areas']	=Area::select('*')->where('city_id',$userAddress->city_id)->get();
		$detail['states']	=State::select('*')->where('country_id',$userAddress->country_id)->get();
		$detail['cities']	=City::select('*')->where('state_id',$userAddress->state_id)->get();	
		$detail['countries'] =Country::select('*')->get();		
		return view('cart.updateaddress',$detail);				
	}
					
	public function addNewAddressBook(Request $request)
	{
		if ( !Auth::check()){
			return redirect('/');
		}

		$validator = $this->registrar->addnewaddressvalidator($request->all());				
		if ($validator->fails())
		{
			$countries = Country::select('*')->get();
			return view('cart.addnewaddress')->with(['countries'=>$countries, 'errors'=> $validator->errors()]);				
		}

		$address=array();
		$country = Country::select('country_id')->where('country_code',$request->input('country'))->first();	
		$address['user_id'] 		= $request->input('user_id');
		$address['first_address'] 	= trim( $request->input('first_address') );
		$address['second_address'] 	= trim( $request->input('second_address') );
		$address['landmark'] 		= trim( $request->input('landmark') );
		$address['zip'] 			= trim( $request->input('zip') );
		$address['country_id'] 		= $country->country_id;		
		if(!is_numeric($request->input('state'))){	
			$state_id = State::insertGetId(['state_name'=>$request->input('state'),'country_id'=>$address['country_id']]);			
			$address['state_id'] = $state_id;
		}else{
			$address['state_id']= $request->input('state');
		}		
		if(!is_numeric($request->input('city'))){												
			$cityId = City::insertGetId(['name'=>$request->input('city'),'state_id'=>$address['state_id']]);			
			$address['city_id']	= $cityId;

		}else{
			$address['city_id']	= $request->input('city');
		}
		if(!is_numeric($request->input('area'))){															
			$AreaId = Area::insertGetId(['name'=>$request->input('area'),'city_id'=>$address['city_id']]);			
			$address['area_id']	= $AreaId;		
		}else{
			$address['area_id']	= $request->input('area');
		}	
		
	    $address['address_json'] = UserAddress::getJsonAddr($address);								  	
	    UserAddress::create($address);	    
	    return view('cart.addaddrsuccess')->with('status','Address Added Successfully.');	    
	
	}

	public function setLang()
	{
		session(['lang' => $_GET['lang']]);
		return view('home.home');		  
	}

	public function help()
	{		
		return view('home.help');
	}

	public function orderHistory()
	{
		if ( !Auth::check()){
			return redirect('/');		    
		}
			
		$perPage = 20;
                $orders = DB::table('order as o')
			->leftJoin('restaurant as r', 'o.restaurant_id', '=', 'r.id')
			->where('o.user_id', Auth::user()->id)
			->where('o.status', '!=', 0)
			->orderBy('o.date', 'desc')
			->orderBy('o.time', 'desc')
			->select('o.*', 'r.name as restaurant_name', 'r.restaurent_urlalias')
			->paginate($perPage);

		return view('orderhistory')->with('orders', $orders);
	}
}
