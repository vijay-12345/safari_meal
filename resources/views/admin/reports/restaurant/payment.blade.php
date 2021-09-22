@extends('admin.layout')
@section('title', trans('admin.reports'))
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
							{{trans('admin.reports')}}
						</div>

						<div class="panel-body">
							@if(Session::has('success'))
							    <div class="alert alert-success">
							        {{ session('success') }}	   
							    </div>
							@endif

							<form action="" method='get' id='filterForm'>
							    <input type="hidden" name="page" value="1" id='page'>
								<table class="table table-responsive">
									<tr>
										<td colspan="3">
											<input type="text" name="start_date" id="start_date" value="{{ Request::get('start_date') }}" class='datepicker' placeholder="Start Month"> &nbsp;&nbsp;
											<input type="text" name="end_date" id="end_date" value="{{ Request::get('end_date') }}" class='datepicker' placeholder="End Month"> &nbsp;&nbsp;
											<input type="submit" value="{{trans('admin.search')}}">
											<input type="button" value="Reset" id='reset'>
											<a href="{{ url('restaurant/reports/export?start_date='.Request::get('start_date').'&end_date='.Request::get('end_date')) }}" class='btn btn-sm btn-default'>Export</a>
										</td>
									</tr>
									<tr>
										<td>&#8377; {{ $amount }} <br>
											<strong>Total</strong>
										</td>
										<td align="center">&#8377; {{ $commission }} <br>
											<strong>Commmission</strong>
										</td>
										<td align="right"> {{ $orders }} <br>
											<strong>Orders</strong>
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
									    	<th>Period</th>
										    <th>Orders</th>
										    <th>Total Amount</th>
										    <th>Admin Commission</th>
										    <th>Sales</th>
									    </tr>
									</thead>
									<tbody>
										@if( $data->isEmpty() )
											<tr>
												<td colspan="4">No records found.</td>
											</tr>
										@else
											@foreach($data as $key => $val)
												<tr>
													<td>{{ $val->period }}</td>
													<td>{{ $val->orders }}</td>
													<td>&#8377; {{ $val->amount }}</td>
													<td>&#8377; {{ $val->commission }}</td>
													<td>&#8377; {{ $val->sales }}</td>
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

<!-- ** Date range script ** -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
jQuery(function($){
	$('.datepicker').datepicker({
		dateFormat:"yy-mm",
		defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        onClose: function(dateText, inst) { 
            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth));
        }
    });

	// Reset Filter Form
    $('#reset').click(function(){
    	$('#filterForm .datepicker').val('');
    	$('#filterForm').submit();
    });
});

</script>

<style type="text/css">
.ui-datepicker-calendar {
	display: none;
}	
</style>

@endsection