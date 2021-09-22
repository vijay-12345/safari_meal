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
use App\Order,App\Image;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use Form;
use Illuminate\Pagination\Paginator;

class DriverController extends \App\Http\Controllers\Controller {

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
			//handle when blank/space inpute
			if($request->input('search'))
				if(session('filter.search')==null)
				{
					$data = null;
					return view('admin.driver.index',['data'=>$data,'filter'=>$filter]);
				}
			// print_r(session('filter.search'));
			// die;
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}

		//print_r($filter);die;
		$data = [];		
		$q = User::where(['role_id'=>\Config::get('constants.user.driver')]);	
		$q->orderBy($filter['field'], $filter['sort']);	

		if(Session::has('filter.search') && session('filter.search') !=''){
			//$q->where('user.first_name','like', '%'.session('filter.search').'%')->orwhere('user.last_name','like', '%'.session('filter.search').'%');
			$q->where(function($query){
                return $query
                          ->where('user.first_name','like', '%'.session('filter.search').'%')
                          ->orWhere('user.last_name','like', '%'.session('filter.search').'%'); //for blank input working
            });		
		}	

		$data['user_data'] = $q->paginate(session('filter.paginate_limit'));

		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc') {
			$filter['sort'] = 'desc';
		} else {
			$filter['sort'] = 'asc';
		}
				
		return view('admin.driver.index',['data'=>$data,'filter'=>$filter]);
	}


	public function add(Request $request){

		if($request->isMethod('post')) {
		    $this->validate($request, [
		        'first_name' => 'required',
		        'last_name' => 'required',
		        //'contact_number' => 'required|max:20|unique:user',
		        'contact_number' => 'required|numeric|digits_between:9,10|unique:user',
		        'email'=>'required|email|unique:user,email',
  				'password'=> 'required|between:4,20|confirmed',
  				'password_confirmation' => 'same:password',
  				'image'=>'mimes:jpeg,bmp,png|max:1000'
		    ]);

		    $input = $request->all();
		    $input['password']= Hash::make($input['password']);
		    $input = array_merge(['role_id' => \Config::get('constants.user.driver')],$input);

		    //image
	 		if($request->file('image')){        	
	        	$type = \Config::get('constants.image.for.user');
	        	Image::deleteImage($request->input('old_image'),$type);
	        	$input['profile_image'] = Image::imageUpload($request->file('image'),$type);        
	        }
		    User::create($input);

		    Session::flash('flash_message', trans('admin.successfully.added'));
		    //return redirect('/admin/customer');	
		    return redirect()->back();	        
	        
		}
		return view('admin.driver.add');
	}


	public function edit($id){
    	$user = User::findOrFail($id);
    	return view('admin.driver.edit')->withUser($user);
	}


	public function update($id, Request $request)
	{
	    $user = User::findOrFail($id);

	    $this->validate($request, [
	        'first_name' => 'required',
	        'last_name' => 'required',
	        'contact_number'=>'required|unique:user,contact_number,'.$user->id,
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

		Session::flash('flash_message', trans('admin.successfully.updated'));
    
	    return redirect()->back();
	}

	
	public function delete($id) {
		$user = User::find($id);
		$user->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect('admin/driver');
	}

}