<br />
<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('admin.first.name')}}*:</label>
  <div class="col-sm-10">
    {!! Form::text('first_name', null, ['class' => 'form-control','placeholder'=>trans('admin.enter.first.name')]) !!}             
  </div>
</div>
<div class="form-group">

  <label class="control-label col-sm-2" for="name">{{trans('admin.last.name')}}*:</label>
  <div class="col-sm-10">
  {!! Form::text('last_name', null, ['class' => 'form-control','placeholder'=>trans('admin.enter.last.name')]) !!}

  </div>
</div>
<div class="form-group">

  <label class="control-label col-sm-2" for="name">{{trans('admin.email')}}*:</label>
  <div class="col-sm-10">
  {!! Form::text('email', null, ['class' => 'form-control','placeholder'=>trans('admin.enter.email')]) !!}

  </div>
</div>
@if(!isset($user))
  <div class="form-group">

    <label class="control-label col-sm-2" for="name">{{trans('admin.password')}}*:</label>
    <div class="col-sm-10">
    {!! Form::password('password', null, ['class' => 'form-control']) !!}

    </div>
  </div>
  <div class="form-group">

    <label class="control-label col-sm-2" for="name">{{trans('admin.confirm.password')}}*:</label>
    <div class="col-sm-10">
    {!! Form::password('password_confirmation', null, ['class' => 'form-control']) !!}

    </div>
  </div>
@else
  <div class="form-group">

    <label class="control-label col-sm-2" for="new_password">{{trans('admin.new.password')}}:</label>
    <div class="col-sm-10">
    {!! Form::password('password', null, ['class' => 'form-control']) !!}

    </div>
  </div>
   <div class="form-group">

    <label class="control-label col-sm-2" for="password_confirmation">{{trans('admin.confirm.password')}}:</label>
    <div class="col-sm-10">
    {!! Form::password('password_confirmation', null, ['class' => 'form-control']) !!}

    </div>
  </div> 
@endif

<div class="form-group">
  <label class="control-label col-sm-2" for="name" >{{trans('admin.contact.number')}}*:</label>
  <div class="col-sm-2">
      @if(isset($user))
        {!! Form::text('countrycode', null, ['class' => 'form-control']) !!}
      @else
        {!! Form::text('countrycode', '91', ['class' => 'form-control']) !!}
      @endif
  </div> 
  <div class="col-sm-8">
  {!! Form::text('contact_number', null,array('class' => 'form-control','maxlength' => 10) ) !!}
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="image">{{trans('admin.image')}}:</label>
  <div class="col-sm-10">
    {!! Form::file('image',null,['class'=>'form-control']) !!}<br>      
      @if(isset($user) && $user->profile_image !='')       
        {!! Form::hidden('old_image',$user->profile_image) !!}                                            
        <img src="{{asset($user->profile_image)  }}" width="50">                                                             
      @endif    
  </div>                    
</div>
<div class="form-group">

  <label class="control-label col-sm-2" for="newsletter">{{trans('admin.newsletter')}}:</label>
  <div class="col-sm-10">
  {!! Form::checkbox('newsletter',1, true, ['class' => '']) !!}

  </div>
</div>					    								    										    								   	
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.status')}}:</label>
  <div class="col-sm-10">
  	{!! Form::select('status', ['1'=>trans('admin.active'),'0'=>trans('admin.inactive')],null, ['class' => 'form-control']) !!}    
    
  </div>							      
</div>