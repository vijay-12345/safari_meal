<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;

use Illuminate\Contracts\Auth\Registrar;
use \Exception;
use Validator;
use Form,App\OptionItem,App\OptionGroup;
use Illuminate\Pagination\Paginator;

class AddonOptionController extends \App\Http\Controllers\Controller {

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
	public function index($group_id)
	{	
		$data = [];	
		$group = OptionGroup::findOrFail($group_id);	
		$data['data'] = OptionItem::lang('option_item')->where('option_group_id',$group_id)->orderBy('item_name','ASC')->get();
		return view('admin.addon.optionindex',['data'=>$data,'group'=>$group]);
		//echo "testig..";die;
	}
	public function add($group_id,Request $request){
		$group = OptionGroup::findOrFail($group_id);
		if($request->isMethod('post')) {
			$input = $request->all();
		   	$this->validate($request,[
		        'item_name' => 'required'		        
		    ]);
		    OptionItem::create($input);		   							    
		    Session::flash('flash_message', trans('admin.successfully.added'));		    	
		    return redirect()->back();	        
	        
		}
		return view('admin.addon.optionadd',['group'=>$group]);
	}
	public function edit($id){

    	$option = OptionItem::findOrFail($id);
    	$group = OptionGroup::findOrFail($option->option_group_id);
    	return view('admin.addon.optionedit',['option'=>$option,'group'=>$group]);
		
	}
	public function update($id, Request $request)
	{
		//echo "testing..";die;
	    $option = OptionItem::findOrFail($id);

	    $this->validate($request, [
	        'item_name' => 'required'	       
	    ]);

	    
	    $input = $request->all();
	    $option->fill($input)->save();
			
	    Session::flash('flash_message', trans('admin.successfully.updated'));

	    return redirect()->back();
	}
	public function delete($id){
		//echo "testing...";die;
		$option = OptionItem::find($id);
		//prd($option);
		$group_id = $option->option_group_id;
		$option->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect('admin/addon_option/'.$group_id);
	}
	/**
	 * =============================================================
	 *				End of Addon Group Manager
	 *==============================================================
	 */

}