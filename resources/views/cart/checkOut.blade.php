@extends('publicLayout')

@section('title', 'Checkout')

@section('content')

@if(Session::has('Successmessage'))
	<!-- <div class="alert alert-success"> -->
	   <!-- {{Session::pull('Successmessage')}} -->
	<!-- </div> -->
@endif

@if(Session::has('Errormessage')) 
	<!-- <div class="alert alert-danger"> -->
	 	<!-- {{Session::pull('Errormessage')}} -->
	<!-- </div> -->
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
					<p><a href="{{url('/restaurentdetail/'.$restaurantUrl)}}" title=""><i class="fa fa-angle-left" aria-hidden="true"></i>&nbsp;Back to restaurants</a></p>
				</div>
			</div>
			<div class="col-md-3">

			</div>
		</div>
	</div>
</div>

<div class="checkout-tab">
	<div class="inner-page-data">
		
		<div class="check-tab-header">
			<div class="container">
				<!-- Nav tabs -->
			  <ul class="nav nav-tabs" role="tablist">
			    <li role="presentation" class="or-sm active"><a href="#OrderSumm" aria-controls="OrderSumm" role="tab" data-toggle="tab">
			    	<i class="cart-summ-icon"></i>
			    	<span class="heading">1. Your Order</span>
			    	<span>Make sure everything is as your wish</span>
			    </a></li>
			    <li role="presentation" class="chck-out"><a href="#Checkout" aria-controls="Checkout" role="tab" data-toggle="tab">
			    	<i class="chckout-icon"></i>
						<span class="heading">2. Secure Checkout</span>
			    	<span>Select delivery address and payment type</span>
			    </a></li>
			  </ul>
			</div>
		</div>
		
		<div class="check-tab-data-outer">
			<div class="container">
				<!-- Tab panes -->
			  	<div class="tab-content">
			    	<div role="tabpanel" class="tab-pane active" id="OrderSumm">
						<div class="menu-restro">

				            <div class="restro-list-data">
								<?php 
									$image = !is_null($restaurantdetails->image) ? $restaurantdetails->image->logo_location : 'images/default-restaurantlog.png';
								?>
								
		                	 	<a href="{{url('/restaurentdetail/'.$restaurantUrl)}}" title="">
					                <div class="restro-thumb" style="background: #fff url('{{url($image)}}')"></div>
				                </a>

				                <div class="title">
				                <!--    <a href="#" title="{{$restaurantdetails->company}}">{{$restaurantdetails->company}}</a> -->
				                </div>
				                
				                <p>{{$restaurantdetails->name}}</p>           
				                
				                <div class="restro-list-bottom">
				                    <ul class="list-inline">
				                        <?php
					                        $timingstatus = "";
					                     	$flag_close = false;
					                     	$currenttime  = strtotime(__dateToTimezone('', date('H:i:s'), 'H:i:s'));
				                            $opentime     = strtotime($restaurantdetails->open);
				                            $closetime    = strtotime($restaurantdetails->closing);
					                        if($currenttime < $opentime) {                                    
					                            $timediff = ($opentime - $currenttime);                         
					                            // $fsd = explode(":",date("H:i", $timediff));
					                            $timingstatus .= Lang::get('home.Opens.in');
					                            $hours 	 = floor($timediff / 3600);
					                            $minutes = floor(($timediff / 60) % 60);
					                            if($hours != 0)  $timingstatus .=' '.$hours.'h';
					                            if($minutes !=0) $timingstatus .=' '.$minutes.'min';
					                         	$flag_close = true;
					                        } else if($restaurantdetails->open == 0) {
					                            $timingstatus =  Lang::get('home.Today').' '.Lang::get('home.Closed').'.';
					                         	$flag_close = true;    
					                        } else if($currenttime>$opentime && $currenttime < $closetime) {
					                            $timingstatus =  Lang::get('home.Open.');
					                        } else {
					                            $timingstatus =  Lang::get('home.Closed').'.';
					                         	$flag_close = true;
					                        }
				                        ?>
				                        <li>
				                        	<div class="restro-time">
					                        	@if($timingstatus == 'Open.')
					                                <i class="flaticon-time green-watch"></i>
					                              @else
					                                <i class="flaticon-time red-watch"></i>
					                              @endif
												{{-- */ echo $timingstatus ;/* --}}
				                        	</div>
				                    	</li>
				                        
				                        <li class="clearfix">
				                            <div class="rating-star-cont">
				                                <span class="rating-static rating-{{$restaurantdetails->rating*10}}"></span>
				                            </div>
				                            <span class="rating-count">{{$restaurantdetails->rating}}</span>
				                            <span class="rating-text">{{Lang::get('home.Rating')}}</span>
				                        </li>
				                    </ul>
				                </div>
				            </div>

        				</div><!--/single restro-->


		<div class="sidebar-data">

			<div class="row">
				<div class="col-md-8">
					<div class="cart-summry-data">	
						@if(count($cart->getData()))									
							@foreach($cart->getData() as $key => $product)
								@if(in_array($key, array('other')))
									<?php $otherdetails = $product; ?>
								@else

									{{-- */ $productitemscount = App\Product::productOptions($product['prodid']);  /* --}}
									<!-- {{$productitemscount}} -->

									<div class="summry-prod-single menu-item-data">
										<!--div class="p-title">
											<a href="#" title="">{{$product['name']}}</a>
										</div-->
										<input name="checkoutproductid" type="hidden" value="{{$product['prodid']}}" class="checkoutproductid">

										<div class="row">										
											<div class="col-md-7">
												<div class="p-title">
													<a href="#" title="">{{$product['name']}}</a>
												</div>												
											</div>

											<div class="col-md-5">
												<div class="row">
													<div class="col-sm-6">
													  	<span rel="checkout" field="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['prodid']}}" class="@if($productitemscount>0){{'hasaddons'}}@else{{'noaddons'}}@endif qtyminus qty-sub" @if($productitemscount>0) data-toggle="modal" data-target="#ProdAddon{{$product['prodid']}}" @endif >
													  		<i class="fa fa-minus-circle"></i>
													  	</span>
													  	<span class="qty-input">
													  		<input name="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['prodid']}}" type="text" value="{{$product['quantity']}}" readonly>
													  	</span>
													  	<span rel="checkout" field="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['prodid']}}" class="qtyplus qty-add @if($productitemscount>0){{'hasaddons'}}@else{{'noaddons'}}@endif" @if($productitemscount>0) data-toggle="modal" data-target="#ProdAddon{{$product['prodid']}}" @endif >
													  		<i class="fa fa-plus-circle"></i>
													  	</span>

													   	<!-- Bhuvnesh ProductAddons popup start-->                                   
													   	<div class="modal fade" id="ProdAddon{{$product['prodid']}}" role="dialog">
															<div class="modal-dialog">    
																<!-- Modal content-->
																<div class="modal-content">
																	<div class="modal-header">
																		<button type="button" class="close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title"> {{$product['name']}} </h4>
																	</div>
																	<div class="modal-body">             		                                          
																		{{-- */ 
																			$productaddons = App\Product::productAddons($product['prodid']);
																		/* --}}

																		<div class="content-block choices-toppings__container">

																		{!! Form::open(array('class' =>'orderitemaddons')) !!}

																			<input type="hidden" name="productid[]" value="{{$product['prodid']}}">          
																			<input type="hidden" name="itemname" value="{{$product['name']}}">        
																			<input type="hidden" name="itemprice" value="{{$product['cost']}}">

																			<div class="choices-toppings__elements__wrapper choices-toppings__product__information toggle-elements-container choices-toppings__product__information--no-image">

																				<table class="table table-cart">
																				<table class="table-cart">
																				   	<tbody>
																					  	<tr>
																						 	<td class="choices-toppings__product__information__text">
																								<label for="cart_product_skeleton_quantity" class="control-label required">
																									Quantity
																								</label>
																						 	</td>
																						 	<td class="choices-toppings__product__information__quantity">           
																								<div class="choices-toppings__product__information__text__description">
																								</div>
																								<input type="number" min="0" value="<?=isset($cart->getData()[$product['prodid']]['quantity'])?$cart->getData()[$product['prodid']]['quantity']:1 ?>"  class="form-control number" name="cart_product_quantity" >
																						 	</td>
																					  	</tr>
																				   	</tbody>
																				</table>
																			</div>

																			@foreach($productaddons as $key => $productaddon)
																				<div class="choices-toppings__wrapper toggle-elements-container">    
																					
																					<div class="choices-toppings__elements__wrapper choices-toppings-checkbox-wrapper">

																					  	<div class="choices-toppings__title__container">

																						 	<h4 class="choices-toppings__title">
																							<?php 
																								echo ($productaddon[0]['required']=="Y")?$key."*":$key;
																								$key = preg_replace('/\s+/', '', $key);?>
																								<span hidden=true id="{{$key}}{{$productaddon[0]['required']}}" style='color:red;font-size: 14px;'>
																									{{Lang::get('validation.option_selection_reqired')}}
																							 	</span>
																							 </h4> 
																					  	</div>

																						<div class="row">     
																						  	@foreach($productaddon as $itemdetail)  
																							  	<div class="col-sm-6">   
																									<div class="{{$itemdetail['type']}}">
																										<label>
																									   	<input 
																										   	<?php 
																										 	if($itemdetail['type']=='checkbox'){ echo "name=itemaddond[]"; } else { echo "name='itemaddond[]".$key."'"; } 
																										   	if(isset($cart->getData()[$product['prodid']]['itemaddond']) && in_array($itemdetail['option_item_id'],$cart->getData()[$product['prodid']]['itemaddond']))
																												  echo ' checked=true';
																										    ?>
																										   	type="{{$itemdetail['type']}}" value="{{$itemdetail['option_item_id']}}"
																										   	class="{{$productaddon[0]['required']}} {{$key}}{{$productaddon[0]['required']}}" rel="{{$key}}"
																									   	>
																									   	{{$itemdetail['item_name']}} ({{format($itemdetail['price'])}})
																										</label>
																									</div>          
																							 	</div>
																						  	@endforeach       
																						</div>                
																					</div>
																				</div> 
																			@endforeach

																			<div class="choices-toppings__submit">
																				<button rel="checkout" class="btn btn-primary btn-lg choices-toppings__button--submit" name="cart_product_skeleton[submit]" id="cart_product_skeleton_submit" type="button">Submit
																				</button>
																			</div>                                                            
																		{!! Form::close() !!}
																		</div>                                        
																																			
																	</div>                                    
																</div>      
															</div>
														</div>
													   <!-- Bhuvnesh ProductAddons popup end -->	
													</div>

													<div class="col-sm-6">
														<h5 class="p-price">{{format($product['totalCost'])}}</h5>
													</div>

												</div>
											</div>
										</div>
									</div>
								@endif
							@endforeach																		
						@endif

						<?php $subtotal = $cart->getSubtotal(); $total = $cart->getTotal(); ?>

						<div class="cart-total-summry">
							<div class="row">
								<div class="col-md-5 col-md-offset-7">
									<div class="row">
										<div class="col-sm-9">
											<div class="cart-total-title text-right">Subtotal:</div>
										</div>
										<div class="col-sm-3">
											<div class="cart-total-title text-center">{{format($subtotal)}}</div>
										</div>
									</div>

									<!-- start commented in old code -->
									<div class="row">
										<div class="col-sm-9">
											<div class="cart-total-title text-right">CGST {{ $cart->getCGST() }} %:</div>
										</div>
										<div class="col-sm-3">
											<div class="cart-total-title text-center">{{ format($cart->getCGST()/100 * $subtotal) }}</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-9">
											<div class="cart-total-title text-right">SGST {{ $cart->getSGST() }} %:</div>
										</div>
										<div class="col-sm-3">
											<div class="cart-total-title text-center">{{ format($cart->getSGST()/100 * $subtotal) }}</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-9">
											<div class="cart-total-title text-right">Packaging Fees:</div>
										</div>
										<div class="col-sm-3">
											<div class="cart-total-title text-center">{{ format($cart->getPackagingFees()) }}</div>
										</div>
									</div>
									<!-- end commented in old code -->

									<div class="row" id="delivery" style="display: none;">
										<div class="col-sm-9">
											<div class="cart-total-title text-right">
												{{Lang::get('proceedtocheckout.Delivery.Charges')}}:
											</div>
										</div>
										<div class="col-sm-3">
											<div id="deliveryChargeVal" data-total="{{ $cart->deliveryCharges() }}" class="cart-total-title text-center">{{format($cart->deliveryCharges())}}</div>
										</div>
									</div>

									@if(!empty($cart->getOtherData()['coupan']))
										<div class="row green-color">
											<div class="col-sm-9">
												<div class="cart-total-title text-right">Coupan Discount:</div>
											</div>
											<div class="col-sm-3">
												<div class="cart-total-title text-center">{{format($cart->getcoupandiscountvalue())}}</div>
											</div>
										</div>
									@endif

									<div class="row">
										<div class="col-sm-9">
											<div class="cart-total-title text-right">Total:</div>
										</div>
										<div class="col-sm-3">
											<div class="cart-total-title text-center" id="total" data-total="{{ $total }}">{{format( $total )}}</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="redeem-coupan">
							<div class="row">
								<?php 
									// prd($cart->getOtherData());
								?>
								<!-- <h5>{{Lang::get('proceedtocheckout.Enter.voucher.code.here')}}</h5> -->
								@if(empty($cart->getOtherData()['coupan']))
									{!! Form::open(array('url' => 'checkcoupanvalid')) !!}
										<div class="col-md-10">
											<h5>{{Lang::get('proceedtocheckout.Enter.voucher.code.here')}}</h5>
											<div class="form-group">
												<input type="text" name="coupancode" value="" placeholder="{{Lang::get('proceedtocheckout.Enter.voucher.code')}}" class="form-control icon-field">
												<i class="flaticon-interface-3 text-danger" id='coupanmessage'></i>
												<button type="submit" id="checkcoupanvalid" class="coupon-submit btn btn-primary btn-lg sbmt-btn-abs">Submit</button>
											</div>
										</div>
									{!! Form::close() !!}
								@else
									<div class="col-md-10">
										<h5>Enter voucher code here</h5>
										<div class="form-group">
											<input type="text" readonly name="coupancode" value="" placeholder="Enter voucher code" class="form-control icon-field">
											<i class="flaticon-interface-3" id='coupanmessage'></i>
											<button type="submit" disabled="true" class="btn btn-primary btn-lg sbmt-btn-abs">{{Lang::get('proceedtocheckout.Submit')}}</button>
										</div>
									</div>
								@endif
							</div>
						</div>

						<div class="g-total">
							<div class="row">
								<div class="col-md-5 col-md-offset-7">
									<h4>{{Lang::get('proceedtocheckout.Total')}}:  
									<span id='net'>
										<?php echo format( $total ); ?>
									</span>
									</h4>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="col-md-4">

					<div class="sidebar-cart">
						<div class="cart-heading">
							<!-- <i class="flaticon-shapes red-color"></i>  -->
							{{Lang::get('proceedtocheckout.Delivery.Option')}}
						</div>

						{!! Form::open(array('url' => 'createorder', 'id' => 'checkoutForm')) !!}
							<input type="hidden" value="{{$cart->getcoupanid()}}" name="coupon_id" id="coupon_id" />
							<div class="side-widget">
								<div class="side-widget-data">
									<div class="radio-cont">
										<input type="hidden" value="{{$restaurantdetails->restaurant_id}}" name="restaurant_id">
										<input type="radio" value="delivery" name="order_type" id="radio1" class="css-checkbox"/>
										<label for="radio1" class="css-label radGroup1">{{Lang::get('proceedtocheckout.Delivery')}}</label>
									</div>
									<div class="radio-cont">
										<input type="radio" value="pickup" name="order_type" id="radio2" class="css-checkbox"/>
										<label for="radio2" class="css-label radGroup1">{{Lang::get('proceedtocheckout.Pickup')}}</label>
										<span class="help-text">{{Lang::get('proceedtocheckout.you.will.pick.up.the.order.yourself.at.restaurant')}}</span>
									</div>
								</div>
							</div>

							<div class="side-widget">
								<div class="side-widget-data">
									<div class="radio-cont delivery-radio-option">
										<input type="radio" value="soon" name="asap" id="radio3" class="css-checkbox" checked="checked"/>
										<label for="radio3" class="css-label radGroup1">{{Lang::get('proceedtocheckout.as.soon.as.possible')}}</label>
									</div>
									<div class="radio-cont delivery-radio-option">
										<input type="radio" value="later" name="asap" id="radio4" class="css-checkbox later"/>
										<label for="radio4" class="css-label radGroup1">{{Lang::get('proceedtocheckout.Later')}}</label>
									</div>
									<div class="form-group later">
										<input name="date" type='text' class="form-control icon-field datepicker" placeholder="Select Date" />
				        				<i class="flaticon-technology-2"></i>
				        			</div>
						        	<div class="form-group later">
										<input name="time" type='text' class="form-control icon-field timepicker" placeholder="Select Time" />
					        			<i class="flaticon-clock-1"></i>
						        	</div>
								</div>
							</div>

							<div class="side-widget">
								<div class="side-widget-data">
									<p class="small-text">({{Lang::get('proceedtocheckout.if.you.want.to.add.some.comment.e.g.delivery.instruction.this.is.right.place')}})</p>
									<div class="form-group">
										<textarea type="text" name="remark" value="" placeholder="{{Lang::get('proceedtocheckout.add.a.message.to.your.order')}}" class="form-control icon-field">
										</textarea>
										<i class="flaticon-letter"></i>
									</div>
								</div>
							</div>

							<!--div class="checkout-btn">Proceed to Checkout</div-->
							<input type ="submit" class="checkout-btn" value="{{Lang::get('proceedtocheckout.Proceed.to.Checkout')}}">
						{!! Form::close() !!}
					</div>
							
				</div>
			</div>
		</div>
	    </div>

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
								<table class="table">
									<tbody>
										<tr>
											<td style="width: 25%;">Full Name<span>:</span></td>
											<td> @if($user) {{$user->first_name.' '.$user->last_name }} @endif </td>
										</tr>
										<tr>
											<td>Email<span>:</span></td>
											<td>@if($user) {{$user->email }} @endif</td>
										</tr>
										<tr>
											<td>Mobile<span>:</span></td>
											<td>+@if($user) {{$user->countrycode.'-' }} @endif @if($user) {{$user->contact_number }} @endif</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="icon-heading-data">
								<i class="flaticon-coins-1"></i>
								Choose How To Pay
							</div>

							<div class="payment-box">
								<div class="payment-tab">
									<div class="row">
										<div class="col-md-5">
											<!-- Nav tabs -->
										  	<ul class="nav nav-tabs" role="tablist">
										    	<li role="presentation" class="active"><a href="#cod" aria-controls="cod" role="tab" data-toggle="tab"><i class="flaticon-icon-1203"></i>Cash on Delivery <span class="flaticon-arrows-1"></span></a></li>
										  	</ul>
										</div>
										<div class="col-md-7">
											<!-- Tab panes -->
										  <div class="tab-content">
										    <div role="tabpanel" class="tab-pane active" id="cod">
												<div class="form-group captcha-field">
													<input type="text" name="" value="" class="form-control" placeholder="Enter Captcha">
													<button type="button" class="btn btn-primary btn-lg sbmt-btn-abs">Confirm Order</button>
												</div>
										    </div>
										  </div>
										</div>
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
											<span class="restro-logo" style="background: url('{{url('images')}}/<?php echo isset($restaurantdetails->image->location) ? $restaurantdetails->image->location : ''; ?>')"></span>
											Your Order
										</div>
									</div>
								</div>
							</div>
							
							<div class="or-products">
								@if(count($cart->getData()))
									@foreach($cart->getData() as $data)
										@if(in_array($key, array('other')))
											<?php 	$otherdetails= $data ?>
										@else
											<div class="row">
												<div class="col-xs-9">
													<div class="single">
														<span>{{ $data['quantity'] }}x</span>
														{{$data['name']}}
													</div>
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
								{{Lang::get('proceedtocheckout.Subtotal')}}: <span>{{format($cart->getSubtotal())}}</span>
							</div>
							<div class="total">Total: {{format($cart->getTotal('pickup'))}}</div>
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

<script type="text/javascript">

	var deliveryCharge = "<?php echo $cart->deliveryCharges() ?>";
	deliveryCharge = deliveryCharge ? deliveryCharge : 0;
	
	jQuery(function($) {
		$( document ).on('click', 'input[name="order_type"]', function(){
			if( $(this).val() == 'delivery' ) {
				$('#delivery').show();
				deliveryCharge = $('#deliveryChargeVal').data('total');
				var total = parseFloat($('#total').data('total')) + parseFloat(deliveryCharge);
				$('#total, #net').html( '$' + total );
			} else {
				$('#delivery').hide();
				$('#total, #net').html( '$' + $('#total').data('total') );
			}
		});
		
		// Check if order type is selected
		$('#checkoutForm').submit(function(){
			if( !$('input[name="order_type"]:checked').length ) {
				alert('Select delivery option to proceed.');
				return false;
			}
			if( $('input[name="asap"]:checked').val() == 'later' )
			{
				if( ! $('input[name="date"] ').val() ) {
					alert('Enter date to proceed.');
					return false;
				}
				if( ! $('input[name="time"] ').val() ) {
					alert('Enter time to proceed.');
					return false;
				}
			}
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
</script>

@endsection