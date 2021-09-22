<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.min.js"></script>
<link href="{{ asset('css/bootstrap-multiselect.css') }}" rel="stylesheet">

<?php
  $product_can_use = [];
  $coupon_can_use  = [];
  use App\Restaurent, App\Product;
  $q = Restaurent::lang()->where('status',1);
  if(Session::get('access.role') != 'admin') {
    $q = $q->whereIn('id',Session::get('access.restaurant_ids'));
  }
  $restaurants = $q->lists('name','id');
  
  foreach($customers as $key=>$v){
    $coupon_can_use[] = $key;
  }
?>

@if(!empty($coupon))
  <?php
      if($coupon->combo_offer ==1){
          $product_can_use = explode(",",$coupon->product_can_use);
          $products = Product::lang()->where(['status'=>1,'restaurant_id'=>$coupon->restaurant_id])->lists('name','id');    
          //prd( $customers);
      }
      if($coupon->coupon_can_use !=''){
          $coupon_can_use = explode(",",$coupon->coupon_can_use);
      }  
  ?>
@endif

<?php 
    // prd($coupan_can_use);
?>

<br />
  
  <div class="form-group">
      <label class="control-label col-sm-2" for="restaurant_id">{{trans('admin.restaurant')}}*:</label>
      <div class="col-sm-10">
            @if(Session::get('access.role') == 'admin')
                {!! Form::select('restaurant_id',['' => trans('admin.select.restaurant')]+$restaurants, null, ['class' => 'form-control','id'=>'restaurant_id']) !!}
            @else
                {!! Form::select('restaurant_id',$restaurants, null, ['class' => 'form-control','id'=>'restaurant_id']) !!}
            @endif
      </div>                
  </div>

<div class="form-group customer-box">
  <label class="control-label col-sm-2" for="restaurant_id">{{trans('admin.select.customer')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('customers[]',$customers, $coupon_can_use, ['multiple' => 'multiple','id'=>'customers']) !!}  
    
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="coupon_code">{{trans('admin.coupon.code')}}*:</label>
  <div class="col-sm-10">
  	{!! Form::text('coupon_code', null, ['class' => 'form-control']) !!}  
  </div>							      
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="description">{{trans('admin.description')}}:</label>
  <div class="col-sm-10">
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}  
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="coupon_type">{{trans('admin.coupon.type')}}*:</label>
  <div class="col-sm-10">
    {!! Form::select('type',[''=>trans('admin.select.type'),'1'=>trans('admin.percentage'),'0'=>trans('admin.fixed')],null, ['class' => 'form-control']) !!}    
    
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.coupon.value')}}*:</label>
  <div class="col-sm-10">
  	{!! Form::text('coupon_value',null, ['class' => 'form-control']) !!}    
    
  </div>							      
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.combo.offer')}}:</label>
  <div class="col-sm-1">
    {!! Form::checkbox('combo_offer',null,null, ['class' => '']) !!}     
  </div>
  
    @if(!empty($products))
    <div class="col-sm-9 product-box">
      {!! Form::select('products[]', $products,$product_can_use, ['class' => 'form-control','id'=>'product','multiple'=>'multiple']) !!}    
     </div>
    @else
    <div class="col-sm-9 product-box" style="display:none;"></div>
    @endif
                        
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.date')}}*:</label>
  
  <div class="col-sm-3 text-left">
  From {!! Form::text('start_date',null, ['class' => '','id'=>'from']) !!}    
    
  </div>  
  <div class="col-sm-3 text-left">
    To {!! Form::text('end_date',null, ['class' => '','id'=>'to']) !!}
  </div>                    
</div> 					    								    										    								   	
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.status')}}:</label>
  <div class="col-sm-10">
  	{!! Form::select('status', ['1'=>trans('admin.active'),'0'=>trans('admin.inactive')],null, ['class' => 'form-control']) !!}    
    
  </div>							      
</div>

 <script>
 //-------------------------multiple product---------------------------
 $(function() { 
    $('#product').multiselect({
        includeSelectAllOption: true
    });
    $('#customers').multiselect({
        includeSelectAllOption: true
    });

    //check combo offer
    $("button[type='submit']").click(function(e){
        e.preventDefault();
        if($("input[name='combo_offer']").prop('checked')){
          var p = $('select#product option:selected').length;
          if(p== undefined || p <=1){
            alert('Please select minimum 2 products');
            return false;
          } else {
            $('form').submit();
          }
        } else {
          $('form').submit();
        }
    });
});

$(function() {
    //on change restaurant
    $("#restaurant_id").change(function(){
        var restaurant_id = $('select#restaurant_id option:selected').val();
        if(restaurant_id.length > 0){          
            productByRestaurantId(restaurant_id);
        } 
    });

    //on click combo_offer
    @if(empty($coupon))
        $("input[name='combo_offer']").prop('checked',false); 
    @endif

    $("input[name='combo_offer']").on('click',function(){
        $(".product-box").hide();
        if($(this).prop('checked')){
          var restaurant_id = $('select#restaurant_id option:selected').val();
          if(restaurant_id.length == 0){
              $(this).prop('checked',false);
              alert("Please first select Restaurant");
          } else {
              $(".product-box").show();  
              productByRestaurantId(restaurant_id);
          }
        } else {
            $(".product-box").html(''); 
        }
    });
});

function productByRestaurantId(restaurantId) {
    $.ajax({
        url:baseUrl+'ajax/getproduct',
        type:'post',  
        dataType:'json',              
        data:"restaurantId="+restaurantId,           
        success:function(data){
            productOption = '<select name="products[]" id="product" multiple="multiple" class="form-control product">';
            $.each(data.products,function(key,product){
                productOption +='<option value='+product.id+'>'+product.name+'</option>';    
            }); 
            productOption +='</select>';                              
            $(".product-box").html(productOption);  
            $('#product').multiselect('rebuild');
        },
        error:function(error){
            alert('Data could not be loaded.');
        }
    });
}
//---------------------end of multiple product--------------

//----------date picker--------------
 $(function() {
    $( "#from" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat:'dd-mm-yy',
        onClose: function( selectedDate ) {
            $( "#to" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#to" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat:'dd-mm-yy',
        onClose: function( selectedDate ) {
            $( "#from" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
});
</script>