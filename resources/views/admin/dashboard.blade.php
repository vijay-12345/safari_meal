@extends('admin.layout')
@section('title', trans('admin.dashboard'))
@section('content')

<?php 
	use App\Order, App\Product;
	$prefix = \Request::segment(2);
?>

<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
			</div>
			
			<div class="col-md-9">
				<div class="page-data-right">
					<div class="date-range-box">
						<div class="date-range-filter">
						   <form name="section1_filter">
								<label for="from">From</label>
								<input type="text" id="from" name="from" value="<?php echo date('Y').'-01-01'; ?>">
								<label for="to">To</label>
								<input type="text" id="to" name="to" value="<?php echo date('Y-m-d'); ?>">
								<button type="submit" class="btn btn-primary" value="Filter">Filter</button>
							</form>				
						</div>
						<div class="db-status clearfix">						
							<div class="status-box box1">
								<h3><?php echo Config::get('constants.currency') ?><span id="sales_amount"></span></h3>
								<div class="icon-text"><span class="fa fa-shopping-cart"></span><span>{{trans('admin.sales')}}</span></div>
							</div>
							<div class="status-box box2">
								<h3 id="order_count"></h3>
								<div class="icon-text"><span class="glyphicon glyphicon-tasks"></span><span>{{trans('admin.orders')}}</span></div>
							</div>
							<div class="status-box box3">
								<h3 id="pending_order_count"></h3>
								<div class="icon-text"><span class="glyphicon glyphicon-send"></span><span>{{trans('admin.pending.orders')}}</span></div>
							</div>	
							<div class="status-box box3">
								<h3 id="tablebook_count"></h3>
								<div class="icon-text"><span class="glyphicon glyphicon-send"></span><span>{{trans('admin.table_book')}}</span></div>
							</div>	
							<div class="status-box box4">
								<h3 id="reviews_count"></h3>
								<div class="icon-text"><span class="glyphicon glyphicon-star"></span><span>{{trans('admin.reviews')}}</span></div>
							</div>
						</div>
					</div>

					<div class="charts-cont">
						<div class="row">
							<div class="col-md-6">
								<div class="chart-widget">
									<!-- <div class="title">Quick statistics</div> -->
									<div class="chart-holder">
										<canvas id="chartfirst"  height="200" width="400"/>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="chart-widget">
									<!-- <div class="title">Member Statistics</div> -->
									<div class="chart-holder chartsec per60">
										<canvas id="chartsec" width="300" height="200"/>
									</div>
								</div>
							</div>
						</div>
					</div><!--Charts Cont-->
			
				</div>
			</div>
		</div>
	</div>
</div>

<!--<script type="text/javascript" src="{{ asset('js/Chart.min.js') }}"></script> -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script> 
<script>
	/**Chart first**/ 
	var ctxfirst = document.getElementById("chartfirst").getContext("2d");	
	var currency = "<?php echo Config::get('constants.currency'); ?>";
	//****************Second pie chart************************
	var ctxsecond = document.getElementById("chartsec").getContext("2d");
</script>
<!-- ************************Date range script******************************** -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>

 	$(document).ready(function(){
   		var dateFormat = "yy-mm-dd",
      	from = $( "#from" )
	        .datepicker({
	          	defaultDate: "+1w",
	          	changeMonth: true,
	          	numberOfMonths: 1,
	          	dateFormat:dateFormat
	        })
	        .on( "change", function() {
	          	to.datepicker( "option", "minDate", getDate( this ) );
	        }),

      	to = $( "#to" ).datepicker({
		        defaultDate: "+1w",
		        changeMonth: true,
		        numberOfMonths: 1,
		        dateFormat:dateFormat
	      	})
	      	.on( "change", function() {
        		from.datepicker( "option", "maxDate", getDate( this ) );
	      	});
 		
    	function getDate( element ) {
      		var date;
	      	try {
		        date = $.datepicker.parseDate( dateFormat, element.value );
	      	} catch( error ) {
		        date = null;
	      	}
      		return date;
    	}
 	});

 	/* Event on date range */ 
 	$(document).ready(function(){  	
 		init();
	 	$("form[name='section1_filter']").submit(function(e) {
	 		e.preventDefault();
		 	var from = $("#from").val();
		 	var to = $("#to").val();
		    var fromObj = new Date(from);
		    var from_y = fromObj.getFullYear();	
		    var toObj = new Date(to);
		    var to_y = toObj.getFullYear();	
		    if(from_y != to_y){
		    	alert('From year and To year must be same.');
		    	return false;
		    }
	 		var dataForm = $(this).serialize(); 		
	 		getSection1Data(dataForm); 		
	 	});
 	});

 	function getSection1Data(dataForm) { 	
		$.ajax({
			url:baseUrl+'ajax/dashboard-section1-filter',
			type:'POST',
			dataType:'json',
			data:dataForm,
			success:function(res){ 				
				$("#pending_order_count").html(res.data.order_pending);
				$("#order_count").html(res.data.order_count);  				
				$("#sales_amount").html(res.data.sales_amount);
				$("#reviews_count").html(res.data.reviews_count);
				$("#tablebook_count").html(res.data.tablebook_count);
				//pie chart			
				updatePieChart(res.data.pie_data);
				// line chart
				updateLineChart(res.data.line_data);
			},
			error:function(error){ 				
				alert(error);
			}
		});	
 	}

 	function init(){
	 	//section1 init
	 	var from = $("#from").val();
	 	var to = $("#to").val();
	 	getSection1Data('from='+from+'&to='+to);  	
 	}

 	var chartsec_i = 0, chartSecObj;

 	function updatePieChart(pieData){ 
	 	if(chartsec_i>0) {
	 		chartSecObj.destroy();
	 	} else {
	 		chartsec_i++;
	 	}
		chartSecObj = new Chart(ctxsecond, {
		    data: {
			    datasets: [{
			        data: pieData,
			        backgroundColor: [
			            "#4D5360",
			            "#949FB1",
			            "#FDB45C",
			            "#5AD3D1",
			            "#008000"           
			        ]
			    }],
			    labels: [
			        "Received",
			        "Delivered",
			        "Pending",
			        "Pending Pickup",
			        "Dispatched"        
			    ]
			},
		    type: "pie",
		    options: {
		        elements: {
		            arc: {
		                borderColor: "#000000"
		            }
		        }
		    },
		});
 	}

  	var chartfirst_i = 0,charFirstObj;

 	function updateLineChart(lineData) {
	 	if(chartfirst_i>0) {
	 		charFirstObj.destroy();
	 	} else {
	 		chartfirst_i++;
	 	} 	
		charFirstObj = new Chart(ctxfirst, {
		    type: 'line',
		    data: {
		      labels : lineData.x_label,
		      datasets: [
		           {
		                label: lineData.search_text,
		                backgroundColor: "rgba(179,181,198,0.2)",
		                borderColor: "rgba(179,181,198,1)",
		                pointBackgroundColor: "rgba(179,181,198,1)",
		                pointBorderColor: "#fff",
		                pointHoverBackgroundColor: "#fff",
		                pointHoverBorderColor: "rgba(179,181,198,1)",
		                data: lineData.y_label
		            }
		        ]       
		    },
		    options: {
		        title: {
		            display: true,
		            text: lineData.heading
		        }
		    }
		}); 	
 	}
 </script>
@endsection

