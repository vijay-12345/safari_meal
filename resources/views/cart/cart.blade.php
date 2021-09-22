<?php
$subtotal = (float) $cart->getSubtotal();

if(isset($frontSide) && $frontSide==0 && isset($deliveryOption) && $deliveryOption=='delivery')
	$total = $cart->getTotal('delivery');
else $total = $cart->getTotal();
?>

@if(count($cart->getData())< 1)
	<div class="sidebar-cart">
		<div class="cart-heading">
			<i class="flaticon-commerce"></i> 
			{{ Lang::get('home.Your.Order') }}
		</div>
		<div class="empty-cart">
		    <p class="empty-title">{{ Lang::get('home.Your.havent.ordered.anythin.yet') }}</p>
		    <p class="empty-sub-title">{{ Lang::get('home.Add.menu.items.into.your.basket') }}</p>
		</div>
	</div>
@else
    <div class="sidebar-cart">
    	<div class="cart-heading">
    		<i class="flaticon-commerce"></i> {{ Lang::get('home.Your.Order') }}
    	</div>
    	<span class="estimate-time">Estimated Delivery Time : 
    		{{(isset($restaurantdetails->delivery_time) && !empty($restaurantdetails->delivery_time))?$restaurantdetails->delivery_time:Config::get('constants.delivery_time')}}min
    	</span> 
    <?php
        foreach($cart->getData() as $key => $cartValue)
        {
			if(in_array($key, array('other'))) {
				$otherdetails = $cartValue;
			} else {
				$productitemscount = App\Product::productOptions($cartValue['prodid']); 
	?>            	
				<div class="{{$cartValue['field']}} cart-item-row">
					
					<span class="item-name" rel="{{$cartValue['prodid']}}">{{$cartValue['name']}}</span>

					<span field="{{$cartValue['field']}}" class="qtyminus qty-sub">
						<i class="fa fa-minus-circle"></i>
					</span>

					<span class="qty-input"><input name="{{$cartValue['field']}}" readonly type="text" value="{{$cartValue['quantity']}}" /></span>

					<span field="{{$cartValue['field']}}" class="qtyplus qty-add">
						<i class="fa fa-plus-circle"></i>
					</span>
					<span class="item-price" rel="{{$cartValue['totalCost']}}"> {{format($cartValue['totalCost'])}}</span>
				</div>
	<?php  
			}
        }
    ?>

    <div class="total-cart">
		@if($subtotal > 0)
			<div>
				<span class="title ttl">Subtotal:</span>
				<span class="value ttl subtotal">{{ format( $subtotal ) }}</span>
			</div>

			<!-- start commented in old code -->
			<div>
				<span class="title ttl">CGST {{ $cart->getCGST() }} %:</span>
				<span class="value ttl dc_value">{{ format( $cart->getCGST()/100 * $subtotal ) }}</span>
			</div>
			
			<div>
				<span class="title ttl">SGST {{ $cart->getSGST() }} %:</span>
				<span class="value ttl dc_value">{{ format( $cart->getSGST()/100 * $subtotal ) }}</span>
			</div>
			
			<div>
				<span class="title ttl">Packaging fees:</span>
				<span class="value ttl dc_value">{{ format($cart->getPackagingFees()) }}</span>
			</div>

			@if(isset($frontSide) && $frontSide == 0)
				<div id="delivery" style="display:<?=(isset($deliveryOption) && $deliveryOption=='pickup') ? 'none' :'block'?>;">
					<span class="title ttl">Delivery Charges:</span>
					<span class="value ttl dc_value" id="deliveryCharge" data-total="{{ ($cart->deliveryCharges()) }}">
						{{ format($cart->deliveryCharges()) }}
					</span>
				</div>
			@endif
			<!-- end commented in old code -->
			
			@if(!empty($cart->getcoupandiscountvalue()) && $cart->getcoupandiscountvalue() > 0 )
				<div>
					<span class="title dev">Coupan Discount:</span>
					<span class="value dev">{{ format($cart->getcoupandiscountvalue())}}</span>
				</div>
			@endif
			
			<div class="delivery_ac">
				<span class="title ttl">Total: </span>
				<span class="value ttl total dc_total" id="total" data-total="{{ ($cart->getTotal()) }}">{{format( $total )}}</span>
			</div>
		@endif
    </div>
</div>
@endif

<script>
/*if (typeof jQuery != 'undefined') {
	$(document).ready(function(){
		var order_type = $('input[name="order_type"]:checked').val();
		showDeliveryPickup(order_type);	
		$('.page-data-cont').on('click','input[name="order_type"]',function(){
			order_type = $(this).val();
			showDeliveryPickup(order_type);
		});
		
	});
}
function showDeliveryPickup(type)
{
	if(type =='delivery')
	{
		$(".delivery_ac").show();
		var subtotal= $(".subtotal").text();
		var total = parseInt($("#check_delivery_charge").val()) + parseInt(subtotal.replace('$',''));
		$(".total").html('$'+total);
		$(".dc_value").html('$'+$("#check_delivery_charge").val())
		$(".pickup_ac").hide();
	} else {
		$(".pickup_ac").show();
		$(".delivery_ac").hide();	
	}
}*/
</script>