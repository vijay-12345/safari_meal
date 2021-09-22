<?php 
	$prefix = Session::get('access.role');
	// prd($order);
?>

<div class="panel-heading">{{trans('admin.search.results')}}:</div>

<div class="panel-body">
	<div class="panel panel-default" >
		<div class="panel-body">

			@if(isset($action) && $action =='orderview')
				<div>
					<div class="row">
						<div class="col-sm-6">
							<h3 class="no-margin">{{ $customer ? ucfirst($customer->first_name).' '.ucfirst($customer->last_name) : ''}}</h3>
					    	<p><strong>{{ $customer ? $customer->contact_number : '' }}</strong></p>
						</div>		
					</div>	
				</div>		
				<div class="row">					
					<div class="col-md-4">
						<div class="panel panel-default">
							<div class="panel-body">
								<div>						
									<label>{{trans('admin.deliver.to.this.address')}}</label>
								</div>
								<p>
									@if($order->ship_add1 !='')
									<b>Address1-</b><?=$order->ship_add1.",<br/> " ?>
									@endif
									@if($order->ship_add2 !='')
									<b>Address2-</b><?=$order->ship_add2.",<br/> "  ?>
									@endif	
									@if($order->ship_city !='')
									<b>City-</b><?=$order->ship_city.", " ?>
									@endif
									@if($order->ship_zip !='')
									<b>Zipcode-</b><?=$order->ship_zip.", " ?>
									@endif
									@if($order->ship_mobile !='')
									<b>Mobile No.-</b><?=$order->ship_mobile ?>
									@endif																									
								</p>
							</div>
						</div>
					</div>
				</div>
			@else
				@if(count($customer) >0)
					<div>
						<div class="row">
							<div class="col-sm-6">
							<h3 class="no-margin">{{ucfirst($customer->first_name).' '.ucfirst($customer->last_name)}}</h3>
						    <p><strong>{{$customer->contact_number}}</strong></p>
							</div>
							<div class="col-sm-6">
								<div class="text-right">
									<a target="_blank" href='{{url($prefix.'/address-add/')}}<?="/".$customer->id?>' ><input type='button' class="btn btn-primary" value='{{trans('admin.add.new.address')}}' /></a> 
								</div>
							</div>
						</div>
						<!--Add Address Popup -->
						<div class="modal fade" id="AddAddress" tabindex="-1" role="dialog" aria-labelledby="AddAddressLabel">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        <h4 class="modal-title" id="AddAddressLabel">{{trans('admin.add.new.address')}}</h4>
						      </div>
						      <div class="modal-body">
						        <div class="row">
						        	<div class="col-md-6">
						        		<div class="form-group">
						        			<input type="text" name="" value="" class="form-control" placeholder="{{trans('admin.enter.street.name')}}">
						        		</div>
						        	</div>
						        	<div class="col-md-6">
						        		<div class="form-group">
						        			<select class="form-control">
						        				<option>{{trans('admin.select.country')}}</option>
						        			</select>
						        		</div>
						        	</div>
						        </div>
						        <div class="row">
						        	<div class="col-md-6">
						        		<div class="form-group">
						        			<select class="form-control">
						        				<option>{{trans('admin.select.state')}}</option>
						        			</select>
						        		</div>
						        	</div>
						        	<div class="col-md-6">
						        		<div class="form-group">
						        			<select class="form-control">
						        				<option>{{trans('admin.select.city')}}</option>
						        			</select>
						        		</div>
						        	</div>
						        </div>
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('admin.cancel')}}</button>
						        <button type="button" class="btn btn-primary">{{trans('admin.submit')}}</button>
						      </div>
						    </div>
						  </div>
						</div>
					</div>
					
					@if(!is_null($customer->address))
						<div class="row">
							@foreach($customer->address as $key=>$addrObj)		
								<div class="col-md-4">
									<?php
										$checked = '';
										if(!empty($order) && ($addrObj->first_address == $order->ship_add1))
											$checked = 'checked';
									?>
									<div class="panel panel-default">
										<div class="panel-body address-box">
											<div class="radio-cont">
												<input type="radio" name="radiog_lite" id="<?= $addrObj->id ?>" value='<?=$addrObj->id ?>' class="css-checkbox form-control" <?=$checked?> />
												<label for="<?=$addrObj->id ?>" class="css-label radGroup1">{{trans('admin.deliver.to.this.address')}}</label>
											</div>
											<p>
												@if($addrObj->first_address !='')
													<?=ucfirst($addrObj->first_address).",<br/> " ?>
												@endif
												@if($addrObj->second_address !='')
													<?=ucfirst($addrObj->second_address)."<br/> "  ?>
												@endif
												@if($addrObj->zip !='')
													<?=$addrObj->zip; ?>
												@endif
											</p>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@endif

				@else
					<div class="row">
						<div class="col-md-12 text-center">
							<strong>{{trans('admin.not.found')}}</strong>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-right">
							<strong><a href="{{url('admin/customer/add')}}">{{trans('admin.add.customer')}}</a></strong>
						</div>
					</div>
				@endif

			@endif
		</div>
	</div>
</div>