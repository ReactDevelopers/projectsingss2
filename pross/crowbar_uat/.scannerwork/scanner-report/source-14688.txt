<?php
    define('TALENT_ROLE_TYPE' ,'talent');
    define('EMPLOYER_ROLE_TYPE' ,'employer');
    define('SUB_ADMIN_ROLE_TYPE' ,'sub-admin');
    define('NONE_ROLE_TYPE' ,'none');
    define('ADMIN_FOLDER' ,'administrator');
    define('FRONT_FOLDER' ,'front');
    
    /*LENGTH*/
    define('BIRTHDAY_MIN_YEAR_LIMIT' ,-0);
    define('BIRTHDAY_MAX_YEAR_LIMIT' ,-100);
    define('CREDIT_CARD_MIN_YEAR_LIMIT' ,-0);
    define('CREDIT_CARD_MAX_YEAR_LIMIT' ,10);
    define('PASSWORD_MIN_LENGTH' ,8);
    define('PASSWORD_MAX_LENGTH' ,20);
    define('FULL_NAME_MAX_LENGTH' ,40);
    define('NAME_MAX_LENGTH' ,20);
    define('JOB_TITLE_MIN_LENGTH' ,0);
    define('JOB_TITLE_MAX_LENGTH' ,80);
    define('COMPANY_NAME_MAX_LENGTH' ,50);
    define('PHONE_NUMBER_MAX_LENGTH' ,15);
    define('PHONE_NUMBER_MIN_LENGTH' ,8);
    define('DESCRIPTION_MIN_LENGTH' ,0);
    define('DESCRIPTION_MAX_LENGTH' ,5000);
    define('DESCRIPTION_COUNTER_LENGTH' ,200);
    define('SHORT_DESCRIPTION_MIN_LENGTH' ,0);
    define('SHORT_DESCRIPTION_MAX_LENGTH' ,500);
    define('READ_MORE_LENGTH' ,150);
    define('PROPOSAL_CONTENT_SHORT_LENGTH', 25);
    define('CARD_LENGTH', 19);
    define('CARD_CVV_LENGTH', 4);

    define('TEMPORARY_PRICE_MIN_LENGTH' ,0);
    define('TEMPORARY_PRICE_MAX_LENGTH' ,1500);
    define('PERMANENT_SALARY_MIN_LENGTH' ,0);
    define('PERMANENT_SALARY_MAX_LENGTH' ,5000000);

    define('JOBID_PREFIX' ,6);
    define('JOB_TITLE' ,'SEND_MESSAGE');
    define('RAISE_DISPUTE_DATE_LIMIT' ,1);
    define('RAISE_DISPUTE_STEP_1_HOURS_LIMIT' ,24);
    define('RAISE_DISPUTE_STEP_2_HOURS_LIMIT' ,24);
    define('RAISE_DISPUTE_STEP_3_HOURS_LIMIT' ,24);
    define('RAISE_DISPUTE_STEP_4_HOURS_LIMIT' ,24);
    define('RAISE_DISPUTE_STEP_5_HOURS_LIMIT' ,72);
    define('RAISE_DISPUTE_STEP_6_HOURS_LIMIT' ,24);
    define('EXPECTED_SALARY_MAX_LENGTH' ,5000000);
    define('EXPECTED_SALARY_MIN_LENGTH' ,0);
    define('POSTAL_CODE_MAX_LENGTH' ,6);
    define('POSTAL_CODE_MIN_LENGTH' ,4);
    define('MIN_AGE' ,14);
    define('EXPERIENCE_MAX_LENGTH',40);
    define('EXPERIENCE_MIN_LENGTH',1);
    define('BUDGET_MAX_LENGTH',20);
    define('BUDGET_MIN_LENGTH',0);
    define('JOB_YEAR_RANGE',5);
    define('TAG_LENGTH',50);
    define('NUMBER_OF_TOP_TALENT_LIST',4);
    define('CARD_NUMBER_MAX_LENGTH' ,19);
    define('CARD_NUMBER_MIN_LENGTH' ,12);
    define('CVV_MAX_LENGTH' ,4);
    define('CVV_MIN_LENGTH' ,3);
    define('PORTFOLIO_MIN_LENGTH' ,10);
    define('PORTFOLIO_MAX_LENGTH' ,30);
    define('PORTFOLIO_DESCRIPTION_MIN_LENGTH' ,0);
    define('PORTFOLIO_DESCRIPTION_MAX_LENGTH' ,500);
    define('REVIEW_DESCRIPTION_MIN_LENGTH' ,0);
    define('REVIEW_DESCRIPTION_MAX_LENGTH' ,500);
    define('RAISE_DISPUTE_REASON_MIN_LENGTH' ,0);
    define('RAISE_DISPUTE_REASON_MAX_LENGTH' ,1500); 
    define('REPORT_ABUSE_REASON_MIN_LENGTH' ,0);
    define('REPORT_ABUSE_REASON_MAX_LENGTH' ,250);
    define('HIRE_TALENT_MESSAGE_MIN_LENGTH' ,0);
    define('HIRE_TALENT_MESSAGE_MAX_LENGTH' ,250);
    define('SUPPORT_CHAT_USER_ID' ,2);
    define('QUESTION_RATE_LENGTH' ,5);
    define('DEFAULT_PRECISION' ,2);
    define('MAX_POST_JOB_MONTH' ,3);
    define('MONTH_DAYS' ,30);
    define('SANDBOX_BRAINTREE_AMOUNT_VALIDATION' ,'no');
    define('SANDBOX_BRAINTREE_MIN_AMOUNT' ,2000);
    define('SANDBOX_BRAINTREE_MAX_AMOUNT' ,3000);
    define('PROPOSAL_DOCUMENT_MAX_SIZE' ,55600);
    define('MARK_COMPLETED_ESCROWED_SECONDS' ,1*60*60);
    define('WORKRATE_MAX' ,5000000);
    define('FAQ_DESCRIPTION_MAX_LENGTH',0);
    define('FAQ_DESCRIPTION_MIN_LENGTH',5000);
    define('DAILY_WORKING_HOURS',8);
    
    /*COLUMN VALUE*/
    define('N_A','N/A');
    define('B_N_A','N/A');
    define('PRICE_UNIT','$');
    define('TRANSACTION_COMMENT','Sent successfully to %s on %s.');
    define('TRANSACTION_COMMENT_MANUAL','Payment will be sent manually to %s.');
    define('CURRENT_PROFILE_STEP','verify-account');
    define('TALENT_PROFILE_PHOTO_UPLOAD','/uploads/profile/');
    define('TALENT_ARTICLE_PHOTO_UPLOAD','/uploads/article/');
    define('TALENT_DEFAULT_PROFILE_PERCENTAGE','15');
    define('TALENT_STEP_ONE_PROFILE_PERCENTAGE_WEIGHTAGE',4.34782609);
    define('TALENT_STEP_TWO_PROFILE_PERCENTAGE_WEIGHTAGE',4.34782609);
    define('TALENT_STEP_THREE_PROFILE_PERCENTAGE_WEIGHTAGE',4.34782609);
    define('TALENT_STEP_FOUR_PROFILE_PERCENTAGE_WEIGHTAGE',4.34782609);
    define('TALENT_STEP_FIVE_PROFILE_PERCENTAGE_WEIGHTAGE',4.34782609);
    define('TALENT_STEP_PAYPAL_ID_PROFILE_PERCENTAGE_WEIGHTAGE',4.34782609); 
    define('EMPLOYER_STEP_ONE_COMPANY_PROFILE_PERCENTAGE_WEIGHTAGE',6.6667);
    define('EMPLOYER_STEP_ONE_INDIVIDUAL_PROFILE_PERCENTAGE_WEIGHTAGE',10.33333);
    define('EMPLOYER_STEP_TWO_COMPANY_PROFILE_PERCENTAGE_WEIGHTAGE',6.6667);
    define('EMPLOYER_STEP_TWO_INDIVIDUAL_PROFILE_PERCENTAGE_WEIGHTAGE',7.69230769231);
    define('EMPLOYER_STEP_THREE_PROFILE_PERCENTAGE_WEIGHTAGE',5);
    define('EMPLOYER_DEFAULT_PROFILE_PERCENTAGE','10');
    define('EMPLOYER_PROFILE_PHOTO_UPLOAD','/uploads/profile/');
    define('PRICING_TERMS_AND_CONDITION_PAGE_URL','/page/pricing-and-terms-and-conditions');
    define('TERMS_AND_CONDITION_PAGE_URL','/page/terms-and-conditions');
    define('POLICY_PAGE_URL','/page/privacy-policy');
    define('PRICING_PAGE_URL','/page/pricing');
    define('DEFAULT_COUNTRY_CODE','SG');
    define('DEFAULT_TIMEZONE','Asia/Singapore');
    define('DEFAULT_LANGUAGE','en');

    /*FIND TALENT*/
    define('CONTENT_NOT_AVAILABLE','The content is not available now.');
    define('MINIMUM_PERCENTAGE_FOR_SEARCHING',0);

    define('ALLOWED_DOCUMENTS' ,'pdf,png,jpg,jpeg,gif,docx,doc,xlsx,xls,txt,PDF,PNG,JPG,JPEG,GIF,DOCX,DOC,XLSX,XLS,TXT');
    define('ALLOWED_BANNER_IMAGE' ,'png,jpg,jpeg,gif,PNG,JPG,JPEG,GIF');
    define('PER_HOUR_POSTFIX' ,' per hr');
    define('EMPLOYER_OTHER_JOBS_LIMIT' ,'2');
    define('SIMILAR_JOBS_LIMIT' ,'2');
    define('TAGGED_PROPOSAL_LIMIT' ,'10');
    define('DEFAULT_PAGING_LIMIT' ,'10');
    define('DEFAULT_NOTIFICATION_LIMIT' ,'4');
    define('TALENT_SUBMIT_PROPOSAL_LIMIT' ,'2');
    define('CHAT_PAGING_LIMIT' ,'20');
    define('DEFAULT_VALUE' ,'N.A.');
    define('DEFAULT_YES_VALUE' ,'yes');
    define('DEFAULT_NO_VALUE' ,'no');
    define('DEFAULT_AVATAR_IMAGE' ,'avatar.png');
    define('DEFAULT_USER_IMAGE' ,'avatar.png');
    define('ADMINPATH', sprintf('%s/%s',env('APP_URL'),ADMIN_FOLDER));
    define('PROJECT_SLUG' ,'crowbar');
    define('CONTACT_NUMBER_MAX_LENGTH' ,20);
    define('POSTAL_CODE_LENGTH' ,6);
    define('LOGIN_REMEMBER' ,PROJECT_SLUG.'_remember');
    define('LOGIN_PASSWORD' ,PROJECT_SLUG.'_password');
    define('LOGIN_EMAIL' ,PROJECT_SLUG.'_email');
    define('SITE_NAME' ,'Crowbar');
    define('SITE_TITLE' ,'Crowbar - Find the best talent for your project');
    define('DELETE_TALENT_EDUCATION' ,'delete_talent_education');
    define('EDIT_TALENT_EDUCATION' ,'edit_talent_education');
    define('EDIT_TALENT_EXPERIENCE' ,'edit_talent_experience');
    define('DELETE_TALENT_EXPERIENCE' ,'delete_talent_experience');
    define('DELETE_CARD' ,'delete_card');
    define('DELETE_DOCUMENT' ,'delete_document');
    define('DELETE_PORTFOLIO' ,'delete_portfolio');
    define('SUBMISSION_FEE',-1);
    define('CROP_WIDTH',500);
    define('CROP_HEIGHT',500);
    define('INDUSTRY_CROP_WIDTH',76);
    define('INDUSTRY_CROP_HEIGHT',73);
    define('COLLEGE_CROP_WIDTH',76);
    define('COLLEGE_CROP_HEIGHT',73);
    define('COMPANY_CROP_WIDTH',76);
    define('COMPANY_CROP_HEIGHT',73);
    define('DEFAULT_COUNTRY_LIMIT',100);
    define('DEFAULT_STATE_LIMIT',100);
    define('DEFAULT_CITY_LIMIT',100);
    define('MINIMUM_RELIEVING_YEAR',1900);
    define('MAXIMUM_RELIEVING_YEAR',(date('Y')));
    define('MAXIMUM_JOINING_YEAR',date('Y'));

    define('BANNER_WIDTH',1920);
    define('BANNER_HEIGHT',600);
    define('BANNER_TEXT_MIN_LENGTH' ,10);
    define('BANNER_TEXT_MAX_LENGTH' ,20);

    /*CHAT*/
    define('CHAT_EMPLOYER_GREETING_MESSAGE','M0270');
    define('CHAT_TALENT_GREETING_MESSAGE','M0271');
    define('CHAT_EMPLOYER_NEW_REQUEST','M0267');
    define('CHAT_EMPLOYER_START_REQUEST','M0285');
    
    /*PAYMENT*/
    define('DEFAULT_CURRENCY','USD');
    define('BYPASS_ESCROW_PAYMENT',false);
    define('ESCROW_PAYMENT_TYPE','PAYPAL');
    define('MASSPAY_EMAIL_SUBJECT','Payment received');
    define('MASSPAY_CURRENCY',DEFAULT_CURRENCY);
    define('REFUNDABLE_DATE_MARGIN',7);
    
    define('PayPal_BASE_URL_SANDBOX','https://api.sandbox.paypal.com/v1/');
    define('PayPal_BASE_URL_LIVE','https://api.paypal.com/v1/');

    define('GOOGLE_MAP_AUTO_SEARCH_KEY','AIzaSyCFN6hK1mAT4moig0d3PEVCr4-NJaTrqLA');


    /*MASSPAY PAYPAL*/
    define('USE_PROXY',FALSE);
    define('PROXY_HOST', '127.0.0.1');
    define('PROXY_PORT', '808');
    
    define('ACK_SUCCESS', 'SUCCESS');
    define('ACK_SUCCESS_WITH_WARNING', 'SUCCESSWITHWARNING');
    
    /*ALERT*/
    define('ADMIN_CONFIRM_TITLE','Please confirm...');


    /*ALERT HTML STRING */
    define('ALERT_DANGER' , '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>%s</div>');
    define('ALERT_INFO' , '<div class="alert alert-info alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>%s</div>');
    define('ALERT_WARNING' , '<div class="alert alert-warning alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>%s</div>');
    define('ALERT_SUCCESS' , '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><i class="icon fa fa-check"></i> %s</div>');

    /*HTML TEMPLATE*/
    define('EDUCATION_TEMPLATE','<div class="col-md-6 col-sm-12 col-xs-12 educationEditSec" id="box-%s"> <div class="addIcons"> <a href="javascript:void(0);" class="edit-icon" title="Edit" data-url="%s" data-request="edit" data-education_id="%s" data-edit-id="education_id"><img src="%s/images/edit-icon.png"></a> <a href="javascript:void(0);" title="Delete" data-url="%s" data-request="delete" data-education_id="%s" data-edit-id="education_id" data-toremove="box" data-ask="Do you really want to delete your education?"><img src="%s/images/delete-icon.png"></a> </div> <ul> <li><span>%s</span><span>%s</span></li> <li><span>%s</span><span>%s</span></li> <li><span>%s</span><span>%s</span></li> <li><span>%s</span><span>%s</span></li> <li><span>%s</span><span>%s</span></li></ul></div>');

    define('TALENT_EDUCATION_TEMPLATE','<div class="educationEditSec" id="box-%s"> <div class="addIcons"> <a href="javascript:void(0);" title="Edit" data-url="%s" data-request="edit" data-education_id="%s" data-edit-id="education_id"><img src="%s/images/edit-icon.png"></a> <a href="javascript:void(0);" title="Delete" data-url="%s" data-request="delete" data-education_id="%s" data-edit-id="education_id" data-toremove="box" data-ask="Do you really want to delete your education?"><img src="%s/images/delete-icon.png"></a> </div> <ul> <li><span>%s</span><span>%s</span></li> <li><span>%s</span><span>%s</span></li> <li><span>%s</span><span>%s</span></li> <li><span>%s</span><span>%s</span></li> <li><span>%s</span><span>%s</span></li><li><span>%s</span><span>%s</span></li></ul></div>');

    define('EXPERIENCE_TEMPLATE','
        <div class="col-md-6 col-sm-12 col-xs-12 educationEditSec" id="experience-%s">
            <span class="company-profile-tag">
                <img src="/images/company.png">
            </span>
            <div class="addIcons">
                <a href="javascript:void(0);" class="edit-icon" title="Edit" data-url="%s" data-request="edit" data-experience_id="%s" data-edit-id="experience_id">
                    <img src="%s/images/edit-icon.png">
                </a>
                <a href="javascript:void(0);" data-edit-id="experience_id" title="Delete" data-url="%s" data-request="delete" data-experience_id="%s" data-delete-id="experience_id" data-toremove="experience" data-ask="Do you really want to delete your experience?">
                    <img src="%s/images/delete-icon.png">
                </a>
            </div>
            <ul>
                <li>
                    <span>%s</span>
                    <span>%s</span>
                </li>
                <li>
                    <span>%s</span>
                    <span>%s</span>
                </li>
                <li>
                    <span>%s</span>
                    <span>%s</span>
                </li>
                <li>
                    <span>%s</span>
                    <span>%s</span>
                </li>
                <li>
                    <span>%s</span>
                    <span>%s</span>
                </li>
                <li>
                    <span>%s</span>
                    <span>%s</span>
                </li>
                <li>
                    <span>%s</span>
                    <span>%s</span>
                </li>
                <li>
                    <span>%s</span>
                    <span>%s</span>
                </li>
            </ul>
        </div>
    '); 

    define('RESUME_TEMPLATE','<div class="row"><div class="col-md-8 col-sm-12 col-xs-12"><div class="new-upload">
        <div class="uploaded-docx clearfix" id="files-%s">
            <a href="%s">
                <div class="grey-attachement-image">
                    <img src="%s/images/pink-attachement.png">
                </div>
            </a>
            <div class="upload-info">
                <p>%s</p>
                <p class="attachement-name">%s</p>
            </div>
            <a href="javascript:void(0);" data-url="%s" data-single="true" data-after-upload=".single-remove" data-toremove="files" title="Delete" data-request="delete" data-file_id="%s" data-delete-id="file_id" data-edit-id="file_id" class="delete-attachment c-p" data-ask="Do you really want to delete the document?"><img src="%simages/delete-icon.png" />
            </a>
        </div>
    </div></div></div>');

    define('NEW_PORTFOLIO_TEMPLATE','
        <div class="row"  id="files-%s">
            <div class="col-md-8 col-sm-12 col-xs-12">
                <div class="new-upload">
                    <div class="uploaded-docx clearfix">
                        <a href="%s">
                            <div class="grey-attachement-image">
                                <img src="%s/images/pink-attachement.png">
                            </div>
                        </a>
                        <div class="upload-info">
                            <p>%s</p>
                            <p class="attachement-name">%s</p>
                        </div>
                        <a href="javascript:void(0);" id="delete_port_doc" data-url="%s" data-single="true" data-after-upload=".single-remove" data-toremove="files" title="Delete" data-request="delete" data-file_id="%s" data-delete-id="file_id" data-edit-id="file_id" class="delete-attachment c-p" data-ask="Do you really want to delete the document?"><img src="%simages/delete-icon.png" />
                        </a>
                    </div>
                </div>
            </div>
            <input type="hidden" name="documents" value="%s">
        </div>
        ');

    define('NEW_VIEW_PORTFOLIO_TEMPLATE','
        <div class="row"  id="files-%s">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="new-upload">
                    <div class="uploaded-docx clearfix">
                        <a href="%s">
                            <div class="grey-attachement-image">
                                <img src="%s/images/pink-attachement.png">
                            </div>
                        </a>
                        <div class="upload-info">
                            <p>%s</p>
                            <p class="attachement-name">%s</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ');

    define('EMPLOYER_VIEW_PORTFOLIO_TEMPLATE','
        <div class="row"  id="files-%s">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="new-upload">
                    <div class="uploaded-docx clearfix">
                        <a href="%s">
                            <div class="grey-attachement-image">
                                <img src="%s/images/pink-attachement.png">
                            </div>
                        </a>
                        <div class="upload-info">
                            <p>%s</p>
                            <p class="attachement-name">%s</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ');



    define('PROPOSALS_TEMPLATE','<div class="form-group"><div class="col-md-12"><div class="uploaded-docx clearfix" id="files-%s"><a href="%s" class="download-docx"><img src="%s/images/attachment-icon.png" /> <div class="upload-info"> <p>%s</p> <span>%s</span> </div></a><a href="javascript:void(0);" data-url="%s" data-toremove="files" title="Delete" data-request="delete" data-file_id="%s" data-delete-id="file_id" data-edit-id="file_id" data-single="true" data-after-upload=".single-remove" class="delete-docx" data-ask="Do you really want to delete the document?"> <img src="%s/images/close-icon-md.png" /></a><input type="hidden" name="documents[]" value="%s"></div></div><div class="clearfix"></div></div>');

    define('EDIT_PORTFOLIO_TEMPLATE','<div class="col-md-6 bottom-margin-10px" id="files-%s"> <span href="%s" class="fancybox add-image-item"> <img src="%s"/> <a href="javascript:void(0);" data-request="remove-local-document" data-source="[name=\'documents[]\']" data-destination="[name=\'removed_portfolio\']" data-target="#files-%s" data-ask="%s" class="add-image-delete"> <img src="%s/images/close-icon-md.png" /> </a> <input type="hidden" name="documents[]" value="%s"> </span> </div>');

    define('AVAILABILITY_TEMPLATE', '<div class="availability-block clearfix" id="availability-%s"> <div class="availability-slot"> <span class="start-time">%s</span> <span class="end-time">%s</span> </div> <div class="availability-info"> <p>%s</p> <span>%s</span> </div> <a class="edit-me" title="Edit" style="right:45px;" href="javascript:void(0);" data-url="%s" data-request="edit-availability" data-availability_id="%s" data-edit-id="availability_id"> <img src="%s/images/edit-icon.png"> </a> <a class="edit-me" href="javascript:void(0);" data-url="%s" title="Delete" data-request="delete" data-availability_id="%s" data-edit-id="availability_id" data-toremove="availability" data-ask="Do you really want to delete your availability?"> <img src="%s/images/delete-icon.png"> </a> </div>');

    define('ADD_CARD_TEMPLATE', '<div class="removable-box cardType" id="box-%s"> <label> <input type="radio" name="card" autocomplete="off" value="%s" %s> <span class="input-value"> <span> <img src="%s"/> </span> <span class="number-value"> %s <small style="padding-left: 5px; font-weight: normal;">%s</small> <span class="addDeletIcon"> <a href="javascript:void(0);" data-target="#payment-checkout" data-url="%s" data-request="delete-card" data-ask="%s"> <img src="%s/images/delete-card.png" /> </a> </span> </span> </label> </div> ');

    define('PORTFOLIO_TEMPLATE','<div class="col-md-6 bottom-margin-10px" id="files-%s"> <span href="%s" class="fancybox add-image-item"> <img src="%s"/> <a href="javascript:void(0);" data-url="%s" data-toremove="files" title="Delete" data-request="delete" data-file_id="%s" data-delete-id="file_id" data-edit-id="file_id" data-single="true" data-after-upload=".single-remove" class="add-image-delete" data-ask="%s"> <img src="%s/images/close-icon-md.png" /> </a> <input type="hidden" name="documents[]" value="%s"> </span> </div>');

    //define('PORTFOLIO_TEMPLATE','<div class="form-group"><div class="col-md-12"><div class="uploaded-docx clearfix" id="files-%s"><a href="%s" class="download-docx"><img src="%s/images/attachment-icon.png" /> <div class="upload-info"> <p>%s</p> <span>%s</span> </div></a><a href="javascript:void(0);" data-url="%s" data-toremove="files" title="Delete" data-request="delete" data-file_id="%s" data-delete-id="file_id" data-edit-id="file_id" data-single="true" data-after-upload=".single-remove" class="delete-docx" data-ask="Do you really want to delete the document?"> <img src="%s/images/close-icon-md.png" /></a><input type="hidden" name="documents" value="%s"></div></div><div class="clearfix"></div></div>');
   
    define('PORTFOLIO_LIST_TEMPLATE','<div class="col-md-4 col-sm-6 col-xs-6 portfolio-outer" id="portfolio-%s"> <div class="amazingBox"> <div class="productImg"> <img class="portfolio-thumbnail" src="%s"/> <a class="portfolio-title" href="%s">%s</a> <a class="portfolio-left-link" href="%s">Edit</a> <a href="javascript:void(0)" data-url="%s" data-toremove="portfolio" title="Delete" data-request="delete" data-file_id="%s" data-delete-id="file_id" data-edit-id="file_id" data-single="true" data-after-upload=".single-remove" class="delete-docx portfolio-right-link" data-ask="Do you really want to delete the document?">Remove</a> </div> </div> </div>');

    define('PERMANENT_SALARY_LOW_FILTER','0');
    define('PERMANENT_SALARY_HIGH_FILTER','5000000');

    define('MAX_LENGTH_DYANIMIC_ID','5');

    define('EMPLOYER_NEWSLETTER_TEMPLATE','<div> <h4> %s </h4> </div> <div> <small> %s start(s) &nbsp;&nbsp;&nbsp; %s Reviews </small> </div> <div> <small> Job Completion Rate: %s &nbsp;&nbsp;&nbsp; Availability: %s &nbsp;&nbsp;&nbsp; Expertise Level: %s </small> </div> <div> <small> Skills: %s </small> </div> ');

    define('TALENT_NEWSLETTER_TEMPLATE','<div> <h3> %s </h3> </div> <div> <small> %s </small> </div> <div> <small> %s </small> </div> <div> <small> Industry: %s </small> </div> ');

    define('ADMIN_PORTFOLIO_LIST_TEMPLATE','<div class="col-md-4 col-sm-6 col-xs-6 portfolio-outer" id="portfolio-%s"><div class="amazingBox"> <div class="productImg"> <img src="%s" /> </div> <div class="productBoxContent"> <h4>%s</h4> <span><a href="javascript:void(0)" data-url="%s" data-toremove="portfolio" title="Delete" data-request="delete" data-file_id="%s" data-delete-id="file_id" data-edit-id="file_id" data-single="true" data-after-upload=".single-remove" class="delete-docx" data-ask="Do you really want to delete the document?">Remove</a></span> </div> </div> </div>');

    define('NOTIFICATION_TEMPLATE','<table width="100%%" border="0" cellpadding="10" cellspacing="0"> <tr> <td> <img src="%s" style="border-radius:100%%;float:left;width:70px;height:70px;border: 3px solid #cccccc;margin: 10px;" /> </td> <td style="vertical-align:middle;font-size:22px;color:#444444;line-height: 18pt; margin: 0;"> %s %s <span style="color: #cccccc;font-size:11px;display:block;">%s</span> </td> </tr> </table> ');

    // define('ADMIN_BANNER_TEMPLATE','<div id="admin-banner-template" class="col-md-6 bottom-margin-10px"> <img src="%s"/> <a href="javascript:void(0);" data-request="remove-local-document" class="delete-template"> <img src="%s/images/close-icon-md.png" /> </a> </span> </div>');

    define('INDUSTRY_TEMPLATE','<div class="col-md-6 bottom-margin-10px" id="files-%s"> <span href="%s" class="fancybox add-image-item"> <img src="%s"/> <a href="javascript:void(0);" data-request="remove-local-document" data-input-name=#%s data-remove-image="#files-%s"> <img src="%s/images/close-icon-md.png" /> </a> <input type="hidden" name="documents[]" value="%s"> </span> </div>');

    define('ADMIN_COLLEGE_IMAGE_TEMPLATE','<div class="col-md-6 bottom-margin-10px" id="files-%s"> <span href="%s" class="fancybox add-image-item"> <img src="%s"/> <a href="javascript:void(0);" data-request="remove-local-document" data-input-name=#%s data-remove-image="#files-%s"> <img src="%s/images/close-icon-md.png" /> </a> <input type="hidden" name="documents[]" value="%s"> </span> </div>');

    define('ADMIN_COMPANY_IMAGE_TEMPLATE','<div class="col-md-6 bottom-margin-10px" id="files-%s"> <span href="%s" class="fancybox add-image-item"> <img src="%s"/> <a href="javascript:void(0);" data-request="remove-local-document" data-input-name=#%s data-remove-image="#files-%s"> <img src="%s/images/close-icon-md.png" /> </a> <input type="hidden" name="documents[]" value="%s"> </span> </div>');

    define('ADMIN_BANNER_TEMPLATE','<div id="admin-banner-template"> <img src="%s"/> <a href="javascript:void(0);" data-request="remove-local-document" class="delete-admin-banner-template"> <img src="%s/images/cancel-icon.png" /> </a> </span> </div>');
    
    
    define('NO_CHAT_CONNECTION_TEMPLATE','<div class=\"col-md-12\"> <div class=\"contentWrapper\"> <div class=\"login-section\"> <div class=\"row has-vr\"> <div class=\"col-md-12 col-sm-12 col-xs-12\"> <div class=\"login-inner-wrapper\"> <h2 class=\"form-heading\">%s</h2> <p>%s</p> <br> <div class=\"row\"> <form action=\"%s\" method=\"get\"> <div class=\"col-md-5 col-sm-5 col-xs-12\"> <div class=\"form-group has-feedback toggle-social\"> <div class=\"\"> <input name=\"_search\" type=\"text\" class=\"form-control\" placeholder=\"%s\"> </div> </div> <div class=\"form-group button-group\"> <div class=\"row form-btn-set\"> <div class=\"col-md-12 col-sm-12 col-xs-12\"> <button type=\"submit\" class=\"btn btn-sm redShedBtn\">%s</button> </div> </div> </div> </div> </form> </div> </div> </div> </div> </div> </div> </div> ');
