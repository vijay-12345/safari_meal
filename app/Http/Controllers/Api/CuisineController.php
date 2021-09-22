<?php 
namespace App\Http\Controllers\Api;
use \Auth;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use App\Cuisine;
use Illuminate\Pagination\Paginator;

class CuisineController extends \App\Http\Controllers\Controller {

 
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){    
		//$this->middleware('auth.api', ['except' => ['login']]);
	}
	/* v1/
	* {"restaurant_id":"4"}
	*/
	public function cuisineList(Request $request){
		$inputs = $request->all();
		if(isset($inputs['restaurant_id'])){
			$cuisineData = Cuisine::getCuisineByRestaurantId($inputs['restaurant_id']);
			return $this->apiResponse($cuisineData);
		}else{
			return $this->apiResponse(['error'=>'Field restaurant_id is missing in request!'],true);
		}
		
		
	}
	/*
	* url : /cuisine 
	* verb : get	
	*/
	public function index(){								
		$q = Cuisine::lang()->where(['status'=>1]);		
		$data = $q->select('id','name')->get();
		return $this->apiResponse(['data'=>$data]);
		return $data->toJson();
	}
	/*
	* url : /customer/create 
	* verb : get	
	*/	
	public function create(){

		return ['status'=>'create...'];
	}
	/*
	* url : /cuisine
	* verb : post	
	*{ "name":"testwqewew123","id":13}
	*/		
	public function store(Request $request){

		$input = $request->all();
	
		if(!isset($input['id'])){			
			return response()->json(['message' => trans('admin.not_found'),'status'=>'error']);
		}
		$cuisine = Cuisine::where(['status'=>1,'deleted_at'=>Null])->find($input['id']);
		if($cuisine == null){
		    return response()->json(['message' => trans('admin.not_found'),'status'=>'error']);
		}
	    $rules = ['name' => 'required']; 
	  	$v = Validator::make($input, $rules);	
	      	
	    $cuisine->fill($input)->save();       
      	    
		if ($v->fails()){
		   $out_error = [];
	       foreach($rules as $key => $value) {
	        	$errors = $v->errors();
	            if($errors->has($key)) { 
	                $out_error[]= $errors->first($key); 
	            }
	        }			
			return response()->json(['message' => $out_error,'status'=>'error']); 
		}
		return response()->json(['message' => trans('admin.success_updated'),'status'=>'ok','lang'=>\Config::get('app.locale_prefix')]);		  

	}
	/*
	* url : /cuisine/{cuisine}
	* verb : get	
	*/		
	public function show($id){
		$q = Cuisine::lang()->select('name')		
		->where(['status'=>1,'id'=>$id]);		
		$data = $q->get();
		return $data->toJson();	
	}
	/*
	* url : /cuisine/{customer}/edit
	* verb : get	
	*/	
	public function edit(){
		return ['status'=>'edit...'];
	}
	/*
	* url : /cuisine/{customer}
	* verb : PUT/PATCH	
	*/	
	public function update($id,Request $request){		
		return ['status'=>'update...'];
	}
	/*
	* url : /cuisine/{customer}
	* verb : DELETE	
	*/	
	public function destroy(){
		return ['status'=>'destroy...'];
	}		
					
}
