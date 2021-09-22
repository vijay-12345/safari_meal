@extends('admin.layout')

@section('title', trans('admin.add.order'))

@section('content')

<?php $prefix = \Request::segment(2); ?>

<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet"  media="all">
<script type="text/javascript" src="{{ asset('js/jquery.bxslider.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.flexslider-min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/typeahead.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/moment-with-locales.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>

<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
			</div>

			<div class="col-md-9">

				{!! Form::open(['class'=>'filter-form admin-order-add','url' => $prefix.'/order/add']) !!}
				
				@if(Session::has('flash_message'))
				    <div class="alert alert-success">
				        {{ Session::pull('flash_message') }}	   
				    </div>
				@endif

				@if($errors->any())
				    <div class="alert alert-danger">
				        @foreach($errors->all() as $error)
				            <p>{{ $error }}</p>
				        @endforeach
				    </div>
				@endif

				<div class="page-data-right">
					<div class="page-title">
						<div class="row">
							<div class="col-md-12">
								<h3>{{trans('admin.add.order')}}</h3>
							</div>
						</div>
					</div>
					 	
					<div class="row">
						<div class="col-md-9">
							<div class="panel panel-default">
							  <div class="panel-body">							  	
							    <div class="table-responsive">
										<table class="table table-bordered no-margin">
											<tr>
												<td><h5>{{trans('admin.select.restaurant')}}:</h5></td>
												<td>
													@if(Session::get('access.role') == 'admin')
														{!! Form::select('restaurant_id',[''=>trans('admin.select.restaurant')]+$data['restaurant_data'], null, ['class' => 'form-control','id'=>'order_restaurant_search']) !!}
													@elseif(count($data['restaurant_data']))
														{!! Form::select('restaurant_id', $data['restaurant_data'], null, ['class' => 'form-control','id'=>'order_restaurant_search']) !!}
													@else
														{!! Form::select('restaurant_id', [''=>trans('admin.select.restaurant')], null, ['class' => 'form-control','id'=>'order_restaurant_search']) !!}
													@endif
												</td>
											</tr>
											<tr>
												<td><h5>{{trans('admin.search.customer')}}:</h5></td>
												<td>
													<input type="text" name="search_customer" maxlength = "10" id="order_search_customer" value="" placeholder="{{trans('admin.mobile.number')}}" class="form-control">
												</td>
												<td>
													<input type='button' name="search_customer_button" id="order_search_customer_button" value="{{trans('admin.search')}}" class="form-control btn btn-primary"/>
												</td>
											</tr>
											<tr>
												<td colspan="3">
													<p class="" id="order_customer_search_result"><!-- ajax customer search data -->
													</p>
												</td>
											</tr>
										</table>
									</div>
							  </div>
							</div>
						</div>
					</div>

					
					<div class="row" id="order_restaurant_search_result">
						<!-- ajax restaurent search result -->
					</div>
					
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$( document ).on('click', 'input[name="order_type"]', function(){
		if( $(this).val() == 'delivery') {
			$('#delivery').show();
			var total = parseFloat($('#total').data('total')) + parseFloat($('#deliveryCharge').data('total'));
			$('#total').html( '$' + total );
		} else {
			$('#delivery').hide();
			$('#total').html( '$' + $('#total').data('total') );
		}
	});	
</script>

@endsection