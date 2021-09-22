<?php  
use App\City;
use App\State;
use App\Country,App\Area;
$countries = Country::lists('country_name','country_id');
$states = [];
$cities = [];
$areas = [];
$states = [];
?>
@if(!empty($userAddress))
<?php
    $country_id = $userAddress->country_id;
    $states = State::where('country_id',$country_id)->lists('state_name','state_id');  
?>
@endif
@if(!empty($userAddress))
<?php
    $state_id = $userAddress->state_id;
    $cities = City::where('state_id',$userAddress->state_id)->lists('name','id');  
?>
@endif
@if(!empty($userAddress))
<?php
    $city_id = $userAddress->city_id;
    $areas = Area::where('city_id',$userAddress->city_id)->whereNotIn('url_alias',[''])->lists('name','id');   
?>
@endif

<br /> 
<div class="form-group">
  <label class="control-label col-sm-2" for="first_address">{{trans('admin.first.address')}}*:</label>
  <div class="col-sm-10">
    {!! Form::text('first_address',null, ['class' => 'form-control']) !!}    
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="second_address">{{trans('admin.second.address')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('second_address',null, ['class' => 'form-control']) !!}    
  </div>                    
</div>       
<div class="form-group">
    <label class="control-label col-sm-2" for="name">{{trans('admin.country')}}*:</label>
    <div class="col-sm-10">                   
  {!! Form::select('country_id', $countries, null, ['class'=>'country form-control']) !!}                   
</div>
</div>
<div class="form-group">
    <label class="control-label col-sm-2" for="name">{{trans('admin.state')}}*:</label>
    <div class="col-sm-10">                 
  {!! Form::select('state_id', [null=>trans('admin.please.select')]+$states, null, ['class'=>'state form-control']) !!}                    
</div>
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="city_id">City*:</label>
  <div class="col-sm-10">
    {!! Form::select('city_id', [null=>trans('admin.please.select')]+$cities,null, ['class' => 'city form-control']) !!}    
    
  </div>                    
</div> 
<div class="form-group">
  <label class="control-label col-sm-2" for="area_id">{{trans('admin.area')}}*:</label>
  <div class="col-sm-10" id="area_box">
    {!! Form::select('area_id', [null=>trans('admin.please.select')] +$areas,null, ['class' => 'area form-control']) !!}    
   <br>
   <a href="#" class="add-area">{{trans('admin.add.new.area')}}</a>
  </div>                    
</div>
<div class="form-group">
    <label class="control-label col-sm-2" for="zip">{{trans('admin.zipcode')}}*:</label>
    <div class="col-sm-10">
      {!! Form::text('zip', null, ['class' => 'form-control']) !!}
  
    </div>
</div>
<hr>
 


					    								    										    								   	

							   		 							 									  	
								  								  						  

