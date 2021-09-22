$(document).ready(function(){
    
    $('.datetimepicker2').datetimepicker({
        format: "HH:mm:ss",
    });

    $(".datetimepicker2").on("keypress",".add-on",function(){
        //return false;
    });

    $("div[id^='view_data']").slideToggle();

    $(".view-action").removeClass('glyphicon-minus-sign').addClass('glyphicon-plus-sign');

    $(".page-data-cont").on("change",".paginate_limit",function(){
        $(".filter-form").submit();
    });
    
    $(".page-data-cont").on("change",".restaurant",function(){
        $(".filter-form").submit();
    });

    $(".page-data-cont").on("change",".rating",function(){
        $(".filter-form").submit();
    });

    $('#reset').click(function() {
        $('#filterForm input[type="text"], #filterForm input[type="email"], #filterForm input[type="number"], #filterForm select, #filterForm textarea').val('');
        $('#filterForm').submit();
    });

    $(".page-data-cont").on("click",".view-action",function() { 
        var id = $(this).attr('data-id');
        if($(this).hasClass('glyphicon-plus-sign')){
            $(this).removeClass('glyphicon-plus-sign').addClass('glyphicon-minus-sign');
        } else {
            $(this).removeClass('glyphicon-minus-sign').addClass('glyphicon-plus-sign');
        } 
        $("#view_data"+id).slideToggle();
    });
    
    $(".page-data-cont").on("click",".order-action",function(event) {
        if(confirm('Are you sure to update this?')) {
            var id = $(this).attr('data-id');
            var controller = $(this).attr('data-controller');
            var status = event.target.value;
            $.ajax({
                url:controller + '/update-order-status/'+id,
                type:'post',
                dataType:'json',
                data: "status="+status,
                success:function(data) {
                    $("#list_data"+id).html(data.list_data);              
                },
                error:function(error) {
                    alert('Data could not be loaded.');
                }
            });
        }
    });

    $(".page-data-cont").on("click",".edit-action",function() {
        var id = $(this).attr('data-id');
        var controller = $(this).attr('data-controller');
        // alert('Under Working..');
        $.ajax({
            url:controller+'/edit/'+id,
            type:'get',
            success:function(data) {
                $("#list_data"+id).html(data);              
            },
            error:function(error){
                alert('Data could not be loaded.');
            }
        });
    });
    
    $(".page-data-cont").on("click",".delete-action", function() { 
        var id = $(this).attr('data-id');
        if(confirm('Are you sure to delete this?')) {      
            var controller = $(this).attr('data-controller');  
            if(controller !='no') {
                window.location = controller+'/delete/'+id;
            } else {
                alert('Under development!');
            }
        } 
        return false;   
    });
    
    $(".page-data-cont").on("click",".update-action",function(){
        var action = $(this).attr('data-action');
        if(confirm('Are you sure to '+action+' this?')) {
            var id = $(this).attr('data-id');
            var controller = $(this).attr('data-controller');            
            var data = $('#editform'+id).serialize();
            // console.log(data)
            $.ajax({
                url:controller+'/update',
                type:'post',
                dataType:'json',
                data:data+"&action="+action,
                success:function(data) {
                    $("#list_data"+id).html(data.list_data);
                    $("#view_data"+id).html(data.view_data);                                           
                },
                error:function(error){
                    alert('Data could not be loaded.');
                }
            });        
        }        
    }); 

    /*==========state according country =======*/
    $(".page-data-cont").on("change",".country",function(){        
        var country_id = $(this).val();      
        stateOptions(country_id);        
    });  

    /*==========city according state =======*/
    $(".page-data-cont").on("change",".state",function(){        
        var state_id = $(this).val();      
        cityOptions(state_id);        
    });

    /*==========area according city =======*/
    $(".page-data-cont").on("change",".city",function(){        
        var city_id = $(this).val();      
        areaOptions(city_id);        
    });

    /*==========Add new area according city =======*/
    $(".page-data-cont").on("click",".add-area",function(e){
        e.preventDefault();              
        var city_id = $(".city").val();              
        addAreaOptions(city_id);        
    });       
    /*==============order js ==================== */    
    
    function getdetailsAddress() {
        var contactNumber = $('#order_search_customer').val();
        if(contactNumber == '' || isNaN(contactNumber)) return false;
         $.ajax({
            url:baseUrl+'ajax/getCustomer',
            type:'post',   
            data:"contactNumber="+contactNumber,           
            success:function(data){                   
                $("#order_customer_search_result").html(data);   
            },
            error:function(error) {
                alert('Data could not be loaded.');
                return false;
            }
        });
    }
    
    var defaultResId = jQuery("#order_restaurant_search").val();
    
    jQuery("#order_search_customer_button").click(function() {
        defaultResId = jQuery("#order_restaurant_search").val();
        if(defaultResId !='') {
            if(!getdetailsAddress()) {
                $("#order_customer_search_result").html("Please enter valid phone number");   
            }
            orderRestaurantResult(defaultResId); 
        } else {
            alert('Please first select restaurant');
        }
    });

    /* 
    if(typeof defaultResId != 'undefined'){
        orderRestaurantResult(defaultResId);
    }*/

    jQuery("#order_restaurant_search").change(function(e) {       
        defaultResId = $(this).val();
        orderRestaurantResult(defaultResId);     
    });

    /*==============end order js==================*/       
    jQuery(".page-data-cont").on('keypress','form input.form-control',function(e){
        if(e.which == 13) {
            e.preventDefault(); 
        }
    });

});
    
$(document).on('click','#admin_order_add',function(e) {
    e.preventDefault();
    var contactNumber = $('#order_search_customer').val();
    if(contactNumber == '' || isNaN(contactNumber)) {
        alert("Please enter valid phone number");
        return false;
    }
    if($("input[name='radiog_lite']").length == 0) {
        alert("Please search customer");
        return false;
    }
    var address = $("input[name='radiog_lite']:checked"). val();
    // if( $('input[name="order_type"]:checked').val() == 'delivery') {
    if(address == undefined || address == '') {
        alert("Please select delivery address");
        return false;
    }
    // }
    if( $('input[name="asap"]:checked').val() == 'later') {
        if( ! $('input[name="orderdatetime"]').val() ) {
            alert('Please select date to proceed.');
            return false;
        }
    }
    var restId = $( "#order_restaurant_search option:selected" ).val();
    var counter = countCartProduct(restId);
});


function countCartProduct(restId) {
    $.ajax({
        url: baseUrl+'ajax/count-cart-product',
        type:'post',      
        data:"restId="+restId,
        success:function(data) {
            if(data == 0) {
                alert("No item exist in cart");
                return false;
            } else {
                $(".admin-order-add").submit();
            }
        },
        error:function(error) {
            return 0;
        }
    });    
}

/* ===========order function =======*/
function orderRestaurantResult(restaurant_id){
    $.ajax({
        url:baseUrl+'ajax/getRestaurant',
        type:'post',                           
        data:"restaurant_id="+restaurant_id,           
        success:function(data) {                   
            $("#order_restaurant_search_result").html(data);                          
        },
        error:function(error){
            alert('Data could not be loaded.');
        }
    });     
}

/*============end order js =========*/
function stateOptions(country_id){
    $.ajax({
        url:baseUrl+'ajax/getstates',
        type:'post',                      
        data:"countryId="+country_id,           
        success:function(data){                     
            $(".state").html(data);                          
        },
        error:function(error){
            alert('Data could not be loaded.');
        }

    });    
}

function cityOptions(state_id) {
    $.ajax({
        url:baseUrl+'ajax/getcities',
        type:'post',                      
        data:"stateId="+state_id,           
        success:function(data){                     
            $(".city").html(data);                          
        },
        error:function(error){
            alert('Data could not be loaded.');
        }
    });    
}

function areaOptions(city_id) {
    $.ajax({
        url:baseUrl+'ajax/getareas',
        type:'post',  
        dataType:'json',              
        data:"cityId="+city_id,           
        success:function(data){   
            areaOption ='<option value="">Please Select</option>';
            $.each(data.html,function(key,area){
                areaOption +='<option value='+area.id+'>'+area.name+'</option>';    
            });                     
            $(".area").html(areaOption);                          
        },
        error:function(error){
            alert('Data could not be loaded.');
        }
    });    
}

var old_area_html = '';

function addAreaOptions(city_id) {
   if(city_id.length == 0){
        alert('Please first select city');
   } else {
        old_area_html = $("#area_box").html();        
        var html = '';      
        html +='<input type="text" name="new_area" id="new_area" placeholder="Enter new area" class="form-control">';              
        html +='<br><span style="cursor:pointer" data-cityid="'+city_id+'" class="glyphicon glyphicon-floppy-save add-area-save"></span>&nbsp;&nbsp;<span style="cursor:pointer" class="glyphicon glyphicon-remove add-area-cancel"></span>';
        $("#area_box").html(html);
   }
}

$(document).ready(function(){
    //save new area
    jQuery(".page-data-cont").on('click','.add-area-save',function(){
        var city_id = $(this).attr('data-cityid');
        var new_area = $("#new_area").val();
        $.ajax({
            url:baseUrl+'ajax/saveareas',
            type:'post',  
            dataType:'json',              
            data:"cityId="+city_id+"&new_area="+new_area,           
            success:function(data) { 
                if(data.success == true){
                    alert('Successfully Added'); 
                    $("#area_box").html(old_area_html); 
                    areaOptions(city_id);
                } else {
                    alert(data.error);
                }
            },
            error:function(error){
                alert('Data could not be loaded.');
            }
        });        
    });

    //save new area
    jQuery(".page-data-cont").on('click','.add-area-cancel',function(){        
        $("#area_box").html(old_area_html);
    });    

});