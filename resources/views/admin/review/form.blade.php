<?php 
use App\Restaurent,App\User;
$q = Restaurent::lang()->where('status',1);
$q->where('is_home_cooked',0);
if(Session::get('access.role') == 'manager'){
  $q = $q->whereIn('id',Session::get('access.restaurant_ids'));
}
$restaurants = $q->lists('name','id');
$customers = User::where(['status'=>1,'role_id'=>Config::get('constants.user.customer')])->lists('email','id');
?>
<br />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<div class="form-group">
  <label class="control-label col-sm-2" for="restaurant_id">{{trans('admin.restaurant')}}*:</label>
  <div class="col-sm-10">
    {!! Form::select('restaurant_id',[''=>trans('Select Restaurant')]+$restaurants, null, ['class' => 'form-control']) !!}  
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="customer_id">{{trans('admin.customer')}}*:</label>
  <div class="col-sm-10">
    {!! Form::select('customer_id',[''=>trans('Select Customer')]+$customers, null, ['class' => 'form-control']) !!}  
  </div>                    
</div>	
<div class="form-group">
  <label class="control-label col-sm-2" for="rating">{{trans('admin.rating')}}*:</label>
  <div class="col-sm-10">
    {!! Form::select('rating',[''=>trans('admin.select.rating'),'1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5],null, ['class' => 'form-control']) !!}  
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="review">{{trans('admin.review')}}*:</label>
  <div class="col-sm-10">
    {!! Form::textarea('review',null, ['class' => 'form-control']) !!}  
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="date">{{trans('admin.date')}}*:</label>
  <div class="col-sm-3">
    {!! Form::text('date',null, ['class' => 'form-control','id'=>'datepicker']) !!}  
  </div>                    
</div> 					    								    										    								   	
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.status')}}:</label>
  <div class="col-sm-10">
  	{!! Form::select('status', ['1'=>trans('admin.active'),'0'=>trans('admin.inactive')],null, ['class' => 'form-control']) !!}    
    
  </div>							      
</div>
<script>
$(function() {
$( "#datepicker" ).datepicker({'dateFormat':'dd-mm-yy'});
});
</script>