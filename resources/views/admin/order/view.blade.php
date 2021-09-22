<?php
	if(empty($orderdetails)) 
		exit("Item Details Not Found");
	foreach($orderdetails as $key=>$val)
		$$key=$val;	

	$totalprice=0;
?>

@if(!empty($order))
	<div class="row">
		<div class="col-md-2">
			Order Number : 
		</div>
		<div class="col-md-4">
			<b>
				@if(empty($order['order_number']))
					N/A
				@else
					{{$order['order_number']}}
				@endif
			</b>
		</div>
		<div class="col-md-2">
			Order Time & Date:
		</div>
		<div class="col-md-4">
			<b>
				@if(empty($order['time']))
					N/A
				@else
					{{$order['time']}} 
					{{date_format(date_create($order['date']), "d-m-Y") }}
					@if($order['asap'] == 1)
						(ASAP)
					@endif
				@endif
			</b>	
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-2">
			Order Status : 
		</div>
		<div class="col-md-4">
			<b>
			@if(empty($order->status))
				N/A
			@else
				@foreach(Config::get('constants.order_status_label.admin') as $key=>$value)
					@if($key == $order->status)					
						@if($key == $order->status)
						 	@if($order->order_type !='delivery' && $order->status == 5)
						 		<b>Picked Up</b>
						 	@else
						 		<b>{{$value}}</b>
						 	@endif									 
						@endif					  
					@endif
				@endforeach
			@endif
			</b>			
		</div>
		<div class="col-md-2">
			Order Type :
		</div>
		<div class="col-md-4">
			<b>
				@if(empty($order['order_type']))
					N/A
				@else
					{{$order['order_type']}}
				@endif
		 	</b>
		</div>
	</div>

	<div class="row">
		<div class="col-md-2">
			Mobile :
		</div>
		<div class="col-md-4">
			<b>
				@if(empty($order['ship_mobile']))
					N/A
				@else
					{{$order['ship_mobile']}}
				@endif
			</b>
		</div>
		<div class="col-md-2">
			Payment Method:
		</div>
		<div class="col-md-4">
			<b>
				@if(empty($order['payment_method']))
					N/A
				@else
					{{$order['payment_method']}}
				@endif
		 	</b>
		</div>
	</div>

	<div class="row">
		<div class="col-md-2">
			Driver :
		</div>

		<div class="col-md-4">
			<b>
				@if($order['driver_id'] > 0 && isset($order->driver->first_name) )
					{{ ucfirst($order->driver->first_name).' '.ucfirst($order->driver->last_name)}}
				@else
					N/A
				@endif
			</b>	
		</div>
		<div class="col-md-2">
			Address :
		</div>
		<div class="col-md-4">
			<b>
				@if(empty($order['ship_add1']))
					N/A
				@else
					{{$order['ship_add1']}}  {{$order['ship_add2']}}  {{$order['ship_city']}}
					{{$order['ship_zip']}}  
				@endif
		 	</b>
		</div>
	</div>
@endif

@if(!empty($orderItems))
	<hr>
	<b>
		Item Details
		<div class="row">
			<div class="col-md-1">Sn.</div>
			<div class="col-md-5">Product Name</div>
			<div class="col-md-2">Unit Price</div>
			<div class="col-md-2">Product Quantity</div>
			<div class="col-md-2">Total</div>
		</div>
	</b>
	@foreach($orderItems as $key=>$item)
		<div class="row">
			<div class="col-md-1">{{$key+1}}</div>
			<div class="col-md-5">{{$item['product_name']}}</div>
			<div class="col-md-2">{{format($item['product_unit_price'])}}</div>
			<div class="col-md-2">{{$item['product_quantity']}}</div>
			<div class="col-md-2">{{format($item['product_unit_price']*$item['product_quantity'])}}</div>
			<?php $totalprice += $item['product_total_price']?>
		</div>

		@if(isset($item['addons_list']) && !empty($item['addons_list']))
			<b><i style='margin-left:25px'>Addons List Under {{$item['product_name']}}</i></b>
			<?php					
				$addons_list= $item['addons_list'];					
			?>
			<b>
				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-5">Name</div>
					<div class="col-md-2">Unit Price</div>
					<div class="col-md-2">Quantity</div>
					<div class="col-md-2">Total</div>
				</div>
			</b>
			<?php $addonstotal = 0; ?>

			@foreach($addons_list as $key1=>$item1)
				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-5">{{isset($item1->item_name)?$item1->item_name:'N/A'}}</div>
					<div class="col-md-2">{{isset($item1->price)?format($item1->price):'N/A'}}</div>
					<div class="col-md-2">{{isset($item['product_quantity'])?$item['product_quantity']:'N/A'}}</div>
					<div class="col-md-2">{{format((isset($item1->price)?$item1->price:0)*(isset($item['product_quantity'])?$item['product_quantity']:0))}}</div>
					<?php $addonstotal += (isset($item1->price)?$item1->price:0)*(isset($item['product_quantity'])?$item['product_quantity']:0); ?>
				</div>
			@endforeach

			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-5"></div>
				<div class="col-md-2"></div>
				<div class="col-md-2"><b>Addons Amount =</b></div>
				<div class="col-md-2"><b>{{format($addonstotal)}}</b></div>
			</div>				
		@endif
	@endforeach

	<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-5"></div>
		<div class="col-md-2"></div>
		<div class="col-md-2"><b>Total Amount =</b></div>
		<div class="col-md-2"><b>{{format($order['amount'])}} ONLY</b></div>
	</div>
@endif