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
		<link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
		<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
		<link href="{{ asset('css/responsive.css') }}" rel="stylesheet" media="all">
		<link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" media="all">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script type="text/javascript" src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/moment-with-locales.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/typeahead.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/admin.js') }}"></script>
		<script>
		   	var baseUrl = "{{url('/')}}/";
		</script>
	</head>

	<body>
		@include('admin.header')
@endif

@yield('content')

@if(!Request::ajax())

	<script>
		$(document).ready(function(){
			
			// Orders notification update
			setInterval(function() {
				
				getNotification();

			}, 5000);

			var pageheight = $('.page-data-right').height();
			$('.side-menu').css({
				'min-height': pageheight + 50,
			});	
		});


		function getNotification() {
			// url: baseUrl + 'admin/order/get-count',
			$.ajax({
				type: 'get',
				url: baseUrl + 'order/get-count',
				success: function (response) {

					var notificationBar = $('.notification_count');

					if (response > notificationBar.html()) {

						notificationBar.html(response);

						var audio = document.getElementById('notification-audio')
						// var audio = new Audio(baseUrl + 'sound/slow-spring-board.ogg');
						audio.play();

					} 
				}
			})
		}

		getNotification();
	</script>

	</body>
	
</html>
@endif
