<?php
use App\FooterLinks;
$footerLinks = FooterLinks::lang()->where(['status'=>1])->orderBy('sort','asc')->get();
?>
<footer>
	<div class="container">
		<div class="row">
			<div class="col-sm-3">
				<div class="footer-widget">
					<h4>Popular Areas</h4>
					<ul class="list-style">
						@foreach($footerLinks as $key=>$v)
							@if($v->item_type=='popular_areas')
							<li><a href="{{url($v->url)}}">{{$v->name}}</a></li>
							@endif
						@endforeach						
					</ul>
				</div>
			</div>

			<div class="col-sm-3">
				<div class="footer-widget">
					<h4>Popular Cuisines</h4>
					<ul class="list-style">
						@foreach($footerLinks as $key=>$v)
							@if($v->item_type=='popular_cuisines')
							<li><a href="{{url($v->url)}}">{{$v->name}}</a></li>
							@endif
						@endforeach						

					</ul>
				</div>
			</div>

			<div class="col-sm-3">
				<div class="footer-widget">
					<h4>Popular Restaurants</h4>
					<ul class="list-style">
						@foreach($footerLinks as $key=>$v)
							@if($v->item_type=='popular_restaurants')
							<li><a href="{{url($v->url)}}">{{$v->name}}</a></li>
							@endif
						@endforeach
					</ul>
				</div>
			</div>

			<div class="col-sm-3">
				<div class="footer-widget">
					<h4>Taxiye Food</h4>
					<ul class="list-style">
						<li><a href="{{url('page/about-safarimeals')}}" title="">{{trans('footer.about.foodbox')}}</a></li>
						<li><a href="{{url('page/contact-us')}}" title="">{{trans('footer.contact')}}</a></li>
						<li><a href="{{url('page/terms-and-conditions')}}" title="">{{trans('footer.terms.and.conditions')}}</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
					<p>© {{trans('footer.copyright')}} {{date('Y')}} {{trans('footer.foodbox.is.a.registered.trademark')}}</p>
				</div>
				<div class="col-sm-6">
					
				</div>
			</div>
		</div>
	</div>
</footer>