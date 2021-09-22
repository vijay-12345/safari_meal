@extends('admin.layout')
@section('title', trans('admin.edit.order'))
@section('content')
<?php 
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
					<div class="panel panel-default">
						<div class="panel-heading text-center">{{trans('admin.edit.order')}}</div>
						<div class="panel-body">
							

							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif
							@if($errors->any())
							    <div class="alert alert-danger">
							        @foreach($errors->all() as $error)
							            <p>{{ $error }}</p>
							        @endforeach
							    </div>
							@endif			
							<?php 
							$order=$data['order'];
							?>
							 {!! Form::model($data['order'], ['method' => 'post','role' => 'form','class'=>'form-horizontal','url' => $prefix.'/order/update'.'/'.$data['order']->id]) !!}	
							  	<br />	
								<div class="form-group">
							      <label class="control-label col-sm-2" for="restaurant_id">{{trans('admin.restaurant')}}:</label>
							      <div class="col-sm-10">
							      	{!! $order->restaurant_name; !!} 
							      </div>							      
						    	</div>	
								<div class="form-group">
							      <label class="control-label col-sm-2" for="customer name">{{trans('admin.customer.name')}}:</label>
							      <div class="col-sm-10">							    	
							    		@if(count($customer) >0)
											{{ucfirst($customer->first_name).' '.ucfirst($customer->last_name)}}
							    		@else
							    			N/A
							    		@endif							    								    
							      </div>							      
						    	</div>					    						    	

						    	<div class="form-group">
							      <label class="control-label col-sm-2" for="restaurant_id">{{trans('admin.order.number')}}:</label>
							      <div class="col-sm-10">
									  {{ $order->order_number	}}
									<input type='hidden' name='order_id' value="<?=$order->id;?>" />
							      </div>							      
						    	</div>
						    	<div class="form-group">
							      <label class="control-label col-sm-2" for="restaurant_id">{{trans('admin.set.order.status')}}:</label>
							      <div class="col-sm-10">
							      	{!! Form::select('status',orderTypeLabel($order->order_type), null, ['class' => 'form-control']) !!} 
							      </div>							      
						    	</div>
						    	<div class="form-group">
								  <div class="col-md-2">
									 
							    	</div>
							      @if(!in_array($order->order_type,['is_pickup','pickup']))
							      <div class="col-md-10">
							      	<?php echo view('admin.order.ajaxcustomer',['customer'=>$customer,'order'=>$order,'action'=>'orderview'])->render();	?>
							      </div>
							      @endif							      
						    	</div>
						    	<div class="clearfix"></div>
									<div class="row">
										<div class="col-md-12">
											<div class="page-data-right">
												<div class="panel panel-default">
													<div class="panel-heading text-center">{{trans('admin.order.view')}}</div>
														<div class="panel-body">	
															 <?php echo view('admin.order.view',['orderdetails'=>$orderdetails, 'order' => $order])->render();	?>
														</div>
													</div>
												</div>
											</div>	
										</div>	
									</div>			    		
						    	</div>
						    	<div class="clearfix"></div><br>
						    	<div class="row">
						    		<div class="col-sm-12">
						    			@if(!empty($data['referer']))
						    				{!! Form::hidden('referer',$data['referer']) !!}
						    				<a href="{{url($prefix.'/'.$data['referer'])}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;
						    			@else
						    				<a href="{{url($prefix.'/order')}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;
						    			@endif						  							  						  
							  			<button type="submit" class="btn btn-primary">{{trans('admin.update')}}</button>
							  		</div>
							  	</div>
							{!! Form::close() !!}
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(".panel-heading").html();
</script>
@endsection
