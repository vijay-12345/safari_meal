<?php

$prefix = \Request::segment(2);

if(Session::get('access.role') == 'admin') { ?>
	
	<ul>
		<li><a href="<?php echo url('admin/dashboard'); ?>" title="">
			{{trans('admin.dashboard')}}
			<!-- <span class="flaticon-arrows-1"></span> -->
		</a></li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['restaurant','coupon','review','menu'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['restaurant','coupon','review','menu'])) echo true; ?>">Restaurants <span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url('admin/restaurant'); ?>">{{trans('admin.restaurant.list')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/menu'); ?>">Menu
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/review'); ?>">{{trans('admin.review')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/coupon'); ?>">{{trans('admin.coupons')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['homecooked','homecookedcoupon','homecookedreview','homecookedmenu'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['restaurant','homecookedcoupon','homecookedreview','homecookedmenu'])) echo true; ?>">Home Cooked <span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url('admin/homecooked'); ?>">{{trans('admin.restaurant.list')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/homecookedmenu'); ?>">Menu
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/homecookedreview'); ?>">{{trans('admin.review')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/homecookedcoupon'); ?>">{{trans('admin.coupons')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['order'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['order'])) echo true; ?>">Orders<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
			<li><a href="<?php echo url('admin/order'); ?>">{{trans('admin.order.list')}}
				<!-- <span class="flaticon-arrows-1"></span> -->
			</a></li>
			<li><a href="<?php echo url('admin/order/add'); ?>">{{trans('admin.add.order')}}
				<!-- <span class="flaticon-arrows-1"></span> -->
			</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['table'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['table'])) echo true; ?>">Tables<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
			<li><a href="<?php echo url('admin/table'); ?>">{{trans('admin.table.list')}}
				<!-- <span class="flaticon-arrows-1"></span> -->
			</a></li>
			<!--<li><a href="<?php echo url('admin/table/add'); ?>">{{trans('admin.add.table')}}
				<!-- <span class="flaticon-arrows-1"></span> -->
			<!--</a></li>--->
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['areamanager','customer','driver'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['areamanager','customer','driver'])) echo true; ?>">Users <span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
			<!--<li><a href="<?php //echo url('admin/areamanager'); ?>">{{trans('admin.area.manager')}}<span class="flaticon-arrows-1"></span></a></li>-->
				<li><a href="<?php echo url('admin/customer'); ?>">{{trans('admin.customer')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/driver'); ?>">{{trans('admin.driver')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['cuisine'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['cuisine','menu'])) echo true; ?>">Category 
				<span class="flaticon-arrows-1"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url('admin/cuisine'); ?>">{{trans('admin.cuisine')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<!-- <li><a href="<?php echo url('admin/menu'); ?>">Menu
					<span class="flaticon-arrows-1"></span>
				</a></li> -->
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['addon_group'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['addon_group'])) echo true; ?>">Addon Group <span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url('admin/addon_group'); ?>">{{trans('admin.group.list')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/addon_group/add'); ?>">{{trans('admin.add.group')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['product'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['product'])) echo true; ?>">Products<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url('admin/product'); ?>">{{trans('admin.product.list')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/product/add'); ?>">{{trans('admin.add.product')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open {{ Request::segment(3) == 'reports' ? 'open' : ''}}">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="{{ Request::segment(3) == 'reports'? true : '' }}">{{trans('admin.reports.reports')}}<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="{{ url('admin/reports/payment') }}">{{trans('admin.reports.payment')}}</a></li>
				<li><a href="{{ url('admin/reports/customer') }}">{{trans('admin.reports.customer')}}</a></li>
				<li><a href="{{ url('admin/reports/sales') }}">{{trans('admin.reports.sales')}}</a></li>
				<li><a href="{{ url('admin/reports/restaurant') }}">{{trans('admin.reports.restaurant')}}</a></li>
				<li><a href="{{ url('admin/reports/driver') }}">{{trans('admin.reports.driver')}}</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['page','newsletter'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"  aria-expanded="<?php if(in_array(Request::segment(3),['page','newsletter'])) echo true; ?>">Other <span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url('admin/newsletter'); ?>">{{trans('admin.newsletter')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url('admin/page'); ?>">{{trans('admin.cms')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>

				<li>
					<a href="{{ url('admin/setting') }}">Setting
				</a></li>
			</ul>
		</li>

	</ul>

<?php

} else if(Session::get('access.role') == 'manager') {

?>
	<ul>
		<li><a href="<?php echo url($prefix.'/dashboard'); ?>" title="">
			{{trans('admin.dashboard')}}
			<!-- <span class="flaticon-arrows-1"></span> -->
		</a></li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['restaurant','coupon','review'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['restaurant','coupon','review'])) echo true; ?>">Restaurants <span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/restaurant'); ?>">{{trans('admin.restaurant.details')}}
					<span class="flaticon-arrows-1"></span>
				</a></li>		
				<li><a href="<?php echo url($prefix.'/review'); ?>">{{trans('admin.reviews')}}
					<span class="flaticon-arrows-1"></span>
				</a></li>
				<li><a href="<?php echo url($prefix.'/coupon'); ?>">{{trans('admin.coupons')}}
					<span class="flaticon-arrows-1"></span>
				</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['areamanager','customer','driver'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['areamanager','customer','driver'])) echo true; ?>">User<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/areamanager'); ?>">{{trans('admin.area.manager')}}
					<span class="flaticon-arrows-1"></span>
				</a></li>
			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['order'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['order'])) echo true; ?>">Orders<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/order'); ?>">{{trans('admin.order.list')}}
					<span class="flaticon-arrows-1"></span>
				</a></li>
				<li><a href="<?php echo url($prefix.'/order/add'); ?>">{{trans('admin.add.order')}}
					<span class="flaticon-arrows-1"></span>
				</a></li>
			</ul>
		</li>
		
		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['product'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['product'])) echo true; ?>">Products<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/product'); ?>">{{trans('admin.product.list')}}
					<span class="flaticon-arrows-1"></span>
				</a></li>
				<li><a href="<?php echo url($prefix.'/product/add'); ?>">{{trans('admin.add.product')}}
					<span class="flaticon-arrows-1"></span>
				</a></li>
			</ul>
		</li>		
	</ul>
	
<?php

} else if(Session::get('access.role') == 'restaurant') {

?>
	<ul>
		<li>
			<a href="<?php echo url($prefix.'/dashboard'); ?>" title="">{{trans('admin.dashboard')}}
				<!-- <span class="flaticon-arrows-1"></span> -->
			</a>
		</li>

		
		<!-- vijayanand -->
		<li><a href="<?php echo url($prefix.'/menu'); ?>">{{trans('admin.menu')}}
			<!-- <span class="flaticon-arrows-1"></span> -->
		</a></li>

        <?php if(Session::get('access.is_home_cooked') == '0') {?>
		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['restaurant','coupon','review'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['restaurant','coupon','review'])) echo true; ?>">Settings <span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/restaurant'); ?>">{{trans('admin.restaurant.details')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>							
				<li><a href="<?php echo url($prefix.'/coupon'); ?>">{{trans('admin.coupons')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>


			</ul>
		</li>
        <?php } ?>	
        <?php if(Session::get('access.is_home_cooked') == '1') {?>
		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['restaurant','coupon','review'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['restaurant','coupon','review'])) echo true; ?>">Settings <span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/homecooked'); ?>">{{trans('admin.restaurant.details')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>							
				<li><a href="<?php echo url($prefix.'/coupon'); ?>">{{trans('admin.coupons')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>

			</ul>
		</li>
        <?php } ?>	
		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['order'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['order'])) echo true; ?>">Orders<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/order'); ?>">{{trans('admin.order.list')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url($prefix.'/order/add'); ?>">{{trans('admin.add.order')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>



			</ul>
		</li>

		<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['product'])) echo 'open'; ?>">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['product'])) echo true; ?>">Products<span class="flaticon-arrows-1"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/product'); ?>">{{trans('admin.product.list')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<li><a href="<?php echo url($prefix.'/product/add'); ?>">{{trans('admin.add.product')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
			</ul>
		</li>
		<?php if(Session::get('access.is_home_cooked') == '0') {?>
			<li class="dropdown keep-open <?php if(in_array(Request::segment(3),['table'])) echo 'open'; ?>">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="<?php if(in_array(Request::segment(3),['table'])) echo true; ?>">Tables<span class="flaticon-arrows-1"></span></a>
				<ul class="dropdown-menu">
				<li><a href="<?php echo url($prefix.'/table'); ?>">{{trans('admin.table.list')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				</a></li>
				<!--<li><a href="<?php echo url('admin/table/add'); ?>">{{trans('admin.add.table')}}
					<!-- <span class="flaticon-arrows-1"></span> -->
				<!--</a></li>--->
				</ul>
			</li>
		<?php } ?>	
		<!-- <li><a href="{{ url($prefix.'/reports') }}">{{trans('admin.reports.reports')}}</a></li> -->
	</ul>
<?php
	}
?>