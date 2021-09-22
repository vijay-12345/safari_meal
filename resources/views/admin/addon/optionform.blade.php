<br />
{!! Form::hidden('option_group_id',$group->id) !!}
<div class="form-group">
  <label class="control-label col-sm-2" for="item_name">{{trans('admin.name')}}*:</label>
  <div class="col-sm-10">
    {!! Form::text('item_name',null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>    
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.language')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('lang_id',\Config::get('app.langs_options') ,null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>                                                                                
<div class="form-group">
  <label class="control-label col-sm-2" for="status">Status:</label>
  <div class="col-sm-10">
    {!! Form::select('status', ['1'=>trans('admin.active'),'0'=>trans('admin.inactive')],null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>						    								    										    								   	

							   		 							 									  	
								  								  						  

