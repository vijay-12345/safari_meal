<?php 

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\User;
use Validator;
use Auth, Config, Exception, DB;
use Mail;
use Illuminate\Contracts\Auth\Registrar;
use Input,App\Page;

class PageController extends Controller 
{

	/*
	|--------------------------------------------------------------------------
	| Page Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function __construct(Registrar $registrar)
	{					
		$this->registrar = $registrar;		
	}

	public function index($slug, Request $request)
	{
		$pages = Page::lang()->where(['status' => 1, 'page_urlalias'=>$slug])->first();

		try
		{
			if($request->isMethod('post')) 
			{
			    $validator = Validator::make($request->all(), [
			        'fname' => 'required',
			        'lname' => 'required',
			        'email' => 'required',
			        'type' => 'required',
			        'message'=>'required'
			    ]);

			    if( $validator->fails() ) {
			    	throw new Exception( $this->getError( $validator ), 1);
			    }

			    $input = $request->all();
				
				Mail::send('emails.contact', ['data'=> $input], function($message)  use ($input) {
$message->from(\Config::get('constants.administrator.email'), \Config::get('constants.site_name'));

					$to = $input['type']=='complaint' ? Config::get('constants.email.care') : Config::get('constants.email.contact');
					$subject = $input['type']=='complaint' ? 'New Complaint' : 'Contact us';

					$message->to($to, $input['fname'])->subject( $subject );
				});

				Session::flash('flash_message', trans('Successfully Sent!'));

				return redirect()->back();
			}
			
		} catch(Exception $e) {
			Session::flash('error', $e->getMessage());
		}

		return view('home.page', ['data' => $pages, 'slug'=> $slug]);
	}
}
