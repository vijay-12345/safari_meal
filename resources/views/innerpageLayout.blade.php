@if(!Request::ajax())
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title')</title>

<link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('css/flat-icon/flaticon.css') }}" rel="stylesheet">
<link href="{{ asset('css/jquery.bxslider.css') }}" rel="stylesheet">
<link href="{{ asset('css/flexslider.css') }}" rel="stylesheet">
<link href="{{ asset('css/ihover.css') }}" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<link href="{{ asset('css/responsive.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" media="all">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="inner-page">
@if(isset($slug) && $slug == 'Policy-foodology')	
@else
@include('globalheader')
@endif	

@endif

@yield('content')

@if(!Request::ajax())
@if(isset($slug) && $slug == 'Policy-foodology')	
@else
@include('footer')
@endif

<script type="text/javascript" src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.bxslider.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.flexslider-min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/typeahead.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/moment-with-locales.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/site.js') }}"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
  
$(function() {
    $( "#book_date" ).datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat:'dd-mm-yy',
        minDate:0
    });
});

$(document).on('click','#admin_table_add',function(e) {
      e.preventDefault();
      var customerName = $('#customer_name').val();
      var contactNumber = $("#customer_contact"). val();
      var total_person = $('#total_person').val();
      var bookDate = $('#book_date').val();
      if(customerName == '' ) {
          alert("Please enter table booked person name");
          return false;
      }
      else if(contactNumber == '' || isNaN(contactNumber)) {
          alert("Please enter valid phone number");
          return false;
      }
      else if(total_person == '') {
          alert("Please enter number of guest for table");
          return false;
      }
      else if(bookDate == '') {
          alert("Please select table booking date");
          return false;
      }else{
        $('.admin-table-add').submit();
        //document.getElementById("myForm").submit();
      }
      // }
  });
</script>

<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a2643e3d0795768aaf8d62d/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->





</body>
</html>
@endif
