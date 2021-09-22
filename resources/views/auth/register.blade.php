<?php 
	use App\City;
?>
@extends('publicLayout')

@section('title', 'app register')

@section('content')

<?php 
	// $country_code = DB::table('country_code')->select(DB::raw('DISTINCT(phonecode)'))->orderBy('phonecode', 'ASC')->where('phonecode','!=','241')->get();
	$city = City::get();
	$countryCode = $setting ? $setting->country_code : '91'; 
?>

<div class="registration">
	 <div class="modal-body">
      	<div class="login-pop-data">
      		<div class="login-pop-header">
	        	<h4>Sign up</h4>
	        	<br>
	        	<!-- <p>with your social network</p>
	        	<div class="social-login">
	        		<a href="{!!URL::to('fbredirect/facebook')!!}" title="" class="sl-btn fb-btn"><i class="fa fa-facebook" aria-hidden="true"></i> FACEBOOK</a>
	        	</div> -->
	        </div>

			{!! Form::open(array('role' => 'form','class' => 'form-horizontal','url' => '/auth/register')) !!}

	        <div class="pop-body">
	        	<div class="form-group clearfix">
	        		<input type="text" name="first_name"  value="{{ Request::input('first_name') }}" placeholder="First Name" class="form-control icon-field dubble-field-first">
	        		<input type="text" data-inputmask='"mask": "(999) 999-9999"' data-mask placeholder="Last Name" value="{{ Request::input('last_name') }}" name="last_name" class="form-control dubble-field-sec">

	        		{!! Form::errorMsg('first_name', $errors) !!}
	        		{!! Form::errorMsg('last_name', $errors) !!}
	        		
	        		<i class="flaticon-shape"></i>
	        	</div>
	        	
	        	<div class="form-group phone-field">
	        		<select class="country-code" name="countrycode">
	        			<!-- <option value="252">+252</option> -->
	        			<option value="<?=$countryCode;?>">+{{$countryCode}}</option>
	        			{{--@foreach($country_code as $country_cod)
	        			<option value="{{$country_cod->phonecode}}">+{{$country_cod->phonecode}}</option>
	        			@endforeach --}}
	        		</select>
	        		<input type="text" name="contact_number" value="<?=(Session::has('loginNumber')) ? Session::pull('loginNumber') : Request::input('contact_number');?>" placeholder="Mobile Number"  class="form-control icon-field" maxlength="10">
	        		{!! Form::errorMsg('contact_number', $errors) !!}
	        		<i class="flaticon-technology"></i>
	        	</div>

	        	<div class="form-group">
	        		<input type="text" name="email" value="{{ Request::input('email') }}" placeholder="Email ID" class="form-control icon-field">
	        		{!! Form::errorMsg('email', $errors) !!}
	        		<i class="flaticon-letter"></i>
	        	</div>
	        	<div class="form-group">
	        		<input type="password" name="password" placeholder="Password" class="form-control icon-field">	        	
	        		{!! Form::errorMsg('password', $errors) !!}
	        		<i class="flaticon-lock"></i>
	        	</div>
	        	<div class="form-group">
	        		<input type="password" name="password_confirmation" value="" placeholder="Confirm Password" class="form-control icon-field">
	        		<i class="flaticon-lock"></i>
	        	</div>
        		<div class="row">
        			<div class="col-md-12">
        				<div class="form-group">
        					<div class="term-cont">
								<input type="checkbox" name="terms_conditions" id="checkboxrG5" class="css-checkbox">
								<label for="checkboxrG5" class="css-label">I have read and accepted the Terms and Conditions and Private Policy.</label>
								<br>{!! Form::errorMsg('terms_conditions', $errors) !!}
							</div>
        				</div>
        			</div>
        		</div>
        		<div class="btn-cont">
        			<button type="submit" class="btn btn-primary btn-lg btn-full">Sign up</button>        			
        			<p>Already a member? <a href="{{ url('auth/login') }}" title="" data-toggle="modal" data-target="#LoginPop" data-dismiss="modal"><strong>Log in</strong></a></p>
        		</div>
	        </div>
	        {!! Form::close() !!}
      	</div>
  	</div>
</div>	
@endsection
