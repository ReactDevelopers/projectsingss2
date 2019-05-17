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

Route::group(['prefix'=>'v1'],function($app){
	\App::setlocale(!empty(request()->language) ? request()->language : DEFAULT_LANGUAGE);
	Route::group(['namespace'=>'Api'],function($app){
		Route::post('general','ApiController@general');
		Route::post('login','ApiController@login');
		Route::post('logout','ApiController@logout');
		Route::post('touch/login','ApiController@touch_login');
		Route::post('talentsignup','ApiController@talentsignup');
		Route::post('edit/talentsignup','ApiController@edit_talentsignup');
		Route::post('employersignup','ApiController@employersignup');
		Route::post('edit/employersignup','ApiController@edit_employersignup');
		Route::post('forgotpassword','ApiController@forgotpassword');
		Route::post('verifyforgotpassword','ApiController@verifyforgotpassword');
		Route::post('resetpassword','ApiController@resetpassword');
		Route::post('resend/activation-link','ApiController@resend_activation_link');
		Route::post('page/{slug}','ApiController@staticpage');
		Route::get('getAllCountry','ApiController@getAllCountry');
		Route::post('getState','ApiController@getState');
		Route::post('getCity','ApiController@getCity');
		Route::post('getStateCity','ApiController@getStateCity');
		Route::post('getBank','ApiController@getBank');
		Route::get('getSpecialRequest','ApiController@getSpecialRequest');
		Route::get('get_common_data','ApiController@get_common_data');
		Route::get('getUsers','ApiController@getUsers');
		Route::post('merchant_login','ApiController@merchant_login');
		Route::post('customer_login','ApiController@customer_login');
		Route::post('merchant_registration','ApiController@merchant_registration');
		Route::post('customer_registration','ApiController@customer_registration');
		Route::post('forgot_password','ApiController@forgot_password');
		Route::post('forgot_password_update','ApiController@forgot_password_update');
		Route::post('resend_otp','ApiController@resend_otp');
		Route::post('/country_phone_codes','ApiController@country_phone_codes');
		Route::post('/countries','ApiController@countries');
		Route::post('/states','ApiController@states');
		Route::post('/cities','ApiController@cities');



		Route::get('/subscription-respond','ApiController@subscription_respond');
		Route::post('/subscription-respond','ApiController@subscription_respond');
	});

	Route::group(['middleware' => ['auth:api','\App\Http\Middleware\Webservice'],'prefix' => 'talent', 'namespace' => 'Api'], function($app){		

		/*select user type*/
		Route::post('/user_type','ApiController@user_type');		
		
		Route::post('viewprofile','Talent@viewprofile');
		Route::post('step-one','Talent@step_one');
		Route::post('step-one-picture','Talent@step_one_picture');
		Route::post('step-two','Talent@step_two');
		Route::post('step-three','Talent@step_three');
		Route::post('step-three-education','Talent@step_three_education');
		Route::post('step-three-education-edit','Talent@step_three_education_edit');
		Route::post('step-three-education-delete','Talent@step_three_education_delete');
		Route::post('step-three-work-experience','Talent@step_three_work_experience');
		Route::post('step-three-work-experience-edit','Talent@step_three_work_experience_edit');
		Route::post('step-three-work-experience-delete','Talent@step_three_work_experience_delete');
		Route::post('step-three-document','Talent@step_three_document');
		Route::post('step-three-document-delete','Talent@step_three_document_delete');
		Route::post('step-four-set-availability','Talent@step_four_set_availability');
		Route::post('step-four-delete-availability','Talent@step_four_delete_availability');
		Route::post('step-five-social-connect','Talent@step_five_social_connect');
		Route::post('/profile/step/process/{step}','Talent@profile_step_process');
		Route::post('/firm-jurisdiction','Talent@firm_jurisdiction');
		
		/*MOBILE VERIFICATION*/
		Route::post('step-five-change-mobile','Talent@step_five_change_mobile');
		Route::post('step-five-verify-mobile','Talent@step_five_verify_mobile');
		
		/*JOBS*/
		Route::post('jobs/find','Talent@find_jobs');
		Route::post('jobs/job-detail','Talent@job_detail');
		Route::post('project/status','Talent@project_status');
		Route::post('jobs/save-unsave','Talent@job_save_unsave');
		
		/*PROPOSALS*/
		Route::post('jobs/submit-proposal','Talent@submit_proposal');
		Route::post('jobs/{type}-proposals','Talent@proposals');
		Route::post('jobs/proposal-details','Talent@proposal_details');
		Route::post('jobs/apply-job','Talent@apply_job');

		/*MYJOBS*/
		Route::post('/my-jobs/start','Talent@start_job');
		Route::post('/my-jobs/close','Talent@close_job');
		Route::post('/my-jobs/{type}','Talent@myjobs');
		// Route::post('/my-jobs/saved','Talent@saved_jobs');
		// Route::post('/my-jobs/current','Talent@current_jobs');
		// Route::post('/my-jobs/scheduled','Talent@scheduled_jobs');
		// Route::post('/my-jobs/history','Talent@history_jobs');

		/*CHANGE PASSWORD*/
		Route::post('/change-password','Talent@change_password');
		
		/*PAYPAL CONFIGURATION*/
		Route::post('paypal/configuration','Talent@paypal_configuration');
		
		/*PORTFOLIO*/
		Route::post('portfolio/add','Talent@add_portfolio');		
		Route::post('portfolio/delete','Talent@delete_portfolio');
		Route::post('portfolio/list','Talent@list_portfolio');
		Route::post('portfolio/image','Talent@image_portfolio');
		Route::post('portfolio/image/delete','Talent@delete_portfolio_image');

		/*WALLET*/
		Route::post('wallet/list','Talent@wallet');

		/*REVIEW*/
		Route::post('/review/add','Talent@add_review');
		Route::post('/reviews/list','Talent@talent_reviews');
		Route::post('/reviews/job/list','Talent@employer_reviews');

		/*RAISE DISPUTE*/
		Route::post('/raise/dispute/details','Talent@raise_dispute_detail');
		Route::post('/raise/dispute','Talent@raise_dispute');
		
		/*SETTINGS*/
		Route::post('/settings/get','Talent@settings');
		Route::post('/settings/save','Talent@savesettings');
		Route::post('/settings/add/thumb/device','Talent@add_thumb_device');

		Route::post('/request/payout','Talent@payout_request');
		Route::post('/get-availability','Talent@get_my_availability');
		Route::post('/change-language','ApiController@change_language');
		Route::post('/change-currency','Talent@change_currency');
		Route::post('/settings/verify-invitation','Talent@verify_invite_talent');
		Route::post('/save_working_hours','Talent@save_working_hours');

		/*Industry Listing*/
		Route::post('/industry/listing/new','Talent@industry_listing');

		/*Question*/
		Route::post('/forum/question/list', 'Talent@community_forum');
		Route::post('/forum/question/add', 'Talent@community_forum_add_question');
		Route::post('/forum/follow-question','Talent@ques_user_follow');
		Route::post('/forum/question/detail','Talent@community_forum_question');
		Route::post('/forum/question/answer/add','Talent@community_forum_add_answer');

		Route::post('/forum/vote_answer','Talent@community_forum_vote');
		/*Article*/
		Route::post('/network/article', 'Talent@community_article');
		Route::post('/network/article/answer/add','Talent@community_article_add_answer');
		Route::post('/network/article/detail/','Talent@show_article_details');
		Route::post('/network/article/add', 'Talent@articles_add_submit');
		/*Home Network My Feeds */
		Route::post('/network/home', 'Talent@community_home');
		Route::post('/network/home/get-group', 'Talent@home_get_group');
		Route::post('/addRsvp','Talent@addRsvp');
		/*Follow Question & Article */
		Route::post('/forum/question/follow-this-question','Talent@follow_this_ques');
		Route::post('/network/article/follow-this-article','Talent@follow_this_article'); 
		/*Bookmark Event*/
		Route::post('/network/home/fav-event','Talent@mark_event_favorite');
		/*Invite Member*/
		Route::post('/get-invite-emails','Talent@get_invite_emails');
		Route::post('/invite-member/process','Talent@post_invite_member');
		/*Events Listing*/
		Route::post('/network/events','Talent@events');


		/******* Group 3 *****/

		/*Invite*/
		Route::post('/talent-connect','Talent@viewTalentConnect');
		Route::post('/talent-connect-added-members','Talent@viewTalentAddedMember');
		Route::post('/talent-connect/store','Talent@storeTalentConnect');
		Route::post('/talent-connect/remove','Talent@removeTalentConnect');
		Route::post('/talent-connect/send-mail','Talent@sendInviteToTalent');
		Route::post('/talent-connect/unlink','Talent@unlinkConnectedTalent');
		// Invite Code Process
		Route::post('/connect-with-talent','Talent@connectTalentByInviteCode');

	});

	Route::group(['middleware' => ['auth:api','\App\Http\Middleware\Webservice'],'prefix' => 'employer', 'namespace' => 'Api'], function($app){
		Route::post('viewprofile','Employer@viewprofile');
		Route::post('step-one','Employer@step_one');
		Route::post('step-one-picture','Employer@step_one_picture');
		Route::post('step-two','Employer@step_two');
		Route::post('step-three-social-connect','Employer@step_three_social_connect');
		
		/*MOBILE VERIFICATION*/
		Route::post('step-three-change-mobile','Employer@step_three_change_mobile');
		Route::post('step-three-verify-mobile','Employer@step_three_verify_mobile');
		
		/*JOBS*/
		Route::post('draft-job/{step}','Employer@job_post');
		Route::post('hire/talent/process/{step}','Employer@job_post_process');
		Route::post('hire/talent/edit/process/{step}','Employer@edit_job_post_process');
		Route::post('delete-job','Employer@delete_job');
		Route::post('cancel-job','Employer@cancel_job');
		Route::post('post-job','Employer@post_job');
		Route::post('myjobs/{type}','Employer@employer_jobs');
		Route::post('job-detail','Employer@job_detail');
		Route::post('project/status','Employer@project_status');
		Route::post('talent-save-unsave','Employer@save_talent');
		Route::post('find-talents','Employer@find_talents');
		Route::post('/my-jobs/start','Employer@start_job');
		Route::post('/my-jobs/close','Employer@close_job');
		
		/*PROPOSALS*/
		Route::post('jobs/proposals','Employer@project_proposals');
		Route::post('jobs/proposal/detail','Employer@proposals_listing');
		Route::post('jobs/proposal/accept','Employer@accept_proposal');
		Route::post('jobs/proposal/decline','Employer@decline_proposal');
		Route::post('jobs/proposal/tag','Employer@proposals_tag');
		Route::post('jobs/proposal/tagged','Employer@tagged_proposals');
		
		/*TALENTS*/
		Route::post('talentprofile','Employer@talentprofile');
		Route::post('talent/availability','Employer@get_talent_availability');
		Route::post('talent/work-history','Employer@talent_work_history');
				
		/*OTHERS*/
		Route::post('user/info','ApiController@userinfo');
		Route::post('mobile_verification','ApiController@mobile_verification');
		Route::post('refresh_token','ApiController@refresh_token');
	    Route::post('getUserInfo/{id}','ApiController@getUserInfo'); 

		/*CHANGE PASSWORD*/
		Route::post('/change-password','Employer@change_password');

		/*PAYMENTS*/
		Route::post('payments/list','Employer@payments');

		/*PORTFOLIO*/
		Route::post('/talent/portfolio','Employer@talent_portfolio');		

		/*REVIEW*/
		Route::post('/review/add','Employer@add_review');
		Route::post('/review/list','Employer@employer_reviews');
		Route::post('/review/talent/list','Employer@talent_reviews');

		/*HIRE PREMIUM TALENTS*/
		Route::post('/hire-premium-talents','Employer@hire_premium_talents');

		/*RAISE DISPUTE*/
		Route::post('/raise/dispute/details','Employer@raise_dispute_detail');
		Route::post('/raise/dispute','Employer@raise_dispute');

		/*PAYMENT*/
		Route::post('/payment/card/add','Employer@payment_add_card');
		Route::post('/payment/card/manage','Employer@payment_manage_card');
		Route::post('/payment/card/select','Employer@payment_select_card');
		Route::post('/payment/card/delete','Employer@payment_delete_card');
		Route::post('/payment/checkout','Employer@payment_checkout');
		Route::post('/payment/confirm','Employer@payment_confirm');
		
		Route::post('/payment/detail','Employer@payment_detail');

		/*Payment for Job without PayPal Payment*/
		Route::post('/project/payout/mgmt','Employer@accept_payout_mgmt');

		/*Upgrade Membership*/
		Route::post('/upgrade/member/checkout','Employer@upgrade_member_checkout');
		Route::post('/upgrade/member/confirm','Employer@upgrade_member_payment_confirm');
		
		/*SETTINGS*/
		Route::post('/settings/get','Employer@settings');
		Route::post('/settings/save','Employer@savesettings');
		Route::post('/settings/add/thumb/device','Employer@add_thumb_device');

		/*PLAN AND FEATURES*/
		Route::post('/plan-features','Employer@plan_features');

		/*HIRE TALENT*/
		Route::post('/suggested/jobs','Employer@suggested_jobs');
		Route::post('/hire/talent','Employer@hire_talent');

		/*CHANGE CURRENCY*/
		Route::post('/change-currency','Employer@change_currency');
		Route::post('/change-language','ApiController@change_language');
		
		/*RAISE DISPUTE DOCUMENT*/
		Route::post('/document/raisedispute','Employer@raise_dispute_document');
		
		/*HIRE TALENT FOR EXISTING JOB*/
		Route::post('/hire/talent/existingjob','Employer@existingjob');
		Route::post('/hire/talent/sendmessage','Employer@sendmessage');
		
	});

	Route::group(['middleware' => ['auth:api','\App\Http\Middleware\Webservice'], 'prefix' => 'review', 'namespace' => 'Front'],function($app){
		Route::post('/details','ReviewController@review_detail');
	});
	
	Route::group(['prefix' => 'chat'], function($app){
		Route::get('chat-list','Front\ChatController@chat_list');
		Route::post('chat-list','Front\ChatController@chat_list');
		Route::get('chat-save','Front\ChatController@chat_save');
		Route::get('chat-update','Front\ChatController@chat_update');
		Route::get('chat-history','Front\ChatController@chat_history');
		Route::post('chat-history','Front\ChatController@chat_history');
		Route::get('chat-reject','Front\ChatController@chat_reject');
		Route::get('chat-accept','Front\ChatController@chat_accept');
		Route::get('chat-readall','Front\ChatController@chat_readall');
		Route::get('chat-deleteall','Front\ChatController@chat_deleteall');
		Route::get('chat-terminate','Front\ChatController@chat_terminate');
		Route::get('chat-report-abuse','Front\ChatController@chat_report_abuse');
		Route::get('chat-offline-messages','Front\ChatController@chat_offline_messages');
		Route::get('chat-user-status','Front\ChatController@chat_user_status');
		Route::post('chat-upload-images','Front\ChatController@chat_upload_image');
		Route::post('initiate-chat-request','Api\Talent@initiate_chat_request');
		Route::post('employer-chat-request','Api\Employer@employer_chat_request');
	});

	/*NOTIFICATION*/
	Route::group(['middleware' => ['auth:api','\App\Http\Middleware\Webservice'],'prefix' => 'notification', 'namespace' => 'Api'], function($app){
		Route::post('/list','Talent@notification_list');
		Route::post('/mark/read','Talent@mark_read_notification');
	});

	Route::group(['prefix' => 'notification', 'namespace' => 'Api'], function($app){
		Route::get('notification-list','Talent@notification_list');
		Route::get('notification-count','Talent@notification_count');
		Route::get('total-chat-count','Talent@total_chat_count');
	});
});
