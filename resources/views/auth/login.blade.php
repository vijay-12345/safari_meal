@extends('publicLayout')

@section('title', 'app login')

@section('content')

<div class="login" id="loginModal">
	<div class="modal-body">
      	<div class="login-pop-data">
      		<div class="login-pop-header">
	        	<h4>Log in / Sign up</h4>
	        	<br>
	        	<!-- <p>with your social network</p>
	        	<div class="social-login">
	        		<a href="{!!URL::to('fbredirect/facebook')!!}" title="" class="sl-btn fb-btn"><i class="fa fa-facebook" aria-hidden="true"></i> FACEBOOK</a>
	        	</div> -->
	        </div>
	        
	        <div class="pop-body">
	        	
	        	@if(Session::has('verified-message')) 
					<p class="text-success">
						You have successfully verified your account. Please Login to continue....
					</p>
				@endif
				
				<?php
					Session::pull("verified-message");
					if(!empty($notverified)) echo "<strong>Whoops!</strong>".$notverified;
				?>
				
				<div class="verify-modal-form" style="display: <?=(isset($showLogin) && $showLogin == true) ? 'none':'block';?>">
					{!! Form::open(array('role' => 'form','class' => 'form-horizontal','url' => '/auth/verify-number')) !!}
			        	<div class="form-group">
			        		<input type="text" name="mobile" value="{{ Request::input('mobile') }}" placeholder="Phone Number"  class="form-control icon-field" required="true" maxlength="10">
		        			{!! Form::errorMsg('mobile', $errors) !!}
			        		<i class="flaticon-technology"></i>
			        	</div>
			        	
		        		<div class="btn-cont">
		        			<button type="submit" class="btn btn-primary btn-lg btn-full">Continue</button>
		        			<!-- <p>Not a member? <a href="{{ url('auth/register') }}"  title="" data-toggle="modal" data-target="#SignupPop" data-dismiss="modal">
		        			<strong>Sign up</strong></a></p> -->
		        		</div>
		        		
		        	{!! Form::close() !!}
		        </div>

	        	<div class="login-modal-form" style="display: <?=(isset($showLogin) && $showLogin == true) ? 'block':'none';?>">
					{!! Form::open(array('role' => 'form','class' => 'form-horizontal','url' => '/auth/login')) !!}
						
						<div class="form-group">
			        		<input type="text" value="<?=(Session::has('loginNumber')) ? Session::pull('loginNumber') : Request::input('email');?>" class="form-control icon-field" name="email" placeholder="Mobile Number or Email ID">
			        		{!! Form::errorMsg('email', $errors) !!}
			        		<i class="flaticon-technology"></i>
			        		<!-- <i class="flaticon-letter"></i> -->
			        	</div>
			        	
			        	<div class="form-group">
			        		<input type="password" name="password" placeholder="Password" class="form-control icon-field">
			        		{!! Form::errorMsg('password', $errors) !!}
			        		<i class="flaticon-lock"></i>
			        	</div>
			        	
		        		<div class="row">
		        			<div class="col-md-6">
		        				<div class="form-group">
		        					<div class="term-cont">
										<input type="checkbox" name="checkboxrG1" id="checkboxrG1" class="css-checkbox">
										<label for="checkboxrG1" class="css-label">Remember me</label>
									</div>
		        				</div>
		        			</div>
		        			<div class="col-md-6">
		        				<div class="form-group">
		        					<div class="fg-pw-cont text-right">
										<a href="{{ url('/password/email') }}" title="" class="fg-link" data-toggle="modal" data-target="#ForgotPasswordPop">Forgot your password?</a>
									</div>
		        				</div>
		        			</div>
		        		</div>
		        		
		        		<div class="btn-cont">
		        			<button type="submit" class="btn btn-primary btn-lg btn-full">Continue</button>
		        			<!-- <p>Not a member? <a href="{{ url('auth/register') }}"  title="" data-toggle="modal" data-target="#SignupPop" data-dismiss="modal">
		        			<strong>Sign up</strong></a></p> -->
		        		</div>

		        	{!! Form::close() !!}
	        	</div>

	        </div>
      	</div>
  	</div>
</div>

@endsection