@extends('admin.layout')

@section('title', "Global Setting")

@section('content')

<?php $prefix = \Request::segment(2); ?>
    
    <div class="page-data-cont">
        <div class="container-fluid">
            <div class="row">           
                <div class="col-md-3 side-menu sidebar-menu">
                    @include('admin.navigation')                
                </div>

                <div class="col-md-9">
                    <div class="page-data-right">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Setting</div>
                            <div class="panel-body">

                                @if(Session::has('flash_message'))
                                    <div class="alert alert-success">
                                        {{ Session::get('flash_message') }}    
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        @foreach($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if ( $model )

                                    {!! Form::model($model, ['url' => $prefix.'/setting', 'class' => 'form-horizontal', 'method' => 'post'] ) !!}

                                        <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="country_id">Select Country*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::select('country_id', [''=>'Select Country'] + $countries, $model->country_id, ['id' => 'country_id', 'class' => 'form-control']) !!}
                                            </div>
                                            <!-- @if ($errors->has('country_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('country_id') }}</strong>
                                                </span>
                                            @endif -->
                                        </div>
                                        
                                        <div class="form-group{{ $errors->has('country_code') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="country_code">Country Code*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::text('country_code', null, ['class' => 'form-control']) !!}  
                                            </div>                
                                        </div>
                                        
                                        <div class="form-group{{ $errors->has('timezone') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="timezone">Select Timezone*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::select('timezone', [''=>'Select Timezone'] + $timezones, $model->timezone, ['id' => 'timezone', 'class' => 'form-control']) !!}
                                            </div>
                                            <!-- @if ($errors->has('timezone'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('timezone') }}</strong>
                                                </span>
                                            @endif -->
                                        </div>

                                        <div class="form-group{{ $errors->has('radius') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="radius">Search Radius (In Km)*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::text('radius', null, ['class' => 'form-control']) !!}  
                                            </div>
                                            <!-- @if ($errors->has('radius'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('radius') }}</strong>
                                                </span>
                                            @endif  -->                
                                        </div>

                                        <div class="form-group{{ $errors->has('order_status') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="order_status">Order Status Preference*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::select('order_status', ['1'=>'Manual', '2'=>'Open For Drivers'], null, ['class' => 'form-control']) !!}    
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-2">
                                            </div>
                                            <div class="col-sm-10">
                                                <button type="submit" class="btn btn-primary">{{trans('admin.update')}}</button>
                                            </div>
                                        </div>
                                    {!! Form::close() !!}

                                @else

                                    {!! Form::open( [ 'url' => $prefix.'/setting', 'method' => 'post', 'class' => 'form-horizontal'] ) !!}

                                        <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="country_id">Select Country*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::select('country_id', [''=>'Select Country'] + $countries, null, ['id' => 'country_id', 'class' => 'form-control']) !!}
                                            </div>
                                            <!-- @if ($errors->has('country_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('country_id') }}</strong>
                                                </span>
                                            @endif -->
                                        </div>
                                        
                                        <div class="form-group{{ $errors->has('country_code') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="country_code">Country Code*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::text('country_code', null, ['class' => 'form-control']) !!}  
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('timezone') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="timezone">Select Timezone*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::select('timezone', [''=>'Select Timezone'] + $timezones, null, ['id' => 'timezone', 'class' => 'form-control']) !!}
                                            </div>
                                            <!-- @if ($errors->has('timezone'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('timezone') }}</strong>
                                                </span>
                                            @endif -->
                                        </div>

                                        <div class="form-group{{ $errors->has('timezone') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="radius">Search Radius (In Km)*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::text('radius', null, ['class' => 'form-control']) !!}  
                                            </div>
                                            <!-- @if ($errors->has('radius'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('radius') }}</strong>
                                                </span>
                                            @endif -->                 
                                        </div>
                                        
                                        <div class="form-group{{ $errors->has('order_status') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-2" for="order_status">Order Status Preference*:</label>
                                            <div class="col-sm-10">
                                                {!! Form::select('order_status', ['1'=>'Manual', '2'=>'Open For Drivers'], null, ['class' => 'form-control']) !!}    
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="col-sm-2">
                                            </div>
                                            <div class="col-sm-10">
                                                <button type="submit" class="btn btn-primary">{{trans('admin.submit')}}</button>
                                            </div>
                                        </div>

                                    {!! Form::close() !!}
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    

<script>

</script>

@endsection