<?php
	use App\Product;
	use App\State, App\City, App\Area;
	use App\Order;
	$order = (new Order())->getOrderFullDetailsByOrderNumber($ordernum);	
	$cart = new App\Cart;
?>
@extends('publicLayout')

@section('title', 'Thanks')

@section('content')

@if(Session::has('Successmessage'))
	<div class="alert alert-success">
	   {{Session::pull('Successmessage')}}
	</div>
@else
	@if(Session::has('Errormessage'))
		<div class="alert alert-danger">
		 {{Session::pull('Errormessage')}}
		</div>
	@endif
@endif

<?php 
	Session::pull('Successmessage');
	Session::pull('Errormessage');
?>

<body class="inner-page">
	<div class="inner-page-header">
		<div class="container">
			<div class="row">
				<div class="col-md-9">
					<div class="breadcrumbs-cont">
						<p><a href="{{url('/')}}" title="">Home</a> / <a href="#" title="">Restaurants</a> / Buy</p>
					</div>
				</div>
				<div class="col-md-3">

				</div>
			</div>
		</div>
	</div>

	<div class="inner-page-data">
		<div class="container">
			<div class="sidebar-data">
				<div class="row">
					<div class="col-md-12">
						<div class="page-data-right">
							<div class="page-data-outer">
								<div class="row">
									<div class="col-md-8 col-md-offset-2">
										<div class="thanks-page text-center">
											<div class="icon-cont"><i class="flaticon-smiley"></i></div>
											<h3>{{ Lang::get('proceedtocheckout.thanks_for_order') }}</h3>
											<h5>{{ Lang::get('proceedtocheckout.your_order_code') }} {{$ordernum}} </h5>
											
											@if($paymentmethod == 'evc')
												<h4>Dial the code below to make the payment</h4>
												<input class="btn btn-primary btn-lg" style="margin-top: 10px;" type="button" value="*712*618000165*{{str_replace('.', '*', $cart->getTotal( $cart->getOrderType() ))}}#"/>
											@endif
																		
											<p style="color: black; padding-top: 34px;">
												{{ Lang::get('proceedtocheckout.confirm_order_message') }}
											</p>
											<ul class="list-inline contact-info">
												<li><a href="#" title=""><i class="flaticon-telephone"></i>{{\Config::get('constants.administrator.mobile')}}</a></li>
												<li><a href="#" title=""><i class="flaticon-letter"></i>{{\Config::get('constants.administrator.email')}}</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>		
				</div>
			</div>
		</div>
	</div><!--/inner page data-->

</body>

<?php $cart->clearcart(); ?>


<script type="text/javascript">
   	(function (global) { 

     	if(typeof (global) === "undefined") {
         	throw new Error("window is undefined");
 		}
 		
     	var _hash = "!";
     	var noBackPlease = function () {
            console.log('no back please');
         	global.location.href += "#";
         	global.setTimeout(function () {
             	global.location.href += "!";
         	}, 50);
     	};

     	global.onhashchange = function () {
         	if (global.location.hash !== _hash) {
             	global.location.hash = _hash;
         	}
 		};

     	global.onload = function () {            
	        noBackPlease();
	        document.body.onkeydown = function (e) {
	            var elm = e.target.nodeName.toLowerCase();
	            if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
	                e.preventDefault();
	            }
	            e.stopPropagation();
	        };          
	    }
	    
	})(window);
	window.history.forward(1);
	function preventBack() { 
	  window.history.forward(1); 
	}
	preventBack();
</script>

@endsection