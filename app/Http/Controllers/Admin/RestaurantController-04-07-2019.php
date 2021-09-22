<?php

namespace App\Http\Controllers\Admin;

use DB;
use Form;
use Hash;
use Auth;
use \Mail;
use Session;
use App\User;
use App\Image;
use Validator;
use \Exception;
use App\Timing;
use App\Restaurent;
use App\RestaurantCuisine;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Pagination\Paginator;

class RestaurantController extends \App\Http\Controllers\Controller 
{
	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');		
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{		
		//clear session first time	
		if ($request->isMethod('get') && count($request->all()) == 0){	
			Session::forget('filter');			
		}	

		/* filter 			
		*/	
		if($request->has('sorting')) {
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		} else {
			$filter = ['sort'=>'asc','field'=>'name'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		// prd(session()->all());
		if($request->has('paginate_limit')) {
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
		} else if(!Session::has('filter.paginate_limit')) {
			session(['filter.paginate_limit' => 10]);
		}
		
		$data = [];
		$q = Restaurent::lang()->select('restaurant.*');					
		$q->orderBy($filter['field'], $filter['sort']);		
		if(Session::has('filter.search') && session('filter.search') !='') {
			$q->where('restaurant.name','like', '%'.session('filter.search').'%');		
		}

		if(Session::has('access.role') && Session::get('access.role') !='admin') {						
			$q->whereIn('restaurant.id', Session::get('access.restaurant_ids'));
		}
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		// prd($data['data']);die;

		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc') {
			$filter['sort'] = 'desc';
		} else {
			$filter['sort'] = 'asc';
		}
		//$data['restaurants']  = Restaurent::restaurant()->select('id','name')->get();

		return view('admin.restaurant.index',['data'=>$data,'filter'=>$filter]);
		//echo "testig..";die;
	}
	
	
	public function add(Request $request) {

		$data['referer'] = '';
		if(isset($_SERVER['HTTP_REFERER'])) {
			preg_match("/product/", $_SERVER['HTTP_REFERER'], $p_c);			
			preg_match("/[^\/]+$/", $_SERVER['HTTP_REFERER'], $p_a);
			//echo $_SERVER['HTTP_REFERER'];die;					
			if(isset($p_c[0]) && $p_c[0] == 'product') {
				if($p_a[0] && $p_a[0] == 'add') {
					$data['referer'] = $p_c[0].'/'.$p_a[0]; 
				} else {
					$data['referer'] = $p_c[0].'/edit/'.$p_a[0]; 
				}		
			} else if(Session::has('referer')) {
				$data['referer'] = Session::pull('referer');			
			}
		} else if(Session::has('referer')) {
			$data['referer'] = Session::pull('referer');			
		}

		if($request->isMethod('post')) {
			$input = $request->all();
		   	//$res = getLatLong('india+gurgoan+GIA+house+sector+16+enuke+software');

			$rules = [];
			if(Session::get('access.role') == 'admin') {
				$rules = [ 
						'delivery_charge'=>'required|numeric',
	        			'delivery_time'=>'required|numeric'
					];
			}

		   	$validator = Validator::make($request->all(), array_merge([
		            'name' => 'required|unique:restaurant',
			        'phone' => 'required|max:20|unique:restaurant',
			        'cuisines'=>'required',
			        'logo'=>'mimes:jpeg,bmp,png|max:1000',
			        'image'=>'mimes:jpeg,bmp,png|max:1000',
			        'street'=>'required',
			        'country_id'=>'required',
			        'state_id'=>'required',
			        'city_id'=>'required',
			        'latitude'=>'required',
			        'longitude'=>'required',
			        'delivery_applicable'=>'required|numeric'
	        	], $rules), [
		        	'street.required' => 'The address field is required.',
		        	'country_id.required' => 'The country field is required.',
		        	'state_id.required' =>'The state field is required.',
		        	'city_id.required' => 'The city field is required.',
		        	'area_id.required' => 'The area field is required.'
	        	]);

			if ( $validator->fails() ) return redirect()->back()->withErrors($validator)->withInput();

		    Restaurent::create($input);

		    $target_id = DB::getPdo()->lastInsertId();

		    $this->__insertDefaultValuesInTiming($target_id);

		    RestaurantCuisine::insertData($input['cuisines'],$target_id);

		    Restaurent::where('id',$target_id)->update(['restaurent_urlalias'=>'retaur-url-'.$target_id]);

		    $image_id = '';
		    if($request->has('image_id') && !empty($request->has('image_id'))) {
		    	$image_id = $request->input('image_id');
		    }

			if($request->file('image')) { 
	        	$type = \Config::get('constants.image.for.restaurant');
	        	$imagePath = Image::imageUpload($request->file('image'),$type);
			    if($image_id !='') {			    		    	
					Image::where('id', $image_id)->update(['location' => $imagePath]);
			    } else {			    	
					Image::create(['location'=>$imagePath,'target_id'=>$target_id,'type'=>$type]);
					$image_id = DB::getPdo()->lastInsertId();
			    }
			}

			if($request->file('logo')) { 			
	        	$type = \Config::get('constants.image.for.restaurant');
	        	$imagePath = Image::imageUpload($request->file('logo'),$type);
			    if($image_id != '') {			    	
					Image::where('id', $image_id)->update(['logo_location' => $imagePath]);
			    } else {			    	
					Image::create(['logo_location'=>$imagePath,'target_id'=>$target_id,'type'=>$type]);
			    }
			}	
			
			// Find latitude and longitude if not entered manually
			if( empty($input['latitude']) && empty($input['longitude']) ) {
				Restaurent::updateLatLong($target_id);
			}
		    
		    Session::flash('flash_message', trans('admin.successfully.added'));	
		    if(!empty($input['referer'])) {
				Session::put('referer',$input['referer']);
		    }	    			     
		    return redirect()->back();	        
		}
		return view('admin.restaurant.add',['data'=>$data]);
	}
	
	
	public function __insertDefaultValuesInTiming($restaurant_id) {
		for($i=1;$i<=7;$i++) {
			$timeData = [
				"restaurant_id"=>$restaurant_id,
				"weekday"=>$i,
				"open"=>'10:00',
				"closing"=>'23:00',
				"delivery_start"=>'10:00',
				"delivery_end"=>'23:00'
			];			
			Timing::create($timeData);
		}
	}
	

	public function edit($id) {
		try {
			if(Session::get('access.role') != 'admin') {
				$restaurant = Restaurent::whereIn('id', Session::get('access.restaurant_ids'))->findOrFail($id);
			} else {
				$restaurant = Restaurent::findOrFail($id);
			}
    		
	    	if(!$restaurant) throw new Exception("No record was found.", 404);

	    	$cuisineIdArr = RestaurantCuisine::getCuisineIds($id);
	    	
	    	return view('admin.restaurant.edit',['restaurant'=>$restaurant,'cuisineIdArr'=>$cuisineIdArr]);

	    } catch (\Exception $e) {
    		
            return redirect()->back();
        }
	}
	
	
	public function update($id, Request $request)
	{	
		if(Session::get('access.role') != 'admin') {
			$restaurant = Restaurent::whereIn('id', Session::get('access.restaurant_ids'))->findOrFail($id);
		} else {
			$restaurant = Restaurent::findOrFail($id);
		}
		
		$rules = [];
		if(Session::get('access.role') == 'admin') {
			$rules = [
					'delivery_charge'=>'required|numeric',
        			'delivery_time'=>'required|numeric',
				];
		}
		
		$validator = Validator::make($request->all(), array_merge([       
            'name' => 'required|unique:restaurant,name,'.$id,
	        'phone' => 'required|max:20|unique:restaurant,phone,'.$id.',id',
	        'cuisines'=>'required',
	        'logo'=>'mimes:jpeg,bmp,png|max:1000',
	        'image'=>'mimes:jpeg,bmp,png|max:1000',
	        'street'=>'required',
	        'country_id'=>'required',
	        'state_id'=>'required',
	        'city_id'=>'required',	
	        'latitude'=>'required',	
	        'longitude'=>'required',
	        'delivery_applicable'=>'required|numeric'
        ], $rules), [
        	'street.required' => 'The address field is required.',
        	'country_id.required' => 'The country field is required.',
        	'state_id.required' =>'The state field is required.',
        	'city_id.required' => 'The city field is required.',
        	'area_id.required' => 'The area field is required.',
        ]);
		
		if ( $validator->fails() ) return redirect()->back()->withErrors($validator)->withInput();

	    $input = $request->all();

	    // ddd($input);

	    if(!$request->has('featured')) $input['featured'] = 0;

	    $input['is_veg'] = isset( $input['is_veg'] ) ? 1 : 0;
	    $input['is_nonveg'] = isset( $input['is_nonveg'] ) ? 1 : 0;
	    $restaurant->fill($input)->save();
	    
	    RestaurantCuisine::updateData($input['cuisines'],$id);
			    
	    $image_id = '';
	    if($request->has('image_id') && !empty($request->has('image_id'))){
	    	$image_id = $request->input('image_id');
	    }

		if($request->file('image')){ 			
	       	$type = \Config::get('constants.image.for.restaurant');
	        $imagePath = Image::imageUpload($request->file('image'),$type);
		    if($image_id !=''){	
		    	Image::deleteImage($request->input('old_image'),$type);	    	    	
				Image::where('id', $image_id)->update(['location' => $imagePath]);
		    }else{			    	
				Image::create(['location'=>$imagePath,'target_id'=>$id,'type'=>$type]);
				$image_id = DB::getPdo()->lastInsertId();
		    }
		}	

		//for logo
		if($request->file('logo')){ 			
	       	$type = \Config::get('constants.image.for.restaurant');
	        $imagePath = Image::imageUpload($request->file('logo'),$type);
		    if($image_id !=''){
		    	Image::deleteImage($request->input('old_logo'),$type);	    	
				Image::where('id', $image_id)->update(['logo_location' => $imagePath]);
		    }else{			    	
				Image::create(['logo_location'=>$imagePath,'target_id'=>$id,'type'=>$type]);
		    }
		}
		
		// Find latitude and longitude if not entered manually
		if( empty($input['latitude']) && empty($input['longitude']) ) {
			Restaurent::updateLatLong($id);
		}

	    Session::flash('flash_message', trans('admin.successfully.updated'));

	    return redirect()->back();
	}
	

	public function delete($id, Request $request){
		$restaurant = Restaurent::findOrFail($id);

		$restaurant->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect($request->segment(2).'/restaurant');
	}
	

	/* access permission*/		
	public function access($restaurant_id, Request $request) 
	{	
		try {

			if(Session::get('access.role') == 'restaurant') {
				$restaurantObj = Restaurent::select('id','name','owner_id')->where('owner_id', Auth::user()->id)->findOrFail($restaurant_id);		
			} else {
				$restaurantObj = Restaurent::select('id','name','owner_id')->findOrFail($restaurant_id);		
			}

			if(!$restaurantObj) throw new Exception("No record was found.", 404);

			$email_v    = 'required|email|unique:user,email';
			$password_v = 'required|min:6|confirmed';
			$password_confirmation_v = 'required|min:6';
			
			if(!empty($restaurantObj->owner_id)) {
				$user = User::find($restaurantObj->owner_id);
				if ($user) {
					$email_v = 'required|email|unique:user,email,'.$user->id;
					$password_v = 'min:6|confirmed';
					$password_confirmation_v = 'same:password';
				}
			}
			
			if($request->isMethod('post')) {	   				
			   	$this->validate($request,[
			        'first_name' => 'required',
			        'last_name' => 'required',
			        'email' => $email_v,
			        'password' => $password_v,
			        'password_confirmation' => $password_confirmation_v
			    ]);
			    
			    $input = $request->all();
		        if(!empty($input['password'])) {
		        	$input['password'] = Hash::make($input['password']);
		        } else {
		        	unset($input['password']);
		        }

				if(!empty($restaurantObj->owner_id)) {				
					$user->fill($input)->save();
					Session::flash('flash_message', trans('admin.successfully.updated'));
				} else {
					$input['role_id'] = \Config::get('constants.user.restaurant');
					$input['status'] = 1;
					//prd($input);
					User::create($input);
					$owner_id = DB::getPdo()->lastInsertId();
					$restaurantObj->fill(['owner_id'=>$owner_id])->save();
					Session::flash('flash_message', trans('admin.successfully.added'));
				}
				return redirect($request->segment(2).'/restaurant');        		        
			}

			if(!empty($restaurantObj->owner_id)) {			
				return view('admin.restaurant.access',['restaurantObj'=>$restaurantObj,'user'=>$user]);
			} else {
				return view('admin.restaurant.access',['restaurantObj'=>$restaurantObj]);
			}

		} catch (\Exception $e) {
	    	
            return redirect()->back();
        }

	}

}