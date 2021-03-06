<br />
<script src="//cdn.ckeditor.com/4.5.9/standard/ckeditor.js"></script>
<div class="form-group">
  <label class="control-label col-sm-2" for="title">{{trans('admin.title')}}*:</label>
  <div class="col-sm-10">
    {!! Form::text('title',null, ['class' => 'form-control']) !!}  
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="page_urlalias">{{trans('admin.urlalias')}}:</label>
  <div class="col-sm-10">
    @if(empty($page))
      {!! Form::text('page_urlalias',null, ['class' => 'form-control','placeholder'=>trans('System will automatically create using title')]) !!} 
    @else
      {!! Form::text('page_urlalias',null, ['class' => 'form-control','placeholder'=>trans('System will create using title'),'readonly'=>true]) !!}     
    @endif

     
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="description">{{trans('admin.description')}}*:</label>
  <div class="col-sm-10">
    {!! Form::textarea('description',null, ['class' => 'form-control ckeditor']) !!}  
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="lang_id">{{trans('admin.language')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('lang_id',\Config::get('app.langs_options') ,\Config::get('app.locale_prefix'), ['class' => 'form-control']) !!}    
    
  </div>                    
</div>					    								    										    								   	
<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.status')}}:</label>
  <div class="col-sm-10">
  	{!! Form::select('status', ['1'=>trans('admin.active'),'0'=>trans('admin.inactive')],null, ['class' => 'form-control']) !!}    
    
  </div>							      
</div>
<script>
CKEDITOR.replace( 'description', {
    format_tags: 'p;h1;h2;h3;h4;h5;h6;pre;div'
});
</script>