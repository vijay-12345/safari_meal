<?php 
	use App\Order; 
?>

@if($type == 'edit')
	
	<td colspan="9">
		{!! Form::open(array('role' => 'form','class'=>'filter-form','id'=>'editform'.$order->id)) !!}	
			{!! Form::hidden('id', $order->id) !!}		
			<table class="table table-responsive" cellspacing="0" border="0" cellpadding="0" width="100%">
				<tr>
					<td>{{$order->order_number}}</td>
					<td>
		                {{ __dateToTimezone('', $order->created_at,'d M Y') }}
		                {{ __dateToTimezone('', $order->created_at,'g:i a') }}
		                <!-- {{date('d M Y',strtotime($order->created_at))}} {{date('g:i a',strtotime($order->created_at))}} -->
		            </td>
		            <td>
		            	@if($order->asap == 1)
		                    {{ __dateToTimezone('', $order->date,'d M Y') }} (ASAP)
		            		<!-- {{date('d M Y',strtotime($order->date))}}(ASAP) -->
		            	@else
		                    {{ __dateToTimezone('', $order->date,'d M Y') }} 
		                    {{ __dateToTimezone('', $order->time,'g:i a') }}
		            		<!-- {{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}} -->
		            	@endif
		            </td>
					<td>{{ $order->restaurant_name }}</td>
					<td>{{ $order->remark }}</td>
					<td>$ {{ $order->amount }}</td>

					<td>
						<select name="status" class="form-control">
							@foreach(orderTypeLabel($order->order_type) as $key=>$value)
								<?php
									if(($key == 0 || $key == 1) && $order->status > 1)
								 		continue;
								 	else if( ($key == 7) && ($order->status >= 2 && $order->status <= 6))
								 		continue;
								 	else if( ($key == 2) && ($order->status > 2 && $order->status <= 6))
								 		continue;
								 	else if( ($key == 3) && ($order->status > 3 && $order->status <= 6))
								 		continue;
								 	else if( ($key == 4) && ($order->status > 4 && $order->status <= 6))
								 		continue;
							 	?>
								<option value="{{$key}}" @if($key == $order->status) selected @endif>{{$value}}</option>
							@endforeach
						</select>
					</td>
					
					<td class="edit-driver-status">
			        	@if($order->order_type !='delivery')
			        		{{trans('admin.order.type')}} : {{trans('admin.pick.up')}}
			        	@else
			        		{{trans('admin.assigned.to')}} {{ $order->driver_name }}
			        	@endif

			        	@if($order->order_type =='delivery' && ($order->status < 4 && $order->status != 7))
							{!! Form::select('driver_id', array('' => '---Select Driver---') + $drivers, $order->driver_id, ['class'=>'form-control']) !!}
						@endif
					</td>

					<td>
						<span class="glyphicon glyphicon-floppy-disk update-action" data-action="update" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;" title="Save"></span>&nbsp;&nbsp;&nbsp;&nbsp;
						<span class="glyphicon glyphicon-remove-circle update-action" style="cursor:pointer;" data-id="{{$order->id}}" data-action="cancel" data-controller="dashboard" title="Cancel"></span>
					</td>
				</tr>
			</table>
		{!! Form::close() !!}
	</td>

@else

 	@if(isset($view))
 		
        <td>{{ $order->order_number }}</td>
        <td>
            {{ __dateToTimezone('', $order->created_at,'d M Y') }}
            {{ __dateToTimezone('', $order->created_at,'g:i a') }}
            <!-- {{date('d M Y',strtotime($order->created_at))}} {{date('g:i a',strtotime($order->created_at))}} -->
        </td>
        <td>
        	@if($order->asap == 1)
                {{ __dateToTimezone('', $order->date,'d M Y') }} (ASAP)
        		<!-- {{date('d M Y',strtotime($order->date))}}(ASAP) -->
        	@else
                {{ __dateToTimezone('', $order->date,'d M Y') }} 
                {{ __dateToTimezone('', $order->time,'g:i a') }}
        		<!-- {{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}} -->
        	@endif
        </td>
        <td>{{ $order->restaurant_name }}</td>
        <td>{{ $order->remark }}</td>
        <td>${{ $order->amount }}</td>

        @if($order->status == 0 || $order->status == 1)
            <td class="order-action" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;">
                <button type="button" class="btn btn-primary" value="7">Confirmed</button>
                <button type="button" class="btn btn-warning" value="6">Rejected</button>
            </td>
        @elseif($order->status == 6)
            <td class="" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;">
                @foreach(Config::get('constants.order_status_label.admin') as $key=>$value)
                    @if($key == $order->status)
                        @if($order->order_type !='delivery' && $order->status == 5)
                            <u>{{trans('admin.picked.up')}}</u>
                        @else
                            <u>{{$value}} </u>
                        @endif
                    @endif
                @endforeach
            </td>
        @else
            <td class="edit-action" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;">
                @foreach(Config::get('constants.order_status_label.admin') as $key=>$value)
                    @if($key == $order->status)
                        @if($order->order_type !='delivery' && $order->status == 5)
                            <u>{{trans('admin.picked.up')}}</u>
                        @else
                            <u>{{$value}} </u>
                        @endif
                    @endif
                @endforeach
            </td>
        @endif

        <td>
        	@if($order->order_type !='delivery')
        		{{trans('admin.order.type')}} : {{trans('admin.pick.up')}}
        	@else
        		{{trans('admin.assigned.to')}} {{ $order->driver_name }}
        	@endif
        </td>

        <td>
        	<a href="{{url($prefix.'/order')}}/edit/{{$order->id}}"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;
        	<span class="glyphicon glyphicon-remove delete-action" data-id="{{$order->id}}" data-controller="order" style="cursor:pointer;"></span>
        </td>

 	@else

		<td colspan="8">
			<table class="table-responsive" cellspacing="0" width="100%">
				<tr>
					<td><strong>{{trans('admin.order.number')}} :</strong> {{ $order->order_number }}</td>			
					<td>
						<strong>{{trans('admin.date')}} :</strong>
						{{ __dateToTimezone('', $order->date, 'd M Y') }}  
						{{ __dateToTimezone('', $order->time, 'g:i a') }}
						<!-- {{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}} -->
					</td>
	 			</tr>
    			<tr>
     				<td><strong>{{trans('admin.order.type')}} :</strong>{{ $order->order_type }}</td>
     				<td>
	     				<?php		         				
		     				$products = Order::select('id')->find($order->id)->items;
		     				foreach($products as $key=>$product) {
		     					echo "<br><strong>".trans('admin.product').($key+1).":</strong>".$product->item_name;
		     					echo ", <strong>".trans('admin.unit.price')." :</strong>$".$product->item_unit_price;
		     					echo ", <strong>".trans('admin.quantity'). " :</strong>".$product->item_quantity;
		     					echo ", <strong>".trans('admin.total.cost')." :</strong>$".$product->item_total_price;
		     				}
	     				?>
     				</td>						        				
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
							 	@if($order->order_type !='delivery' && $order->status == 5)
							 		{{trans('admin.picked.up')}}
							 	@else
							 		{{$value}} 
							 	@endif									 
							@endif									
						@endforeach
					</td>
				</tr>

				<tr>
	 				<td><strong>{{trans('admin.driver.status')}}  :</strong> 
	 					{{trans('admin.assigned.to')}} {{ $order->driver_name }}
	 				</td>			        				
					<td><strong>{{trans('admin.customer')}} :</strong> 
						{{ucfirst($order->customer_first_name).' '.ucfirst($order->customer_last_name)}} 								
					</td>
				</tr>

			</table>
		</td>

	@endif

@endif