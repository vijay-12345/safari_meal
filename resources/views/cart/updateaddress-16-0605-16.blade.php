@extends('publicLayout')
@section('title', 'Add New address')
@section('content')
<?php  
use App\Product;
use DB;
/*pr(Request::all());
if(Request::input('addr')){	
	$addressid = Request::input('addr');
	$userAddress = DB::table('user_address')->where('id',$addressid)->first();
	pr($userAddress);
	$formaction ='updateaddressbook';
	$formclass ='updateaddress';
	$addressid = $userAddress->id;
	$user_id = $userAddress->user_id;
	$first_address = $userAddress->first_address;
	$second_address = $userAddress->second_address;
	$country = $userAddress->country;
	$state = $userAddress->state;
	$city = $userAddress->city;
	$area = $userAddress->area;
	$zip = $userAddress->zip;
	
}else{
	echo 2222;die;
	$addressid = Request::input('id');
	$user_id = Request::input('user_id');
	$first_address = Request::input('first_address');
	$second_address = Request::input('second_address');
	$city = Request::input('city');
	$zip = Request::input('zip');
	$country = Request::input('country');
	$state = Request::input('state');
	$area =  Request::input('area');
}*/
?>
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
				<input type="text" name="first_address" value="{{$first_address}}" placeholder="First Address" class="form-control icon-field">	
				<i class="flaticon-letter"></i>
				{!! Form::errorMsg('first_address', $errors) !!}
			</div>	
			<div class="form-group">
				<input type="text" name="second_address" value="{{$second_address}}" placeholder="Second Address" class="form-control icon-field">	
				<i class="flaticon-letter"></i>
			</div>
			<div class="form-group">
				<select class="form-control icon-field selectpicker" name="country" id="country">
					<option value="">Country</option>
					@if(isset($countries))
					@foreach($countries as $countrie)
					<option data-option="{{$countrie['country_id']}}" @if($country==$countrie['country_code']) selected @endif value="{{$countrie['country_code']}}">{{$countrie['country_name']}}</option>
					@endforeach
					@endif					
				</select>								
				<i class="flaticon-letter"></i>
				{!! Form::errorMsg('country', $errors) !!}
			</div>
			<div class="form-group">
				<select class="form-control icon-field selectpicker" name="state" id="states">					
					<option data-option="{{$stateId}}" value="{{$stateId}}">{{$state}}</option>					
				</select>
				<i class="flaticon-letter"></i>
				{!! Form::errorMsg('state', $errors) !!}
			</div>
			<div class="form-group">
				<select class="form-control icon-field selectpicker" name="city" id="cities">					
					<option data-option="{{$cityId}}" value="{{$cityId}}">{{$city}}</option>					
				</select>
				<i class="flaticon-letter"></i>
				{!! Form::errorMsg('city', $errors) !!}
			</div>	
			<div class="form-group">
				<select class="form-control icon-field selectpicker" name="area" id="areaa">					
					<option data-option="{{$areaId}}" value="{{$areaId}}">{{$area}}</option>									
				</select>
				<i class="flaticon-letter"></i>
				{!! Form::errorMsg('area', $errors) !!}
			</div>				
			<div class="form-group">
				<input type="text" name="zip" value="{{$zip}}" placeholder="Zip" class="form-control icon-field">	
				<i class="flaticon-letter"></i>
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
