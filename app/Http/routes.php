<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
 *  Set up locale and locale_prefix if other language is selected
 */
if (in_array(Request::segment(1), Config::get('app.alt_langs'))) { 
    App::setLocale(Request::segment(1));
    Config::set('app.locale_prefix', Request::segment(1));        
} else if(!in_array(Request::segment(1), Config::get('app.alt_langs')) && Request::segment(1) != 'fbredirect' && Request::segment(1) !='captcha') {
    Config::set('app.locale_prefix', 'en');
    header('Location: '. URL::to('/'));
    exit;
}

Form::macro('errorMsg', function($field, $errors) {
    if($errors->has($field)) {
        $msg = $errors->first($field);
        return "<span class='alert-danger'>$msg</span>";
    }
    return '';
});

Route::get('fbredirect', 'FbredirectController@index');

Route::group(array('prefix' => Config::get('app.locale_prefix')), function()
{
    
    /*----------common routes for restaurant and admin---------------*/
    // Get orders count for notification bar
    Route::get('order/get-count', 'Admin\OrderController@getOrdersCount');
    // Route::get('firebase', 'FirebaseController@index');
    /*----------common routes for restaurant and admin---------------*/
    
	Route::get('/', 'HomeController@index');
	Route::get('home', 'HomeController@index');
	Route::get('page/{slug}', 'PageController@index');
    Route::post('page/{slug}', 'PageController@index');
	Route::get('auth/verify','Auth\AuthController@confirm');    
    Route::post('auth/verify-number', 'Auth\AuthController@verifyNumber');
    Route::controllers([
        'auth' => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]);
    
	Route::get('temp', 'FbredirectController@temp');
	Route::resource('fbredirect/facebook', 'FbredirectController@facebook');
    Route::get('editprofile', 'HomeController@editProfile');
    Route::post('updateprofile', 'HomeController@updateProfile');
    Route::get('changepassword', 'HomeController@changePassword');
    Route::post('updatepassword', 'HomeController@updatePassword');
    Route::get('order-history', 'HomeController@orderHistory');
    Route::get('tablebook-history', 'HomeController@tablebookingHistory');
    Route::get('addressbook', 'HomeController@addressBook');
    Route::post('updateaddressbook', 'HomeController@updateAddressBook');
    Route::post('deleteaddress', 'HomeController@deleteAddress');
    Route::get('deleteaddress', 'HomeController@deleteAddress');
    Route::post('addnewaddressbook', 'HomeController@addNewAddressBook'); 
    Route::get('loadnewaddressbook', 'HomeController@loadNewAddressBook');    
    Route::get('loadupdatedaddressbook', 'HomeController@loaduUpdatedAddressBook');    
	Route::get('home/registration_successful', 'HomeController@registrationSuccessful');   
    
    Route::get('restaurentdetail/{restaurantUrl}','RestaurentController@restaurentdetail');
    Route::get('restaurentlist/{restaurantUrl}','RestaurentController@restaurentList');
    Route::get('restaurentlist','RestaurentController@restaurentList');
    Route::post('review/post','RestaurentController@reviewpost');   
    
    
    Route::get('tablebooking/{restaurantUrl}','TableController@tablebook');
    Route::post('/table/add', 'TableController@add'); // add
    Route::post('/table/update-table-status/', 'TableController@add');
    
    Route::post('ajax/checkcoupanvalid', 'RestaurentController@checkcoupanvalid');
    Route::get('ajax/checkcoupanvalid', 'RestaurentController@checkcoupanvalid');
    Route::get('morecuisines','RestaurentController@morecuisines'); 
    Route::post('addtocart','RestaurentController@addtocart');    
    Route::get('proceedtocheckout/{restaurantUrl}','RestaurentController@proceedToCheckout');      
    Route::post('proceedtocheckout/{restaurantUrl}','RestaurentController@proceedToCheckout');      
    Route::get('proceedtosecurecheckout/{ordernumber}','RestaurentController@proceedToSecureCheckout');      
    Route::post('proceedtosecurecheckout/{ordernumber}','RestaurentController@proceedToSecureCheckout');      
    Route::get('thanks/{ordernumber}','RestaurentController@thanks');      
    Route::get('updatecheckout','RestaurentController@updateCheckout'); 
    Route::post('createorder','RestaurentController@createOrder');      
    Route::post('/ajax/getstates','AjaxController@getStates');          
    Route::post('/ajax/getcities','AjaxController@getCities');
    Route::post('ajax/saveareas', 'AjaxController@saveareas');          
    Route::post('ajax/getareas', 'AjaxController@getareas');
    Route::post('ajax/getareasnonjsn', 'AjaxController@getareasnonjsn');   
    Route::post('ajax/getCustomer', 'AjaxController@getCustomer');   
    Route::post('ajax/optdeliveryaddress', 'AjaxController@optDeliveryAddress');   
    Route::post('ajax/getCustomer', 'AjaxController@getCustomer'); 
    Route::post('ajax/getRestaurant', 'AjaxController@getRestaurant'); 
    Route::get('ajax/getRestaurant', 'AjaxController@getRestaurant'); 
    Route::post('ajax/getproduct', 'AjaxController@getProductByRestaurantId');
    Route::post('ajax/subscribe', 'AjaxController@subscribe');
    Route::post('ajax/dashboard-section1-filter', 'AjaxController@section1Filter');
    
    Route::post('/ajax/count-cart-product','AjaxController@countCartProduct');
    Route::post('/ajax/get-menu-list','AjaxController@getmenulist');

});


/**
 * For reference, to be used inside language blocks
 * 
    Route::get( 
        '/{contact}/',
        function () {
            return Lang::get('test.password')."--contact page ".App::getLocale();
        }
    )->where('contact', Lang::get('routes.contact'));
**/


/*
 * 
 * API routing 
 * check this: https://laravel.com/docs/5.2/controllers#restful-resource-controllers
 * 
 */

// Route::group([    
//     'namespace' => 'Api',    
//     'prefix' => 'password'
// ], function () {    
//     Route::post('create', 'PasswordResetController@create');
//     Route::get('find/{token}', 'PasswordResetController@find');
//     Route::post('reset', 'PasswordResetController@reset');
// });

//Route::get('/send/email', 'HomeController@mail');

Route::group(['prefix' => Config::get('app.locale_prefix').'/api/v1', 'namespace' => 'Api'], function()
{

    //vijay anand
    //Route::get('send/email', 'LoginController@mail');
    // Route::post('restaurant/viewes','MenuRestaurantController@add');
    // Route::get('restaurant/viewes','MenuRestaurantController@add');
    //Route::get('helloworld','TestController@add');

    
    Route::post('update','UserController@update');
    Route::post('logout','UserController@logout');
    Route::post('delete-address','UserController@deleteAddress');
    Route::get('delete-address','UserController@deleteAddress');
    Route::post('changepassword','UserController@changePassword');
    Route::post('restaurant/mobil-forget-password','LoginController@forgetpasswordfunction');
    Route::post('restaurant/mobil-signin','LoginController@loginfunction');
    Route::post('restaurant/mobil-signout','LoginController@logoutfunction');
    //Route::post('restaurant/mobil-forget-password','LoginController@forgetpasswordfunction');
    Route::post('restaurant/mobil-order-list','OrderlistController@orderList');
    Route::post('restaurant/mobil-update-order-status','UpdateorderController@updateOrderStatus');
    Route::post('restaurant/mobil-driver-list','DriverlistController@driverList');
    Route::post('restaurant/mobil-device-token-update','LoginController@DeviceTokenUpdateFunction');


    //driver //vijayanand
    Route::post('driver/signup','DriverlistController@register');
    Route::post('driver/update','DriverlistController@update');
    Route::post('driver/signout','DriverlistController@signout');
    Route::post('driver/changepassword','DriverlistController@changePassword');
    // Route::post('driver/decline','DriverlistController@decline');
    // Route::post('driver/mobil-order-list','DriverlistController@orderList');
    // Route::get('test-notification','DriverlistController@testNotification');



    //Route::resource('SignIn', 'Auth\AuthController@login');
    Route::resource('SignIn', 'Auth\AuthController@login');
    Route::resource('SignUp', 'Auth\AuthController@register');
    Route::post('check-number','Auth\AuthController@checkMobileNumber');
    Route::post('register-otp','Auth\AuthController@sendOtpBeforeRegister');
    
    //----------------------Area management-----------------
    Route::resource('restaurant/signin', 'Auth\AuthController@restaurantSignIn');
    Route::post('restaurant/order-list', 'Restaurant\RestaurantController@orderList');
    Route::post('restaurant/update-order-status', 'Restaurant\RestaurantController@updateOrderStatus');
    Route::post('restaurant/driver-list', 'Restaurant\RestaurantController@driverList');
    
    Route::resource('welcome', 'WelcomeController');
    Route::get('home/logout', 'HomeController@logout');
    Route::post('home/logout', 'HomeController@logout');
    Route::resource('home/logout', 'HomeController@logout');
    Route::resource('customer', 'CustomerController');
    Route::get('page/{slug}', 'CustomerController@page');
    Route::post('page/{slug}', 'CustomerController@page');
    Route::post('address', 'CustomerController@address');
    
    Route::resource('cuisine', 'CuisineController');
    Route::resource('CuisineList', 'CuisineController@cuisineList');
    Route::resource('MenuList', 'MenuController@menuList');
    
    Route::post('RestaurantList/{flag}','RestaurentController@restaurentList');
    Route::post('orderList','CustomerController@orderList');
    Route::post('order-detail','CustomerController@orderByOrderNumber');
    
    Route::post('restaurantfulldetails','RestaurentController@restaurantfulldetails');
    Route::post('Restaurant','RestaurentController@restaurantDetail');
    Route::post('Restaurant/{id}','RestaurentController@restaurantDetail');
    Route::get('Deals','RestaurentController@deals');
    Route::post('ApplyCoupon','RestaurentController@applyCoupon');
    Route::resource('ProductList', 'RestaurentController@productList');
    Route::get('CityList','RestaurentController@cityList');
    Route::post('AreaList','RestaurentController@areaList');
    
    Route::resource('SocialSignIn', 'SocialController@getSocialLogin');
    Route::resource('driver/signin', 'Auth\AuthController@driverSignIn');
    Route::resource('driver/OrderList', 'DriverController@orderList');
    Route::resource('driver/Update', 'DriverController@update'); 
    Route::resource('driver/OrderUpdate', 'DriverController@orderStatusUpdate');   
    Route::resource('driver/OrderByDriverId', 'DriverController@orderByDriverId');   
    
    Route::resource('addtocart', 'CartController@addToCart'); 
    Route::resource('add-order', 'CartController@addOrder');
    Route::resource('getCart', 'CartController@getCart');  
    Route::resource('clear', 'CartController@clear');  
    Route::resource('cancel-order', 'CartController@cancelOrder');
    
    Route::post('get-reviews','RestaurentController@getReviews');
    
    // Update users device token for push notification.
    Route::post('update-device-token','CustomerController@updateDeviceToken');
    
    Route::post('booktable','TableBookController@bookTable');
    Route::post('tablebookingList','CustomerController@tablebookList');
    Route::post('canceltable','CustomerController@updateTableStatus');
});


/*
 * 
 * Admin routing 
 * 
 */
// route to show the login form
Route::group(['prefix' => Config::get('app.locale_prefix').'/admin', 'namespace'=>'Admin'], function()
{
    Route::get('/', 'LoginController@getLogin');
    Route::post('/', 'LoginController@postLogin');
    Route::get('/logout', 'LoginController@getLogout');
});

Route::group(['middleware' => ['auth', 'admin'], 'prefix' => Config::get('app.locale_prefix').'/admin', 'namespace'=>'Admin'], function()
{
    Route::get('/dashboard', 'WelcomeController@index');     
    Route::post('/dashboard', 'WelcomeController@index');
    Route::get('/dashboard/edit/{id}', 'WelcomeController@edit');
    Route::post('/dashboard/update', 'WelcomeController@update');
    Route::get('/dashboard/delete/{id}', 'WelcomeController@delete');
    Route::post('/dashboard/update-order-status/{id}', 'WelcomeController@updateOrderStatus');
    Route::post('/dashboard/update-table-status/{id}', 'WelcomeController@updateTableStatus');

    //----------------------customer management-----------------
    Route::get('/customer', 'CustomerController@index'); //list
    Route::post('/customer', 'CustomerController@index'); // list

    Route::get('/customer/add', 'CustomerController@add'); // add
    Route::post('/customer/add', 'CustomerController@add'); // add

    Route::get('/customer/edit/{id}', 'CustomerController@edit'); // edit
    Route::post('/customer/update/{id}', 'CustomerController@update'); // edit
    Route::get('/customer/delete/{id}', 'CustomerController@delete'); // delete
    
    Route::get('/address/{id}', 'CustomerController@address'); // list
    Route::get('/address-add/{id}', 'CustomerController@addressAdd'); // add
    Route::post('/address-add/{id}', 'CustomerController@addressAdd'); // add
    Route::get('/address-edit/{id}', 'CustomerController@addressEdit'); // edit
    Route::post('/address-update/{id}', 'CustomerController@addressUpdate'); // update
    Route::get('/address-delete/{id}', 'CustomerController@addressDelete'); // update
    //----------------------End customer management-----------------

    //----------------------Area management-----------------
    Route::get('/areamanager', 'AreaManagerController@index'); //list
    Route::post('/areamanager', 'AreaManagerController@index'); // list

    Route::get('/areamanager/add', 'AreaManagerController@add'); // add
    Route::post('/areamanager/add', 'AreaManagerController@add'); // add

    Route::get('/areamanager/edit/{id}', 'AreaManagerController@edit'); // edit
    Route::post('/areamanager/update/{id}', 'AreaManagerController@update'); // edit
    Route::get('/areamanager/delete/{id}', 'AreaManagerController@delete'); // delete
    //----------------------End area management-----------------  
    //----------------------driver management-----------------
    Route::get('/driver', 'DriverController@index'); //list
    Route::post('/driver', 'DriverController@index'); // list

    Route::get('/driver/add', 'DriverController@add'); // add
    Route::post('/driver/add', 'DriverController@add'); // add

    Route::get('/driver/edit/{id}', 'DriverController@edit'); // edit
    Route::post('/driver/update/{id}', 'DriverController@update'); // edit
    Route::get('/driver/delete/{id}', 'DriverController@delete'); // delete
    //---------end   
    Route::get('/cuisine', 'CuisineController@index');
    Route::post('/cuisine', 'CuisineController@index'); // list
    Route::get('/cuisine/add', 'CuisineController@add'); // add
    Route::post('/cuisine/add', 'CuisineController@add'); // add

    Route::get('/cuisine/edit/{id}', 'CuisineController@edit'); // edit
    Route::post('/cuisine/update/{id}', 'CuisineController@update'); // edit
    Route::get('/cuisine/delete/{id}', 'CuisineController@delete'); // delete   
    //----------------------End menu management-----------------     
    Route::get('/menu', 'MenuController@index');
    Route::post('/menu', 'MenuController@index'); // list
    Route::get('/menu/add', 'MenuController@add'); // add
    Route::post('/menu/add', 'MenuController@add'); // add

    Route::get('/menu/edit/{id}', 'MenuController@edit'); // edit
    Route::post('/menu/update/{id}', 'MenuController@update'); // edit
    Route::get('/menu/delete/{id}', 'MenuController@delete'); // delete  

     //----------------------End home cooked menu management-----------------     
    Route::get('/homecookedmenu', 'HomeCookedMenuController@index');
    Route::post('/homecookedmenu', 'HomeCookedMenuController@index'); // list
    Route::get('/homecookedmenu/add', 'HomeCookedMenuController@add'); // add
    Route::post('/homecookedmenu/add', 'HomeCookedMenuController@add'); // add

    Route::get('/homecookedmenu/edit/{id}', 'HomeCookedMenuController@edit'); // edit
    Route::post('/homecookedmenu/update/{id}', 'HomeCookedMenuController@update'); // edit
    Route::get('/homecookedmenu/delete/{id}', 'HomeCookedMenuController@delete'); // delete  

    //-----------------------Start restaurant management------------
    Route::get('/restaurant', 'RestaurantController@restindex');
    Route::post('/restaurant', 'RestaurantController@restindex'); // list
    Route::get('/restaurant/add', 'RestaurantController@add'); // add
    Route::post('/restaurant/add', 'RestaurantController@add'); // add

    Route::get('/restaurant/edit/{id}', 'RestaurantController@edit'); // edit
    Route::post('/restaurant/update/{id}', 'RestaurantController@update'); // edit
    Route::get('/restaurant/delete/{id}', 'RestaurantController@delete'); // delete 

    Route::get('/restaurant/access/{id}', 'RestaurantController@access'); // edit
    Route::post('/restaurant/access/{id}', 'RestaurantController@access'); // update


    //-----------------------Start home cooked management------------
    Route::get('/homecooked', 'HomecookedController@index');
    Route::post('/homecooked', 'HomecookedController@index'); // list
    Route::get('/homecooked/add', 'HomecookedController@add'); // add
    Route::post('/homecooked/add', 'HomecookedController@add'); // add

    Route::get('/homecooked/edit/{id}', 'HomecookedController@edit'); // edit
    Route::post('/homecooked/update/{id}', 'HomecookedController@update'); // edit
    Route::get('/homecooked/delete/{id}', 'HomecookedController@delete'); // delete 

    Route::get('/homecooked/access/{id}', 'HomecookedController@access'); // edit
    Route::post('/homecooked/access/{id}', 'HomecookedController@access'); // update


    //-----------------------order management------------
    Route::get('/order', 'OrderController@index');
    Route::post('/order', 'OrderController@index'); // list
    Route::get('/order/add', 'OrderController@add'); // add
    Route::post('/order/add', 'OrderController@add'); // add
	Route::get('/order/edit/{id}', 'OrderController@edit'); // edit
    Route::post('/order/update/{id}', 'OrderController@update'); // edit
    Route::get('/order/delete/{id}', 'OrderController@delete'); // delete  
    //Route::get('/order/create','OrderController@createordernow'); // create
    Route::post('/order/create','OrderController@create'); // create  

    // Get orders count for notification bar
    // Route::get('order/get-count', 'OrderController@getOrdersCount');



    //-----------------------table management------------
    Route::get('/table', 'TableController@index');
    Route::post('/table', 'TableController@index'); // list
    Route::get('/table/add', 'TableController@add'); // add
    Route::post('/table/add', 'TableController@add'); // add
    Route::get('/table/edit/{id}', 'TableController@edit'); // edit
    Route::post('/table/update/{id}', 'TableController@update'); // edit
    Route::get('/table/delete/{id}', 'TableController@delete'); // delete  
    //Route::get('/table/create','TableController@createtablenow'); // create
    Route::post('/table/create','TableController@create'); // create  

    //-----------------------product management------------
    Route::get('/product', 'ProductController@index');
    Route::post('/product', 'ProductController@index'); // list
    Route::get('/product/add', 'ProductController@add'); // add
    Route::post('/product/add', 'ProductController@add'); // add

    Route::get('/product/edit/{id}', 'ProductController@edit'); // edit
    Route::post('/product/update/{id}', 'ProductController@update'); // edit
    Route::get('/product/delete/{id}', 'ProductController@delete'); // delete

    //-----------------------addon group management------------
    Route::get('/addon_group', 'AddonController@index');
    Route::post('/addon_group', 'AddonController@index'); // list
    Route::get('/addon_group/add', 'AddonController@add'); // add
    Route::post('/addon_group/add', 'AddonController@add'); // add

    Route::get('/addon_group/edit/{id}', 'AddonController@edit'); // edit
    Route::post('/addon_group/update/{id}', 'AddonController@update'); // edit
    Route::get('/addon_group/delete/{id}', 'AddonController@delete'); // delete   
    //-----------------------addon option management------------
    Route::get('/addon_option/{id}', 'AddonOptionController@index');
   
    Route::get('/addon_option/add/{id}', 'AddonOptionController@add'); // add
    Route::post('/addon_option/add/{id}', 'AddonOptionController@add'); // add

    Route::get('/addon_option/edit/{id}', 'AddonOptionController@edit'); // edit
    Route::post('/addon_option/update/{id}', 'AddonOptionController@update'); // edit
    Route::get('/addon_option/delete/{id}', 'AddonOptionController@delete'); // delete  
    //-----------------------Restaurant Timing  management------------
    Route::get('/timing/{id}', 'TimingController@index');    
    Route::get('/timing/add/{id}', 'TimingController@add'); // add
    Route::post('/timing/add/{id}', 'TimingController@add'); // add

    Route::get('/timing/edit/{id}', 'TimingController@edit'); // edit
    Route::post('/timing/update/{id}', 'TimingController@update'); // edit
    Route::get('/timing/delete/{id}', 'TimingController@delete'); // delete  
    //--------------coupon manament-------------------------------
    Route::get('/coupon', 'CouponController@index');
    Route::post('/coupon', 'CouponController@index'); // list
    Route::get('/coupon/add', 'CouponController@add'); // add
    Route::post('/coupon/add', 'CouponController@add'); // add

    Route::get('/coupon/edit/{id}', 'CouponController@edit'); // edit
    Route::post('/coupon/update/{id}', 'CouponController@update'); // edit
    Route::get('/coupon/delete/{id}', 'CouponController@delete'); // delete      
    //--------------end coupon management------------------------   

     //--------------home cooked coupon manament-------------------------------
    Route::get('/homecookedcoupon', 'HomeCookedCouponController@index');
    Route::post('/homecookedcoupon', 'HomeCookedCouponController@index'); // list
    Route::get('/homecookedcoupon/add', 'HomeCookedCouponController@add'); // add
    Route::post('/homecookedcoupon/add', 'HomeCookedCouponController@add'); // add

    Route::get('/homecookedcoupon/edit/{id}', 'HomeCookedCouponController@edit'); // edit
    Route::post('/homecookedcoupon/update/{id}', 'HomeCookedCouponController@update'); // edit
    Route::get('/homecookedcoupon/delete/{id}', 'HomeCookedCouponController@delete'); // delete      
    //--------------end coupon management------------------------   

    //--------------review management-------------------------------
    Route::get('/review', 'ReviewController@index');
    Route::post('/review', 'ReviewController@index'); // list
    Route::get('/review/add', 'ReviewController@add'); // add
    Route::post('/review/add', 'ReviewController@add'); // add

    Route::get('/review/edit/{id}', 'ReviewController@edit'); // edit
    Route::post('/review/update/{id}', 'ReviewController@update'); // edit
    Route::get('/review/delete/{id}', 'ReviewController@delete'); // delete      
    //--------------end review management------------------------ 

    //--------------home cooked review management-------------------------------
    Route::get('/homecookedreview', 'HomeCookedReviewController@index');
    Route::post('/homecookedreview', 'HomeCookedReviewController@index'); // list
    Route::get('/homecookedreview/add', 'HomeCookedReviewController@add'); // add
    Route::post('/homecookedreview/add', 'HomeCookedReviewController@add'); // add

    Route::get('/homecookedreview/edit/{id}', 'HomeCookedReviewController@edit'); // edit
    Route::post('/homecookedreview/update/{id}', 'HomeCookedReviewController@update'); // edit
    Route::get('/homecookedreview/delete/{id}', 'HomeCookedReviewController@delete'); // delete      
    //--------------end review management------------------------ 

    //--------------page management-------------------------------
    Route::get('/page', 'PageController@index');
    Route::post('/page', 'PageController@index'); // list
    Route::get('/page/add', 'PageController@add'); // add
    Route::post('/page/add', 'PageController@add'); // add

    Route::get('/page/edit/{id}', 'PageController@edit'); // edit
    Route::post('/page/update/{id}', 'PageController@update'); // edit
    Route::get('/page/delete/{id}', 'PageController@delete'); // delete      
    //--------------end page management------------------------   
   
    //--------------footer management-------------------------------
    Route::get('/footer-links', 'FooterLinkController@index');
    Route::post('/footer-links', 'FooterLinkController@index'); // list
    Route::get('/footer-links/add', 'FooterLinkController@add'); // add
    Route::post('/footer-links/add', 'FooterLinkController@add'); // add

    Route::get('/footer-links/edit/{id}', 'FooterLinkController@edit'); // edit
    Route::post('/footer-links/update/{id}', 'FooterLinkController@update'); // edit
    Route::get('/footer-links/delete/{id}', 'FooterLinkController@delete'); // delete          
    //--------------end footer management------------------------  

    //--------------newsletter management-------------------------------
    Route::get('/newsletter', 'NewsLetterController@index');
    Route::post('/newsletter', 'NewsLetterController@index'); // list
    Route::get('/newsletter/add', 'NewsLetterController@add'); // add
    Route::post('/newsletter/add', 'NewsLetterController@add'); // add

    // Reports
    Route::get('reports/payment', 'Reports\Admin\PaymentController@index');
    Route::post('reports/payment/pay', 'Reports\Admin\PaymentController@pay');
    Route::get('reports/payment/export', 'Reports\Admin\PaymentController@export');
    Route::get('reports/payout/{restaurant}', 'Reports\Admin\PayoutController@index');

    Route::get('reports/customer', 'Reports\Admin\CustomerController@index');
    Route::get('reports/customer/export', 'Reports\Admin\CustomerController@export');

    Route::get('reports/sales', 'Reports\Admin\OrderSalesController@index');
    Route::get('reports/sales/export', 'Reports\Admin\OrderSalesController@export');
    
    Route::get('reports/restaurant', 'Reports\Admin\RestaurantController@index');
    Route::get('reports/restaurant/export', 'Reports\Admin\RestaurantController@export');
    
    Route::get('reports/driver', 'Reports\Admin\DriverController@index');
    Route::get('reports/driver/export', 'Reports\Admin\DriverController@export');
    
    Route::match(['get', 'post'], 'setting', 'SettingController@index');

});


/*
 * 
 * Area manager routing 
 * 
 */
// route to show the login form
Route::group(['prefix' => Config::get('app.locale_prefix').'/manager', 'namespace'=>'Admin'], function()
{
    Route::get('/', 'LoginController@getLogin');
    Route::post('/', 'LoginController@postLogin');
    Route::get('/logout', 'LoginController@getLogout');
});

Route::group(['middleware' => ['auth'], 'prefix' => Config::get('app.locale_prefix').'/manager', 'namespace'=>'Admin'], function()
{
    Route::get('/dashboard', 'WelcomeController@manager_index');     
    Route::post('/dashboard', 'WelcomeController@manager_index');
    Route::get('/dashboard/edit/{id}', 'WelcomeController@edit'); 
    Route::post('/dashboard/update', 'WelcomeController@update');
    //----------------------Area management-----------------
    Route::get('/areamanager', 'AreaManagerController@index'); //list
    Route::post('/areamanager', 'AreaManagerController@index'); // list
    Route::get('/areamanager/edit/{id}', 'AreaManagerController@edit'); // edit
    Route::post('/areamanager/update/{id}', 'AreaManagerController@update'); // edit    
    //----------------------End area management----------------- 

    Route::get('/address/{id}', 'CustomerController@address'); // list
    Route::get('/address-add/{id}', 'CustomerController@addressAdd'); // add
    Route::post('/address-add/{id}', 'CustomerController@addressAdd'); // add
    Route::get('/address-edit/{id}', 'CustomerController@addressEdit'); // edit
    Route::post('/address-update/{id}', 'CustomerController@addressUpdate'); // update
    Route::get('/address-delete/{id}', 'CustomerController@addressDelete'); // update       
    //-----------------------order management------------
    Route::get('/order', 'OrderController@index');
    Route::post('/order', 'OrderController@index'); // list
    Route::get('/order/add', 'OrderController@add'); // add
    Route::post('/order/add', 'OrderController@add'); // add
    Route::get('/order/edit/{id}', 'OrderController@edit'); // edit
    Route::post('/order/update/{id}', 'OrderController@update'); // edit
    Route::get('/order/delete/{id}', 'OrderController@delete'); // delete  
    Route::post('/order/create','OrderController@create'); // create  
    //--------------coupon manament-------------------------------
    Route::get('/coupon', 'CouponController@index');
    Route::post('/coupon', 'CouponController@index'); // list
    Route::get('/coupon/add', 'CouponController@add'); // add
    Route::post('/coupon/add', 'CouponController@add'); // add

    Route::get('/coupon/edit/{id}', 'CouponController@edit'); // edit
    Route::post('/coupon/update/{id}', 'CouponController@update'); // edit
    Route::get('/coupon/delete/{id}', 'CouponController@delete'); // delete      
    //--------------end coupon management------------------------   

    //--------------review management-------------------------------
    Route::get('/review', 'ReviewController@index');
    Route::post('/review', 'ReviewController@index'); // list
    Route::get('/review/add', 'ReviewController@add'); // add
    Route::post('/review/add', 'ReviewController@add'); // add

    Route::get('/review/edit/{id}', 'ReviewController@edit'); // edit
    Route::post('/review/update/{id}', 'ReviewController@update'); // edit
    Route::get('/review/delete/{id}', 'ReviewController@delete'); // delete      
    //--------------end review management------------------------ 
    //-----------------------Start restaurant management------------
    Route::get('/restaurant', 'RestaurantController@index');
    Route::post('/restaurant', 'RestaurantController@index'); // list
    Route::get('/restaurant/edit/{id}', 'RestaurantController@edit'); // edit
    Route::post('/restaurant/update/{id}', 'RestaurantController@update'); // edit 
    //----------------------customer address update---------------
    Route::get('/address-add/{id}', 'CustomerController@addressAdd'); // add
    Route::post('/address-add/{id}', 'CustomerController@addressAdd'); // add 
    //-----------------------Restaurant Timing  management------------
    Route::get('/timing/{id}', 'TimingController@index');    
    Route::get('/timing/add/{id}', 'TimingController@add'); // add
    Route::post('/timing/add/{id}', 'TimingController@add'); // add

    Route::get('/timing/edit/{id}', 'TimingController@edit'); // edit
    Route::post('/timing/update/{id}', 'TimingController@update'); // edit
    Route::get('/timing/delete/{id}', 'TimingController@delete'); // delete  
    //-----------------------product management------------
    Route::get('/product', 'ProductController@index');
    Route::post('/product', 'ProductController@index'); // list
    Route::get('/product/add', 'ProductController@add'); // add
    Route::post('/product/add', 'ProductController@add'); // add

    Route::get('/product/edit/{id}', 'ProductController@edit'); // edit
    Route::post('/product/update/{id}', 'ProductController@update'); // edit
    Route::get('/product/delete/{id}', 'ProductController@delete'); // delete       
    
});


/*
 * 
 * Restaurant routing 
 * 
 */
// route to show the login form
Route::group(['prefix' => Config::get('app.locale_prefix').'/restaurant', 'namespace'=>'Admin'], function()
{
    Route::get('/', 'LoginController@getLogin');
    Route::post('/', 'LoginController@postLogin');
    Route::get('/logout', 'LoginController@getLogout');
});

Route::group(['middleware' => ['auth', 'restaurant'], 'prefix' => Config::get('app.locale_prefix').'/restaurant', 'namespace'=>'Admin'], function()
{


    //vijayanand

    // Route::get('/menu', 'MenuController@index');
    // Route::post('/menu', 'MenuController@index'); // list
    // Route::get('/menu/add', 'MenuController@add'); // add
    // Route::post('/menu/add', 'MenuController@add'); // add

    // Route::get('/menu/edit/{id}', 'MenuController@edit'); // edit
    // Route::post('/menu/update/{id}', 'MenuController@update'); // edit
    // Route::get('/menu/delete/{id}', 'MenuController@delete'); // delete
    
    //vijayanand
    Route::get('/menu', 'MenuRestaurantController@index');
    Route::post('/menu', 'MenuRestaurantController@index'); // list
    Route::get('/menu/add','MenuRestaurantController@add'); // add
    Route::post('/menu/add', 'MenuRestaurantController@add'); // add
    Route::get('/menu/edit/{id}', 'MenuRestaurantController@edit'); // edit
    Route::post('/menu/update/{id}', 'MenuRestaurantController@update'); // edit
    Route::get('/menu/delete/{id}', 'MenuRestaurantController@delete'); // delete  
    //----------------------End menu management-----------------     


    // Get orders count for notification bar
    // Route::get('order/get-count', 'OrderController@getOrdersCount');
    
    Route::get('/dashboard', 'WelcomeController@restaurant_index');      
    Route::post('/dashboard', 'WelcomeController@restaurant_index');
    Route::get('/dashboard/edit/{id}', 'WelcomeController@edit'); 
    Route::post('/dashboard/update', 'WelcomeController@update'); 
    Route::post('/dashboard/update-order-status/{id}', 'WelcomeController@updateOrderStatus');

    Route::get('/address/{id}', 'CustomerController@address'); // list
    Route::get('/address-add/{id}', 'CustomerController@addressAdd'); // add
    Route::post('/address-add/{id}', 'CustomerController@addressAdd'); // add
    Route::get('/address-edit/{id}', 'CustomerController@addressEdit'); // edit
    Route::post('/address-update/{id}', 'CustomerController@addressUpdate'); // update
    Route::get('/address-delete/{id}', 'CustomerController@addressDelete'); // update       
    //-----------------------order management------------
    Route::get('/order', 'OrderController@index');
    Route::post('/order', 'OrderController@index'); // list
    Route::get('/order/add', 'OrderController@add'); // add
    Route::post('/order/add', 'OrderController@add'); // add
    Route::get('/order/edit/{id}', 'OrderController@edit'); // edit
    Route::post('/order/update/{id}', 'OrderController@update'); // edit
    Route::get('/order/delete/{id}', 'OrderController@delete'); // delete  
    Route::post('/order/create','OrderController@create'); // create  
    //--------------coupon manament-------------------------------
    Route::get('/coupon', 'CouponController@index');
    Route::post('/coupon', 'CouponController@index'); // list
    Route::get('/coupon/add', 'CouponController@add'); // add
    Route::post('/coupon/add', 'CouponController@add'); // add

    Route::get('/coupon/edit/{id}', 'CouponController@edit'); // edit
    Route::post('/coupon/update/{id}', 'CouponController@update'); // edit
    Route::get('/coupon/delete/{id}', 'CouponController@delete'); // delete      
    //--------------end coupon management------------------------   
    //-----------------------Start restaurant management------------
    Route::get('/restaurant', 'RestaurantController@restindex');
    Route::get('/homecooked', 'RestaurantController@homeindex');
    Route::post('/restaurant', 'RestaurantController@restindex'); // list
     Route::post('/homecooked', 'RestaurantController@homeindex'); // list
    Route::get('/restaurant/edit/{id}', 'RestaurantController@edit'); // edit
    Route::post('/restaurant/update/{id}', 'RestaurantController@update'); // update 
    Route::get('/restaurant/access/{id}', 'RestaurantController@access'); // edit
    Route::post('/restaurant/access/{id}', 'RestaurantController@access'); // update    
    //----------------------customer address update---------------
    Route::get('/address-add/{id}', 'CustomerController@addressAdd'); // add
    Route::post('/address-add/{id}', 'CustomerController@addressAdd'); // add 
    //-----------------------Restaurant Timing  management------------
    Route::get('/timing/{id}', 'TimingController@index');    
    Route::get('/timing/add/{id}', 'TimingController@add'); // add
    Route::post('/timing/add/{id}', 'TimingController@add'); // add

    Route::get('/timing/edit/{id}', 'TimingController@edit'); // edit
    Route::post('/timing/update/{id}', 'TimingController@update'); // edit
    Route::get('/timing/delete/{id}', 'TimingController@delete'); // delete  
    //-----------------------product management------------
    Route::get('/product', 'ProductController@index');
    Route::post('/product', 'ProductController@index'); // list
    Route::get('/product/add', 'ProductController@add'); // add
    Route::post('/product/add', 'ProductController@add'); // add

    Route::get('/product/edit/{id}', 'ProductController@edit'); // edit
    Route::post('/product/update/{id}', 'ProductController@update'); // edit
    Route::get('/product/delete/{id}', 'ProductController@delete'); // delete      

    // Reports
    Route::get('reports', 'ReportController@restauantReoprt');
    Route::get('reports/export', 'ReportController@restauantReoprtExport');
    
    Route::get('/table', 'TableController@index');
    Route::post('/table', 'TableController@index'); // list
});


// Route::any('{all}', function() { 
//     return view('errors.404');
//     //return redirect('/'); 
// })->where('all', '.*');