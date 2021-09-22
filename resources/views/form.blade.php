<?php  
use App\Restaurent;
$q = Restaurent::lang()->where('status',1);
$q = Restaurent::where('is_home_cooked',0);
if(Session::get('access.role') != 'admin'){ 
  $q = $q->whereIn('id',Session::get('access.restaurant_ids'));
}
$restaurants = $q->lists('name','id');


//prd($productOption);
?>

<br />
<!-- <div class="form-group">
  <label class="control-label col-sm-2" for="restaurant_id">{{trans('admin.restaurant')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('restaurant_id',$restaurants ,null, ['class' => 'form-control']) !!}    
    @if(Session::get('access.role') == 'admin')
      <a href="{{url($prefix.'/restaurant')}}/add">{{trans('admin.add.new.restaurant')}}</a>
    @endif
  </div>                    
</div> -->
<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('admin.name')}}:</label>
  <div class="col-sm-10">
  	{!! Form::text('name', null, ['class' => 'form-control']) !!}
  </div>							      
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="image">{{trans('admin.image')}}:</label>
  <div class="col-sm-10">

    {!! Form::file('image',null,['class'=>'form-control']) !!}<br>
      
      @if(isset($menu) and !is_null($menu->image))
        <?php 
        //prd($product->image);
        $menuImage = $menu->image->location; 
        //echo $productImage;                           
        ?>
        {!! Form::hidden('old_image',$menu->image->location) !!}  
        {!! Form::hidden('image_id',$menu->image->id) !!}                                       
        <img src="{{asset($menuImage)  }}" width="50">                                                             
      @endif                        
       
    
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