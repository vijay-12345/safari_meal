<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\Menu;
use App\Image;
use App\Restaurent;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use Validator;
use Form;
use Illuminate\Pagination\Paginator;

class MenuRestaurantController extends \App\Http\Controllers\Controller {

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


	// public function __construct()
	// {
	// 	$this->middleware('auth');
	// }

	


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
			$filter = ['sort'=>'asc','field'=>'name'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}
		//print_r($filter);die;
		//DB::enableQueryLog(); 
		$data = [];		
		$q = Menu::select('menu.*','restaurant.name as restaurant_name','restaurant.is_home_cooked')
			->leftjoin('restaurant', 'menu.restaurant_id', '=', 'restaurant.id')
			->where('restaurant.is_home_cooked','=','0')
			->orderBy($filter['field'], $filter['sort']);		

		if(Session::has('filter.search') && session('filter.search') !=''){
			$q->where('menu.name','like', '%'.session('filter.search').'%');
		
		}
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		//echo "<pre>";
		// prd($data['data']);
		//dd(DB::getQueryLog());
		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}
		//$data['restaurants']  = Restaurent::restaurant()->select('id','name')->get();

		return view('admin.menu.index',['data'=>$data,'filter'=>$filter]);
		//echo "testig..";die;
	}


	public function add(Request $request){
		$data['referer'] = '';
		if(isset($_SERVER['HTTP_REFERER'])){			
			preg_match("/product/", $_SERVER['HTTP_REFERER'], $p_c);			
			preg_match("/[^\/]+$/", $_SERVER['HTTP_REFERER'], $p_a);
			//prd($p_c);								
			if(isset($p_c[0]) && $p_c[0] =='product'){
				if($p_a[0] && $p_a[0] == 'add'){
					$data['referer'] = $p_c[0].'/'.$p_a[0]; 
				}else{
					$data['referer'] = $p_c[0].'/edit/'.$p_a[0]; 
				}				
			}elseif(Session::has('referer')){
				$data['referer'] = Session::pull('referer');			
			}
		}elseif(Session::has('referer')){
			$data['referer'] = Session::pull('referer');			
		}		
		if($request->isMethod('post')) {
			$input = $request->all();
		   	$this->validate($request,[
		        'name' => 'required',
		        'image'=>'mimes:jpeg,bmp,png|max:1000'
		    ]);

		   	//vijayanand
		    $id = Auth::user()->id; 
			$res = Restaurent::where('owner_id',$id)->first(); 
			$resid = $res['id'];
			// echo $resid;
			// die;
			// $request->request->add(['restaurant_id' => $resid]);
			$input['restaurant_id'] = $resid;
		    Menu::create($input);
		    $target_id = DB::getPdo()->lastInsertId();
		    //image
	 		if($request->file('image')){        	
	        	$type = \Config::get('constants.image.for.menu');
	        	$imagePath = Image::imageUpload($request->file('image'),$type);
	        	Image::insert(['location'=>$imagePath,'target_id'=>$target_id,'type'=>$type]);
	        
	        }		    
		    Session::flash('flash_message', trans('admin.successfully.added'));				     
		    if(!empty($input['referer'])){
				Session::put('referer',$input['referer']);
		    }
		    return redirect()->back();	        
	        
		}
		return view('admin.menu.add',['data'=>$data]);
	}
	public function edit($id){
    	$menu = Menu::findOrFail($id);
    	//prd($menu);
    	return view('admin.menu.edit')->withMenu($menu);
		
	}
	public function update($id, Request $request)
	{
		//echo "testing..";die;
	    $menu = Menu::findOrFail($id);

	    $this->validate($request, [
	        'name' => 'required',
	        'image'=>'mimes:jpeg,bmp,png|max:1000'	       
	    ]);

	    $input = $request->all(); 	    
	    $menu->fill($input)->save();
	    //image
	   	$image_id = '';
	   	if($request->has('image_id') && !empty($request->has('image_id'))){
	    	$image_id = $request->input('image_id');
	    }	    
 		if($request->file('image')){        	
        	$type = \Config::get('constants.image.for.menu');
        	$imagePath = Image::imageUpload($request->file('image'),$type);
		    if($image_id !=''){
		    	Image::deleteImage($request->input('old_image'),$type);			    				    		    	
				Image::where('id', $image_id)->update(['location' => $imagePath]);
		    }else{			    	
				Image::create(['location'=>$imagePath,'target_id'=>$id,'type'=>$type]);
				
		    }
        	
        }
	    Session::flash('flash_message', trans('admin.successfully.updated'));

	    return redirect()->back();
	}
	public function delete($id){
		//echo "testing...";die;
		$menu = Menu::find($id);

		$menu->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect('admin/menu');
	}	

}