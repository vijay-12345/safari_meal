<?php  
use App\Product;
//echo $order->amount.'<br>';
//echo $order->id.'<br>';
?>
@extends('publicLayout')

@section('title', 'Foo app application')

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

<div class="inner-page-header">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<div class="breadcrumbs-cont">
					<p><a href="{{url('restaurentdetail/'.$restaurantdetails->restaurent_urlalias)}}" title=""><i class="fa fa-angle-left" aria-hidden="true"></i> &nbsp;Back to restaurants</a></p>
				</div>
			</div>
			<div class="col-md-3">

			</div>
		</div>
	</div>
</div>

<?php $url='proceedtosecurecheckout/'.$ordernumber; ?>

{!! Form::open(['class'=>'form-horizontal secure_form','url' => $url]) !!}	
							
	<div class="checkout-tab">
		<div class="inner-page-data">

			<div class="check-tab-header">
				<div class="container">
					<!-- Nav tabs -->
				  	<ul class="nav nav-tabs" role="tablist">
					    <li role="presentation" class="or-sm">
					    	<a href="#OrderSumm" aria-controls="OrderSumm" role="tab" data-toggle="tab">
						    	<i class="cart-summ-icon"></i>
						    	<span class="heading">1. Your Order</span>
						    	<span>Make sure everything is as your wish</span>
					    	</a>
						</li>
					    <li role="presentation" class="chck-out active">
					    	<a href="#Checkout" aria-controls="Checkout" role="tab" data-toggle="tab">
						    	<i class="chckout-icon"></i>
								<span class="heading">2. Secure Checkout</span>
						    	<span>Select delivery address and payment type</span>
					    	</a>
						</li>
				  	</ul>
				</div>
			</div>

			<div class="check-tab-data-outer">
				<div class="container">
					<!-- Tab panes -->			  
				    <div role="tabpanel" class="tab-pane" id="Checkout">
						<div class="sidebar-data">
							<div class="row">
								<div class="col-md-8">
									<div class="cart-summry-data">
										<div class="page-data-header">
											<i class="flaticon-round-1"></i>
											My Contact Details
										</div>

										<div class="border-data user-detail">
											<div class="form-group">
												<div class="row" >
													<div class="col-md-5">
														<i>Address Book</i>
													</div>
													<div class="col-md-5" >
													</div>
													<div class="col-md-2" style="padding:15px;" >
														<a data-toggle="modal" data-target="#addnewaddress" href="{{asset('/')}}{{Config::get('app.locale_prefix')}}/loadnewaddressbook">
															<button class="btn btn-primary">Add new address</button>
														</a>
													</div>
												</div>

												@if($addressflag)
													<div class="row">
														<div class="col-md-12">
															<select id="OptDelivAddr" name="area" class="form-control icon-field selectpicker" >
															@foreach($useraddresses as $useraddress)
																<?php
																// $guestAddress = isset($useraddress->first_address) && ($useraddress->first_address) ? ucfirst($useraddress->first_address).', ' : '';
																// $guestAddress .= isset($useraddress->second_address) && ($useraddress->second_address) ? ucfirst($useraddress->second_address).', ' : '';
																// $guestAddress .= isset($useraddress->name) && ($useraddress->name) ? ucfirst($useraddress->name).', ' : '';
																// $guestAddress .= isset($useraddress->state_name) && ($useraddress->state_name) ? ucfirst($useraddress->state_name).', ' : '';
																// $guestAddress .= isset($useraddress->country_name) && ($useraddress->country_name) ? ucfirst($useraddress->country_name).', ' : '';
																// $guestAddress .= isset($useraddress->zip) && $useraddress->zip ? $useraddress->zip : '';
																?>
																<option rel="{{$useraddress->user_id}}" value="{{$useraddress->user_addressid}}">
																	@if(isset($useraddress->first_address))
																		{{ ucfirst($useraddress->first_address) }}
																	@endif
																	@if(isset($useraddress->second_address))
																		{{ ', '.ucfirst($useraddress->second_address) }}
																	@endif
																	@if(isset($useraddress->name))
																		{{ ', '.ucfirst($useraddress->name).',  ' }}
																	@endif
																	@if(isset($useraddress->state_name))
																		{{ ucfirst($useraddress->state_name) }}
																	@endif
																	@if(isset($useraddress->country_name))
																		{{ ',  '.ucfirst($useraddress->country_name).'  ' }}
																	@endif
																	@if(isset($useraddress->zip))
																		{{ $useraddress->zip }}
																	@endif
																</option>
															@endforeach
															</select>
														</div>
													</div>
												@endif
											</div>

											<table class="table">												
											</table>
										</div>

										<div class="icon-heading-data">
											<i class="flaticon-coins-1"></i>
											Choose How To Pay
										</div>

										<div class="payment-box">
											<div class="payment-tab">
												<div class="row">

													<div class="col-md-12 customPayment">
														<!-- Nav tabs -->
													  	<ul class="nav nav-tabs" role="tablist">
														    <li role="presentation" class="active">
														    	<a href="#cod" aria-controls="cod" role="tab" data-toggle="tab" class="paymentType">
														    	<i class="flaticon-icon-1203"></i>{{ Lang::get('proceedtocheckout.cod')}} 
														    	<!-- <span class="flaticon-arrows-1"></span> -->
														    	</a>
															</li>
														    <li role="presentation">
														    	<a href="#cod" aria-controls="evc" role="tab" data-toggle="tab" class="paymentType"><i class="flaticon-icon-1203"></i>{{ Lang::get('proceedtocheckout.evc')}} 
														    	</a>
														    </li>
													  	</ul>
													</div>

													<!-- <div class="col-md-7"> -->
													  	<!-- <div class="tab-content"> -->
														    <!-- <div role="tabpanel" class="tab-pane active" id="cod"> -->
																<?php 
																 	// $imageurl = captcha_src();
																 	// foreach(Config::get('app.alt_langs') as $key=>$v){
																	 	// $imageurl= str_replace("/".$v,"",$imageurl);
																	// }
																?>
																<!-- {{-- <img src='{{$imageurl}}'></img> --}} -->
																<!-- <div class="form-group captcha-field">
																	<input type="hidden" name="ordernum" value="<?=$ordernumber?>" class="form-control">
																	<input type="text" name="captcha" value="" class="form-control" placeholder="Enter Captcha">
																	<input type="hidden" name="paymentmethod" value="COD" class="form-control" >
																	<button type="submit" class="btn btn-primary btn-lg sbmt-btn-abs">Confirm Order</button>
																	<div class="min-order error"></div>
																</div> -->
														    <!-- </div> -->
													  	<!-- </div> -->
													<!-- </div> -->

												</div>
											</div>
										</div>

									</div>

								</div>

								<div class="col-md-4">
									<div class="sidebar-cart">
										<div class="side-widget">
											<div class="order-review">
												<div class="side-widget-data or-header-outer">
													<div class="or-header">
														<?php
															$image = isset($restaurantdetails->image->logo_location) ? $restaurantdetails->image->logo_location : 'images/default-restaurantlog.png';
														?>
														<span class="restro-logo" style="background: url('{{url($image)}}')">
															
														</span>
														Your Order
													</div>
												</div>
											</div>
										</div>
										
										<?php
											$subtotal = $cart->getSubtotal(); 
											$order_type = $cart->getOrderType();
											$total = $cart->getTotal( $order_type );
										?>

										<div class="or-products">
											@if(count($cart->getData()))
												@foreach($cart->getData() as $key => $data)
													@if(in_array($key, array('other')))
														<?php $otherdetails= $data ?>
													@else
														<div class="row"> 
															<div class="col-xs-9">
																<div class="single"><span>{{ $data['quantity'] }}x</span>{{$data['name']}}</div>
															</div>
															<div class="col-xs-3">
																<div class="price text-right">{{format($data['totalCost'])}}</div>
															</div>
														</div>
													@endif
												@endforeach
											@endif
										</div>

										<div class="sub-total">
											Subtotal: <span>{{format( $subtotal )}}</span>
										</div>

										<!-- start commented in old code -->
										<div class="sub-total">
											CGST {{ $cart->getCGST() }} %: <span>{{ format($cart->getCGST()/100 * $subtotal) }}</span>
										</div>

										<div class="sub-total">
											SGST {{ $cart->getSGST() }} %: <span>{{ format($cart->getSGST()/100 * $subtotal) }}</span>
										</div>

										<div class="sub-total">
											Packaging Fees: <span>{{ format($cart->getPackagingFees()) }}</span>
										</div>
										<!-- end commented in old code -->

										@if($order_type == 'delivery')
											<div class="sub-total">
												Delivery Charges: <span>{{format($cart->deliveryCharges())}}</span>
											</div>
										@endif
										
										@if(!empty($cart->getcoupandiscountvalue()))
											<div class="sub-total" style="color:green">
												Coupan Discount: <span>{{format($cart->getcoupandiscountvalue())}}</span>
											</div>
										@endif
										
										<div id="confim_total" class="total" data-total="{{ $total }}" data-min_order="{{$restaurantdetails->min_order}}" data-delivery_type="{{$order_type}}">
											Total:  {{ format($total) }}
										</div>

										<div class="confirmOrderButton">
											<div class="form-group">
												<input type="hidden" name="ordernum" value="<?=$ordernumber?>" class="form-control">
												<input type="hidden" name="paymentmethod" value="COD" class="form-control" >
										       <button type="submit" class="btn btn-primary btn-lg sbmt-btn-abs">Confirm Order</button>
									        </div>
										</div>
											
									</div>
								</div>

							</div>
						</div>
						
				    </div>
			  	</div>
			</div>

		</div>
	</div>

{!! Form::close() !!}

</div>

<script type="text/javascript">
	$(function() {
		$('.paymentType').click(function() {
			$('input[name="paymentmethod"]').val($(this).attr('aria-controls'));
			$(this).parent().addClass('active');
			$(this).parent().siblings().removeClass('active');
			return false;
		});
	});
</script>

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

	
$(document).ready(function () {
	var addressId = $('select#OptDelivAddr').val();	
	var userId = $('select#OptDelivAddr').find("option:selected").attr("rel");	
	if(addressId){
		$.ajax({			
			'url' : '{{url("ajax/optdeliveryaddress")}}',			
			'data' : { 'addressId' : addressId,'userId' : userId},
			'type' : 'post',
			'success' : function(response){				
				/*if(response=='noaddress'){
					$(".user-detail .form-group select").replaceWith(response);											
				}else{
					$('.user-detail table').replaceWith(response);						
				}*/				
				$('.user-detail table').replaceWith(response);						
			}			
		});
	}
})


</script>

@endsection