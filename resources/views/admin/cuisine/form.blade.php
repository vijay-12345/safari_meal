<br />
<div class="form-group">
  <label class="control-label col-sm-2" for="user_id">{{trans('admin.name')}}:</label>
  <div class="col-sm-10">
  	{!! Form::text('name', null, ['class' => 'form-control','placeholder'=>trans('admin.name')]) !!}  
  </div>							      
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.language')}}:</label>
  <div class="col-sm-10">
  	{!! Form::select('lang_id',\Config::get('app.langs_options') ,\Config::get('app.locale_prefix'), ['class' => 'form-control']) !!}    
    
  </div>							      
</div>						    								    										    								   	
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.status')}}:</label>
  <div class="col-sm-10">
  	{!! Form::select('status', ['1'=>trans('admin.active'),'0'=>trans('admin.inactive')],null, ['class' => 'form-control']) !!}    
    
  </div>							      
</div>