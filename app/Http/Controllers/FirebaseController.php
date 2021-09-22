<?php 

namespace App\Http\Controllers;

use App\User;
use Validator;
use Auth, Config, Exception, DB, Session;
use Input;

use Illuminate\Http\Request;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseController extends Controller 
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

	public function __construct()
	{					

	}

	public function index(Request $request)
	{
		// echo __DIR__.'/laravelfirebase-9d875-firebase-adminsdk-wltre-a1b8486a6c.json';
		$serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/laravelfirebase-9d875-firebase-adminsdk-wltre-a1b8486a6c.json');
        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://laravelfirebase-9d875.firebaseio.com/')
        ->create();

        $database = $firebase->getDatabase();
	}


}
