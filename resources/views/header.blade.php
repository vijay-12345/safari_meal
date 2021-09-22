<header>		
	<style>
	.modal-backdrop {
		z-index:0 !important;
	}
	.theme-background-color { background-color: #2dca03 !important; }
	</style>

	<script>
		var baseUrl = "{{url('/')}}/";
	</script>

	<div class="container">
		
		<div class="row">
			<div class="col-sm-4">
				<a href="{{url('/')}}" title="Taxiye Food" class="logo"><img src="/images/logo.png" alt="Taxiye Food"></a>
			</div>
			<div class="col-md-8">
				<div class="top-link text-right">
					<ul class="list-inline">
						<!-- comment multi language functionality -->
						<!-- <li>
							<div class="dropdown">
							  	<a id="dLabel" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								  	<i class="flaticon-earth"></i>
								   	{{ 'Language' }}
								    <span class="flaticon-arrows"></span>
							  	</a>
							  	<ul class="dropdown-menu" aria-labelledby="dLabel">
									<li><a href="/en" >{{Lang::get('home.English')}}</a></li>
								    <li><a href="/so" >{{ 'Somali' }}</a></li>
							  	</ul>
							</div>
						</li> -->
						<li><a href="{{ url('page/help') }}" title=""><i class="flaticon-shapes"></i>{{Lang::get('home.Help')}}</a></li>
						<li>
							<div class="dropdown">
							  	<a id="dLabel" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								  	<i class="flaticon-round"></i> @if(isset(Auth::user()->first_name))  {{ Auth::user()->first_name}} @else
								    {{Lang::get('home.My.Account')}}  @endif
								    <span class="flaticon-arrows"></span>
							  	</a>
							  	
							  	<ul class="dropdown-menu" aria-labelledby="dLabel">
									<?php if(!(Auth::check())) { ?>
										<li>
											<a href="{{ url('/auth/login') }}" id="loginPopUp" class="" title="" data-toggle="modal" data-target="#LoginPop">
												Login / Sign up
											<!-- {{Lang::get('home.Login')}} -->
											</a>
										</li>
										<li style="display:none;">
											<a href="{{ url('/auth/register') }}" id="signupPopUp" class="" title="" data-toggle="modal" data-target="#SignupPop">{{Lang::get('home.Signup')}}</a>
										</li>
									<?php } else { ?>
										<li>
											<a href="{{ url('editprofile') }}" class="" title="" >{{Lang::get('home.My Profile')}}</a>
										</li>
										<li>
											<a href="{{ url('order-history') }}" class="" title="" >{{Lang::get('home.My Orders')}}</a>
										</li>
										<li>
											<a href="{{ url('/tablebook-history') }}" class="" title="" >{{Lang::get('home.My Table Bookings')}}</a>
										</li>
										<li>
											<a href="{{ url('/auth/logout') }}" class="" title="" data-toggle="modal" >{{Lang::get('home.logout')}}</a>
										</li>
									<?php } ?>
							  	</ul>
							</div>
						</li>
					<!-- 	<li >
							<a href="{{ url('page/help') }}" target='_blank' title="Facebook"><i class="fa fa-shopping-cart"></i></a>
						</li>
						<li class="social-link first-link">
							<a href="https://www.facebook.com/SafariMeals/" target='_blank' title="Facebook"><img src="/images/fb-btn.png" alt="Facebook"></a>
						</li>
						<li class="social-link">
							<a href="https://twitter.com/safarimeal" target='_blank' title="Twitter"><img src="/images/twitter-btn.png" alt="Twitter"></a>
						</li>
						<li class="social-link">
							<a href="https://www.instagram.com/safarimeal" target='_blank' title="Instagram"><img src="/images/instagram-btn.png" alt="Instagram"></a>
						</li> -->
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

<!-- Forgot password Modal -->
<div class="modal fade" id="ProductAddons" tabindex="-1" role="dialog" aria-labelledby="ProductAddons">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Add new address Modal -->
<div class="modal fade ProdAddonaddresscustom" id="addnewaddress" role="dialog">
	<div class="modal-dialog">           
        <div class="modal-content">
          							                
        </div>      
    </div>
</div>