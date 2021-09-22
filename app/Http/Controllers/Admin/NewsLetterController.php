<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\NewsLetter,App\User;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use Validator;
use Form;
use Illuminate\Pagination\Paginator;

class NewsLetterController extends \App\Http\Controllers\Controller {

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
			$newsletters = NewsLetter::lists('email');
			if(!empty($newsletters)){				
				$users = User::where(['status'=>1,'newsletter'=>1])->whereNotIn('email',$newsletters)->lists('email');
				if(!empty($users)){
					$insertData = [];
					foreach($users as $key=>$v){
						$insertData[] = ['email'=>$v];
					}					
					NewsLetter::insert($insertData);					
				}
			}
		}	
		/* filter 			
		*/	
		if($request->has('sorting')){
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		}else{
			$filter = ['sort'=>'asc','field'=>'email'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
			//handle when blank/space inpute in search
			if($request->input('search'))
				if(session('filter.search')==null)
				{
					$data = null;
					return view('admin.newsletter.index',['data'=>$data,'filter'=>$filter]);
				}
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}		
		$data = [];		
		$q = NewsLetter::orderBy($filter['field'], $filter['sort']);		
		if(Session::has('filter.search') && session('filter.search') !=''){
			$q->where('email','like', '%'.session('filter.search').'%');		
		}
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}
		return view('admin.newsletter.index',['data'=>$data,'filter'=>$filter]);
	}
	public function add(Request $request){

		if($request->isMethod('post')) {
			$input = $request->all();
		   	$this->validate($request,[
		        'email'=>'required|email|unique:newsletter,email'	       
		    ]);			        
		    NewsLetter::create($input);		    		    
		    Session::flash('flash_message', trans('admin.successfully.added'));
		    return redirect()->back();	        
	        
		}
		return view('admin.newsletter.add');
	}
/*	public function edit($id){
    	$newsletter = NewsLetter::findOrFail($id);    	
    	return view('admin.newsletter.edit')->withNewsletter($newsletter);
		
	}
	public function update($id, Request $request)
	{
		
	    $newsletter = NewsLetter::findOrFail($id);

	    $this->validate($request, [
	        'name' => 'required'	               
	    ]);
	    $input = $request->all(); 	    
	    $newsletter->fill($input)->save();	   
	    Session::flash('flash_message', trans('admin.successfully.updated'));
	    return redirect()->back();
	}
	public function delete($id){		
		$newsletter = NewsLetter::find($id);
		$newsletter->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect('admin/newsletter');
	}	*/

}