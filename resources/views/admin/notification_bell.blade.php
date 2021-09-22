<?php

	$notification_count = 0; // \App\Order::getNotifications();
	$prefix = \Request::segment(2);
?>

<li>
	<a href="{{ url($prefix.'/order?status=open&date=all') }}">
		<i class="fa fa-fw fa-bell"></i>
		Order(s) <span class="badge"><span class="notification_count">{{ $notification_count }}</span> New</span>
	</a>
</li>

<?php
	$soundUrl = rtrim(url(), Config::get('app.locale_prefix'));
    $soundUrl = $soundUrl . 'sound/uber_beep.mp3';
	// $soundUrl = $soundUrl . 'sound/jingle-bells-sms.ogg';
?>

<audio id="notification-audio" controls style="display: none">
  	<source src="{{ $soundUrl }}" type="audio/ogg">
</audio>