<?php use App\Order; ?>
@if($type== 'edit')
	<td colspan="8">
		
		{!! Form::open(array('role' => 'form','class'=>'filter-form','id'=>'editform'.$order->id)) !!}	
		{!! Form::hidden('id',$order->id) !!}		
		<table class="table table-responsive" cellspacing="0" border="0" cellpadding="0" width="100%">
		<tr>
		<td>{{$order->order_number}}</td>
		<td>{{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}}</td>
		<td>{{ $order->restaurant_name }}</td>
		<td>{{ $order->remark }}</td>
		<td>${{ $order->amount }}</td>					            
		<td>
		<select name="status" class="form-control">
		@foreach(orderTypeLabel($order->order_type) as $key=>$value)
		<option value="{{$key}}" @if($key == $order->status) selected @endif>{{$value}}</option>
		@endforeach
		</select>         	
		</td>
		<td class="edit-driver-status"> 
        	@if($order->order_type !='delivrey')
        		{{trans('admin.order.type')}} : {{trans('admin.pick.up')}}
        	@else
        		{{trans('admin.assigned.to')}} {{ $order->driver_name }}
        	@endif
			<!--{!! Form::select('driver_id', $drivers, $order->driver_id, ['class'=>'form-control']) !!}-->								    
		</td>
		<td><span class="glyphicon glyphicon-floppy-disk update-action" data-action="update" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;" title="Save"></span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-remove-circle update-action" style="cursor:pointer;" data-id="{{$order->id}}" data-action="cancel" data-controller="dashboard" title="Cancel"></span></td>
		</tr>
		
		</table>
		{!! Form::close() !!}
	</td>
@else

 	@if(isset($view))
        <td>{{ $order->order_number }}</td>
        <td>{{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}}</td>
        <td>{{ $order->restaurant_name }}</td>
        <td>{{ $order->remark }}</td>
        <td>${{ $order->amount }}</td>					            
        <td class="edit-action" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;">
		@foreach(Config::get('constants.order_status_label.admin') as $key=>$value)
			@if($key == $order->status)
			 	@if($order->order_type !='delivrey' && $order->status == 5)
			 		{{trans('admin.pick.up')}}
			 	@else
			 		{{$value}} 
			 	@endif									 
			@endif									
		@endforeach						            	
    	</td>
        <td class="edit-driver-status edit-action" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;">
        	@if($order->order_type !='delivrey')
        		{{trans('admin.order.type')}} : {{trans('admin.pick.up')}}
        	@else
        		{{trans('admin.assigned.to')}} {{ $order->driver_name }}
        	@endif
        </td>
        <td><span class="glyphicon glyphicon-plus-sign view-action" data-id="{{$order->id}}" style="cursor:pointer;"></span>&nbsp;&nbsp;<span class="glyphicon glyphicon-pencil edit-action" style="cursor:pointer;" data-id="{{$order->id}}" data-controller="dashboard"></span>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$order->id}}" data-controller="no" style="cursor:pointer;"></span></td>

 	@else
		<td colspan="8">
			<table class="table-responsive" cellspacing="0" width="100%">
				<tr>
					<td><strong>{{trans('admin.order.number')}} :</strong> {{ $order->order_number }}</td>						        				
					<td><strong>{{trans('admin.date')}} :</strong> {{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}}</td>
	 			</tr>
    			<tr>	
     				<td><strong>{{trans('admin.order.type')}} :</strong>{{ $order->order_type }}</td>
     				<td>
     				<?php 								         				
     				$products = Order::select('id')->find($order->id)->items;
     				//pr($products);
     				foreach($products as $key=>$product){
     					echo "<br><strong>".trans('admin.product').($key+1).":</strong>".$product->item_name;
     					echo ", <strong>".trans('admin.unit.price')." :</strong>$".$product->item_unit_price;
     					echo ", <strong>".trans('admin.quantity'). " :</strong>".$product->item_quantity;
     					echo ", <strong>".trans('admin.total.cost')." :</strong>$".$product->item_total_price;							         			
     					
     					
     				}
     				?></td>						        				
    									        			       			
    			</tr>	 			
	 			<tr>	
	 				<td><strong>{{trans('admin.restaurant.name')}} :</strong> {{ $order->restaurant_name }}</td>						        				
					<td><strong>{{trans('admin.remark')}}  :</strong> {{ $order->remark }}</td>
				       			
				</tr>
				<tr>	
	 				<td><strong>{{trans('admin.amount')}} :</strong> {{ $order->amount }}</td>						        				
					<td><strong>{{trans('admin.order.status')}} :</strong> 								
					@foreach(Config::get('constants.order_status_label.admin') as $key=>$value)
						@if($key == $order->status)
						 	@if($order->order_type !='delivrey' && $order->status == 5)
						 		{{trans('admin.picked.up')}}
						 	@else
						 		{{$value}} 
						 	@endif									 
						@endif									
					@endforeach
					</td>						        			       			
				</tr>
				<tr>	
	 				<td><strong>{{trans('admin.driver.status')}}  :</strong> {{trans('admin.assigned.to')}} {{ $order->driver_name }}</td>						        				
					<td><strong>{{trans('admin.customer')}} :</strong> {{ucfirst($order->customer_first_name).' '.ucfirst($order->customer_last_name)}} 								

					</td>						        			       			
				</tr>			

			</table>
		</td>
	@endif   					        	


@endif
