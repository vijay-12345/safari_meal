<br />
<div class="form-group">
  <label class="control-label col-sm-2" for="user_id">{{trans('Name')}}*:</label>
  <div class="col-sm-10">
  	{!! Form::text('name', null, ['class' => 'form-control','placeholder'=>'Name']) !!}  
  </div>							      
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="url">{{trans('Url')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('url', null, ['class' => 'form-control','placeholder'=>'Url']) !!}  
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="sort">{{trans('Order')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('sort', null, ['class' => 'form-control','placeholder'=>'0']) !!}  
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="item_type">{{trans('Type')}}:</label>
  <div class="col-sm-10">
  	{!! Form::select('item_type',['popular_areas'=>'Popular Areas','popular_cuisines'=>'Popular Cuisines','popular_restaurants'=>'Popular Restaurants'] ,null, ['class' => 'form-control']) !!}    
    
  </div>							      
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('Language')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('lang_id',\Config::get('app.langs_options') ,\Config::get('app.locale_prefix'), ['class' => 'form-control']) !!}    
    
  </div>                    
</div>						    								    										    								   	
<div class="form-group">
  <label class="control-label col-sm-2" for="status">Status:</label>
  <div class="col-sm-10">
  	{!! Form::select('status', ['1'=>'Active','0'=>'Inactive'],null, ['class' => 'form-control']) !!}    
    
  </div>							      
</div>