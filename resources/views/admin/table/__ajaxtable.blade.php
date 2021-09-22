<?php $prefix = \Request::segment(2); ?>
@if(count($data['data']) > 0)
	<?php $i = 0; ?>
	@foreach($data['data'] as $order)				    		
	<?php $i++; ?>
    <tr id="list_data{{$order->id}}">					        	
        <td>{{ $order->order_number }}</td>
        <td>{{date('d M Y',strtotime($order->created_at))}} {{date('g:i a',strtotime($order->created_at))}}</td>
        <td>
        	@if($order->asap == 1)
        		{{date('d M Y',strtotime($order->date))}}(ASAP)
        	@else
        		{{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}}
        	@endif
        </td>
        <td>{{ $order->restaurant_name }}</td>
        <td>{{ $order->remark }}</td>
        <td>{{ format($order->amount) }}</td>					            
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
        <td>
        	@if($order->order_type !='delivery')
        		{{trans('admin.order.type')}} : {{trans('admin.pick.up')}}
        	@else
        		{{trans('admin.assigned.to')}} {{ $order->driver_name }}
        	@endif
        	
        </td>
        <td><a href="{{url($prefix.'/order')}}/edit/{{$order->id}}"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$order->id}}" data-controller="order" style="cursor:pointer;"></span></td>
    
    </tr>		        
    
	
    @endforeach
	@else
		<tr><td colspan="8" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
	@endif
<tr>
	<td colspan="8">
		<?php echo $data['data']->appends(['sort' => $filter['paginate_sort'],'field'=>$filter['field']])->render(); ?>
	</td>
</tr>        
