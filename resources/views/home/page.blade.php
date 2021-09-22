@extends(Request::get('mobile')==1 ? 'mobileLayout': 'innerpageLayout')

@section('title', Lang::get('common.title'))

@section('content')

@if( !Request::get('mobile') )
<div class="inner-page-header">
	<div class="container">
	<div class="row">
		<div class="col-md-9">
			<div class="breadcrumbs-cont">
				<p><a href="{{url('/')}}" title="">Home</a> / {{ucfirst($data->title)}}</p>
			</div>
		</div>
		<div class="col-md-3">

		</div>
	</div>
	</div>
</div>
@endif

@include('home.apipage')

@endsection