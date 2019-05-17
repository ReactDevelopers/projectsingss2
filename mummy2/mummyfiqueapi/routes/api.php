<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1', 'middleware'=> 'apiLog'], function () {
    Route::post('auth/client', '\App\Mummy\Api\V1\Controllers\ApiAuthController@client');
    Route::post('auth/refresh-token', '\App\Mummy\Api\V1\Controllers\ApiAuthController@refreshToken');
    Route::post('/customers/postLogin', '\App\Mummy\Api\V1\Controllers\CustomersController@postLogin');
    Route::post('/customers/postRegister', '\App\Mummy\Api\V1\Controllers\CustomersController@postRegister');
    Route::post('/customers/postForgotPassword', '\App\Mummy\Api\V1\Controllers\CustomersController@postForgotPassword');
    Route::post('/customers/postResendEmail', '\App\Mummy\Api\V1\Controllers\CustomersController@postResendEmail');
    Route::post('/customers/postSocialLogin', '\App\Mummy\Api\V1\Controllers\CustomersController@postSocialLogin');
     Route::get('/vendors/getListVendor', '\App\Mummy\Api\V1\Controllers\VendorsController@getListVendor');

     //Page
      Route::get('/getTermsAndConditions', '\App\Mummy\Api\V1\Controllers\ProfileController@getTermsAndConditions');
      Route::get('/getContact', '\App\Mummy\Api\V1\Controllers\ProfileController@getContact');
      Route::get('/getAbout', '\App\Mummy\Api\V1\Controllers\ProfileController@getAbout');
       Route::get('/getPrivacyPolicy', '\App\Mummy\Api\V1\Controllers\ProfileController@getPrivacyPolicy');
       Route::get('/getENV', '\App\Mummy\Api\V1\Controllers\ProfileController@getENV');
       Route::get('/updateProductName', '\App\Mummy\Api\V1\Controllers\ProfileController@updateProductName');
    
    // get version
    Route::get('/getVersion', '\App\Mummy\Api\V1\Controllers\HomeController@getVersion');
    
    Route::get('/log-download/{filename}', function($filename) {
        $path = base_path('storage/logs/' . $filename);
        return response()->download($path);
    });
    Route::get('/log-scan', function() {
        dd(scandir(base_path('storage/logs')));
    });
    Route::get('/log-clear', function() {
        $path = base_path('storage/logs/laravel.log');
        exec('echo "" > ' . $path);
        print_r("Done!!!");
    });
    
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:api','apiLog']], function () {
	Route::get('/customers/getLogout', '\App\Mummy\Api\V1\Controllers\CustomersController@getLogout');
    Route::get('/customer', '\App\Mummy\Api\V1\Controllers\UsersController@profile');
    Route::get('/customers/getCustomer', '\App\Mummy\Api\V1\Controllers\CustomersController@getCustomer');

    //Vendors
    Route::get('/vendors/getVendorProfile', '\App\Mummy\Api\V1\Controllers\VendorsController@getVendorProfile');
    Route::post('/vendors/getAllReviewByVendor', '\App\Mummy\Api\V1\Controllers\VendorsController@getAllReviewByVendor');

    Route::post('/vendors/viewAction', '\App\Mummy\Api\V1\Controllers\VendorsController@viewAction');
    
    Route::post('/vendors/submitComment', '\App\Mummy\Api\V1\Controllers\VendorsController@submitComment');
    Route::post('/vendors/saveVendor', '\App\Mummy\Api\V1\Controllers\VendorsController@saveVendor');
    Route::post('/vendors/deleteSaveVendor', '\App\Mummy\Api\V1\Controllers\VendorsController@deleteSaveVendor');
    
    Route::post('/vendors/likeVendor', '\App\Mummy\Api\V1\Controllers\VendorsController@likeVendor');
    //Home
    Route::get('/homes/getHome', '\App\Mummy\Api\V1\Controllers\HomeController@getHome');
    Route::get('/homes/getListFavourite', '\App\Mummy\Api\V1\Controllers\HomeController@getListFavourite');
    Route::post('/homes/deleteFavourite', '\App\Mummy\Api\V1\Controllers\HomeController@deleteFavourite');
    
    Route::post('/vendors/addFavourite', '\App\Mummy\Api\V1\Controllers\VendorsController@addFavourite');

    //get instagram feed
    Route::get('/vendors/getInstagramFeed', '\App\Mummy\Api\V1\Controllers\VendorsController@getInstagramFeed');
    Route::get('/vendors/getInstagramFeedDetail', '\App\Mummy\Api\V1\Controllers\VendorsController@getInstagramFeedDetail');

    //Vendor Screen

   Route::get('/vendors/getVendorScreen', '\App\Mummy\Api\V1\Controllers\VendorsController@getVendorScreen');
   Route::post('/vendors/postListVendorByCategory', '\App\Mummy\Api\V1\Controllers\VendorsController@postListVendorByCategory');
   
    Route::get('/vendors/update-like-activity', '\App\Mummy\Api\V1\Controllers\VendorsController@getUpdateLikeActivity');

    //Search
    Route::get('/homes/showPopup', '\App\Mummy\Api\V1\Controllers\HomeController@showPopup');
    Route::get('/homes/userBadgeNumber', '\App\Mummy\Api\V1\Controllers\HomeController@userBadgeNumber');
    
    Route::get('/homes/getSearchScreen', '\App\Mummy\Api\V1\Controllers\HomeController@getSearchScreen');
    Route::post('/homes/postSearch', '\App\Mummy\Api\V1\Controllers\HomeController@postSearch');
    Route::post('/homes/postSearchNearBy', '\App\Mummy\Api\V1\Controllers\HomeController@postSearchNearBy');
    Route::post('/homes/postSearchByName', '\App\Mummy\Api\V1\Controllers\HomeController@postSearchByName');

    //Profile
    Route::get('/profiles/getAccountDetail', '\App\Mummy\Api\V1\Controllers\ProfileController@getAccountDetail');
    Route::post('/profiles/postUpdateAvatar', '\App\Mummy\Api\V1\Controllers\ProfileController@postUpdateAvatar');

    Route::post('/profiles/postAccountDetail', '\App\Mummy\Api\V1\Controllers\ProfileController@postAccountDetail');
    //Children
    Route::get('/profiles/getChildrenDetail', '\App\Mummy\Api\V1\Controllers\ProfileController@getChildrenDetail');
    Route::post('/profiles/postChildrenDetail', '\App\Mummy\Api\V1\Controllers\ProfileController@postChildrenDetail');
    
    Route::post('/profiles/postChangePassword', '\App\Mummy\Api\V1\Controllers\ProfileController@postChangePassword');

    //Static 
    Route::get('/homes/getCategory', '\App\Mummy\Api\V1\Controllers\HomeController@getCategory');
     Route::Post('/homes/postSubCategory', '\App\Mummy\Api\V1\Controllers\HomeController@postSubCategory');
      Route::get('/homes/getPriceRange', '\App\Mummy\Api\V1\Controllers\HomeController@getPriceRange');
      Route::Post('/homes/postCities', '\App\Mummy\Api\V1\Controllers\HomeController@postCities');
      Route::get('/homes/getCountries', '\App\Mummy\Api\V1\Controllers\HomeController@getCountries');
      //Filter
       Route::post('/homes/postFilter', '\App\Mummy\Api\V1\Controllers\HomeController@postFilter');

       //Portfolio
       Route::post('/homes/postAllPortfolioByVendor', '\App\Mummy\Api\V1\Controllers\HomeController@postAllPortfolioByVendor');

       Route::post('/homes/postPortfolioAllComment', '\App\Mummy\Api\V1\Controllers\HomeController@postPortfolioAllComment');

       Route::post('/homes/postLovePortfolio', '\App\Mummy\Api\V1\Controllers\HomeController@postLovePortfolio');
       

       //Profile screen
       Route::post('/homes/postProfileMyVendor', '\App\Mummy\Api\V1\Controllers\HomeController@postProfileMyVendor');
       Route::post('/homes/postProfileMyReview', '\App\Mummy\Api\V1\Controllers\HomeController@postProfileMyReview');
       Route::get('/homes/getProfileMyAccount', '\App\Mummy\Api\V1\Controllers\HomeController@getProfileMyAccount');
       
       //Review
       Route::post('/homes/editReview', '\App\Mummy\Api\V1\Controllers\HomeController@editReview');
       Route::post('/homes/deleteReview', '\App\Mummy\Api\V1\Controllers\HomeController@deleteReview');
       Route::post('/homes/postReviewDetail', '\App\Mummy\Api\V1\Controllers\HomeController@postReviewDetail');
       Route::post('/homes/sendReview', '\App\Mummy\Api\V1\Controllers\HomeController@sendReview');

       Route::post('/homes/postPricelist', '\App\Mummy\Api\V1\Controllers\HomeController@postPricelist');

       //MLink
       Route::get('/homes/getMLink', '\App\Mummy\Api\V1\Controllers\HomeController@getMLink');
       //Send Message
       Route::get('/homes/getSendMessage', '\App\Mummy\Api\V1\Controllers\HomeController@getSendMessage');
       Route::post('/homes/postSendMessage', '\App\Mummy\Api\V1\Controllers\HomeController@postSendMessage');

       Route::post('/homes/MessageScreen', '\App\Mummy\Api\V1\Controllers\HomeController@MessageScreen');

        Route::get('/homes/getReadMessage', '\App\Mummy\Api\V1\Controllers\HomeController@getReadMessage');

        Route::post('/homes/deleteMessage', '\App\Mummy\Api\V1\Controllers\HomeController@deleteMessage');

       //Delete Comment
       Route::post('/homes/deleteComment', '\App\Mummy\Api\V1\Controllers\HomeController@deleteComment');

       //Report Review
       Route::post('/homes/postReportReview', '\App\Mummy\Api\V1\Controllers\HomeController@postReportReview');
       //Notification Screen
       Route::get('/profiles/getNotificationScreen', '\App\Mummy\Api\V1\Controllers\ProfileController@getNotificationScreen');
       Route::post('/profiles/postNotificationScreen', '\App\Mummy\Api\V1\Controllers\ProfileController@postNotificationScreen');

       
    Route::get('/homes/show-config', '\App\Mummy\Api\V1\Controllers\HomeController@showConfig');
    Route::get('/homes/check-cache', function(){
        dd(scandir(base_path('bootstrap/cache')));
    });
    Route::get('/homes/clear-config', function(){
        \Artisan::call('config:clear');
    });
    Route::get('/homes/clear-cache', function(){
        \Artisan::call('cache:clear');
    });
    // Route::get('/homes/log-download/{filename}', function($filename) {
    //     $path = base_path('storage/logs/' . $filename);
    //     return response()->download($path);
    // });
    Route::get('/homes/log-scan', function() {
        dd(scandir(base_path('storage/logs')));
    });
    Route::get('/homes/update-rating-point', '\App\Mummy\Api\V1\Controllers\HomeController@getUpdateRatingPoint');
    
    Route::get('/command/update-user-review', '\App\Mummy\Api\V1\Controllers\CommandController@getUpdateUserReview');
    Route::get('/command/update-user-rating', '\App\Mummy\Api\V1\Controllers\CommandController@getUpdateUserRating');
    Route::get('/command/update-vendor-photo-thumb', '\App\Mummy\Api\V1\Controllers\CommandController@updateVendorPhotoThumb');
    
});




