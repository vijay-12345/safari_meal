<?php  
use App\Product;
//use DB;
?>
@extends('publicLayout')
@section('title', Lang::get('home.add_new_address'))
@section('content')
<div class="updateaddress">	
	 <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal">&times;</button>	    
	    <h4 class="modal-title">Edit Address </h4>	    
	</div> 	
	<div class="modal-body">                                                       						                
		<div class="content-block choices-toppings__container">                                                                   
			{!! Form::open(array('url'=>'updateaddressbook','class' =>'updateaddress')) !!} 
			<input type="hidden" value="{{Auth::user()->id}}" name="user_id" class="form-control icon-field"> 									
			<input type="hidden" value="{{$addressid}}" name="id" class="form-control icon-field"> 								
			<div class="form-group">
<label for="first_address">{{ Lang::get('home.first_address') }} <span class="red">*</span></label>
				<input type="text" name="first_address" value="@if(!empty($first_address)) {{$first_address}} @endif" placeholder="{{ Lang::get('home.first_address') }}" class="form-control icon-field">	
				<i class="flaticon-internet"></i>
<p>{{ Lang::get('home.first_address_hint') }}</p>
				{!! Form::errorMsg('first_address', $errors) !!}
			</div>	
			<div class="form-group">
				<input type="text" name="second_address" value="@if(!empty($second_address)) {{$second_address}} @endif" placeholder="{{ Lang::get('home.second_address') }}" class="form-control icon-field">	
				<i class="flaticon-internet"></i>
<p>{{ Lang::get('home.second_address_hint') }}</p>
			</div>
			<div class="form-group">
<label for="country">{{ Lang::get('home.country') }} <span class="red">*</span></label>
				<select class="form-control icon-field selectpicker" name="country" id="country">
					<option value="">Country</option>
					@if(isset($countries))
					@foreach($countries as $countrie)
					<option data-option="{{$countrie['country_id']}}" @if($country == $countrie['country_code']) selected @endif value="{{$countrie['country_code']}}">{{$countrie['country_name']}}</option>
					@endforeach
					@endif					
				</select>								
				<i class="flaticon-earth"></i>
				{!! Form::errorMsg('country', $errors) !!}
			</div>
			<div class="form-group">
<label for="states">{{ Lang::get('home.state') }} <span class="red">*</span></label>
				<select class="form-control icon-field selectpicker selector" name="state" id="states">										
					@if(isset($states))
					@foreach($states as $state)					
					<option data-option="{{$state['state_id']}}" @if($stateId==$state['state_id']) selected @endif value="{{$state['state_id']}}">{{$state['state_name']}}</option>
					@endforeach
					@endif
				</select>
				<input style="display:none" class="form-control icon-field selector" type="text" name="state"></input>								
				<i class="flaticon-location"></i>
				{!! Form::errorMsg('state', $errors) !!}
			</div>
			<div class="form-group">
<label for="cities">{{ Lang::get('home.city') }} <span class="red">*</span></label>
				<select class="form-control icon-field selectpicker selector" name="city" id="cities">										
					@if(isset($cities))
					@foreach($cities as $city)					
					<option data-option="{{$city['id']}}" @if($cityId==$city['id']) selected @endif value="{{$city['id']}}">{{$city['name']}}</option>
					@endforeach
					@endif
				</select>
				<input style="display:none" class="form-control icon-field selector" type="text" name="city" value=""></input>
				<i class="flaticon-gps-1"></i>
				{!! Form::errorMsg('city', $errors) !!}
			</div>	
			<div class="form-group">
<label for="areaa">{{ Lang::get('home.area') }} <span class="red">*</span></label>
				<select class="form-control icon-field selectpicker selector" name="area" id="areaa">										
					@if(isset($areas))
					@foreach($areas as $area)					
					<option data-option="{{$area['id']}}" @if($areaId==$area['id']) selected @endif value="{{$area['id']}}">{{$area['name']}}</option>
					@endforeach
					@endif
				</select>
				<input style="display:none" class="form-control icon-field selector" type="text" name="area"></input>
				<i class="flaticon-gps-2"></i>
				{!! Form::errorMsg('area', $errors) !!}
			</div>		
<div class="form-group">
<label for="landmark">{{ Lang::get('home.landmark') }} <span>(Optional)</span></label>
				<input type="text" name="landmark" value="@if(!empty($landmark)) {{$landmark}} @endif" placeholder="{{ Lang::get('home.landmark') }}" class="form-control icon-field">	
				<i class="flaticon-signpost"></i>
				{!! Form::errorMsg('landmark', $errors) !!}
			</div>
		
			<div class="form-group">
<label for="zip">{{ Lang::get('home.zip') }} <span>(Optional)</span></label>
				<input type="text" name="zip" value="@if(!empty($zip)) {{$zip}} @endif" placeholder="{{ Lang::get('home.zip') }}" class="form-control icon-field">	
				<i class="flaticon-signpost"></i>
				{!! Form::errorMsg('zip', $errors) !!}
			</div>
											
			<div class="choices-toppings__submit">
				<button class="btn btn-primary btn-lg " id="product_skeleton_submit" type="submit">Submit</button>
			</div>                                                            
			{!! Form::close() !!}
		</div>                                                                                                                    
	</div>							                
</div>
@endsection
<script type="text/javascript">
$(document).ready(function(){		
	$("form.addnewaddressbook").find('.selector').filter('input').attr('disabled', true);
	$("form.updateaddress").find('.selector').filter('input').attr('disabled', true);
});
</script>
