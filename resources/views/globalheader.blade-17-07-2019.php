<header>	
	<script>
		var baseUrl = "{{url('/')}}/";
	</script>	
	<div class="container" >
		
		<div class="row">
			<div class="col-sm-4">
				<a href="{{url('/')}}" title="Safari Meals" class="logo"><img src="/images/logo.png" alt="Safari Meals"></a>
			</div>
			<div class="col-md-8">
				<div class="top-link text-right">
					<ul class="list-inline">
						<!-- comment multi language functionality -->
						<!-- <li>
							<div class="dropdown">
							  <a id="dLabel" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							  	<i class="flaticon-earth"></i>
							    {{'Language'}}
							    <span class="flaticon-arrows"></span>
							  </a>

							  <ul class="dropdown-menu" aria-labelledby="dLabel">
								<li><a href="/en" >English</a></li>
							    <li><a href="/so" >Somali</a></li>
							  </ul>
							</div>
						</li> -->
						<li><a href="{{ url('page/help') }}" title=""><i class="flaticon-shapes"></i>{{Lang::get('home.Help')}}</a></li>
						<li>
							<div class="dropdown">
							  <a id="dLabel" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							  	<i class="flaticon-round"></i>							    
							     {{Lang::get('home.My.Account')}}
							    <span class="flaticon-arrows"></span>
							  </a>
							  <ul class="dropdown-menu" aria-labelledby="dLabel">
								<?php 	if(!(Auth::check())) {?>
								<li><a href="{{ url('auth/login') }}" class="" title="" data-toggle="modal" data-target="#LoginPop">Login</a></li>
								<li><a href="{{ url('auth/register') }}" class="" title="" data-toggle="modal" data-target="#SignupPop">Signup</a></li>
								<?php } else{?>
								<li><a href="{{ url('editprofile') }}" class="" title=""> {{Lang::get('home.My Profile')}}</a></li>
								<li><a href="{{ url('auth/logout') }}" class="" title="" data-toggle="modal" >logout</a></li>
								<?php }?>
							  </ul>
							</div>
						</li>					
					</ul>
				</div>
			</div>
		</div>
	</div>
</header><!--/header-->
<!-- Button trigger modal -->

<!-- Login Modal -->
<div class="modal fade" id="LoginPop" tabindex="-1" role="dialog" aria-labelledby="LoginPopLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     
    </div>
  </div>
</div>

<!-- Signup Modal -->
<div class="modal fade" id="SignupPop" tabindex="-1" role="dialog" aria-labelledby="SignupPopLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
         
    </div>
  </div>
</div>

<!-- Forgot password Modal -->
<div class="modal fade" id="ForgotPasswordPop" tabindex="-1" role="dialog" aria-labelledby="ForgotPasswordLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>


