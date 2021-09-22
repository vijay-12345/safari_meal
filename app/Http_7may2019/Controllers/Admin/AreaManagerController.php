<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Hash;
use Illuminate\Http\Request;
use App\Restaurent;
use App\User;
use App\Driver,App\UserRestaurant;
use App\Order,App\Image;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use Form;
use Illuminate\Pagination\Paginator;

class AreaManagerController extends \App\Http\Controllers\Controller {

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
		if($request->has('sorting')){
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		}else{
			$filter = ['sort'=>'asc','field'=>'user.first_name'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}
		//print_r($filter);die;
		$data = [];		
		$order_q = User::where('role_id',\Config::get('constants.user.area_manager'))			
		->orderBy($filter['field'], $filter['sort'])
		->select('user.*');	
		if(Session::has('filter.search') && session('filter.search') !=''){
			$order_q->where('user.first_name','like', '%'.session('filter.search').'%')->orwhere('user.last_name','like', '%'.session('filter.search').'%');
		
		}	
		if(Session::has('access.role') && Session::get('access.role') =='manager'){			
			$order_q->where('user.id', Auth::user()->id);
		}
		$data['user_data'] = $order_q->paginate(session('filter.paginate_limit'));
		
		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}				
		return view('admin.areamanager.index',['data'=>$data,'filter'=>$filter]);
		//echo "testig..";die;
	}
	public function add(Request $request){

		if($request->isMethod('post')) {
		    $this->validate($request, [
		        'first_name' => 'required',
		        'last_name' => 'required',
		        'contact_number' => 'required',
		        'email'=>'required|email|unique:user,email',
  				'password'=> 'required|between:4,20|confirmed',
  				'password_confirmation' => 'same:password',	
  				'image'=>'mimes:jpeg,bmp,png|max:1000'	        
		    ]);

		    $input = $request->all();
		    $input['password']= Hash::make($input['password']);
		    $input = array_merge(['role_id'=>\Config::get('constants.user.area_manager')],$input);		   
		    //image
	 		if($request->file('image')){        	
	        	$type = \Config::get('constants.image.for.user');
	        	Image::deleteImage($request->input('old_image'),$type);
	        	$input['profile_image'] = Image::imageUpload($request->file('image'),$type);        
	        }
		    User::create($input);
		    $target_id = DB::getPdo()->lastInsertId();
			if(isset($input['restaurants'])){
				UserRestaurant::insertData($input['restaurants'],$target_id);	
			}	    
		    

		    Session::flash('flash_message', trans('admin.successfully.added'));
		    //return redirect('/admin/customer');	
		    return redirect()->back();	        
	        
		}
		return view('admin.areamanager.add');
	}
	public function edit($id){
		if(Session::get('access.role') != 'admin'){
			$user = User::where('id',Auth::user()->id)->findOrFail($id);
		}else{
			$user = User::findOrFail($id);
		}
    	$restaurantIdArr = UserRestaurant::getRestaurantIds($id);
    	//prd($restaurantIdArr);
		return view('admin.areamanager.edit',['user'=>$user,'restaurantIdArr'=>$restaurantIdArr]);
	}
	public function update($id, Request $request)
	{
		if(Session::get('access.role') != 'admin'){
			$user = User::where('id',Auth::user()->id)->findOrFail($id);
		}else{
			$user = User::findOrFail($id);
		}
	    $this->validate($request, [
	        'first_name' => 'required',
	        'last_name' => 'required',
	        'contact_number' => 'required',
	        'email'=>'required|email|unique:user,email,'.$user->id,        	
  			'password'=> 'between:4,20|confirmed',
  			'password_confirmation' => 'same:password',
  			'image'=>'mimes:jpeg,bmp,png|max:1000'       
	    ]);

	    $input = $request->all();

        if(!empty($input['password'])){
        	$input['password'] = Hash::make($input['password']);
        }else{
        	unset($input['password']);
        }
	    //image
 		if($request->file('image')){        	
        	$type = \Config::get('constants.image.for.user');
        	Image::deleteImage($request->input('old_image'),$type);
        	$input['profile_image'] = Image::imageUpload($request->file('image'),$type);        
        }
		$user->fill($input)->save();
		if(isset($input['restaurants'])){
			UserRestaurant::updateData($input['restaurants'],$id);	
		}else{
			UserRestaurant::updateData([],$id);
		}
		
		Session::flash('flash_message', trans('admin.successfully.updated'));
    

	    return redirect()->back();
	}
	public function delete($id){
		//echo "testing...";die;
		$user = User::find($id);

		$user->delete();		
		Session::flash('flash_message',  trans('admin.successfully.deleted'));
		return redirect('admin/areamanager');
	}	

}