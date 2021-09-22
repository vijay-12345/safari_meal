<div class="inner-page-data">
	<div class="container">
		<div class="full-page-data">
			<div class="page-data-header">
				<i class="flaticon-security"></i>
				{{$data->title}}
			</div>
			<div class="data-padding">	
				@if($data->page_urlalias !='contact-us' && $data->page_urlalias !='help')
					<?php echo $data->description; ?>
				@else
				
				<?php echo $data->description; ?>
				
				{!! Form::open(['role'=>'form','id'=>'contact-form','url' => 'page/contact-us']) !!}	
					@if(Session::has('flash_message'))
					    <div class="alert alert-success">
					        {{ Session::get('flash_message') }}	   
					    </div>
					@endif

					@if( Session::has('error') )
					    <div class="alert alert-danger">
					        <p>{{ Session::get('error') }}</p>
					    </div>
					@endif
				    
				    <div class="controls">

				        <div class="row">
				            <div class="col-md-6">
				                <div class="form-group">
				                    <label for="form_name">Firstname *</label>
				                    {!! Form::text('fname', null, ['class' => 'form-control','placeholder'=>'Please enter your firstname *','data-error'=>'Firstname is required.','required'=>'required']) !!}  
				                </div>
				            </div>
				            <div class="col-md-6">
				                <div class="form-group">
				                    <label for="form_lastname">Lastname *</label>
				                    {!! Form::text('lname', null, ['class' => 'form-control','placeholder'=>'Please enter your lastname *','data-error'=>'Lastname is required.','required'=>'required']) !!}  
				                    
				                </div>
				            </div>
				        </div>
				        <div class="row">
				            <div class="col-md-6">
				                <div class="form-group">
				                    <label for="form_email">Email *</label>
				                    {!! Form::email('email', null, ['class' => 'form-control','placeholder'=>'Please enter your email *','data-error'=>'Valid email is required.','required'=>'required']) !!}
				                </div>
				            </div>
				            <div class="col-md-6">
				                <div class="form-group">
				                    <label for="form_phone">Type *</label>
				                    {!! Form::select('type', ['' => '---Select Type---','complaint' => 'complaint', 'contact' => 'contact'], '', ['class' => 'form-control', 'required'=>'required']) !!}
				                </div>
				            </div>
				        </div>
				        <div class="row">
				            <div class="col-md-6">
				                <div class="form-group">
				                    <label for="form_phone">Phone</label>
				                    {!! Form::text('phone', null, ['class' => 'form-control','placeholder'=>'Please enter your phone']) !!}
				                </div>
				            </div>
				        </div>
				        <div class="row">
				            <div class="col-md-12">
				                <div class="form-group">
				                    <label for="form_message">Message *</label>
				                    {!! Form::textarea('message', null, ['class' => 'form-control','placeholder'=>'Message for us *','rows'=>4,'required'=>'required','data-error'=>'Please,leave us a message.']) !!}  
				                    
				                </div>
				            </div>
				            <div class="col-md-12">
				                <input type="submit" class="btn btn-success btn-send" value="Send message">
				            </div>
				        </div>
				        <div class="row">
				            <div class="col-md-12">
				                <p class="text-muted"><strong>*</strong> These fields are required.</p>
				            </div>
				        </div>
				    </div>

				{!! Form::close() !!}
				@endif			
							
			</div>
		</div>
	</div>
</div><!--/inner page data-->