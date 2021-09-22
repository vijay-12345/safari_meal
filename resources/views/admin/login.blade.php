@extends('admin.loginLayout')
@section('title', ucfirst(\Request::segment(2)).' '.trans('admin.login'))
@section('content')
<header>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-5">
        <a href="{{url('/')}}" title="" class="logo"><img src="{{ asset('images/admin-logo.png') }}" alt=""></a>
      </div>
      <div class="col-sm-7">
          <div class="top-link text-right">
    					<ul class="list-inline">
    						<!-- <li>
    							<div class="dropdown">
    							  <a id="dLabel" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
    							  	<i class="flaticon-earth"></i>
    							    {{trans('admin.language')}}
    							    <span class="flaticon-arrows"></span>
    							  </a>
                    
    							  <ul class="dropdown-menu" aria-labelledby="dLabel">
      							  	@foreach(Config::get('app.alt_langs') as $lang)
      							  		<li><a href="{{'/'.$lang.'/admin'}}" class="" title="">{{Config::get('constants.language_map.'.$lang)}}</a></li>
      							  	@endforeach
    							  </ul>
                    
    							</div>
    						</li> -->
    					</ul>
				  </div>

      </div>
    </div>
  </div>
</header>
<div class="page-data-cont">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default margin-top">
          <div class="panel-heading">{{trans('admin.login')}}</div>
          <div class="panel-body">
            {!! Html::ul($errors->all(), array('class'=>'alert alert-danger errors')) !!}
           {!! Form::open(array('role' => 'form','url' => \Request::segment(2))) !!}  
            <div class="form-group">
              <input type="text" name="email" value="" placeholder="Username" class="form-control" autofocus>
            </div>
            <div class="form-group">
              <input type="password" name="password" value="" placeholder="Password" class="form-control">
            </div>
            <div class="form-group">
              <input type="checkbox" name="remember_me" value="1" placeholder="" id="rem-pw">
              <label for="rem-pw">{{trans('admin.remember.passward')}}</label>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-full">{{trans('admin.login')}}</button>
            </div>
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection