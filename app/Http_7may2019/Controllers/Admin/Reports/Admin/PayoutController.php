<?php

namespace App\Http\Controllers\Admin\Reports\Admin;

use Auth, Session, DB, Request, Exception;
use App\Restaurent, App\User, App\Order, App\Payout;
use App\Traits\Export;

class PayoutController extends \App\Http\Controllers\Controller
{
	use Export;

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
	 * Show the reports to the user.
	 *
	 * @return Response
	 */
	public function index( $restaurant = '' )
	{
		$per_page = 10;
		$payoutModel = DB::table('payout_history as ph');

		// Check if restaurant exists
		$exist = Restaurent::where('restaurent_urlalias', $restaurant)->withTrashed()->first();
		if( ! $exist ) {
			return redirect()->back();
		}

		$payoutModel->where('ph.restaurant_id', $exist->id)
			->leftJoin('restaurant as r', 'r.id', '=', 'ph.restaurant_id')
			->select('ph.restaurant_id', 'ph.amount', 'ph.json', 'ph.created_at', 'r.name');

		$start_date = Request::get('start_date');
		if( $start_date ) {
			$payoutModel->where('created_at', '>=', $start_date);
		}

		$end_date = Request::get('end_date');
		if( $end_date ) {
			$payoutModel->where('created_at', '<=', $end_date);
		}

        return view('admin.reports.admin.payout', [
        	'data' 		 => $payoutModel->paginate( $per_page ),
        	'restaurant' => $exist
        ]);
	}
}
