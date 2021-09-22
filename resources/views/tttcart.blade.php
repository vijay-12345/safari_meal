<?php  
use App\Product;
?>
<div class="sidebar-cart">
    <div class="cart-heading"><i class="flaticon-commerce"></i> Your Order</div>
    <span class="estimate-time">Estimated Delivery Time : 45min</span> 
    <?php    
         $cart = new App\Cart();  
          $subtotal = $cart->getSubtotal();           
          $total = $cart->getTotal();           
        foreach($cart->getData() as $cartValue){         	
        	$productitemscount = Product::productOptions($cartValue['prodid']);                                                                   	
            ?>            
	            <div class="{{$cartValue['field']}} cart-item-row">
		            <span class="item-name" rel="{{$cartValue['prodid']}}" >{{$cartValue['name']}}</span>
		            <span  field= "{{$cartValue['field']}}" class="qtyminus qty-sub"><i class="fa fa-minus-circle"></i></span>
		            <span class="qty-input"><input name="{{$cartValue['field']}}" type="text" value="{{$cartValue['quantity']}}" /></span>
		            <span field= "{{$cartValue['field']}}" class="qtyplus qty-add"><i class="fa fa-plus-circle"></i></span>
		            <span class="item-price" rel="{{$cartValue['totalCost']}}"> {{format($cartValue['totalCost'])}}</span>
	            </div>
            
            <?php           
        }
       
    ?>                  
    <div class="total-cart">
        <span class="title ttl">Subtotal:</span><span class="value ttl subtotal">{{format($subtotal)}}</span>
        <span class="title dev">Delivery Charges:</span><span class="value dev">{{ format($cart->deliveryCharges()) }} </span>
        <span class="title">Total: </span><span class="value ttl total">{{format($total)}}</span>
    </div>
</div>
