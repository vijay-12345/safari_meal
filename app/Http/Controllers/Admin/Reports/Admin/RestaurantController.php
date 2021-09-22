<?php

namespace App\Http\Controllers\Admin\Reports\Admin;

use Auth, Session, DB, Request, Exception;
use App\Restaurent, App\User, App\Order, App\Calendar;
use App\Traits\Export;

class RestaurantController extends \App\Http\Controllers\Controller
{
	use Export;

	private $per_page = 10;

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
	public function index()
	{
		// Get all active restaurants
		$restaurants = Restaurent::select('id', 'name', 'restaurent_urlalias')
			->where('status', 1)
			->withTrashed()->get()->toArray();
		
		// Set filter types
		$types = array(
			'monthly'	=>	'Monthly',
			'weekly'	=> 	'Weekly',
			'daily'		=>	'Daily'
		);

		$data = new \stdClass;
		$name = Request::get('name');
		if( $name )
		{
			$restaurant = Restaurent::where('restaurent_urlalias', $name)->first();
			if( ! $restaurant ) {
				goto LoadView;
			}

			$data = $this->getData($restaurant, $this->per_page);
		}

		LoadView:

        return view('admin.reports.admin.restaurant', [
        	'data' 			=>	$data,
        	'types'			=> 	$types,
        	'restaurants'	=> 	$restaurants
        ]);
	}

	public function getData( $restaurant, $per_page = 0 )
	{
		$type = Request::get('type');
		$type = $type ? $type : 'monthly';

		$start_date = Request::get('start_date');
		$start_date = $start_date ? $start_date : $restaurant->created_at->format('Y-m');

		$end_date = Request::get('end_date');
		$end_date = $end_date ? $end_date : date('Y-m');

		$date = date('Y-m-d');
		$data = DB::table('calendar as c')
			->leftJoin('order as o', function($join) use ($restaurant) {
				$join->on('c.dt', '=', DB::raw("date(o.created_at)"));
				$join->where('o.restaurant_id', '=', $restaurant->id);
			})
			->whereRaw("DATE_FORMAT(c.dt, '%Y-%m') <='".$end_date."'")
			->whereRaw("DATE_FORMAT(c.dt, '%Y-%m') >= '".$start_date."'");

		switch( $type )
		{
			case 'monthly':
				$data->select( DB::raw("DATE_FORMAT(c.dt,'%Y-%m') as period") );
				$data->groupBy( DB::raw("DATE_FORMAT(c.dt,'%Y-%m')") );
				break;

			case 'daily':
				$data->select( DB::raw("DATE_FORMAT(c.dt,'%Y-%m-%d') as period") );
				$data->groupBy( DB::raw("DATE_FORMAT(c.dt,'%Y-%m-%d')") );
				break;

			case 'weekly':
				$data->select( DB::raw("yearweek(c.dt) as period") );
				$data->groupBy( DB::raw("yearweek(c.dt)") );
				break;
				
			default:
				break;
		}

		$data->addSelect(
			DB::raw("count(o.status) as total_orders"),
			DB::raw("sum(case when o.status='".config('constants.order.pending')."' then 1 else 0 end) as pending_orders"),
			DB::raw("sum(case when o.status='".config('constants.order.open_for_drivers')."' then 1 else 0 end) as open_orders"),
			DB::raw("sum(case when o.status='".config('constants.order.accepted_by_driver')."' then 1 else 0 end) accepted_orders"),
			DB::raw("sum(case when o.status='".config('constants.order.dispatched')."' then 1 else 0 end) as dispatched_orders"),
			DB::raw("sum(case when o.status='".config('constants.order.delivered')."' then 1 else 0 end) as delivered_orders"),
			DB::raw("sum(case when o.status='".config('constants.order.cancelled')."' then 1 else 0 end) as cancelled_orders")
		);

		if( $per_page > 0 ) {
			return $data->paginate( $per_page );
		}

		return $data->get();
	}

	// Export report
	public function export()
	{
		$name = Request::get('name');
		if( $name )
		{
			$restaurant = Restaurent::where('restaurent_urlalias', $name)->first();
			if( ! $restaurant ) {
				return redirect()->back();
			}
			
			$data = $this->getData($restaurant);
		}

		$type = Request::get('type');
		$type = $type ? $type : 'monthly';

		$this->setFileName('restaurant-report-'.$type);
        $this->setHeaders( array('Period', 'Total Orders', config('constants.order_status_label.admin.1'), config('constants.order_status_label.admin.2'), config('constants.order_status_label.admin.3'), config('constants.order_status_label.admin.4'), config('constants.order_status_label.admin.5'), config('constants.order_status_label.admin.6')) );

        $this->setOutput( function() use ($data, $type) {
            $output = '';
            foreach ($data as $key => $value) 
            {
            	$value->period = $this->getPeriodLabel($value->period, $type);
                $output .= implode("\t", (array) $value)."\n";
            }
            
            return $output;
        });
        
        $this->exportFinally();
	}

	public function getPeriodLabel($date, $type)
	{
		if($type == 'monthly') {
			return date('M Y', strtotime($date));
		}

		if($type == 'daily') {
			return date('d M Y', strtotime($date));
		}

		$date = date('d M Y', strtotime(substr($date, 0, 4).'W'.substr($date, 4, 2)));
		return $date.' - '.date('d M Y', strtotime("+6 day", strtotime($date)));
	}
}
