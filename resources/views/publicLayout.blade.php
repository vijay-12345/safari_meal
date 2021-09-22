@if(!Request::ajax())
	<!doctype html>
	<html>
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>@yield('title', 'Taxiye Food')</title>
			
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
			<script type="text/javascript">
			var CURRENCY = "<?php echo Config::get('constants.currency');?>";
			</script>
			<script type="text/javascript" src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>
		</head>
	<body>
	
	@include('header')
	
@endif

@yield('content')

@if(!Request::ajax())
		
		@include('footer')

		<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/jquery.bxslider.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/jquery.flexslider-min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/moment-with-locales.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/typeahead.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/site.js') }}"></script>

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
	
	@if(Session::has('verified-message'))
		<script type="text/javascript">
			$('#loginPopUp').trigger('click');
		</script>
	@endif

@endif