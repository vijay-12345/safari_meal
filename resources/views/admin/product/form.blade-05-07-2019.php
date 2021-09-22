<?php  
use App\Menu;
use App\Restaurent,App\OptionGroup,App\OptionItem;
$menus = Menu::where('status',1)->lists('name','id');;
$q = Restaurent::lang()->where('status',1);
if(Session::get('access.role') != 'admin'){ 
  $q = $q->whereIn('id',Session::get('access.restaurant_ids'));
}
$restaurants = $q->lists('name','id');
$addonGroup = OptionGroup::lang()->get();

$productOption = [];
if(isset($product)){
  foreach($product->addonsOptions as $key=>$optionObj){
    $productOption[$optionObj->option_item_id] = ['price'=>$optionObj->price,'option_item_id'=>$optionObj->option_item_id];
  }
}

//prd($productOption);
?>
<br />

<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('admin.name')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('name',null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('Cost')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('cost',null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="restaurant_id">{{trans('admin.restaurant')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('restaurant_id',$restaurants ,null, ['class' => 'form-control']) !!}    
    @if(Session::get('access.role') == 'admin')
      <a href="{{url($prefix.'/restaurant')}}/add">{{trans('admin.add.new.restaurant')}}</a>
    @endif
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="menu_id">{{trans('admin.menu')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('menu_id',$menus ,null, ['class' => 'form-control']) !!}    
    @if(Session::get('access.role') == 'admin')
      <a href="{{url($prefix.'/menu')}}/add">{{trans('admin.add.new.menu')}}</a>
    @endif    
  </div>                    
</div> 
<div class="form-group">
  <label class="control-label col-sm-2" for="image">{{trans('admin.image')}}:</label>
  <div class="col-sm-10">

    {!! Form::file('image',null,['class'=>'form-control']) !!}<br>
      
      @if(isset($product) and !is_null($product->image))
        <?php 
        //prd($product->image->location);
        $productImage = $product->image->location; 
        //echo $productImage;                           
        ?>
        {!! Form::hidden('old_image',$product->image->location) !!}  
        {!! Form::hidden('image_id',$product->image->id) !!} 
        @if(!empty($productImage))                                      
          <img src="{{asset($productImage)}}" width="50"> 
        @endif                                                            
      @endif                        
       
    
  </div>                    
</div>
 	{!! Form::hidden('lang_id',\Config::get('app.locale_prefix'), ['class' => '']) !!}    
    

<div class="form-group">
  <label class="control-label col-sm-2" for="lang_id">{{trans('admin.status')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('status',['1'=>trans('admin.active'),'0'=>trans('admin.inactive')] ,null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>
<hr>

  <div class="panel-default">
      <div class="panel-heading text-center" style="border-color:#fff;">
          Extra Addon
        </div>
  </div>
<br>

@foreach($addonGroup as $key => $group)

    {{-- */
      $optionCount = OptionItem::where('option_group_id',$group->id)->count(); 
      if($optionCount == 0) continue; 
      $options = OptionItem::where('option_group_id',$group->id)->get();                                
      /* --}}
      
      <h4 style="cursor:pointer;" data-id="{{$group->id}}" class="glyphicon glyphicon-minus-sign view-action">&nbsp;{{$group->name}}</h4> 
      <div id="view_data{{$group->id}}">    
          @foreach($options as $itemdetail)  
              <div class="form-group">
                  <label class="control-label col-sm-2" for="price">                              
                  {!! Form::checkbox('addons[option][]',$itemdetail->id,isset($productOption[$itemdetail->id]['option_item_id'])?true:false,['class' => ''])  !!}
                  &nbsp;{{$itemdetail->item_name}}:</label>
                  <div class="col-sm-2">
                      {!! Form::text('addons[price]['.$itemdetail->id."]",isset($productOption[$itemdetail->id]['price'])?$productOption[$itemdetail->id]['price']:null, ['class' => 'form-control','placeholder'=>trans('admin.price')]) !!}                
                  </div>
                  <label class="col-sm-2">{{trans('admin.price')}}</label>                    
              </div>
          @endforeach 
      </div>
      <div class="clearfix"></div>

@endforeach 

<script>
  $(document).ready(function(){      
      $("input:checkbox[name^=addons]").each(function(i,v){
          if($(this).is(':checked')){
            $(this).parent().siblings('div').show();
            $(this).parent().siblings('label').show();
          }else{
            $(this).parent().siblings('div').hide();
            $(this).parent().siblings('label').hide();          
          }
      });
      $("input:checkbox[name^=addons]").click(function(){
        if($(this).is(':checked')){
          $(this).parent().siblings('div').show();
          $(this).parent().siblings('label').show();
        }else{
          $(this).parent().siblings('div').hide();
          $(this).parent().siblings('label').hide();          
        }

      });
  });
</script>