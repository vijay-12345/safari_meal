@extends('admin.layout')

@section('title', trans('admin.order.list'))

@section('content')

<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
			</div>
			
			<div class="col-md-9">
				
				<?php $prefix = \Request::segment(2); ?>

				<div class="page-data-right">
					<div class="panel panel-default">
						<div class="panel-heading">{{trans('admin.order.manager')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif
							
							{!! Form::open(array('role' => 'form','class'=>'filter-form search-form','url' => $prefix.'/order')) !!}
								<table class="table table-responsive filter-inner-form" cellspacing="0" width="100%">
									<tr>
										<td>
											<select name="paginate_limit" class="paginate_limit">							
												@foreach(Config::get('constants.paginate_limit_option') as $limitOption))
													<option value="{{$limitOption}}" @if(session('filter.paginate_limit')==$limitOption) selected @endif>{{$limitOption}}</option>
												@endforeach
											</select>
											<select name="date">
												<option value="{{date('Y-m-d')}}">Today</option>							
												<option value="all" @if(session('filter.date')=='all') selected @endif>All</option>
											</select>
											<select name='status'>
												<option value=''>---Select Status---</option>
												<option value='open' {{ session('filter.status') == 'open' ? 'selected' : ''}}>Open</option>
												<option value='closed' {{ session('filter.status') == 'closed' ? 'selected' : ''}}>Closed</option>
											</select>
											<input name="custom_date" id="custom_date" value="{{session('filter.custom_date')}}" placeholder="yyyy-mm-dd">
											<input type="text" name="search" value="{{session('filter.search')}}" placeholder="{{trans('admin.order.number')}}">
										   	<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
										</td>
										<td>
											<a href="{{url($prefix.'/order')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}</a>
										</td>
									</tr>
								</table>
							{!! Form::close() !!}
							
							<table id="table" class="table table-striped" cellspacing="0" width="100%">
								<thead>
								    <tr>				           
									    <th><a class="action-ajax" href="{{URL::to('/')}}/{{$prefix}}/order?sorting={{$filter['sort']}}&amp;field=order.order_number">{{trans('admin.order.id')}}</a></th>
									    <th><a class="action-ajax" href="{{URL::to('/')}}/{{$prefix}}/order?sorting={{$filter['sort']}}&amp;field=order.created_at">{{trans('admin.order_placed_time')}}</a></th>
								        <th><a class="action-ajax" href="{{URL::to('/')}}/{{$prefix}}/order?sorting={{$filter['sort']}}&amp;field=order.date">{{trans('admin.order_delivery_time')}}</a></th>
								        <th><a class="action-ajax" href="{{URL::to('/')}}/{{$prefix}}/order?sorting={{$filter['sort']}}&amp;field=restaurant.name">{{trans('admin.restaurants')}}</a></th>
								        <th><a class="action-ajax" href="{{URL::to('/')}}/{{$prefix}}/order?sorting={{$filter['sort']}}&amp;field=order.remark">{{trans('admin.client.remark')}}</a></th>
								        <th><a class="action-ajax" href="{{URL::to('/')}}/{{$prefix}}/order?sorting={{$filter['sort']}}&amp;field=order.amount">{{trans('admin.amount')}}</a></th>
								        <th><a class="action-ajax" href="{{URL::to('/')}}/{{$prefix}}/order?sorting={{$filter['sort']}}&amp;field=order.status">{{trans('admin.order.status')}}</a></th>
								        <th><a class="action-ajax" href="{{URL::to('/')}}/{{$prefix}}/order?sorting={{$filter['sort']}}&amp;field=driver.first_name">{{trans('admin.driver.status')}}</a></th>
								        <th>{{trans('admin.action')}}</th>
								    </tr>
								</thead>
								<!-- handle when blank input in search -->
								@if($data == null)
								<tr><td colspan="8" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
								@else
								<tbody id="ajax_data" data-href="{{Request::fullUrl()}}">							    
								</tbody>
								@endif

							</table>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- ************************Date range script******************************** -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">

$(document).ready(function() {

	window.setInterval(function() {
	  loadOrderData();
	}, 30000);

	loadOrderData(); // first time run 

	$(".action-ajax").on("click",function(e){
		e.preventDefault();
		var ahref = $(this).attr('href');
		$("#ajax_data").attr('data-href',ahref);
		loadOrderData();
	});
	$( "#custom_date" ).datepicker({
		dateFormat:"yy-mm-dd",
		defaultDate: "+1w",
      	changeMonth: true
  	});
});

function loadOrderData() {
   var form_search_data = $(".search-form").serialize();  	  
   $("#ajax_data").html('<b style="text-align:center;">Please Wait...</b>');
   $.ajax({
	   	url:$("#ajax_data").attr('data-href'),
	   	type:'POST',
	   	dataType:'text',
	   	data:form_search_data,
	   	success:function(response){
	   		$("#ajax_data").html(response);
	   	},
	   	error:function(errorResponseText){
	   		console.log(errorResponseText);
	   	}
   });	
}
</script>

@endsection

