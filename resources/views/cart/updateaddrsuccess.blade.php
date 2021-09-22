<?php  
use App\Product;
?>

 	
<div class="updateaddress">	
	<div class="modal-header">
		<button data-dismiss="modal" class="close" type="button">×</button>	    
		<h4 class="modal-title">Edit Address </h4>
	</div> 	
	<div class="modal-body">  
		 @if ($status)
		<div class="alert alert-success">
		{{ $status }}
		</div>
		@endif                                                    						                
	</div>							                
</div>

