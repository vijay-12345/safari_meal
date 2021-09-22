@extends('admin.layout')

@section('title', trans('admin.add.table'))

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

				{!! Form::open(['class'=>'filter-form admin-table-add','url' => $prefix.'/table/add']) !!}
				
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
								<h3>{{trans('admin.add.table')}}</h3>
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
														{!! Form::select('restaurant_id',[''=>trans('admin.select.restaurant')]+$data['restaurant_data'], null, ['class' => 'form-control','id'=>'table_restaurant_search']) !!}
													@elseif(count($data['restaurant_data']))
														{!! Form::select('restaurant_id', $data['restaurant_data'], null, ['class' => 'form-control','id'=>'table_restaurant_search']) !!}
													@else
														{!! Form::select('restaurant_id', [''=>trans('admin.select.restaurant')], null, ['class' => 'form-control','id'=>'table_restaurant_search']) !!}
													@endif
												</td>
											</tr>
											<tr>
												<td><h5>{{trans('admin.search.customer')}}:</h5></td>
												<td>
													<input type="text" name="search_customer" id="table_search_customer" value="" placeholder="{{trans('admin.mobile.number')}}" class="form-control">
												</td>
												<td>
													<input type='button' name="search_customer_button" id="table_search_customer_button" value="{{trans('admin.search')}}" class="form-control btn btn-primary"/>
												</td>
											</tr>
											<tr>
												<td colspan="3">
													<p class="" id="table_customer_search_result"><!-- ajax customer search data -->
													</p>
												</td>
											</tr>
											<tr>
												<td><h5>{{trans('admin.total.person')}}:</h5></td>
												<td>
													<input type="number" name="total_person" id="total_person" value="" placeholder="{{trans('admin.total.person')}}" class="form-control">
												</td>
												<td>
													<input type='button' name="table_save_button" id="admin_table_add" value="{{trans('admin.save.table')}}" class="form-control btn btn-primary"/>
												</td>
											</tr>
										</table>
									</div>
							  </div>
							</div>
						</div>
					</div>

					
					<!-- <div class="row" id="order_restaurant_search_result">
						<!-- ajax restaurent search result -->
					</div> -->
					
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

    jQuery("#table_search_customer_button").click(function() {
       // alert();
        defaultResId = jQuery("#table_restaurant_search").val();
        if(defaultResId !='') {
            if(!getdetailsAddress()) {
                $("#table_customer_search_result").html("Please enter valid phone number");   
            }
            orderRestaurantResult(defaultResId); 
        } else {
            alert('Please first select restaurant');
        }
    });

    function orderRestaurantResult(restaurant_id){
	    $.ajax({
	        url:baseUrl+'ajax/getRestaurant',
	        type:'post',                           
	        data:"restaurant_id="+restaurant_id,           
	        success:function(data) {                   
	            $("#order_restaurant_search_result").html(data);                          
	        },
	        error:function(error){
	            alert('Data could not be loaded.');
	        }
	    });     
	}

	function getdetailsAddress() {
        var contactNumber = $('#table_search_customer').val();
        if(contactNumber == '' || isNaN(contactNumber)) return false;
         $.ajax({
            url:baseUrl+'ajax/getCustomer',
            type:'post',   
            data:"contactNumber="+contactNumber,           
            success:function(data){                   
                $("#table_customer_search_result").html(data);   
            },
            error:function(error) {
                alert('Data could not be loaded.');
                return false;
            }
        });
    }

    $(document).on('click','#admin_table_add',function(e) {
	    e.preventDefault();
	    var contactNumber = $('#table_search_customer').val();
	    var address = $("input[name='radiog_lite']:checked"). val();
	    var total_person = $('#total_person').val();
	    if(contactNumber == '' || isNaN(contactNumber)) {
	        alert("Please enter valid phone number");
	        return false;
	    }
	    else if($("input[name='radiog_lite']").length == 0) {
	        alert("Please search customer");
	        return false;
	    }
	    // if( $('input[name="order_type"]:checked').val() == 'delivery') {
	    else if(address == undefined || address == '') {
	        alert("Please select your address");
	        return false;
	    }
	    else if(total_person == '' || isNaN(total_person)) {
	        alert("Please enter number of person for table");
	        return false;
	    }else{
	    	$('.admin-table-add').submit();
	    	//document.getElementById("myForm").submit();
	    }
	    // }
	});
</script>

@endsection