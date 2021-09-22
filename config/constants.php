<?php

return [
    'order' => [
		'noconfirm'=>0,
        'pending' => 1,
        'confirmed'=>7, 
        'open_for_drivers' => 2,
        'accepted_by_driver' => 3,        
        'dispatched' => 4,
        'delivered'=>5,
        'cancelled'=>6       
    ],
    'order_status_label'=>[
    	'admin'=>[
    		'1'=>'Pending',
    		'7'=>'Confirmed',
    		'2'=>'Open for drivers',
    		'3'=>'Accepted by driver',
    		'4'=>'Dispatched',
    		'5'=>'Delivered',
    		'6'=>'Cancelled'
    	],
     	'weborapp'=>[
    		'1'=>'Processing',
    		'2'=>'Preparing',
    		'3'=>'Preparing',
    		'4'=>'On the way',
    		'5'=>'Delivered',
    		'6'=>'Cancelled'
    	], 
      	'driver'=>[
    		'1'=>'Pending',
    		'2'=>'Pending',
    		'3'=>'Accepted',
    		'4'=>'Picked Up',
    		'5'=>'Delivered',
    		'6'=>'Cancelled'
    	]   	  	
    ],
    'user'=> [
        'customer'      => 3,
        'area_manager'  => 5,
        'super_admin'   => 1,
        'admin'         => 2,
        'driver'        => 4,
        'restaurant'    => 6
    ],
    'paginate_limit_option' => [2, 10, 20, 30, 40, 50, 70, 100],
    'language_map'  => [
        'en'    => 'English',
        'fr'    => 'French',
        'so'    => 'Somali'
    ],
    'image' => [
        'for' =>  [
            'restaurant'    =>  'restaurant',
            'menu'          =>  'menu',
            'product'       =>  'product',
            'user'          =>  'user'
        ]
    ],
    'driver' =>  [
        'radius' =>  4000
    ],
    'search_m' => 100,
    'optiongroup'   =>  [
        'type' => [
            'checkbox'  =>  'Checkbox',
            'radio'     =>  'Radio'
        ],
        'required'  =>  [
            'Y' =>  'Yes',
            'N' =>  'No'
        ]
    ],
    'timing'  =>  [
        'options' => [
            '1'=>'Monday',
            '2'=>'Tuesday',
            '3'=>'Wednesday',
            '4'=>'Thursday',
            '5'=>'Friday',
            '6'=>'Saturday',
            '7'=>'Sunday'            
        ]
    ],
    'page'  =>  [
        'nodelete' => ['terms-and-conditions','privacy-policy','about-foodbox','help','contact-us']
    ],
    'administrator' => [
        'email'     => 'info@taxiyefood.com',
        'mobile'    =>  '+252-618000165'
    ],
    'currency' => '$', //"&#x20B9;",
    'min_order' => 100, // amount,
    'delivery_charge'=>'FREE',
    'delivery_time'=>'45', // in minutes  
    'site_name'=>'Taxiye Food',
    'google_api_key' => 'AIzaSyAB3fKkM-Kuw9Xx-qpCcYIfJh71qTGNsGk',
    'email' => [
        'contact'   => 'contactus@taxiyefood.com',
        'care'      => 'customercare@taxiyefood.com'
    ]
];
