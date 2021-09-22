@extends('admin.layout')
@section('title', trans('admin.dashboard'))
@section('content')
<?php 
use App\Order,App\Product;
$prefix = \Request::segment(2);
?>
<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
				
			</div>
			<div class="col-md-9">
				<div class="page-data-right">
					<div class="db-status clearfix">
						@if(Session::get('access.role') !='restaurant')
						<div class="status-box box1">
							<h3>{{$data['total_restaurant']}}</h3>
							<div class="icon-text"><span class="flaticon-cross"></span><span>{{trans('admin.restraurants')}}</span></div>
						</div>
						<div class="status-box box2">
							<h3>{{$data['total_driver']}}</h3>
							<div class="icon-text"><span class="flaticon-car-steering-wheel"></span><span>{{trans('admin.drivers')}}</span></div>
						</div>
						<div class="status-box box3">
							<h3>{{$data['total_customer']}}</h3>
							<div class="icon-text"><span class="flaticon-music-social-group"></span><span>{{trans('admin.customers')}}</span></div>
						</div>
						@endif
						<div class="status-box box4">
							<h3>{{$data['order_pending']}}</h3>
							<div class="icon-text"><span class="flaticon-serving-lunch"></span><span>{{trans('admin.pending.orders')}}</span></div>
						</div>
					</div>

					<div class="charts-cont">
						<div class="row">
							<div class="col-md-6">
								<div class="chart-widget">
									<!-- <div class="title">Quick statistics</div> -->
									<div class="chart-holder">
										<canvas id="chartfirst"  height="200" width="400"/>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="chart-widget">
									<!-- <div class="title">Member Statistics</div> -->
									<div class="chart-holder">
										<canvas id="chartsec" width="200" height="200"/>
									</div>
								</div>
							</div>
						</div>
					</div><!--Charts Cont-->

					<div class="panel panel-default">
						<div class="panel-heading">{{trans('admin.orders')}}</div>
						<div class="panel-body">
							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => $prefix.'/dashboard')) !!}
							<table class="table table-responsive" cellspacing="0" width="100%">
								
								<tr>
									<td>
										<select name="paginate_limit" class="paginate_limit">							
											@foreach(Config::get('constants.paginate_limit_option') as $limitOption))
												<option value="{{$limitOption}}" @if(session('filter.paginate_limit')==$limitOption) selected @endif>{{$limitOption}}</option>
											@endforeach
										</select>
									</td>
									@if(Session::get('access.role') !='restaurant')
									<td>										
										<select name="restaurant" class="restaurant">
											<option value="" @if(Session::has('filter.restaurant') && session('filter.restaurant') =='') selected @endif>All Restaurants</option>
											@foreach($data['restaurant_data'] as $key=>$restaurant)
												<option value="{{$restaurant->id}}" @if(Session::has('filter.restaurant') && session('filter.restaurant') ==$restaurant->id) selected @endif>{{$restaurant->name}}</option>
											@endforeach
										</select>															
									</td>
									@endif
									<td class="col-right">{{trans('admin.search.by.orderid')}}&nbsp;&nbsp;&nbsp;<input type="text" name="search" value="{{session('filter.search')}}">&nbsp;&nbsp;&nbsp;<input type="submit" value="Search"></td>
								</tr>
							</table>
							{!! Form::close() !!}
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
							    <thead>
							        <tr>					           
							            <th><a href="{{URL::to('/')}}/{{$prefix}}/dashboard?sorting={{$filter['sort']}}&amp;field=order.order_number">{{trans('admin.order.id')}}</a></th>
							            <th><a href="{{URL::to('/')}}/{{$prefix}}/dashboard?sorting={{$filter['sort']}}&amp;field=order.date">{{trans('admin.date')}}</a></th>
							            <th><a href="{{URL::to('/')}}/{{$prefix}}/dashboard?sorting={{$filter['sort']}}&amp;field=restaurant.name">{{trans('admin.restaurants')}}</a></th>
							            <th><a href="{{URL::to('/')}}/{{$prefix}}/dashboard?sorting={{$filter['sort']}}&amp;field=order.remark">{{trans('admin.client.remark')}}</a></th>
							            <th><a href="{{URL::to('/')}}/{{$prefix}}/dashboard?sorting={{$filter['sort']}}&amp;field=order.amount">{{trans('admin.amount')}}</a></th>
							            <th><a href="{{URL::to('/')}}/{{$prefix}}/dashboard?sorting={{$filter['sort']}}&amp;field=order.status">{{trans('admin.order.status')}}</a></th>
							            <th><a href="{{URL::to('/')}}/{{$prefix}}/dashboard?sorting={{$filter['sort']}}&amp;field=driver.first_name">{{trans('admin.driver.status')}}</a></th>
							            <th>{{trans('admin.action')}}</th>
							        </tr>
							    </thead>
							    <tbody>
							    	@if(count($data['orders_data']) > 0)
							    		<?php $i = 0; ?>
								    	@foreach($data['orders_data'] as $order)
								    		<?php $i++; ?>
								        <tr id="list_data{{$order->id}}" @if($i%2==0) style="background-color: #f9f9f9;" @else style="background-color: #FFFFFF;" @endif>					        	
								            <td>{{ $order->order_number }}</td>
								            <td>
								            	@if($order->asap == 1)
								            		{{date('d M Y',strtotime($order->date))}}(ASAP)
								            	@else
								            		{{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}}
								            	@endif								            	
								            </td>
								            <td>{{ $order->restaurant_name }}</td>
								            <td>{{ $order->remark }}</td>
								            <td>${{ $order->amount }}</td>					            
								            <td class="edit-action" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;">
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
								            <td class="edit-driver-status" data-id="{{$order->id}}" data-controller="dashboard">
								            	@if($order->order_type !='delivrey')
								            		{{trans('admin.order.type')}} : {{trans('admin.pick.up')}}
								            	@else
								            		{{trans('admin.assigned.to')}} {{ $order->driver_name }}
								            	@endif
								            </td>
								            <td><span class="glyphicon glyphicon-plus-sign view-action" data-id="{{$order->id}}" style="cursor:pointer;"></span>&nbsp;&nbsp;<a href="{{url($prefix.'/order')}}/edit/{{$order->id}}" class="glyphicon glyphicon-pencil"></a></span>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$order->id}}" data-controller="dashboard" style="cursor:pointer;"></span></td>
								        
								        </tr>						        
								        <tr style="display:none;" id="view_data{{$order->id}}">
								        	<td colspan="8">
								        		<table class="table-responsive" cellspacing="0" width="100%">
								        			<tr>
								        				<td><strong>{{trans('admin.order.number')}} :</strong> {{ $order->order_number }}</td>						        				
								        				<td><strong>{{trans('admin.date')}} :</strong>
								        				@if($order->asap == 1)
										            		{{date('d M Y',strtotime($order->date))}}(ASAP)
										            	@else
										            		{{date('d M Y',strtotime($order->date))}} {{date('g:i a',strtotime($order->time))}}
										            	@endif
										            	</td>
								         			</tr>
								        			<tr>	
								         				<td>
								         					<strong>{{trans('admin.order.type')}} :</strong>{{ $order->order_type }}
								         				</td>
								         				<td>

								         				<?php 								         				
								         				$products = Order::select('id')->find($order->id)->items;
								         				//pr($products);
								         				foreach($products as $key=>$product){
								         					echo "<br><strong>".trans('admin.product').($key+1).":</strong>".$product->item_name;
								         					echo ", <strong>".trans('admin.unit.price')." :</strong>$".$product->item_unit_price;
								         					echo ", <strong>".trans('admin.quantity'). " :</strong>".$product->item_quantity;
								         					echo ", <strong>".trans('admin.total.cost').":</strong>$".$product->item_total_price;							         			
								         					
								         					
								         				}
								         				?></td>						        				
								        									        			       			
								        			</tr>	         			
								         			<tr>	
								         				<td><strong>{{trans('admin.restaurant.name')}} :</strong> {{ $order->restaurant_name }}</td>						        				
								        				<td><strong>{{trans('admin.remark')}} :</strong> {{ $order->remark }}</td>
								        			       			
								        			</tr>
								        			<tr>	
								         				<td><strong>{{trans('admin.amount')}} :</strong>$ {{ $order->amount }}</td>						        				
								        				<td><strong>{{trans('admin.order.status')}}  :</strong> 								
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
								         				<td><strong>{{trans('admin.driver.status')}} :</strong> Assigned to {{ $order->driver_name }}</td>						        				
								        				<td><strong>{{trans('admin.customer')}} :</strong> {{ucfirst($order->customer_first_name).' '.ucfirst($order->customer_last_name)}} 								
										
														</td>						        			       			
								        			</tr>			

								        		</table>
								        	</td>	
								        </tr>
								    	
								        @endforeach
							      	@else
							      		<tr><td colspan="8" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
							      	@endif
							        <tr>
							        	<td colspan="8">
							        		<?php echo $data['orders_data']->appends(['sort' => $filter['paginate_sort'],'field'=>$filter['field']])->render(); ?>
							        	</td>
							        </tr>
							        
							    </tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<?php $pieData = getPieChartData($data['ordergroupbystatus']); ?>
<script type="text/javascript" src="{{ asset('js/Chart.min.js') }}"></script>

<script type="text/javascript">
	/**Chart first**/ 
	var monthlyChartData = <?php echo getMonthlyChartData($data['orderamount_monthly_by']); ?>;
	var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
	//alert(randomScalingFactor());

	var lineChartData = {
		labels : ["Jan.","Feb.","Mar.","Apr.","May","June","July","Aug.","Sept.","Oct.","Nov.","Dec."],
		datasets : [
			{
				label: "My First dataset",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "rgba(220,220,220,1)",
				pointColor : "rgba(220,220,220,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(220,220,220,1)",
				data:monthlyChartData
				//data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
			},
/*			{
				label: "My Second dataset",
				fillColor : "rgba(151,187,205,0.2)",
				strokeColor : "rgba(151,187,205,1)",
				pointColor : "rgba(151,187,205,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(151,187,205,1)",
				data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
			}*/
		]

	}
	 
	//Chart second  
	var pieData = <?php echo $pieData; ?>;


	window.onload = function(){

	var ctx = document.getElementById("chartsec").getContext("2d");
	window.myPie = new Chart(ctx).Pie(pieData);


	var ctxfirst = document.getElementById("chartfirst").getContext("2d");
		window.myLine = new Chart(ctxfirst).Line(lineChartData, {
			responsive: true
		});
	
};
</script> 
@endsection
