<br />
<?php //echo "testing"; die;?>
<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('admin.name')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('name',null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div> 
<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('admin.type')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('type',\Config::get('constants.optiongroup.type'),null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('admin.required')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('required',\Config::get('constants.optiongroup.required'),null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>   
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('Language')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('lang_id',\Config::get('app.langs_options') ,null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>                                                                                
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.status')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('status', ['1'=>trans('admin.active'),'0'=>trans('admin.inactive')],null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>						    								    										    								   	

							   		 							 									  	
								  								  						  

