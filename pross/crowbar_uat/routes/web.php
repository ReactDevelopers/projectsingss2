<?php

	/*
	|--------------------------------------------------------------------------
	| Web Routes
	|--------------------------------------------------------------------------
	|
	| This file is where you may define all of the routes that are handled
	| by your application. Just tell Laravel the URIs it should respond
	| to using a Closure or controller method. Build something great!
	|
	*/

	Route::get('/check', function() {
	   Artisan::call('TransferJob:transferjob');
	}); 

	Route::get('/google-translate','Front\FrontController@googleTranslate');   
	Route::get('/plans','FrontController@fetchPlan');
	Route::get('/language','Front\FrontController@change_language');
	Route::get('/currency','Front\FrontController@change_currency');
	Route::get('/validate-paypalemail','Front\FrontController@validatePayPalEmail');
	Route::get('/redirect','FrontController@redirect');

	Route::get('/mynetworks/eventsdetail/{hashid}','Front\TalentController@eventDetail');
	Route::get('/project/show-details/category/{category_id}/job_id/{job_id}','Front\EmployerController@publicJobDetail');

	/*PayPal Express Checkout for Web*/
	Route::post('/payment/create-payment','Front\AjaxController@new_expchk');
	Route::post('/payment/execute-payment','Front\AjaxController@execute_expchk');

	/*PayPal Express Checkout for Mobile*/
	Route::get('/mobile-express-checkout','Front\AjaxController@mobile_open_expchk');
	Route::post('/payment/mobile-create-payment','Front\AjaxController@mobile_new_expchk');
	Route::post('/payment/mobile-execute-payment','Front\AjaxController@mobile_execute_expchk');
	Route::get('/payment/payment-redirect','Front\AjaxController@mobile_payment_redirect');

	/*PayPal Mobile validate*/
	Route::get('talent/verified-mobile-paypal-email','Front\AjaxController@save_verified_mobile_paypal_email');

	/*Payment for Job without PayPal Payment*/
	Route::post('/project/payout/mgmt','Front\AjaxController@accept_payout_mgmt');

	/*For select type*/
	Route::get('select-profile','Front\FrontController@select_profile');
	Route::get('select-profile-modal','Front\FrontController@select_profile_modal');
	Route::post('select-profile-save','Front\FrontController@select_profile_save');

	
	Route::group(['middleware' => '\App\Http\Middleware\TempLogin'], function () {
		Route::group(['middleware' => ['\App\Http\Middleware\AdminAuth','revalidate'],'prefix'=> ADMIN_FOLDER,'namespace' => 'Admin'], function () {
			Route::get('/', function () { return redirect('/login'); });
			Route::get('/dashboard','AdminController@index');
			Route::get('/general','AdminController@general');
			Route::post('/general/update/setting','AdminController@update_settings'); 
			Route::post('/general/upload/collection','AdminController@upload_collection');

			Route::get('/get-availability','AdminController@get_talent_availability');
			
			/*SUBADMIN CONTACT*/
			Route::get('/help','AdminController@contact');
			Route::post('/_contact','AdminController@_contactpage');

			/*CHANGE PASSWORD*/
			Route::get('/change-password','AdminController@change_password'); 
			Route::post('/__change-password','AdminController@__change_password');

			/*Subscription listing*/
			Route::get('/employer/subscription','AdminController@employer_subscription_list'); 
			Route::get('/subscription/detail/{id_user}','AdminController@employer_subscription_detail');
			Route::post('/subscription/delete','AdminController@subscription_delete');

			/*Banner Management*/
			Route::get('/banner','AdminController@banner_list');
			Route::put('/banner/image','AdminController@banner_image_upload');
			Route::get('/banner/edit/{id_banner}','AdminController@banner_edit');
			Route::put('/banner/edit/{id_banner}','AdminController@banner_update');
			Route::put('/banner/image/delete','AdminController@banner_image_delete'); 

			/*Project Management*/
			Route::get('/project/listing','AdminController@project_list');
			Route::get('/project/detail/{id_project}','AdminController@project_detail');
			Route::get('/project/proposal/detail/','AdminController@proposal_detail');
			Route::post('/project/delete','AdminController@project_delete');

			/*Newsletter*/
			Route::get('/newsletter/subscriber','AdminController@newsletter_subscriber');
			Route::post('/newsletter/unsubscribe-user','AdminController@newsletter_unsubscribe_user'); 

			Route::get('/newsletter/subscribe','AdminController@newsletter_subscribe'); 
			Route::post('/newsletter/unsubscribe','AdminController@newsletter_unsubscribe'); 

			/*REPORT ABUSE*/
			Route::get('/report-abuse','AdminController@report_abuse'); 
			Route::post('/report-abuse/resolve','AdminController@report_abuse_resolve');
			Route::post('/report-abuse/unlink','AdminController@report_abuse_unlink');

			Route::get('/report-abuse/view','AdminController@report_abuse_view');
			
			/*REQUEST PAYOUT*/
			Route::get('/request/payout','AdminController@listRequestPayout');

			Route::get('/raise-dispute','AdminController@raise_dispute');
			Route::get('/raise-dispute/detail','AdminController@raise_dispute_detail');

			/*PAYMENT*/
			Route::post('/payment/refund', 'AdminController@payment_refund');
			Route::post('/payment/pay', 'AdminController@payment_pay');

			/*Messages Listing*/
			Route::get('/messages/{messages_status}','AdminController@message_list');
			Route::get('/ajax/messages/{messages_status}','AjaxController@message_list');
			Route::get('/messages/detail/{id_message}','AdminController@message_detail');
			Route::put('/messages/reply/{id_message}','AdminController@message_replay');
			Route::get('/messages/delete/{id_message}','AdminController@message_delete');
			/*Messages Listing*/

			/*Forum Section*/
			Route::get('/forum/question','AdminController@forum_question_list');
			Route::post('/forum/question/update','AdminController@forum_question_update');
			Route::post('/forum/answer/update','AdminController@forum_answer_update');
			Route::post('/forum/question/delete','AdminController@forum_question_delete');
			Route::get('/forum/question/detail/{id_question}','AdminController@forum_question_detail');
			Route::get('/forum/question/reply/{id_question}','AdminController@forum_question_reply');
			Route::put('/forum/question/reply/{id_question}','AdminController@forum_question_reply_insert');

			Route::post('/forum/answer/reply','AdminController@forum_list_answer');
			Route::post('/forum/answer/delete','AdminController@forum_delete_answer');
			Route::post('/forum/answer/add','AdminController@forum_add_answer');


			Route::get('/currency','AdminController@currency_list');
			Route::get('/currency/add','AdminController@add_currency');
			Route::put('/currency/add','AdminController@insert_currency');
			Route::get('/currency/edit/{id}','AdminController@edit_currency');
			Route::put('/currency/edit/{id}','AdminController@update_currency');
			Route::post('/currency/update_currency_status','AdminController@update_currency_status');
			Route::post('/currency/delete','AdminController@delete_currency');

			/*User Management*/
			Route::get('/users/talent/edit','AdminController@edit_talent');
			Route::get('/users/talent/edit/activity_log','AdminController@edit_talent_activity_log');

			Route::get('/users/employer/edit','AdminController@edit_employer');
			Route::get('/users/employer/edit/activity_log','AdminController@edit_employer_activity_log');

			Route::put('/employer-users/{id_user}/update','AdminController@update_employer');
			Route::get('/users/talent/add','AdminController@add_talent');
			Route::put('/users/talent/add','AdminController@insert_talent');
			Route::get('/users/employer/add','AdminController@add_employer');
			Route::put('/users/employer/add','AdminController@insert_employer'); 

			Route::post('/users/status','AdminController@users_status');
			
			Route::get('/users/sub-admin/edit','AdminController@edit_subadmin');
			Route::put('/users/sub-admin/update/{id_user}','AdminController@update_subadmin');

			Route::get('/users/sub-admin/add','AdminController@add_subadmin');
			Route::put('/users/sub-admin/add','AdminController@insert_subadmin');


			Route::get('/users/premium/add','AdminController@add_premium');
			Route::put('/users/premium/add','AdminController@insert_premium');
			Route::get('/users/premium/edit','AdminController@edit_premium');
			Route::put('/users/employer/update','AdminController@update_premium'); 


			Route::get('/users/{type}','AdminController@users');
			Route::get('/del-users/{type}','AdminController@del_users');

			Route::post('/users/partial_delete','AdminController@partial_delete_or_suspend');	
			Route::post('/auth_delete_pwd','AdminController@auth_delete_or_suspend_process');

			Route::get('/activity-log/talent','AdminController@activity_log_talent');
			Route::post('/activity-log/talent/countActivity','AdminController@talentCountActivity');
			Route::get('/activity-log/employer','AdminController@activity_log_employer');
			Route::post('/activity-log/employer/countActivity','AdminController@employerCountActivity');


			Route::get('/payout/management','AdminController@payout_management_list');
			Route::get('/payout/management/add','AdminController@show_add_payout_mgmt');
			Route::put('/payout/management/add','AdminController@add_payout_mgmt');
			Route::post('/payout/management/delete','AdminController@delete_payout_mgmt');
			Route::get('/payout/management/edit','AdminController@show_edit_payout_mgmt');
			Route::put('/payout/management/update/{id}','AdminController@update_payout_mgmt');
			Route::get('/payout/management/duplicate','AdminController@show_duplicate_payout_mgmt');
			Route::put('/payout/management/duplicate/{id}','AdminController@add_duplicate_payout_mgmt');

			Route::get('group/list','AdminController@group_list');
			Route::get('group/add','AdminController@addGroup');
			Route::put('group/add','AdminController@insertGroup');
			Route::post('group/delete','AdminController@groupDelete'); 
			Route::post('group/user/delete','AdminController@groupDeleteUser'); 
			Route::get('group/edit','AdminController@showEditGroup');
			Route::put('group/update/{hashid}','AdminController@updateGroup');


			Route::get('/ajax/users','AjaxController@user_list');
			Route::post('/ajax/user/status','AjaxController@userstatus');
			Route::post('/ajax/question/status','AjaxController@questionstatus');
			Route::post('/ajax/question-type/status','AjaxController@questionTypestatus');
			Route::post('/ajax/industry/status','AjaxController@industrystatus');
			Route::post('/ajax/abusive-word/status','AjaxController@abusive_word_status');
			Route::post('/ajax/degree/status','AjaxController@degreestatus');
			Route::post('/ajax/certificate/status','AjaxController@certificatestatus');
			Route::post('/ajax/college/status','AjaxController@collegestatus');
			Route::post('/ajax/skill/status','AjaxController@skillstatus');
			Route::post('/ajax/features/status','AjaxController@featurestatus');
			Route::post('/ajax/faq/status','AjaxController@faqstatus');

			Route::get('/talent-users/{id_user}/edit','AdminController@edit_talent');
			Route::put('/talent-users/{id_user}/update','AdminController@update_talent');
			Route::get('/talent/delete-education/{id_education}/{id_user}/','AdminController@delete_education');
			Route::get('/talent/delete-experience/{id_experience}/{id_user}/','AdminController@delete_talent_experience');
			Route::post('/talent/doc-submit','AdminController@user_document_upload');
			Route::post('/premium/doc-submit','AdminController@premium_document_upload');

			/*User Management*/

			Route::get('/emails','AdminController@emails');
			Route::get('/emails/{id_email}/edit','AdminController@editemail');
			Route::put('/emails/{id_email}/update','AdminController@updateemail');
			Route::resource('templates','TemplateController');

			/*GENERAL*/
			Route::post('/general/countries/add','AdminController@add_country');
			Route::post('/general/states/add','AdminController@add_state');
			Route::post('/general/city/add','AdminController@add_city');
			Route::post('/general/industry/add','AdminController@add_industry');
			Route::post('/general/sub_industry/add','AdminController@add_sub_industry');
			Route::post('/general/abusive_words/add','AdminController@add_abusive_words');
			Route::post('/general/degree/add','AdminController@add_degree');
			Route::post('/general/certificate/add','AdminController@add_certificate');
			Route::post('/general/college/add','AdminController@add_college');
			Route::post('/general/skill/add','AdminController@add_skill');
			Route::post('/general/dispute-concern/add','AdminController@add_dispute_concern');
			
			Route::post('/general/countries/edit','AdminController@add_country');
			Route::post('/general/states/edit','AdminController@add_state');
			Route::post('/general/city/edit','AdminController@add_city');
			Route::post('/general/industry/edit','AdminController@add_industry');
			Route::post('/general/sub_industry/edit','AdminController@add_sub_industry');
			Route::post('/general/abusive_words/edit','AdminController@add_abusive_words');
			Route::post('/general/degree/edit','AdminController@add_degree');
			Route::post('/general/certificate/edit','AdminController@add_certificate');
			Route::post('/general/college/edit','AdminController@add_college');
			Route::post('/general/skill/edit','AdminController@add_skill');			
			Route::post('/general/dispute-concern/edit','AdminController@add_dispute_concern');
			
			/*AJAX REQUESTS*/
			Route::get('/general/ajax/messages','AjaxController@messages');
			Route::post('/general/ajax/addmessage','AjaxController@addmessage');
			
			Route::get('/general/ajax/countries','AjaxController@countries');
			Route::post('/general/ajax/addcountry','AjaxController@addcountry');
			Route::post('/ajax/country/status','AjaxController@countrystatus');
			Route::post('/ajax/state/status','AjaxController@statestatus');
			Route::post('/ajax/city/status','AjaxController@citystatus');
			Route::post('/ajax/dispute-concern/status','AjaxController@dispute_concern_status');
			
			Route::post('/ajax/setting/update','AjaxController@updatesetting');
			Route::get('/ajax/emails','AjaxController@emails');

			Route::get('/pages','AdminController@page');
			Route::get('/page/{id_page}/edit','AdminController@editpage');
			Route::put('/page/{id_page}/update','AdminController@updatepage');
			Route::get('/ajax/page','AjaxController@pages');

			/*INTERVIEW SECTION*/
			Route::get('/question-list','AdminController@questionList');
			Route::get('/interview/question/add','AdminController@add_interview_question');
			Route::post('/question/add','AdminController@add_question');
			Route::get('/interview/question/edit','AdminController@edit_interview_question');
			Route::post('/question/edit','AdminController@edit_question');
			Route::get('/question-type-list','AdminController@questionTypeList');
			Route::get('/interview/question-type/add','AdminController@add_interview_questionType');
			Route::post('/question-type/add','AdminController@add_questionType');
			Route::get('/interview/question-type/edit','AdminController@edit_interview_questionType');
			Route::post('/question-type/edit','AdminController@edit_questionType');

			/*PLAN SECTION*/
			Route::get('/plan/list','AdminController@plan_list');
			Route::get('/plan/edit','AdminController@plan_edit');
			Route::post('/plan/edit','AdminController@edit_plan');
			Route::get('/plan/features-list/features/add','AdminController@features_add');
			Route::get('/plan/features-list','AdminController@features_list');
			Route::post('/plan/add-feature','AdminController@add_feature');			
			Route::get('/plan/features-list/features/edit','AdminController@features_edit');
			Route::post('/plan/features/edit','AdminController@edit_features');

			/*LIST ADD*/
			Route::post('/add-state','AdminController@add_state');
			Route::post('/add-city','AdminController@add_city');
			Route::post('/add-industry','AdminController@add_industry');
			Route::post('/add-sub-industry','AdminController@add_sub_industry');
			Route::post('/add-abusive-word','AdminController@add_abusive_words');
			Route::post('/add-degree','AdminController@add_degree');
			Route::post('/add-certificate','AdminController@add_certificate');
			Route::post('/add-college','AdminController@add_college');
			Route::post('/add-skill','AdminController@add_skill');
			Route::post('/add-dispute-concern','AdminController@add_dispute_concern');

			/*INDUSTRY MANAGEMENT*/
			Route::get('/industry','AdminController@industry_list');
			Route::get('/industry/add','AdminController@industry_add_edit');
			Route::get('/sub-industry/add','AdminController@sub_industry_add_edit');
			Route::post('/industry/add','AdminController@add_industry');
			Route::post('/sub-industry/add','AdminController@add_sub_industry');
			Route::get('/industry/edit','AdminController@industry_add_edit');
			Route::get('/sub-industry/edit','AdminController@sub_industry_add_edit');
			Route::get('/sub-industry','AdminController@sub_industry_list');
			Route::post('/industry/image','AdminController@industry_image_upload');
			Route::get('/industry/tag','AdminController@industry_tag');

			/*SKILLS MANAGEMENT*/
			Route::get('/skill','AdminController@skill');
			Route::get('/skill/add','AdminController@add_edit_skill');
			Route::post('/skill/add','AdminController@add_skill');
			Route::get('/skill/edit','AdminController@add_edit_skill');
			Route::post('/skill/edit','AdminController@add_skill');

			/*COLLEGE MANAGEMENT*/
			Route::get('/college','AdminController@college');
			Route::get('/college/add','AdminController@add_edit_college');
			Route::post('/college/add','AdminController@add_college');
			Route::get('/college/edit','AdminController@add_edit_college');
			Route::post('/college/edit','AdminController@add_college');			
			Route::post('/college/image','AdminController@college_image_upload');

			/*COLLEGE MANAGEMENT*/
			Route::get('/company','AdminController@company');
			Route::get('/company/add','AdminController@add_edit_company');
			Route::post('/company/add','AdminController@add_company');
			Route::get('/company/edit','AdminController@add_edit_company');
			Route::post('/company/edit','AdminController@add_company');			
			Route::post('/company/image','AdminController@company_image_upload');

			/*WORK FIELDS MANAGEMENT*/
			Route::get('/workfields','AdminController@workfields');
			Route::get('/workfields/add','AdminController@add_edit_workfield');
			Route::post('/workfields/add','AdminController@add_workfield');
			Route::get('/workfields/edit','AdminController@add_edit_workfield');
			Route::post('/workfields/edit','AdminController@add_workfield');			
			
			Route::get('/translations','\Barryvdh\TranslationManager\Controller@getIndex');
			// Route::get('/view/{group}','\Barryvdh\TranslationManager\Controller@getIndex');
			// Route::post('/edit/{group}','\Barryvdh\TranslationManager\Controller@postEdit');
			// Route::post('/publish','\Barryvdh\TranslationManager\Controller@postPublish');
			// Route::post('/import','\Barryvdh\TranslationManager\Controller@postImport');
			// Route::post('/find','\Barryvdh\TranslationManager\Controller@postFind');

			Route::get('/faq/{type}','AdminController@faq'); 
			Route::get('faq/{type}/{name}','AdminController@add_edit_faq')->where('name','(add|edit)');
			Route::post('faq/topic/add','AdminController@_add_edit_faq_topic');
			Route::post('faq/category/add','AdminController@_add_edit_faq_category');
			Route::post('faq/post/add','AdminController@_add_edit_faq_post');

			// Report Section Menu.
			Route::get('report','AdminController@jobsReport');

			//Transaction log
			Route::get('transaction/list','AdminController@transactionList');

			//Coupon Management
			Route::get('coupon/list','AdminController@couponList');
			Route::get('coupon/add','AdminController@addCoupon');
			Route::put('coupon/add','AdminController@insertCoupon');
			Route::get('coupon/assign','AdminController@assignCoupon');
			Route::put('coupon/assign','AdminController@sentCouponCode');
			Route::post('coupon/delete','AdminController@couponDelete'); 
			Route::get('coupon/view','AdminController@couponView'); 

			//Testimonial
			Route::get('testimonial','AdminController@listTestimonial');	 
			Route::get('testimonial/edit','AdminController@editTestimonial');	
			Route::post('testimonial/edit','AdminController@updateTestimonial');
			Route::post('testimonial/image','AdminController@testimonial_image_upload');	 

			/*Article Section*/
			Route::get('/forum/article','AdminController@articleList');
			Route::get('/forum/article/view/{article_id}','AdminController@articleDetail');
			Route::post('/forum/{type}/delete/{article_id}','AdminController@deleteArticle')->where('type','(comment|article)');

			/*Events Section*/
			Route::get('/forum/event','AdminController@eventList');
			Route::get('/forum/event/view/{event_id}','AdminController@eventDetail');
			Route::post('/forum/event/delete/{event_id}','AdminController@deleteEvent');

			/*Award Management*/
			Route::resource('/award','AwardController');

		});
	
		Route::group(['middleware' => ['\App\Http\Middleware\AdminAuth','revalidate'],'prefix'=> ADMIN_FOLDER,'namespace' => 'Admin'], function () {
            Route::get('translations/view/{group?}', '\Barryvdh\TranslationManager\Controller@getView')->where('group', '.*');
            Route::get('translations/{group?}', '\Barryvdh\TranslationManager\Controller@getIndex')->where('group', '.*');
            Route::post('translations/add/{group}', '\Barryvdh\TranslationManager\Controller@postAdd')->where('group', '.*');
            Route::post('translations/edit/{group}', '\Barryvdh\TranslationManager\Controller@postEdit')->where('group', '.*');
            Route::post('translations/delete/{group}/{key}', '\Barryvdh\TranslationManager\Controller@postDelete')->where('group', '.*');
            Route::post('translations/import', '\Barryvdh\TranslationManager\Controller@postImport');
            Route::post('translations/find', '\Barryvdh\TranslationManager\Controller@postFind');
            Route::post('translations/publish/{group}', '\Barryvdh\TranslationManager\Controller@postPublish')->where('group', '.*');
        });

		Route::get('/'.ADMIN_FOLDER.'/login','Admin\AdminController@login');  
		Route::get('/'.ADMIN_FOLDER.'/logout', 'Admin\AdminController@getLogout');
		Route::get('/'.ADMIN_FOLDER.'/generatepassword','Admin\AdminController@generatePassword');
		Route::get('/'.ADMIN_FOLDER.'/decryptPassword','Admin\AdminController@decryptPassword');
		Route::post('/'.ADMIN_FOLDER.'/authenticate','Admin\AdminController@authenticate');

		Route::get('/'.ADMIN_FOLDER.'/create-subadmin/account','Admin\AdminController@completeAccount');
		Route::post('/'.ADMIN_FOLDER.'/create-subadmin/account','Admin\AdminController@createPassword');
		Route::get('/'.ADMIN_FOLDER.'/forgot-password','Admin\AdminController@forgotpassword');
		Route::post('/'.ADMIN_FOLDER.'/forgot-password','Admin\AdminController@_forgotpassword');
		Route::get('/'.ADMIN_FOLDER.'/reset-password','Admin\AdminController@resetpassword');
		Route::post('/'.ADMIN_FOLDER.'/reset-password','Admin\AdminController@_resetpassword'); 

		Auth::routes();
		Route::get('/', 'Front\FrontController@index'); 
		Route::get('/search-job','Front\FrontController@search_jobs'); 
		Route::post('/_search-job','Front\FrontController@_search_jobs');

		/*Talent profile public url*/
		Route::get('/showprofile/{name}/{user_id}','Front\FrontController@show_talent_profile');

		Route::group(['middleware'=>'\App\Http\Middleware\PublicAuth', 'namespace' => 'Front'],function(){
			Route::get('/share', 'FrontController@share');

			Route::post('temp-data', 'FrontController@tempUserData'); 
			Route::get('tiasignup', 'FrontController@openTempUserData');
			Route::post('temp-data', 'FrontController@openTempUserData');
			
			Route::get('/login', 'FrontController@login');			
			Route::get('/hello_pp', 'FrontController@hello_pp'); 			
			Route::post('/authenticate', 'FrontController@authenticate');   

			Route::get('/network/community/forum', 'FrontController@community_forum');
			Route::get('/network/community/forum/question/ask', 'FrontController@community_forum_ques_ask');
			Route::put('/mynetworks/community/forum/question/add', 'FrontController@community_forum_add_question');

			Route::get('/network/community/forum/question/{id_question}','FrontController@community_forum_question');
			Route::put('/network/community/forum/answer/add/{id_question}','FrontController@community_forum_add_answer');

			Route::get('/mynetworks/upvote_answer','FrontController@community_forum_upvote'); 
			Route::get('/mynetworks/downvote_answer','FrontController@community_forum_downvote');
 
			Route::get('/network/home/fav-event','FrontController@mark_event_favorite');

			/*home*/
			Route::get('/network/home','FrontController@community_home');
			Route::post('/_mynetworks/_home','FrontController@_community_home');


			Route::post('/network/community/forum/load/answer/{id_question}', 'FrontController@forum_list_answer');

			Route::get('/network/article', 'FrontController@community_article');
			Route::post('/_mynetworks/_article','FrontController@_community_article');

			Route::get('/network/article/add','FrontController@articles_add');
			Route::post('/mynetworks/article/add', 'FrontController@articles_add_submit');

			Route::get('/network/article/detail/{id}','FrontController@show_article_details');
			Route::put('/mynetworks/article/answer/add/{id}','FrontController@community_article_add_answer');

			/*Follow User from anywhere*/
			Route::get('/mynetworks/community/follow-user','FrontController@followUser');

			/*Follow Any Post from anywhere*/
			Route::get('/mynetworks/community/follow-post','FrontController@followPost');


			Route::get('/newsletter/unsubscribe/{token}', 'FrontController@newsletter_unsubscribe');
			Route::get('/update-project', 'FrontController@updateProjectStatus');
			Route::get('/send-newsletter-employer', 'FrontController@sendNewsletterToEmployer');
			Route::get('/send-newsletter-talent', 'FrontController@sendNewsletterToTalent');
			Route::get('/signup', 'FrontController@signup');
			Route::get('/signup/talent', 'FrontController@signuptalent');

			Route::get('/signup/selectType', 'FrontController@selectType');

			Route::post('/signup/none/process', 'FrontController@__signupnone');
			Route::get('/none/signup/success', 'FrontController@__editsignupnone');
			Route::post('/signup/talent/process', 'FrontController@__signuptalent');
			Route::get('/talent/signup/success', 'FrontController@__editsignuptalent');
			Route::get('/signup/employer', 'FrontController@signupemployer');
			Route::post('/signup/employer/process', 'FrontController@__signupemployer');
			Route::get('/employer/signup/success', 'FrontController@__editsignupemployer');
			Route::get('/download/collection', 'FrontController@download_postman_collection');
			Route::get('/reset/password','FrontController@resetpassword');
			Route::post('/_resetpassword','FrontController@_resetpassword');
			Route::get('/activate/account','FrontController@activateaccount');
			Route::get('/forgot/password','FrontController@forgotpassword');
			Route::post('/_forgotpassword','FrontController@_forgotpassword');
			Route::get('/forgot/password/verify','FrontController@verifyforgotpassword');
			Route::post('/_verifyforgotpassword','FrontController@_verifyforgotpassword');
			Route::get('/create/account','FrontController@completeAccount');
			Route::post('/create/account','FrontController@createPassword');
			Route::get('/emailverify/account','FrontController@completeAccountEmail');

			Route::get('/accept/event','FrontController@acceptevent');
			
			/*SOCIAL SIGNUP*/
			Route::get('/social/signup','FrontController@social_signup');
			/*Newsletter*/
			Route::get('/confirm-newsletter','FrontController@confirmNewsLetter');
			Route::put('/newsletter-subscribed','FrontController@subscribedNewsLetter');
		
			/* SOCIAL CONFIGURATION */
			Route::get('/login/facebook','FrontController@facebook');
			Route::get('/login/twitter','FrontController@twitter');
			Route::get('/login/instagram','FrontController@instagram');
			Route::get('/login/linkedin','FrontController@linkedin');
			Route::get('/login/googleplus','FrontController@googleplus');

			Route::get('/facebook/callback','FrontController@facebook_callback');
			Route::get('/login/instagram/callback','FrontController@instagram_callback');
			Route::get('/login/twitter/callback','FrontController@twitter_callback');

			/*STATIC PAGES*/
			Route::get('/page/{slug}','FrontController@staticpage');		
			Route::post('/page/_contact','FrontController@_contactpage');		
			Route::get('/pricing_page','EmployerController@hire_premiums_talents');		

			Route::get('/country_phone_codes','AjaxController@country_phone_codes');
			Route::get('/countries','AjaxController@countries');
			Route::get('/states','AjaxController@states');
			Route::get('/cities','AjaxController@cities');
			Route::get('/currency-exchange','FrontController@currency_exchange');

			Route::get('/talents','AjaxController@talents');
			Route::get('/employers','AjaxController@employers');

			Route::get('/talents_members','AjaxController@talents_members');


			/***FAQ RESPONSE***/
			Route::post('/like-dislike','FrontController@faq_response');   

		});

		/*TALENT LOGIN*/		
		Route::group(['middleware' => ['\App\Http\Middleware\TalentAuth','revalidate'],'prefix'=> TALENT_ROLE_TYPE, 'namespace' => 'Front'], function () { 

			/* PROFILE ROUTES*/
			Route::get('/{profile}','TalentController@viewprofile')->where('profile','profile/view|profile');
			Route::get('/profile/step/{step}','TalentController@profile_step');
			Route::get('/profile/edit/step/{step}','TalentController@profile_step');
			Route::post('/profile/step/process/{step}','TalentController@profile_step_process');
			Route::post('/doc-submit','TalentController@save_document');
			Route::post('/work-experience','TalentController@save_work_experience');
			Route::post('/add-education','TalentController@save_education');
			Route::post('/firm-jurisdiction','TalentController@firm_jurisdiction');
  
			/*PORTFOLIO ROUTES*/
			Route::get('/profile/portfolio','PortfolioController@view_portfolio'); 
			Route::get('/profile/portfolio/add','PortfolioController@addportfolio');
			Route::post('/profile/portfolio/__add','PortfolioController@__addportfolio');
			Route::get('/profile/portfolio/edit','PortfolioController@editportfolio');
			Route::get('/profile/portfolio/view','PortfolioController@singleportfolio');
			Route::post('/profile/portfolio/image','PortfolioController@portfolio_image');

			Route::post('/port-doc-submit','PortfolioController@portfolia_save_document');
 
			/*REVIEW ROUTES*/
			Route::get('/profile/reviews','TalentController@view_reviews');   
			
			/*TALENT AVAILABILITY*/
			Route::get('/availability/setup','TalentController@set_availability'); 
			Route::post('/availability/save','TalentController@save_availability');
			Route::post('/profile/availability/edit','TalentController@get_availability');
			Route::get('/availability','TalentController@talent_availability');
			Route::get('/get-availability','TalentController@get_talent_availability'); 
			
			/*JOBS*/
			Route::get('/find-jobs','TalentController@find_jobs');	
			Route::post('/_find-jobs','TalentController@_find_jobs');	
			
			Route::get('/find-jobs/{type}','TalentController@project_details'); 
			Route::get('/project/details','TalentController@project_details');
			Route::get('/project/status/{status}','TalentController@project_status');
			Route::get('/find-jobs/job-details','TalentController@job_details');
			Route::get('/project/dispute/details','TalentController@job_dispute_detail');
			Route::get('/jobs/save-job','TalentController@save_job');

			/*PROPOSALS*/
			Route::post('/proposals/submit','TalentController@submit_proposal');
			Route::post('/proposals/submit/document','TalentController@proposal_document');
			Route::get('/proposals','TalentController@proposals');
			Route::get('/proposals/{type}','TalentController@proposals');
			
			Route::get('/jobs/{review}/job-details','TalentController@job_details');
			Route::get('/jobs/job-details','TalentController@job_details');
			Route::get('/jobs/get-talent-reviews','TalentController@get_talent_reviews');
			Route::get('/jobs/reviews/{type}','TalentController@reviews');
			Route::get('/jobs/get-employer-reviews','TalentController@get_employer_reviews');
			Route::get('/jobs/actions/{action}','TalentController@actions');
			Route::get('/job/actions','TalentController@job_actions');

			Route::get('/my-jobs','TalentController@myjobs');
			Route::get('/my-jobs/start','TalentController@start_job');
			Route::get('/my-jobs/close','TalentController@close_job');
			Route::get('/my-jobs/disputes','TalentController@job_dispute_detail');
			Route::get('/my-jobs/{type}','TalentController@myjobs');

			Route::post('/save/working/hours','TalentController@save_working_hours');

			// Route::get('/my-jobs','TalentController@saved_jobs');
			// Route::get('/my-jobs/saved','TalentController@saved_jobs');
			// Route::get('/my-jobs/current','TalentController@current_jobs');
			// Route::get('/my-jobs/scheduled','TalentController@jobs_scheduled');
			// Route::get('/my-jobs/history','TalentController@past_job_history');
			
			/*APPLY JOB*/
			// Route::get('/jobs/apply','TalentController@apply_job');

			Route::get('/chat','TalentController@chat');
			Route::get('/chat/initiate-chat-request','TalentController@initiate_chat_request');

			/* PAYMENT */
			Route::get('/payment/start','TalentController@payment_talent');
			Route::post('/payment_method','TalentController@payment_method');

			/* ADDCARD */
			Route::get('/add/card','TalentController@add_card');
			Route::post('/add_payment_card','TalentController@add_payment_card'); 
			
			Route::get('/profile/notifications','TalentController@view_notifications');			
			Route::get('/notifications/list','TalentController@notification_list');
			Route::get('/notifications/mark/read','TalentController@mark_read_notification');

			/*NOTIFICATION SETTINGS*/
			Route::get('/{settings}','TalentController@notificationsettings')->where('settings','settings/notification|settings');
			Route::post('/__notificationsettings','TalentController@__notificationsettings');

			/*CHANGE PASSWORD*/
			Route::get('/settings/change-password','TalentController@change_password');
			Route::post('/__change-password','TalentController@__change_password');

			/*SOCIAL SETTINGS*/
			Route::get('/settings/social','TalentController@socialsettings');
			Route::post('/__socialsettings','TalentController@__socialsettings');

			/*CURRENCY EXCHANGE*/
			Route::get('/settings/currency','TalentController@currency_exchange');

			/*PAYPAL CONFIGURATION*/
			Route::get('/settings/payments','TalentController@payments');
			Route::post('/__payments','TalentController@__payments'); 
			Route::get('/verified-paypal-email','TalentController@save_verified_paypal_email'); 

			/*TRANSFER OWNERSHIP*/
			Route::get('/settings/transferownership','TalentController@transferownership');
			Route::get('/accept-transfer-ownership','TalentController@accept_transfer');
			Route::post('/accept-transfer-ownership/process','TalentController@post_accept_transfer');
			Route::get('/settings/transferownership/list','TalentController@transferownership_list');
			Route::post('/__transferownership','TalentController@__transferownership');
			Route::get('/confirm-transfer-ownership','TalentController@confirm_transfer');
			Route::post('/confirm-transfer-ownership/process','TalentController@post_confirm_transfer');
			Route::get('/accept-reject-transfer-ownership','TalentController@accept_reject_transfer');
			Route::get('accept-reject-transfer-modal/{id}','TalentController@accept_reject_transfer_modal');
			Route::post('accept-reject-transfer-save','TalentController@accept_reject_transfer_save');

			/*PAYMENT*/
			Route::get('/wallet','TalentController@wallet');
			Route::get('/wallet/{all}','TalentController@wallet'); 
			Route::get('/wallet/{paid}','TalentController@wallet');
			Route::get('/wallet/{disputed}','TalentController@wallet');
			Route::get('/wallet/request/payout','TalentController@payout_request');
			Route::post('/profile/availability/delete','AjaxController@delete_availability');

			Route::get('/networks','TalentController@networks');

			/*COMMUNITY*/
			Route::get('/network/mynetworks','TalentController@mynetworks');  
			Route::get('/network/members','TalentController@members');
			Route::get('/network/events','TalentController@events');
			
			Route::get('/add-to-circle','TalentController@add_member');

			Route::get('/invite-to-crowbar','TalentController@invite_to_crowbar');
			Route::post('/invite-to-crowbar','TalentController@send_invite_to_crowbar');

			Route::get('/acceptmember','TalentController@acceptmember'); 

			Route::get('/network/post-event','TalentController@post_event');
			Route::post('/post-event/process','TalentController@save_post_event');
			Route::post('/post-event/process/draft','TalentController@save_post_event_draft'); 
			Route::post('/delete-event','TalentController@delete_event');
			Route::get('/network/edit-event','TalentController@edit_event');
			Route::post('/post-event/editprocess/{hashid}','TalentController@update_event');
			Route::post('/post-event/file','TalentController@save_event_file');
			Route::post('/delete_event_file','TalentController@delete_event_file');
			Route::get('/addmember','TalentController@addmember');
			Route::post('/addmember/note','TalentController@addmember_note');
			Route::get('/invite-member','TalentController@invite_member');
			Route::post('/invite-member/process','TalentController@post_invite_member');
			Route::get('/get_user_emails','TalentController@get_user_emails');

			Route::get('/addRsvp','TalentController@addRsvp');
			Route::get('/fav-event','TalentController@save_fav_event');

			Route::get('/view/{hashid}','TalentController@view_talent_profile');

			/*INTERVIEW*/
			Route::get('/my-interview','InterviewController@myInterview');
			Route::get('/interview','InterviewController@interview_question');
			Route::post('/interview','InterviewController@save_interview_answer');
			Route::get('/interview-summary','InterviewController@interviewAnswerReview');

			/*RAISE DISPUTE*/
			Route::get('/raise/dispute/{type}','DisputeController@submit');
			Route::post('/project/submit/dispute','DisputeController@submit');

			/*RATINGS*/
			Route::get('/project/submit/reviews','ReviewController@submit_review_talent');
			Route::post('/project/submit/reviews','ReviewController@submit_review_talent');
			Route::get('/project/received/reviews','ReviewController@received_review_talent');


			Route::get('/refund','TalentController@refund');

			/*RAISE DISPUTE*/
			Route::post('/document/raisedispute','TalentController@raise_dispute_document');

			/*TALENT CONNECT*/
			Route::get('/talent-connect','TalentController@viewTalentConnect');
			Route::post('/talent-connect/send-mail','TalentController@sendInviteToTalent');
			Route::post('/talent-connect/store','TalentController@storeTalentConnect');
			Route::post('/talent-connect/remove/{id}','TalentController@removeTalentConnect');
			Route::post('/talent-connect/validate-talent/','TalentController@validateTalentConnect');
			Route::get('/connect-with-talent/','TalentController@connectWithTalent');
			Route::post('/connect-with-talent/','TalentController@connectTalentByInviteCode');
			Route::post('/talent-connect/unlink/','TalentController@unlinkConnectedTalent');

			/*Talent Disconnect*/
			Route::get('/send-unlink-application/','TalentController@unlinkFromFirm');
			Route::post('/send-unlink-application/','TalentController@sendUnlinkRequest');

			/*Transfer Job */
			Route::get('/disconnected-job-list/{talent_id}','TalentController@disconnectedTalentJobs');
			Route::get('project/connected-talent/','TalentController@connectedTalentList');
			Route::get('project/transfer-project/','TalentController@transferJob');
		});
		
		/*SOCIAL CONFIGURATION TO CONNECT FOR TALENT*/
		Route::group(['prefix'=> sprintf('%s/connect',TALENT_ROLE_TYPE)], function () {	
			Route::post('/_verify_phone','Front\TalentController@_verify_phone');
			Route::post('/_verify_otp','Front\TalentController@_verify_otp');
		});

		/*NOTIFICATION*/
		Route::group(['prefix'=> 'notification', 'namespace' => 'Front'], function () {	
			Route::get('/desktop/mark/read','FrontController@mark_read_desktop');
			Route::get('/mark/read','TalentController@mark_read_notification'); 
		});

		/*EMPLOYER LOGIN*/		
		Route::group(['middleware' => ['\App\Http\Middleware\EmployerAuth','revalidate'],'prefix'=> EMPLOYER_ROLE_TYPE, 'namespace' => 'Front'], function () {
			/*EMPLOYER SETUP PROFILE STEPS*/
			Route::get('/hire-talent','EmployerController@hire_talent');
			Route::get('/hire/talent/{step}','EmployerController@job_post');
			Route::get('/check-job/payment-configure','EmployerController@check_jobpayment_configure');
			Route::get('/hire/talent/edit/{step}','EmployerController@job_post');
			Route::post('/hire/talent/process/{step}','EmployerController@job_post_process');
			
			Route::get('/{profile}','EmployerController@view_profile')->where('profile','profile/view|profile');
			Route::get('/profile/edit/{step}','EmployerController@profile_step');
			Route::post('/profile/process/{step}','EmployerController@profile_step_process');

			// Route::get('/profile/general','EmployerController@step_one');
			// Route::post('/_step_one','EmployerController@_step_one');

			// Route::get('/profile/setup','EmployerController@step_two');
			// Route::post('/_step_two','EmployerController@_step_two');

			// Route::get('/profile/verify-account','EmployerController@step_three');
			// Route::post('/_step_three','EmployerController@_step_three');

			// Route::get('/dashboard','EmployerController@index');

			// Route::get('/post-job','EmployerController@post_job');
			// Route::post('/_post_job','EmployerController@_post_job');
			
			/*JOBS*/
			Route::get('/project/details','EmployerController@project_details');
			Route::get('/project/status/{status}','EmployerController@project_status');
			Route::get('/project/dispute/details','EmployerController@job_dispute_detail');
			Route::get('/my-jobs/start','EmployerController@start_job');
			Route::get('/my-jobs/close','EmployerController@close_job');
			Route::get('/my-jobs','EmployerController@myjobs');
			Route::get('/my-jobs/{type}','EmployerController@myjobs');
			Route::get('/job/actions','EmployerController@job_actions');
			Route::get('/my-jobs/{page}/job_details','EmployerController@job_details');
			Route::get('/project/delete-job','EmployerController@delete_job');
			Route::get('/project/cancel-job','EmployerController@cancel_job');

			/*RATINGS*/
			Route::get('/project/submit/reviews','ReviewController@submit_review_employer');
			Route::post('/project/submit/reviews','ReviewController@submit_review_employer');
			Route::get('/project/received/reviews','ReviewController@received_review_employer');
			
			/*FIND TALENTS*/
			Route::get('/find-talents','EmployerController@find_talents');
			Route::post('/_find-talents','EmployerController@_find_talents');
			Route::get('/find-talents/profile','EmployerController@talent_profile');
			Route::get('/find-talents/work-history','EmployerController@talent_work_history');
			Route::get('/find-talents/reviews','EmployerController@talent_reviews');
			Route::get('/find-talents/portfolio','EmployerController@talent_portfolio');
			Route::get('/find-talents/interview','EmployerController@talent_interview');
			Route::get('/save','EmployerController@save_talent');
			
			/*AVAILABILITY*/
			Route::get('/find-talents/availability','EmployerController@talent_availability');
			Route::get('/get-talents-availability','EmployerController@get_talent_availability');

			Route::get('/project/proposals','EmployerController@projectproposals');
			Route::get('/project/proposals/detail','EmployerController@proposal_listing');
			Route::get('/project/proposals/talent','EmployerController@proposal_details');
			Route::get('/proposals/accept','EmployerController@proposals_accept');
			Route::get('/proposals/decline','EmployerController@proposals_decline');
			Route::get('/proposals/tag','EmployerController@proposals_tag');

			/*View Profile*/
			Route::get('/profile','EmployerController@view_profile');
			Route::get('/profile/view','EmployerController@view_profile');
			Route::get('/profile/reviews','EmployerController@view_reviews');
			Route::get('/profile/edit/general','EmployerController@edit_general');
			Route::get('/profile/edit/setup','EmployerController@edit_setup');
			Route::get('/profile/edit/verify-account','EmployerController@edit_verify_account');
			
			Route::get('/chat','EmployerController@chat');
			Route::get('/chat/employer-chat-request','EmployerController@employer_chat_request');

			/* HIRE  PREMIMUM  TALENTS */
			Route::get('/plan-purchase/{id_plan}','EmployerController@plan_purchase');
			Route::post('/plan/payment/initiate/{id_plan}','EmployerController@plan_payment_initiate');
			Route::get('/hire-premium-talents','EmployerController@hire_premiums_talents');
			Route::get('/checkout','EmployerController@checkout');
			Route::post('/proceed_payment','EmployerController@proceed_payment');

			Route::post('/_hire-premium-talents','EmployerController@_hire_premium_talents');

			/* PAYMENT */
			Route::get('/payment/start','EmployerController@payment_talent');
			Route::post('/payment_method','EmployerController@payment_method');

			/*PAYPAL CONFIGURATION*/
			Route::get('/settings/payments','EmployerController@setting_payments');
			Route::post('/__payments','EmployerController@__payments'); 

			/* ADD CARDS */
			Route::get('payments/settings','EmployerController@payment_manage_card');
			Route::get('/payment/card/manage','EmployerController@payment_manage_card');
			Route::post('/payment/card/select','EmployerController@payment_select_card');
			Route::post('/payment/card/add','EmployerController@payment_add_card');
			Route::get('/payment/card/delete','EmployerController@payment_delete_card');
			
			Route::get('/profile/notifications','EmployerController@view_notifications');
			Route::get('/notifications/list','EmployerController@notification_list');
			Route::get('/notifications/mark/read','EmployerController@mark_read_notification');
			
			/*CHANGE PASSWORD*/
			Route::get('/change-password','EmployerController@change_password');
			Route::post('/__change-password','EmployerController@__change_password');
			
			/*NOTIFICATION SETTINGS*/
			Route::get('/{settings}','EmployerController@notificationsettings')->where('settings','settings/notification|settings');
			Route::post('/__notificationsettings','EmployerController@__notificationsettings');

			/*CHANGE PASSWORD*/
			Route::get('/settings/change-password','EmployerController@change_password');
			Route::post('/__change-password','EmployerController@__change_password');

			/*SOCIAL SETTINGS*/
			Route::get('/settings/social','EmployerController@socialsettings');
			Route::post('/__socialsettings','EmployerController@__socialsettings');

			/*INVITE TALENT*/
			Route::get('/settings/invite-talent','EmployerController@invite_talent');
			Route::post('/__invitetalent','EmployerController@__invitetalent');

			/*CURRENCY EXCHANGE*/
			Route::get('/settings/currency','EmployerController@currency_exchange'); 
			
			/*PAYMENT*/
			Route::get('/payments','EmployerController@payments');
			Route::get('/payments/detail','EmployerController@payments_detail');
			Route::get('/payments/{all}','EmployerController@payments');
			Route::get('/payments/{paid}','EmployerController@payments');
			Route::get('/payments/{refunded}','EmployerController@payments');
			Route::get('/payment/checkout','EmployerController@payment_checkout');

			#Route::post('/payment/create-payment','EmployerController@new_expchk');
			#Route::post('/payment/execute-payment','EmployerController@execute_expchk');

			Route::post('/payment/initiate','EmployerController@payment_initiate');
			Route::get('/payment/success','EmployerController@payment_callback');

			/*RECURSIVE PAYMENT(MONTHLY)*/
			Route::get('/payment/paypal-billing-success','EmployerController@paypal_payment_billing_success');
			Route::get('/payment/paypal-billing-cancel','EmployerController@paypal_payment_billing_cancel');


			Route::get('/payment/paypal-success','EmployerController@paypal_payment_success');
			Route::get('/payment/cancel','EmployerController@payment_callback');
			Route::get('/payment/paypal-cancel','EmployerController@paypal_payment_cancel');

			/*RAISE DISPUTE*/
			Route::get('/project/submit/dispute/{type}','DisputeController@submit');
			Route::post('/project/submit/dispute','DisputeController@submit');

			/*HIRE TALENT*/
			Route::get('/hire/talent','EmployerController@hire_talent');
			Route::post('/hire/talent','EmployerController@hire_talent');
			Route::get('/invitation-list','EmployerController@invitation_list');
			Route::post('/invitation-status','EmployerController@invitation_status');

			/*RAISE DISPUTE*/
			Route::post('/document/raisedispute','EmployerController@raise_dispute_document');		

			/*HIRE TALENT*/
			Route::post('/existingjob','EmployerController@existingjob');	
			Route::post('/sendmessage','EmployerController@sendmessage');	
			Route::get('/prepare_message','EmployerController@prepare_message');	

			/*CONNECTED TALENT*/
			Route::get('/find-talents/connected-talent','EmployerController@getConnectedTalentList');
			
		});

		/*SOCIAL CONFIGURATION TO CONNECT FOR EMPLOYER*/
		Route::group(['prefix'=> sprintf('%s/connect',EMPLOYER_ROLE_TYPE)], function () {	
			Route::post('/_verify_phone','Front\EmployerController@_verify_phone');
			Route::post('/_verify_otp','Front\EmployerController@_verify_otp');
		});


		Route::get(sprintf('%s/profile/phone',TALENT_ROLE_TYPE),'Front\TalentController@verify_phone');
		Route::get(sprintf('%s/profile/verify-otp',TALENT_ROLE_TYPE),'Front\TalentController@verify_otp');
		Route::get(sprintf('%s/profile/edit/verify-account/phone',TALENT_ROLE_TYPE),'Front\TalentController@edit_verify_phone');
		Route::get(sprintf('%s/profile/edit/verify-account/otp',TALENT_ROLE_TYPE),'Front\TalentController@edit_verify_otp');

		Route::get(sprintf('%s/profile/phone',EMPLOYER_ROLE_TYPE),'Front\EmployerController@verify_phone');
		Route::get(sprintf('%s/profile/verify-otp',EMPLOYER_ROLE_TYPE),'Front\EmployerController@verify_otp');
		Route::get(sprintf('%s/profile/edit/verify-account/phpne',EMPLOYER_ROLE_TYPE),'Front\EmployerController@edit_verify_phone');
		Route::get(sprintf('%s/profile/edit/verify-account/otp',EMPLOYER_ROLE_TYPE),'Front\EmployerController@edit_verify_otp');

		Route::group(['middleware'=>'\App\Http\Middleware\PublicAuth', 'prefix' => 'ajax','namespace' => 'Front'],function(){
			Route::post('edit-talent-education','AjaxController@get_talent_education');
			Route::post('edit-talent-experience','AjaxController@get_talent_experience'); 
			Route::post('delete-user-document','AjaxController@delete_user_document');
			Route::post('state-list','AjaxController@country_state_list');
			Route::post('city-list','AjaxController@state_city_list');
			Route::post('subindustry-list','AjaxController@industry_subindustry_list');
			Route::post('subindustry-skill-list','AjaxController@subindustry_skill_list');
			Route::get('subindustry-skills-list','AjaxController@subindustry_skills_list');
			Route::get('resend_activation_link','AjaxController@resend_activation_link');			
			Route::post(DELETE_TALENT_EDUCATION,'AjaxController@delete_talent_education');
			Route::post(EDIT_TALENT_EDUCATION,'AjaxController@edit_talent_education');
			Route::post(EDIT_TALENT_EXPERIENCE,'AjaxController@edit_talent_experience');
			Route::post(DELETE_TALENT_EXPERIENCE,'AjaxController@delete_talent_experience');
			Route::post(DELETE_DOCUMENT,'AjaxController@delete_document');
			Route::post(DELETE_CARD,'AjaxController@delete_card');
			Route::post(DELETE_PORTFOLIO,'AjaxController@delete_portfolio');
			Route::post('crop','AjaxController@crop');
			Route::post('validate-calendar','AjaxController@validate_calendar');
			Route::get('find_all_jobs','AjaxController@find_all_jobs');
			Route::get('country-state-list','AjaxController@country_state_list');
			Route::get('state-city-list','AjaxController@state_city_list');
			Route::get('industry-subindustry-list','AjaxController@industry_subindustry_list');
			Route::get('city-list','AjaxController@city_list');
			Route::get('state-list','AjaxController@state_list');
			Route::get('user-state-list','AjaxController@user_state_list');
			Route::get('notification/list','AjaxController@notification_list');
			Route::get('notification/count','AjaxController@notification_count');
		});

		/*DOWNLOAD ATTACHMENT*/
		Route::get('/download/file','Front\FrontController@download_file');


	});
	
	Route::get('/project/dispute/details', function () {
		return redirect(sprintf('%s/project/dispute/details?job_id=%s',request()->user()->type,request()->job_id)); 
	});
	Route::get('paypal-express-checkout','Front\FrontController@paypal_express_checkout');
	Route::get('paypal-express-checkout-callback','Front\FrontController@paypal_payment_callback');
	Route::get('paypal-express-checkout-cancel-callback','Front\FrontController@paypal_payment_cancel_callback');
	Route::get('paypal-payment-success', 'Front\FrontController@paypal_payment_success');
	Route::get('paypal-payment-cancel', 'Front\FrontController@paypal_payment_cancel');

	/*Recurring Monthly*/
	Route::get('/payment/paypal-billing-success','Front\FrontController@recurrsive_success_url');
	Route::get('/payment/paypal-billing-cancel','Front\FrontController@recurrsive_cancel_url');

	/*LOGOUT*/
	Route::get('/logout', function () {
        /* RECORDING ACTIVITY LOG */
        if(!empty(\Auth::guard('web')->check())){
	        event(new \App\Events\Activity([
	            'user_id'           => \Auth::guard('web')->user()->id_user,
	            'user_type'         => \Auth::guard('web')->user()->type,
	            'action'            => \Auth::guard('web')->user()->type.'-logout',
	            'reference_type'    => 'users',
	            'reference_id'      => \Auth::guard('web')->user()->id_user
	        ]));
        }

		\Auth::logout();
		\Session::forget('social'); 
		Session::forget('front_login');  

		return redirect('/login'); 
	});

	Route::get('/clear-cache', function() {
	    $exitCode = Artisan::call('cache:clear'); 
	});

	Route::get('/cron/payments', function() {
	    $exitCode = Artisan::call('transferamount');
	});

	Route::get('/cron/refundpayment', function() { 
	    $exitCode = Artisan::call('refundpayment');
	});	

	Route::get('/currency-conversion', function() {
	    $exitCode = Artisan::call('currencyconversion'); 
	});

	#templogin

	Route::get('/templogin', function () { return redirect('/'); }); 
	
	// Route::get('templogin', 'TempController@login');
	Route::post('templogin', 'TempController@auth');
	Route::get('create_paypal_plan', 'PaypalController@create_plan');
	