<?php $prefix = \Request::segment(2); ?>

@if(count($data['data']) > 0)
	<?php $i = 0; ?>
    
	@foreach($data['data'] as $order)
        
    	<?php $i++; ?>

        <tr id="list_data{{$order->id}}">
            <td>{{ $order->order_number }}</td>
            <td>
                {{date('d M Y',strtotime($order->book_date))}}
                <!-- {{ __dateToTimezone('', $order->created_at,'g:i a') }} -->
                <!-- {{date('d M Y',strtotime($order->created_at))}} {{date('g:i a',strtotime($order->created_at))}} -->
            </td>
           <!-- <td>
            	@if($order->asap == 1)
                    {{ __dateToTimezone('', $order->date,'d M Y') }} (ASAP)
            		<!-- {{date('d M Y',strtotime($order->date))}}(ASAP) 
            	@else
                    {{ __dateToTimezone('', $order->date,'d M Y') }} 
                    {{ __dateToTimezone('', $order->time,'g:i a') }}
            		<!-- {{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}} 
            	@endif
            </td>-->
            <td> {{$order->book_time}} </td>
            <td>{{ $order->restaurant_name }}</td>
            <td>{{ $order->customer_name}}   <!-- {{$order->customer_last_name }} --></td>
             <td>{{ $order->customer_contact}}</td>
            <td>{{ $order->total_person }}</td>
            <!--<td>{{ format($order->amount) }}</td>-->

            @if($order->status == 0 || $order->status == 1)
                <td class="torder-action" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;">
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
            
            <!--<td>
            	@if($order->order_type !='delivery')
            		{{trans('admin.order.type')}} : {{trans('admin.pick.up')}}
            	@else
            		{{trans('admin.assigned.to')}} {{ $order->driver_name }}
            	@endif
            </td>

            <td>
                <a href="{{url($prefix.'/order')}}/edit/{{$order->id}}">
                    <span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;
                    <span class="glyphicon glyphicon-remove delete-action" data-id="{{$order->id}}" data-controller="order" style="cursor:pointer;">
                </span>
            </td>-->
        </tr>

    @endforeach

	@else
		<tr><td colspan="9" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
	@endif

    <tr>
    	<td colspan="9">
            {!! $data['data']->appends(Request::except('_token'))->render() !!}
    		<?php // echo $data['data']->appends(['sort' => $filter['paginate_sort'],'field'=>$filter['field']])->render(); ?>
    	</td>
    </tr>