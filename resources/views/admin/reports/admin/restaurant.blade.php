@extends('admin.layout')

@section('title', trans('admin.reports.restaurant'))

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
							{{trans('admin.reports.restaurant')}}
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
											<select name='name' required>
												<option value=''>---Select Restaurant---</option>
												@foreach($restaurants as $key => $val)
													<option value="{{ $val['restaurent_urlalias'] }}" {{ Request::get('name')==$val['restaurent_urlalias'] ? 'selected':'' }}>{{ $val['name'] }}</option>
												@endforeach
											</select>
											
											<select name='type' required>
												@foreach($types as $key => $val)
													<option value="{{ $key }}" {{ Request::get('type')==$key ? 'selected' :'' }}>{{ $val }}</option>
												@endforeach
											</select>
											
											<input type="text" name="start_date" id="start_date" value="{{ Request::get('start_date') }}" class='datepicker' placeholder="Start Date">

											<input type="text" name="end_date" id="end_date" value="{{ Request::get('end_date') }}" class='datepicker' placeholder="End Date">

											<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
											<button type="button" id="reset" class="btn btn-primary" value="{{trans('admin.reset')}}">{{trans('admin.reset')}}</button>
											
											@if( method_exists($data, 'total') && $data->total() )
												<a href="{{ url('admin/reports/restaurant/export?name='.Request::get('name').'&type='.Request::get('type').'&start_date='.Request::get('start_date').'&end_date='.Request::get('end_date')) }}" class='btn btn-primary btn-md btn-default'>Export</a>
											@endif
										</td>
									</tr>
								</table>
							</form>

							@if( method_exists($data, 'total') && $data->total() )
			                    <p>{{ 'Showing '.$data->firstItem().'-'.$data->lastItem().' of '.$data->total() }}</p>
			                @endif

			                <div class='table-responsive'>
								<table class="table table-striped">
									<thead>
									    <tr>
									    	<th>Period</th>
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
										@if( !method_exists($data, 'total') || (method_exists($data, 'total') && $data->isEmpty()) )
											<tr>
												<td colspan="7">No records found.</td>
											</tr>
										@else
											@foreach($data as $key => $val)
												<tr>
													<td>
													@if(!Request::get('type') || Request::get('type') == 'monthly')
														{{ date('M Y', strtotime($val->period)) }}
													@elseif( Request::get('type') == 'daily' )
														{{ date('M d, Y', strtotime($val->period)) }}
													@else
													<?php $date = date('M d, Y', strtotime(substr($val->period, 0, 4).'W'.substr($val->period, 4, 2))); ?>

														{{ $date.' - '.date('M d, Y', strtotime("+6 day", strtotime($date))) }}
													@endif
													</td>
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

							@if( method_exists($data, 'total') )
								{!! $data->appends( Request::all() )->render() !!}
							@endif
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
		dateFormat:"yy-mm",
        changeMonth: true,
        changeYear: true,
        onClose: function(dateText, inst) { 
            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        }
    });
});
</script>

<style type="text/css">
.ui-datepicker-calendar {
	display: none;
}	
</style>

@endsection