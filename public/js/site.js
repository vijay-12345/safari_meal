var nowDate = new Date();		
var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), nowDate.getHours(), nowDate.getMinutes(), nowDate.getSeconds(), 0);
// var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
$('.datetimepicker').datetimepicker({
	format: "YYYY-MM-DD HH:mm:ss",
 	minDate:today
});
// $('.datetimepicker').datetimepicker();

//default initialization on back button press
$(function() {
	$('#searchcity').trigger('change');
});

$('#FoodSwitch').on('change',function(){
  $('body').removeClass('healthyState');
  if ($(this).is(':checked')) {
    $('body').addClass('healthyState');
  }
});
$('body').on('change', '.finalformcheck', function () {
	$(".submit").prop('disabled', true);
	if($(this).is(':checked')) {
	  	$(".submit").prop('disabled', false);
  	}	
});

//Signup code//
$('body').on('submit', '.registration form', function() {
	var formData = $(this).serialize();	
	$.ajax({
		url: $(this).attr('action'),
		data: formData,
		type: 'POST',
		success: function(data) {			
			$('.registration').replaceWith(data);			
		},
		error: function(e) {		
		}
	});
	return false;
});

//Signup code//
$('body').on('submit', '.registration form', function() {
	var formData = $(this).serialize();	
	$.ajax({
		url: $(this).attr('action'),
		data: formData,
		type: 'POST',
		success: function(data) {			
			$('.registration').replaceWith(data);			
		},
		error: function(e) {		
		}
	});
	return false;
});

$('body').on('submit', '.updateaddress form', function(e) {
	var formData = $(this).serialize();		
	$.ajax({
		url: $(this).attr('action'),
		data: formData,
		type: 'POST',
		success: function(data) {						   
			$('.updateaddress').replaceWith(data);
		},
		error: function(e) {				
		}
	});
	return false;
});

$('.ProdAddonaddresscustom').on('hidden.bs.modal', function () {
	location.reload();	 	
});

$('body').on('submit', '.addnewaddress form', function(e) {	
	var formData = $(this).serialize();			
	$.ajax({
		url: $(this).attr('action'),
		data: formData,
		type: 'POST',
		success: function(data) {			
		    substring = '<div class="inner-page-header">';			
			$('.addnewaddress').replaceWith(data);							
		},
		error: function(e) {				
		}
	});
	return false;
});

$('.customadd').delay(5000).fadeOut();
//Signup code ends//
//Login//
// $('body').on('submit', '.login form', function(e) {
// 	var formData = $(this).serialize();
// 	$.ajax({
// 		url: $(this).attr('action'),
// 		data: formData,
// 		type: 'POST',
// 		success: function(data) {		
// 			$('.login').replaceWith(data);
// 		},
// 		error: function(e) {
// 		}
// 	});
// 	return false;
// });
$('body').on('submit', '.login form', function(e) {
	var formData = $(this).serialize();
	$.ajax({
		url: $(this).attr('action'),
		data: formData,
		type: 'POST',
		'dataType' : 'json',
		success: function(response) {
			if(response.success === false) {
				$('.login').replaceWith(response.html);
			} else {
				if(response.number_exist === false) {
					$("#LoginPop").removeClass("fade").modal("hide");
  					// $("#SignupPop").modal("show").addClass("fade");
					$('#signupPopUp').trigger('click')
				} else {
					window.location.reload();
				}
			}
		},
		error: function(e) {
		}
	});
	return false;
});
//Login code ends//

//Password reset
$('body').on('submit', '.password form', function() {
	var formData = $(this).serialize();
	$.ajax({
		url: $(this).attr('action'),
		data: formData,
		type: 'POST',
		success: function(data){
			$('.password').replaceWith(data);
		},
		error: function(e) {			
		}
	});
	return false;
});
//end Password reset


//Food Slider
$('.fslider').bxSlider({
  minSlides: 1,
  maxSlides: 3,
  slideWidth: 280,
  slideMargin: 40,
  moveSlides: 1,
  pager: false
});

//testimonial slider
$(window).load(function(){
  $('#carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 60,
    itemMargin: 10,
    asNavFor: '#slider'
  });

  $('#slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: true,
    directionNav: false,
    sync: "#carousel",
    start: function(slider){
      $('body').removeClass('loading');
    }
  });
});
$(function() {
  $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 500);
        return false;
      }
    }
  });
});


var states = [];
var selectedAreaUrlAlias = '';
//get Area via ajax call in Home Page
	$(document).on('change', '#searchcity, .country', function(){		
		var city = $(this).find(':selected').data('option');
		if(city != ''){
			$('input.search-field').removeAttr('disabled');
		}		
		if(city){
			states = [];
			$.ajax({			
				'url' : baseUrl+'ajax/getareas',
				'data' : { 'cityId' : city },
				'type' : 'post',
				'dataType' : 'json',
				'success' : function(response){	
					states = response.html;	
					$('.typeahead').typeahead('val', '');
					$('.typeahead').typeahead('destroy');
					bindTypeAhead();							
				}			
			});	
		}else{			
		}
		
	});

var substringMatcher = function() {

  return function findMatches(q, cb) {

    var matches, substringRegex;

    // an array that will be populated with substring matches
    matches = [];

    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');

    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(states, function(i, str) {
      if (substrRegex.test(str.name)) {
        matches.push(str.name);
      }
    });
    cb(matches);
  };
};

function bindTypeAhead() {

	$('.typeahead').typeahead({
	  hint: true,
	  highlight: true,
	  minLength: 0
	},
	{
	  name: 'area-dropdown',
	  source: substringMatcher(),
	  async: true
	});
};
bindTypeAhead();

$('.number').keydown(function(e){
if ( e.keyCode == 46 || e.keyCode == 8 ) {}
else if (e.keyCode < 48 || e.keyCode > 57 ) 
	e.preventDefault();	
});


$('body').on('typeahead:select', '.typeahead', function(e, s){	
	// regex used to determine if a string contains the substring `q`
    var substrRegex = new RegExp(s, 'i');

    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(states, function(i, str) {
      if (substrRegex.test(str.name)) {
      	selectedAreaUrlAlias = str.url_alias;
      }
    });
});

$('.search-from form [type="button"]').click(function(e) {
	window.location = $('.search-from form').attr('action') + '/' + selectedAreaUrlAlias;
});

$("button.btn").on("click",function(){	
	var area = $("input[name='searcharea']").val();	
});

$('form.filterform input[name="restauranttitle"]').keydown(function(event){ 
    var keyCode = (event.keyCode ? event.keyCode : event.which);    
    if (keyCode == 13) { 	
    	//var sd = $(this).val();
       /// $('form.filterform .hiddedfiletbutton').trigger('submit');        
    }
});

//Restaurant list filter handling starts
$('.sidebar').on('change', 'form.filterform', function(e) {

	var filter = $(this).serialize();
	window.location = $(this).attr('action') + '?' + $(this).serialize();
});

$('#reset').click(function() {
    $('.filterform input[type="text"], .filterform input[type="email"], .filterform input[type="number"], .filterform select, .filterform textarea').val('');
    $('input[type=checkbox]').prop('checked', false);
    $('input[type=radio]').prop('checked', false);
    $('.filterform').submit();
});

$(document).on("click","ul.nav-tabs ",function(e){		
	$(this).find("li:nth-child(2)").addClass('active');
	$(this).find("li:nth-child(1)").removeClass('active');
});	

$('.btn-see-more').click(function(){

	var limit = $(this).attr("rel");		
	$.get(baseUrl+'morecuisines/?limit='+limit, function(data) {		
		var fulldata = data.split("**%&%**");
		var html = fulldata[0];
		var isdata = fulldata[1];
		var limit = fulldata[2];			
		if(isdata > 0) {
			$("div.filtercousine div.checkbox-cont:last").after	(html);		
			$('.btn-see-more').attr("rel",limit);
			$('input.cousinlimit').val(limit);			
		} else {
			$('.btn-see-more').remove();			
		}
	});
})
//Restaurant list filter handling ends

//Forgot password starts
$('#ForgotPasswordPop').on('show.bs.modal', function (e) {

	$('.modal').modal('hide');
});
//Forgot password ends

// Cart code starts
//$("form.orderitemaddons button").on("click",function(){	

	$(".Y").click(function(){
		if($(this).is(':checked'))
		{	var form = $(this).closest("form");
			form.find("#"+$(this).attr('rel')+"Y").hide();
		}	
	});

$(document).on("click",'form.orderitemaddons button',function(){
	var allrequireds = [];
	var checked = '';
	var requiredname = '';
	var requiredunchecked = '';
	var form = $(this).closest("form");
	$(this).closest("form").find(".Y").each(function( index ) {		
		allrequireds.push($(this).attr('rel'));
	});
	$.each(allrequireds, function(index, val) {
		if(requiredname != val)
    	{
			requiredname = val;
			checked = '';
			form.find("."+val+"Y").each(function() {
				if($(this).is(':checked'))
					checked	='Yes';
			});
			if(checked == "" && requiredunchecked == "")
				requiredunchecked = val;
		}
	});
	if(requiredunchecked != "") {
		form.find("#"+requiredunchecked+"Y").show();
    	return false;
	}
	var formData = $(this).closest("form").serialize();	
	var productobject = $(this).closest("div.row");	
	var itemname = productobject.find('div.item-name').text();
	var productId = productobject.find('div.item-name').attr('rel');	
	var itemprice = productobject.find('div.item-price').attr('rel');	
	var currentVal = productobject.find('input[name="cart_product_quantity"]').val();	
	var productfieldname = productobject.find('span.qtyminus').attr('field');
	/*Checkout Code Begins*/
	var proceedtocheckout = $(this).attr('rel');
	if(proceedtocheckout == 'checkout') {
		var productobject = $(this).closest("div.summry-prod-single");    	
    	var checkoutproductid = productobject.find('input[name="checkoutproductid"]').val();    	
    	var productquantity = $('#ProdAddon'+checkoutproductid).find('form input[name="cart_product_quantity"]').val();    	
    	updatecheckout(productobject,checkoutproductid,productquantity,formData,productfieldname) ;   		
    } else {
	 	updatecart(itemname,productId,itemprice,productfieldname,currentVal,formData);
    }
	/*Checkout Code Ends*/
});

$(document).on('click', '.noaddons' , function() {
	var action = $(this).hasClass('qtyminus') ? 'minus' : 'add';		
	var fieldName = $(this).attr('field');
	var productobject = $(this).closest("div.row");
	var currentVal = parseInt($('input[name='+fieldName+']').val());
    /*Checkout Code Begins*/
    var proceedtocheckout = $(this).attr('rel');
    if(proceedtocheckout == 'checkout') {
    	var productobject = $(this).closest("div.summry-prod-single");
    	var productquantity = productobject.find('span.qty-input input').val();
    	var checkoutproductid = productobject.find('input[name="checkoutproductid"]').val();
    	var productfieldname = productobject.find('span.qtyminus').attr('field');    	
    	var formdata = '';
    	var quantity;
		if(action == 'minus') {
			quantity = parseInt(productquantity)-1;
		} else {
			quantity = parseInt(productquantity)+1;
		}
    	updatecheckout(productobject,checkoutproductid,quantity,formdata,productfieldname) ;   		
    } else {
	 	addOrRemoveProduct(productobject,currentVal, action);
    }
    /*Checkout Code Ends*/
});


function addOrRemoveProduct(productobject,currentVal,action) {
	// var chackoutpagecheck = $('.noaddons').attr('rel');	
	// var pageurl = productobject.find('input[name="pageurl"]').val();	
	var itemname  = productobject.find('div.item-name').text();
	var productId = productobject.find('div.item-name').attr('rel');	
	var itemprice = productobject.find('div.item-price').attr('rel');	
	var productfieldname = productobject.find('span.qtyminus').attr('field');		
	var formdata = '';
	if(action == 'minus') {
		if (isNaN(currentVal) || currentVal <= 0) {
			return;
		}
		updatecart(itemname,productId,itemprice,productfieldname,currentVal-1,formdata);
	} else {
		updatecart(itemname,productId,itemprice,productfieldname,currentVal+1,formdata);		
	}
}


function updatecart(itemname,productId,itemprice,productfieldname,currentVal,formdata) {

	var restId = $("#order_restaurant_search option:selected").val();
	var deliveryOption = 'pickup';
	var frontSide = 0;
	if(restId == undefined && isNaN(restId)) frontSide = 1;
	else {
		var orderType = $('input[name="order_type"]:checked').val();
		if(orderType == 'delivery') deliveryOption = 'delivery';
	}
	$.ajax({
		url:baseUrl+'addtocart',
		type:'post',
		data:'frontSide='+frontSide+'&deliveryOption='+deliveryOption+'&prodid='+productId+'&formdata='+formdata+'&quantity='+(currentVal)+'&field='+productfieldname,
		success:function(data) {
			$( ".sidebar-cart" ).replaceWith(data);	
			$('input[name='+productfieldname+']').val(currentVal);
		}
	});
	// $('button.close').trigger('click');
}


function updatecheckout(productobject, checkoutproductid, quantity, formdata, fieldval) {
	var orderType = $('input[name="order_type"]:checked').val();
	if(orderType == 'delivery') deliveryOption = 'delivery';
	else deliveryOption = 'pickup';
	$.get(baseUrl+'updatecheckout/?prodid='+checkoutproductid+'&formdata='+formdata+'&deliveryOption='+deliveryOption+'&field='+fieldval+'&quantity='+(quantity), 
		function(data) {
			$("body").removeClass("modal-open");
			$("#OrderSumm .cart-summry-data").empty();
			$("#OrderSumm .cart-summry-data").html(data);
			$("div").removeClass("modal-backdrop fade in");	
		}
	);
}


// $('.sidebar-data').on('click', '.sidebar-cart i', function(e) {
$(document).on('click', '.sidebar-cart i', function(e) {	
	var targetId = $(e.target).parent('span').attr('field');
	var targetClass = $(e.target).parent('span').attr('class');
	$('[field~="'+ targetId +'"]', '.restro-menu-tab').each(function(k, v) {		
		var obj = $(v);
		if(obj.hasClass(targetClass.split(' ')[0])) {
			obj.trigger('click');
			return;
		}
	});
});


// Cart code ends
$('.useraddressdelet').on("click",function(){
	var confirm = show_confirm($(this));  	
  	if(confirm<1){
  		return false;	
  	}  
  	var user_and_addrid = $(this).attr('rel').split("===="); 
  	var addrresid  = user_and_addrid[0];
  	var userid  = user_and_addrid[1];
  	$.ajax({
        type: "POST",
        url: "deleteaddress",
        data: {'addrid':addrresid,'uid':userid},         
        success: function (response) {               
            if (response==0) {            
            	alert('There is some problem.');	        
            }else{   
            	 location.reload();
            }
        },       
    });	
});


//get states via ajax call
$(document).on('change', '#country, .country', function(){
	$('#cities').html("");
	$('#areaa').html("");	
	$('#cities').html("<option value=''>City</option>");
	$('#areaa').html("<option value=''>Area</option>");
	var countryId = $(this).find(':selected').data('option');		
	if(countryId){
		$.ajax({			
			'url' : baseUrl+'ajax/getstates',
			'data' : { 'countryId' : countryId},
			'type' : 'post',
			'success' : function(response){					
				var resultlen = $(response).length;
				var elements = $(this).parent('div').nextAll().find('.selector');				
				if(resultlen>1){
					//console.log($(response).length);
					$('#states').html(response);
					$('#states').trigger('change');				
					$('#cities').trigger('change');
					$('#areaa').trigger('change');
					$(elements).filter('select').attr('disabled', false).css('display', 'block');
					$(elements).filter('input').attr('disabled', true).css('display', 'none');
					$('input[name="state"]').trigger('change');							
				}else{					
					//console.log($(elements).filter('input'));
					$(elements).filter('select').attr('disabled', true).css('display', 'none');
					$(elements).filter('input').attr('disabled', false).css('display', 'block');
				}				
			}.bind(this)			
		});	
	}
});

$(document).on('change', '#states, .states', function(){
	$('#areaa').html("");	
	$('#areaa').html("<option value=''>Area</option>");	
	var stateId = $(this).find(':selected').data('option');		
	if(stateId){
		$.ajax({		
			'url' : baseUrl+'ajax/getcities',
			'data' : { 'stateId' : stateId},
			'type' : 'post',
			'success' : function(response){	
				var resultlen = $(response).length;
				var elements = $(this).parent('div').nextAll().find('.selector');				
				if(resultlen>1){					
					$("#cities").html(response);
					$('#cities').trigger('change');
					$('#areaa').trigger('change');
					$(elements).filter('select').attr('disabled', false).css('display', 'block');
					$(elements).filter('input').attr('disabled', true).css('display', 'none');				
				}else{
					$(elements).filter('select').attr('disabled', true).css('display', 'none');
					$(elements).filter('input').attr('disabled', false).css('display', 'block');
				}
			}.bind(this)			
		});	
	}
});


$(document).on('change', '#cities, .cities', function(){	
	var cityId = $(this).find(':selected').data('option');		
	if(cityId){
		$.ajax({			
			'url' : baseUrl+'ajax/getareasnonjsn',
			'data' : { 'cityId' : cityId},
			'type' : 'post',
			'success' : function(response){	
				var resultlen = $(response).length;
				var elements = $(this).parent('div').nextAll().find('.selector');				
				if(resultlen>1){					
					$("#areaa").html(response);				
					$('#areaa').trigger('change');
					$(elements).filter('select').attr('disabled', false).css('display', 'block');
					$(elements).filter('input').attr('disabled', true).css('display', 'none');				
				}else{
					$(elements).filter('select').attr('disabled', true).css('display', 'none');
					$(elements).filter('input').attr('disabled', false).css('display', 'block');
				}
			}.bind(this)			
		});	
	}
});

$(".menu_review_info").click(function(){
	$(".menu_review_info_li").removeClass('active');
	this.parent().addClass('active');
	$("#menuTab, #reviewTab, #infoTab").removeClass('in active');
	$($(this).attr('href')).addClass('in active');
});


function show_confirm(obj) {
    var r = confirm("Are you sure you want to delete?");
    if (r != true) {
    	event.preventDefault();
    	return 0; 	
    } else {
    	return 1;
    }           
}


$(function () {
	var nowDate = new Date();		
	var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);	
	$('.datepicker').datetimepicker({
		format: "YYYY-MM-DD",
	 	minDate:today,
	});
	$('.timepicker').datetimepicker({
  		format: "HH:mm:ss",
  	});
});


$(".delivery-radio-option input").click(function() {	
	if($(this).hasClass("later")) {
		$("div.later").show();
	} else {
		$("div.later").hide();
	}    
});


$(document).on("click",".tt-dataset-area-dropdown",function(){
	var inputval = $(".search-field").val();	
	if(inputval!="") {		
		$('.btn-srch').prop("disabled", false);
	} else {		
		$('.btn-srch').prop("disabled", true);
	}	
});


//filter search
$(document).on('click','.change-location-btn .loc-btn',function(){
	$('.filter-search-full').slideToggle();
	$(this).toggleClass('open');
});


$(document).on('change','select#OptDelivAddr',function(e){
	var addressId = $(this).val();	
	var userId = $(this).find("option:selected").attr("rel");	
	if(addressId){
		$.ajax({			
			'url' : baseUrl+'ajax/optdeliveryaddress',			
			'data' : { 'addressId' : addressId,'userId' : userId},
			'type' : 'post',
			'success' : function(response){				
				/*if(response=='noaddress'){
					$(".user-detail .form-group select").replaceWith(response);											
				}else{
					$('.user-detail table').replaceWith(response);						
				}*/				
				$('.user-detail table').replaceWith(response);						
			}			
		});
	}
});


var contactnum = $("input[name='contact_number']").val(); 
if(!(typeof pagetype === 'undefined') && contactnum.indexOf('-') !== -1 ) {
	var index= contactnum.indexOf('-');
	$("input[name='contact_number']").val(contactnum.substring(index+1));
	var countrycode	= contactnum.substring(0,index);		
	if( countrycode.indexOf('+') !== -1 )
		countrycode= countrycode.substring(1);
	$("select[name='countrycodeoption']").val(countrycode).change();
}
else $("select[name='countrycodeoption']").val($("input[name='countrycode']").val()).change();

$("select[name='countrycodeoption']").on('change',function(){
	$("input[name='countrycode']").val($("select[name='countrycodeoption']").val());
});


$(document).on('click','#checkcoupanvalid',function(e){
	var coupancode = $("input[name='coupancode']").val();
	if(coupancode == '') {
		$("#coupanmessage").html("Please enter voucher code.");
		return false;
	}
	$(".coupon-submit").prop('disabled', true);
	var orderType = $('input[name="order_type"]:checked').val();
	if(orderType == 'delivery') deliveryOption = 'delivery';
	else deliveryOption = 'pickup';
	$.ajax({
		'url' : baseUrl + 'ajax/checkcoupanvalid',			
		'type' : 'post',
		'data' : {'coupancode':coupancode, 'deliveryOption':deliveryOption},
		'dataType' : 'json',
		'success' : function(response) {
			if(response.success === false) {
				$("#coupanmessage").html(response.data);
				$(".coupon-submit").prop('disabled', false);
				// $("#coupanmessage").html("coupan code is not valid please try again");
			} else {
				$("#OrderSumm .cart-summry-data").empty();
				$("#OrderSumm .cart-summry-data").html(response.html);
				$('#coupon_id').val(response.data);
				$("#coupanmessage").html("coupan code is Successfully used.");
				// window.location.reload();
			}
		},
		error: function(e) {
			alert(e);
		}
	});
});	


$("input, .fa").on('change click',function(){
	$('.alert').hide();
});

$(".qty-input input").prop("readonly", "true");

/*==========submit subscribe form at home page====== */
$(document).ready(function(){
	$(document).on("click",'form.subscribe button',function(){
		//check validation
		var email = $("form.subscribe input[name='email']").val();
		var text = '',msg='';
		if(email == ''){
			text += 'Invalid Email!';
		}				
		if(!$("form.subscribe :checkbox[name='policy']").is(":checked")){	
			text += ' Policy required!';
		}
		if(text !=''){
			msg = '<div class="alert alert-danger">';
			msg += '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
			msg += text;
			msg += '</div>';
			$(".subscribe-msg").html(msg);
		}else{
			$(".subscribe-msg").html('');
			//submit data
			$.ajax({
				url : baseUrl+'ajax/subscribe',
				type:'post',
				dataType:'json',			
				data : {email : email},			
				success : function(data){
					if(data.success == false){
						msg = '<div class="alert alert-danger">';					
					}else{
						msg = '<div class="alert alert-success">';
					}
					msg += '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
					msg += data.message;
					msg += '</div>';				
					$(".subscribe-msg").html(msg);
					$("form.subscribe input[name='email']").val('')
				},
				error: function(e) {
					alert(e);
				}			
			});			
		}	
		
	});	
});

////////For Submit button disable or enable//////////

$('body').on('show.bs.modal', '.modal', function () {
	//$('form').attr('autocomplete', 'off');
	$(".submit").prop('disabled', true);
	//$(".modal-backdrop").show();
});

////model refresh comtent on close
$('body').on('hidden.bs.modal', '.modal', function () {
  	$(this).removeData('bs.modal');
});
//end


$(document).on('click','#SignupPopFrmLogin1',function(){
  	$("#LoginPop").removeClass("fade").modal("hide");
  	$("#SignupPop").modal("show").addClass("fade");
	$("#SignupPop").html("deeceevevv");
});
/*========================== post review=============*/
$(document).ready(function(){
	//under working...
});


/*=========check minimum order on secure/confirm checkout page in front*/
$(document).ready(function(){
	$(".sbmt-btn-abs").on('click',function(e){
		e.preventDefault();
		var total = $("#confim_total").attr('data-total');
		var min_order = $("#confim_total").attr('data-min_order');
		var delivery_type = $("#confim_total").attr('data-delivery_type');
		if(parseInt(min_order) > parseInt(total) && delivery_type !='pickup') {
			$(".min-order").html('You must have an order with a minimum of '+parseInt(min_order)+CURRENCY+' to place your order, your current order total is '+parseInt(total)+CURRENCY);
		} else {
			$(".min-order").html('');
			$(".secure_form").submit();
		}
	});
})