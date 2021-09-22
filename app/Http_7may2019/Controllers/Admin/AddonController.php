<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;

use Illuminate\Contracts\Auth\Registrar;
use \Exception;
use Validator;
use Form,App\OptionGroup;
use Illuminate\Pagination\Paginator;

class AddonController extends \App\Http\Controllers\Controller {

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
	 * =============================================================
	 *				Addon Group Manager start
	 *==============================================================
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
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}
		//print_r($filter);die;
		$data = [];		
		$q = OptionGroup::lang();
		$q->orderBy($filter['field'], $filter['sort']);		
		if(Session::has('filter.search') && session('filter.search') !=''){
			$q->where('option_group.name','like', '%'.session('filter.search').'%');
		
		}
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		//prd($data['data']);
		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}
		//prd($data['data']);

		return view('admin.addon.groupindex',['data'=>$data,'filter'=>$filter]);
		//echo "testig..";die;
	}
	public function add(Request $request){

		if($request->isMethod('post')) {
			$input = $request->all();
		   	$this->validate($request,[
		        'name' => 'required'		        
		    ]);
		    OptionGroup::create($input);		   							    
		    Session::flash('flash_message', trans('admin.successfully.added'));		    	
		    return redirect()->back();	        
	        
		}
		return view('admin.addon.groupadd');
	}
	public function edit($id){

    	$group = OptionGroup::findOrFail($id);
    	
    	return view('admin.addon.groupedit',['group'=>$group]);
		
	}
	public function update($id, Request $request)
	{
		//echo "testing..";die;
	    $group = OptionGroup::findOrFail($id);

	    $this->validate($request, [
	        'name' => 'required'	       
	    ]);

	    
	    $input = $request->all();
	    $group->fill($input)->save();
			
	    Session::flash('flash_message', trans('admin.successfully.updated'));

	    return redirect()->back();
	}
	public function delete($id){
		//echo "testing...";die;
		$group = OptionGroup::find($id);

		$group->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect('admin/addon_group');
	}
	/**
	 * =============================================================
	 *				End of Addon Group Manager
	 *==============================================================
	 */

}