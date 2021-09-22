<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\Product,App\ProductOption;
use App\Image;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use Validator;
use Form;
use Illuminate\Pagination\Paginator;

class ProductController extends \App\Http\Controllers\Controller {

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
			$filter = ['sort'=>'asc','field'=>'name'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')) {
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
		} elseif(!Session::has('filter.paginate_limit')) {
			session(['filter.paginate_limit' => 10]);
		}

		if($request->has('restaurant_id')) {
			session(['filter.restaurant_id' => $request->input('restaurant_id')]);
		} elseif(!Session::has('filter.restaurant_id')) {
			session(['filter.restaurant_id' => 'All']);
		}		
		//print_r($filter);die;
		$data = [];		
		$q = Product::lang('product')->select('product.*','restaurant.name as restaurant_name')
			->join('restaurant','restaurant.id','=','product.restaurant_id');
		$q->orderBy($filter['field'], $filter['sort']);		

		if(Session::has('filter.search') && session('filter.search') !='') {
			$q->where('product.name','like', '%'.session('filter.search').'%');
		}

		if(Session::has('filter.restaurant_id') && session('filter.restaurant_id') !='All') {
			$q->where('product.restaurant_id','=', session('filter.restaurant_id'));
		}

		if(Session::has('access.role') && Session::get('access.role') !='admin') {			
			$q->whereIn('product.restaurant_id', Session::get('access.restaurant_ids'));
		}			
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		//prd($data['data']);

		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc') {
			$filter['sort'] = 'desc';
		} else {
			$filter['sort'] = 'asc';
		}
		//prd($data['data']);

		return view('admin.product.index',['data'=>$data,'filter'=>$filter]);
	}


	public function add(Request $request) {

		if($request->isMethod('post')) {
			$input = $request->all();
		   	$this->validate($request,[
		        'name' => 'required'		        
		    ]);
		    Product::create($input);		    		    
		    $target_id = DB::getPdo()->lastInsertId();	
		    $addons = [];
		    if(isset($input['addons']['option'])){
			    foreach($input['addons']['option'] as $key=>$optionId){
			    	$addons[] = array('product_id'=>$target_id,'option_item_id'=>$optionId,'price'=>$input['addons']['price'][$optionId]);
			    }
			    ProductOption::insert($addons); 		    	
		    }	
		    //image
	 		if($request->file('image')){        	
	        	$type = \Config::get('constants.image.for.product');
	        	$imagePath = Image::imageUpload($request->file('image'),$type);
	        	Image::insert(['location'=>$imagePath,'target_id'=>$target_id,'type'=>$type]);
	        }		    	    							    
		    Session::flash('flash_message', trans('admin.successfully.added'));		    	
		    return redirect()->back();	        
	        
		}
		return view('admin.product.add');
	}


	public function edit($id) {
		
		try {
			if(Session::get('access.role') == 'restaurant') {
				$product = Product::whereIn('restaurant_id', Session::get('access.restaurant_ids'))->findOrFail($id);
			} else {
				$product = Product::findOrFail($id);
			}

			if(!$product) throw new Exception("No record was found.", 404);

			return view('admin.product.edit',['product'=>$product]);

		} catch (\Exception $e) {
            return redirect()->back();
        }
				
	}


	public function update($id, Request $request)
	{
		//echo "testing..";die;
		try{		    
			if(Session::get('access.role') != 'admin'){
				$product = Product::whereIn('restaurant_id',Session::get('access.restaurant_ids'))->findOrFail($id);
			}else{
				$product = Product::findOrFail($id);
			}
		    $this->validate($request, [
		        'name' => 'required'	       
		    ]);    
		    $input = $request->all();
		    //prd( $input);
		    $addons = [];
		    if(isset($input['addons']['option'])){
			    foreach($input['addons']['option'] as $key=>$optionId){
			    	$addons[] = array('product_id'=>$id,'option_item_id'=>$optionId,'price'=>$input['addons']['price'][$optionId]);
			    }
			    ProductOption::where('product_id',$id)->delete();
			    ProductOption::insert($addons); 		    	
		    }else{
		    	ProductOption::where('product_id',$id)->delete();
		    }		    
		    $product->fill($input)->save();
		    //image
		   	$image_id = '';
		   	if($request->has('image_id') && !empty($request->has('image_id'))){
		    	$image_id = $request->input('image_id');
		    }	    
	 		if($request->file('image')){        	
	        	$type = \Config::get('constants.image.for.product');
	        	$imagePath = Image::imageUpload($request->file('image'),$type);
			    if($image_id !=''){
			    	Image::deleteImage($request->input('old_image'),$type);			    				    		    	
					Image::where('id', $image_id)->update(['location' => $imagePath]);
			    }else{			    	
					Image::create(['location'=>$imagePath,'target_id'=>$id,'type'=>$type]);
					
			    }
	        	
	        }		    
		    Session::flash('flash_message', trans('admin.successfully.updated'));
		}catch(Exception $e){
			 Session::flash('flash_message', $e->getMessage());
		}
	    return redirect()->back();
	}
	public function delete($id,Request $request){
		if(Session::get('access.role') != 'admin'){
			$product = Product::whereIn('restaurant_id',Session::get('access.restaurant_ids'))->findOrFail($id);
		}else{
			$product = Product::findOrFail($id);
		}
		$product->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect($request->segment(2).'/product');
	}


}