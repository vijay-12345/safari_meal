@extends('admin.layout')
@section('title', 'Footer List')
@section('content')

<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
				
			</div>
			<div class="col-md-9">
				<div class="page-data-right">
					<div class="panel panel-default">
						<div class="panel-heading text-center">Footer Manager</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif			
							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => 'admin/footer-links')) !!}
							<table class="table table-responsive" cellspacing="0" width="100%">
								
								<tr>
									<td>
										<select name="paginate_limit" class="paginate_limit">							
											@foreach(Config::get('constants.paginate_limit_option') as $limitOption))
												<option value="{{$limitOption}}" @if(session('filter.paginate_limit')==$limitOption) selected @endif>{{$limitOption}}</option>
											@endforeach
										</select>
									</td>																				
									<td class="col-right">Search&nbsp;&nbsp;&nbsp;<input type="text" name="search" value="{{session('filter.search')}}" placeholder="Searach by type">&nbsp;&nbsp;&nbsp;<input type="submit" value="Search"></td>
									<td><a href="{{url('admin/footer-links')}}/add" class="btn btn-primary btn-md">+Add</a></td>
								</tr>
							</table>
							{!! Form::close() !!}
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>					           
					            
					            <th><a href="{{URL::to('/')}}/admin/footer-links?sorting={{$filter['sort']}}&amp;field=name">Name</a></th>
					            <th>Url</th>
					           	<th><a href="{{URL::to('/')}}/admin/footer-links?sorting={{$filter['sort']}}&amp;field=item_type">Type</a></th>
					           	<th><a href="{{URL::to('/')}}/admin/footer-links?sorting={{$filter['sort']}}&amp;field=sort">Order</a></th>
					            <th><a href="{{URL::to('/')}}/admin/footer-links?sorting={{$filter['sort']}}&amp;field=status">Status</a></th>
					            
					            <th>Action</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if(count($data['data']) > 0)
					    		
						    	@foreach($data['data'] as $v)
						    		
						        <tr>					        	
						            
						            <td>{{ ucfirst($v->name) }}</td>
						            <td>{{ $v->url }}</td>
						            <td>{{ ucfirst(str_replace('_',' ', $v->item_type)) }}</td>
						            <td>{{ $v->sort }}</td>
						             <td>
						             	@if($v->status == 1)
						            		Active
						            	@else
						            		Inactive
						            	@endif</td>	
						            						            							
						            <td><a href="{{url('admin/footer-links').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$v->id}}" data-controller="footer-links" style="cursor:pointer;"></span></td>
						        </tr>						        
						        
						        @endforeach
					      	@else
					      		<tr><td colspan="8" class="text-center">Record Not Found</td></tr>
					      	@endif
					        <tr>
					        	<td colspan="8">
					        		<?php echo $data['data']->appends(['sort' => $filter['paginate_sort'],'field'=>$filter['field']])->render(); ?>
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
@endsection
