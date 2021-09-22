<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Session;
use DB;
use Hash;
use Illuminate\Http\Request;
use App\Restaurent;
use App\User;
use App\Driver;
use App\Order,App\UserAddress,App\Image;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use Form;
use Illuminate\Pagination\Paginator;

class CustomerController extends \App\Http\Controllers\Controller {

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
		}else if(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}

		$data = [];		
		$q = User::where(['role_id'=>\Config::get('constants.user.customer')]);	
		$q->orderBy($filter['field'], $filter['sort']);		
		if(Session::has('filter.search') && session('filter.search') !='') {
			//$q->where('user.first_name','like', '%'.session('filter.search').'%')->orwhere('user.last_name','like', '%'.session('filter.search').'%');
			
			$q->where(function($query) {
                return $query
                          ->where('user.first_name','like', '%'.session('filter.search').'%')
                          ->orWhere('user.last_name','like', '%'.session('filter.search').'%');
            });		

		}	

		$data['user_data'] = $q->paginate(session('filter.paginate_limit'));

		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc') {
			$filter['sort'] = 'desc';
		} else {
			$filter['sort'] = 'asc';
		}				
		return view('admin.customer.index',['data'=>$data,'filter'=>$filter]);
	}


	public function add(Request $request){

		if($request->isMethod('post')) {
		    $this->validate($request, [
		        'first_name' => 'required',
		        'last_name' => 'required',
		        // 'contact_number' => 'required',
		        'contact_number' => 'required|max:20|unique:user',
		        'email'=>'required|email|unique:user,email',
  				'password'=> 'required|between:4,20|confirmed',
  				'password_confirmation' => 'same:password',
  				'image'=>'mimes:jpeg,bmp,png|max:1000'
		    ]);

		    $input = $request->all();

		    //image
	 		if($request->file('image')) {        	
	        	$type = \Config::get('constants.image.for.user');
	        	$input['profile_image'] = Image::imageUpload($request->file('image'),$type);        
	        }		    
		    $input['password'] = Hash::make($input['password']);

		    User::create($input);
		
		    Session::flash('flash_message', trans('admin.successfully.added'));
		    //return redirect('/admin/customer');	
		    return redirect()->back();	        
		}
		return view('admin.customer.add');
	}


	public function edit($id) {
    	$user = User::findOrFail($id);
    	return view('admin.customer.edit')->withUser($user);
	}


	public function update($id, Request $request)
	{
	    $user = User::findOrFail($id);

	    $this->validate($request, [
	        'first_name' => 'required',
	        'last_name' => 'required',
	        // 'contact_number' => 'required',
	        'contact_number'=>'required|unique:user,contact_number,'.$user->id,        	
	        'email'=>'required|email|unique:user,email,'.$user->id,        	
  			'password'=> 'between:4,20|confirmed',
  			'password_confirmation' => 'same:password',	
  			'image'=>'mimes:jpeg,bmp,png|max:1000'        	       
	    ]);

	    $input = $request->all();

	    //image
 		if($request->file('image')){        	
        	$type = \Config::get('constants.image.for.user');
        	Image::deleteImage($request->input('old_image'),$type);
        	$input['profile_image'] = Image::imageUpload($request->file('image'),$type);        
        }
        if(!empty($input['password'])) {
        	$input['password'] = Hash::make($input['password']);
        } else {
        	unset($input['password']);
        }
        //prd($input);

		$user->fill($input)->save();

		Session::flash('flash_message', trans('admin.successfully.updated'));
   
	    return redirect()->back();
	}


	public function delete($id){
		//echo "testing...";die;
		$user = User::find($id);

		$user->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect('admin/customer');
	}


	/* Address manager */
	public function address($userid){
		if(Session::get('access.role') == 'manager'){
			$data['user'] = User::where('id',Auth::user()->id)->findOrFail($userid);
		}else{
			$data['user'] = User::findOrFail($userid);
		}				
		$data['data'] = UserAddress::where('user_id',$userid)->get();	
			
		return view('admin.address.index',['data'=>$data]);		
	}

	public function addressAdd($id,Request $request){
		if(Session::get('access.role') == 'manager'){
			$user = User::where('id',Auth::user()->id)->findOrFail($id);
		}else{
			$user = User::findOrFail($id);
		}
		if($request->isMethod('post')) {
			$this->validate($request,[			
				'first_address' => 'required',
				'city_id' => 'required',
				'zip' => 'required',
				'state_id' => 'required',			
				'country_id' => 'required',
				'area_id' => 'required',				
			]);	
		    $input = $request->all();
		    UserAddress::create($input);
		    $addressid = DB::getPdo()->lastInsertId();
		    UserAddress::updateLatLong($addressid);
		    Session::flash('flash_message', trans('admin.successfully.added'));		    
		    return redirect()->back();	        
		}
		//prd($request->all());
		return view('admin.address.add')->withUser($user);
	}

	public function addressEdit($addressid){
		if(Session::get('access.role') == 'manager'){
			$userAddress = UserAddress::where('user_id',Auth::user()->id)->findOrFail($addressid);
		}else{
			$userAddress = UserAddress::findOrFail($addressid);
		}    	
    	return view('admin.address.edit',['userAddress'=>$userAddress]);
		
	}

	public function addressUpdate($addressid, Request $request)
	{
		if(Session::get('access.role') == 'manager'){
			$userAddress = UserAddress::where('user_id',Auth::user()->id)->findOrFail($addressid);
		}else{
			$userAddress = UserAddress::findOrFail($addressid);
		} 		
		$this->validate($request,[			
			'first_address' => 'required',
			'city_id' => 'required',
			'zip' => 'required',
			'state_id' => 'required',			
			'country_id' => 'required',
			'area_id' => 'required',				
		]);	
	    $input = $request->all();
	    $userAddress->fill($input)->save();
	    UserAddress::updateLatLong($addressid);
	    Session::flash('flash_message', trans('admin.successfully.updated'));

	    return redirect()->back();
	}

	public function addressDelete($id, Request $request){	
		if(Session::get('access.role') == 'manager'){
			$UserAddress = UserAddress::where('user_id',Auth::user()->id)->findOrFail($id);
		}else{
			$UserAddress = UserAddress::findOrFail($id);
		}		
		$userid = $UserAddress->user_id;
		$UserAddress->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect($request->segment(2).'/address/'.$userid);
	}	
	
}