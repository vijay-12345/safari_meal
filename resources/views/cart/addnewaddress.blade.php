<?php
use App\Product;
use App\State, App\City, App\Area;
//use DB;

?>

@extends('publicLayout')

@section('title', Lang::get('home.add_new_address'))

@section('content')

<?php  

$formaction ='addnewaddressbook';
$formclass ='addnewaddress';
if(Request::input('addr')){

}

/*if(Request::input('addr')){
	$addressid = Request::input('addr');
	$userAddress = DB::table('user_address')->where('id',$addressid)->first();	
	$formaction ='updateaddressbook';
	$formclass ='updateaddress';
}*/

?>
<div class="{{$formclass}}">	
	 <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal">&times;</button>	    
	    @if(Request::input('addr'))
			<h4 class="modal-title">Edit Address </h4>
	    @else 
			<h4 class="modal-title">{{ Lang::get('home.add_new_address') }}</h4> 
		@endif
	</div> 	
	<div class="modal-body">                                                       						                
		<div class="content-block choices-toppings__container">                                                                   
			{!! Form::open(array('url'=>$formaction,'class' =>$formaction)) !!} 			
			<input type="hidden" value="{{Auth::user()->id}}" name="user_id" class="form-control icon-field"> 
			@if(Request::input('addr'))								
			<input type="hidden" value="{{$addressid}}" name="id" class="form-control icon-field"> 								
			@endif			
			<div class="form-group">
<label for="first_address">{{ Lang::get('home.first_address') }} <span class="red">*</span></label>
				<!--input type="text" name="first_address" value="@if(Request::input('addr')){{$userAddress->first_address}}@endif" placeholder="{{ Lang::get('home.first_address') }}" class="form-control icon-field" -->	
				<input type="text" name="first_address" value="@if(Request::input('first_address')){{Request::input('first_address')}}@endif" placeholder="{{ Lang::get('home.first_address') }}" class="form-control icon-field">	
				<i class="flaticon-internet"></i>
<p>{{ Lang::get('home.first_address_hint') }}</p>
				{!! Form::errorMsg('first_address', $errors) !!}
			</div>	
			<div class="form-group">
<label for="second_address">{{ Lang::get('home.second_address') }} <span>(Optional)</span></label>
				<!--input type="text" name="second_address" value="@if(Request::input('addr')){{$userAddress->second_address}}@endif" placeholder="{{ Lang::get('home.second_address') }}" class="form-control icon-field"-->	
				<input type="text" name="second_address" value="@if(Request::input('second_address')){{Request::input('second_address')}}@endif" placeholder="{{ Lang::get('home.second_address') }}" class="form-control icon-field">
				<i class="flaticon-internet"></i>
<p>{{ Lang::get('home.second_address_hint') }}</p>
			</div>			
			<div class="form-group">
<label for="country">{{ Lang::get('home.country') }} <span class="red">*</span></label>
				<select class="form-control icon-field selectpicker" name="country" id="country">
					<option value="">---Select---</option>
					@if(isset($countries))
						@foreach($countries as $countrie)
							<option data-option="{{$countrie['country_id']}}" value="{{$countrie['country_code']}}" 
								@if(null !== Request::input('country') && Request::input('country')) ==$countrie['country_name'])
								{{'selected'}}
								@endif		
							>
							{{$countrie['country_name']}}
							</option>
						@endforeach
					@endif					
				</select>				
				<i class="flaticon-earth"></i>
				{!! Form::errorMsg('country', $errors) !!}
			</div>
			<div class="form-group">
<label for="states">{{ Lang::get('home.state') }} <span class="red">*</span></label>
				<select class="form-control icon-field selectpicker selector" name="state" id="states">					
					@if(count(Request::all())>0 && "" != Request::input('state'))
					<option data-option ="{{Request::input('state')}}" value="{{Request::input('state')}}">{{ State::getStateById(Request::input('state'))->state_name }}</option>
					@else
					<option value="">---Select---</option>
					@endif					
				</select>
				<input style="display:none" class="form-control icon-field selector" type="text" name="state"></input>								
				<i class="flaticon-location"></i>
				{!! Form::errorMsg('state', $errors) !!}
			</div>
			
			<div class="form-group">
<label for="cities">{{ Lang::get('home.city') }}<span class="red">*</span></label>
				<select class="form-control icon-field selectpicker selector" name="city" id="cities">
					@if(count(Request::all())>0 && "" != Request::input('city'))
					<option data-option ="{{Request::input('city')}}" value="{{Request::input('city')}}">{{ City::getCityById(Request::input('city'))->name }}</option>
					@else
					<option value="">---Select---</option>
					@endif					
				</select>
				<input style="display:none" class="form-control icon-field selector" type="text" name="city" value=""></input>
				<i class="flaticon-gps-1"></i>
				{!! Form::errorMsg('city', $errors) !!}
			</div>	
			<div class="form-group">
<label for="areaa">{{ Lang::get('home.area') }} <span class="red">*</span></label>
				<select class="form-control icon-field selectpicker selector" name="area" id="areaa">					
					@if(count(Request::all())>0 && "" != Request::input('area'))
					<option value="{{Request::input('area')}}">{{ Area::getAreaById(Request::input('area'))->name }}</option>
					@else
					<option value="">---Select---</option>
					@endif					
				</select>				
				<input style="display:none" class="form-control icon-field selector" type="text" name="area"></input>
				<i class="flaticon-gps-2"></i>
				{!! Form::errorMsg('area', $errors) !!}
			</div>
			<div class="form-group">
<label for="landmark">{{ Lang::get('home.landmark') }} <span>(Optional)</span></label>
				<input type="text" name="landmark" value="@if(Request::input('landmark')){{Request::input('landmark')}}@endif" placeholder="{{ Lang::get('home.landmark') }}" class="form-control icon-field">					
				<i class="flaticon-signpost"></i>
				{!! Form::errorMsg('landmark', $errors) !!}
			</div>
			<div class="form-group">
<label for="zip">{{ Lang::get('home.zip') }} <span>(Optional)</span></label>
				<!--input type="text" name="zip" value="@if(Request::input('addr')){{$userAddress->zip}}@endif" placeholder="Zip" class="form-control icon-field"-->	
				<input type="text" name="zip" value="@if(Request::input('zip')){{Request::input('zip')}}@endif" placeholder="{{ Lang::get('home.zip') }}" class="form-control icon-field">					
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
