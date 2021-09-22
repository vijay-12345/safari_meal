<br />
{!! Form::hidden('restaurant_id',$restaurant_id) !!}
<div class="form-group">
    <label class="control-label col-sm-2" for="weekday">{{trans('admin.weekday')}}*:</label>
    <div class="col-sm-10">
        {!! Form::select('weekday',\Config::get('constants.timing.options') ,null, ['class' => 'form-control']) !!}    
    </div>                    
</div>
<div class="form-group">
    <label class="control-label col-sm-2" for="open">{{trans('admin.open.time')}}*:</label>
    <div class="col-sm-10">
        {!! Form::text('open', null, ['class' => 'datetimepicker2 form-control add-on', 'data-format'=>"HH:mm:ss"]) !!}
    </div>                    
</div>
<div class="form-group">
    <label class="control-label col-sm-2" for="closing">{{trans('admin.closing.time')}}*:</label>
    <div class="col-sm-10">
        {!! Form::text('closing', null, ['class' => 'datetimepicker2 form-control add-on', 'data-format'=>"HH:mm:ss"]) !!}
    </div>                    
</div> 
<div class="form-group">
    <label class="control-label col-sm-2" for="delivery_start">{{trans('admin.delivery.start')}}*:</label>
    <div class="col-sm-10">
          {!! Form::text('delivery_start', null, ['class' => 'datetimepicker2 form-control add-on', 'data-format'=>"HH:mm:ss"]) !!}
    </div>                    
</div>

<div class="form-group">
    <label class="control-label col-sm-2" for="delivery_end">{{trans('admin.delivery.end')}}*:</label>
    <div class="col-sm-10">
        {!! Form::text('delivery_end', null, ['class' => 'datetimepicker2 form-control add-on', 'data-format'=>"HH:mm:ss"]) !!}
    </div>                    
</div>  