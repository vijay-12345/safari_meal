@extends('admin.layout')

@section('title', trans('admin.reports.sales'))

@section('content')

<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
			</div>

			<div class="col-md-9">
				<div class="page-data-right">
					<div class="panel panel-default">
						<div class="panel-heading">
							{{trans('admin.reports.sales')}}
						</div>

						<div class="panel-body">
							@if(Session::has('success'))
							    <div class="alert alert-success">
							        {{ session('success') }}	   
							    </div>
							@endif
							
							<form action="" method='get' id='filterForm'>
							    <input type="hidden" name="page" value="1" id='page'>
								<table class="table table-responsive filter-inner-form">
									<tr>
										<td>
											<input type="text" name="name" id="name" value="{{ Request::get('name') }}" placeholder="Restaurant Name">
											<input type="text" name="date" id="date" value="{{ Request::get('date') ? Request::get('date') : date('Y-m-d') }}" class='datepicker' placeholder="Date">

											<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
											<button type="button" id="reset" class="btn btn-primary" value="{{trans('admin.reset')}}">{{trans('admin.reset')}}</button>

											<a href="{{ url('admin/reports/sales/export?name='.Request::get('name').'&date='.Request::get('date')) }}" class='btn btn-primary btn-md btn-default'>Export</a>
										</td>
									</tr>
								</table>
							</form>
							
							@if( $data->total() )
			                    <p>{{ 'Showing '.$data->firstItem().'-'.$data->lastItem().' of '.$data->total() }}</p>
			                @endif

			                <div class='table-responsive'>
								<table class="table table-striped">
									<thead>
									    <tr>					           
										    <th>Restaurant</th>
										    <th>Phone</th>
										    <th>Total Orders</th>
										    <th>{{ config('constants.order_status_label.admin.1') }}</th>
										    <th>{{ config('constants.order_status_label.admin.2') }}</th>
										    <th>{{ config('constants.order_status_label.admin.3') }}</th>
										    <th>{{ config('constants.order_status_label.admin.4') }}</th>
										    <th>{{ config('constants.order_status_label.admin.5') }}</th>
										    <th>{{ config('constants.order_status_label.admin.6') }}</th>
									    </tr>
									</thead>
									<tbody>
										@if( $data->isEmpty() )
											<tr>
												<td colspan="9">No records found.</td>
											</tr>
										@else
											@foreach($data as $key => $val)
												<tr>
													<td><a href="{{ url('admin/reports/restaurant?name='.$val->restaurent_urlalias) }}" target="_blank" title='Click to view full report'>{{ trim($val->name) }}</a></td>
													<td>{{ $val->phone }}</td>
													<td>{{ (int) $val->total_orders }}</td>
													<td>{{ (int) $val->pending_orders }}</td>
													<td>{{ (int) $val->open_orders }}</td>
													<td>{{ (int) $val->accepted_orders }}</td>
													<td>{{ (int) $val->dispatched_orders }}</td>
													<td>{{ (int) $val->delivered_orders }}</td>
													<td>{{ (int) $val->cancelled_orders }}</td>
												</tr>
											@endforeach
										@endif
									</tbody>
								</table>
							</div>

							{!! $data->appends( Request::all() )->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
jQuery(function($){
	$('.datepicker').datepicker({
		dateFormat:"yy-mm-dd",
		// defaultDate: (new Date()).getDate(),
        changeMonth: true,
        changeYear: true
    });
});	
</script>

@endsection