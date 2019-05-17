<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="row">
            <div class="col-md-12"><?php echo e(___alert((!empty($alert))?$alert:'')); ?></div>
            <div class="col-md-3">
                <div class="panel">
                    <div class="panel-body box-profile">
                        <div class="profile-user-img img-responsive img-circle" style="overflow:hidden;"><img alt="Picture" src="<?php echo e(asset('/images/small-logo.png')); ?>" style="padding: 15px"></div>
                        <p class="text-muted text-center"><?php echo e($setting->site_description); ?></p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Site Mode</b> <a class="pull-right" id="site_environment"><?php echo ucfirst($setting->site_environment); ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Last Activity</b> <a class="pull-right"><?php echo e(___ago(date('Y-m-d H:i:s'))); ?> </a>
                            </li>
                        </ul>
                        <p>
                            <a href="javascript:;" data-url="<?php echo e(url($uri_placeholder.'/ajax/setting/update?site_environment='.$site_environment)); ?>" data-request="html" data-ask="Do you really want to continue with this action?" data-target="#site_environment" class="btn btn-primary btn-block">
                                Switch <u><b>Site Mode</b></u>
                            </a>
                        </p>
                        <!-- <a class="btn btn-default btn-block" href="<?php echo sprintf("%s%s",'','&page=activity'); ?>"><b>See all request</b></a> -->
                    </div>
                </div>
            </div>
            <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="<?php echo ($page == 'basic')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=basic'); ?>"><?php echo e(trans('admin.A0027')); ?></a></li>
                    <li class="<?php echo ($page == 'countries')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=countries'); ?>"><?php echo e(trans('admin.A0028')); ?></a></li>
                    <li class="<?php echo ($page == 'states')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=states'); ?>"><?php echo e(trans('admin.A0029')); ?></a></li>
                    <li class="<?php echo ($page == 'city')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=city'); ?>"><?php echo e(trans('admin.A0030')); ?></a></li>
                    <li class="<?php echo ($page == 'abusive_words')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=abusive_words'); ?>"><?php echo e(trans('admin.A0033')); ?></a></li>
                    <li class="<?php echo ($page == 'degree')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=degree'); ?>"><?php echo e(trans('admin.A0039')); ?></a></li>
                    <?php if(0): ?><li class="<?php echo ($page == 'certificate')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=certificate'); ?>"><?php echo e(trans('admin.A0043')); ?></a></li><?php endif; ?>
                    <li class="<?php echo ($page == 'dispute-concern')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=dispute-concern'); ?>"><?php echo e(trans('admin.A0081')); ?></a></li>
                    <?php if(0): ?><li class="<?php echo ($page == 'college')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=college'); ?>"><?php echo e(trans('admin.A0047')); ?></a></li><?php endif; ?>
                    <?php if(0): ?><li class="<?php echo ($page == 'skill')?'active':''; ?>"><a href="<?php echo sprintf("%s%s",$url,'?page=skill'); ?>"><?php echo e(trans('admin.A0051')); ?></a></li><?php endif; ?>
                    <li class="<?php echo ($page == 'api')?'active':''; ?>" style="display: none;"><a href="<?php echo sprintf("%s%s",$url,'?page=api'); ?>"><?php echo e(trans('admin.A0034')); ?></a></li>
                </ul>    
                <div class="tab-content no-padding">
                    <?php if($page == 'basic'){ ?>
                        <div class="tab-pane <?php echo e(($page == 'basic')?' active':''); ?>">
                            <form role="add-talent" class="form-horizontal" method="post" action="<?php echo e(sprintf("%s/%s",$url,'update/setting')); ?>">
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <?php echo e(csrf_field()); ?>

                                        <div class="row">
                                            <p class="lead" style="margin-bottom:10px;">Basic Information<hr style="margin-top:5px;"></p>
                                        </div>
                                        <div class="form-group <?php if($errors->has('site_name')): ?>has-error <?php endif; ?>">
                                            <label>Site Name:</label>
                                            <input type="text" class="form-control" name="site_name" value="<?php echo e(old('site_name',$setting->site_name)); ?>" placeholder="Enter your site name">
                                            <?php if($errors->has('site_name')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('site_name')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('site_description')): ?>has-error <?php endif; ?>">
                                            <label>Site Description:</label>
                                            <input type="text" class="form-control" name="site_description" value="<?php echo e(old('site_description',$setting->site_description)); ?>" placeholder="Enter your site name">
                                            <?php if($errors->has('site_description')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('site_description')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('site_email')): ?>has-error <?php endif; ?>">
                                            <label>Site Email:</label>
                                            <input type="text" class="form-control" name="site_email" value="<?php echo e(old('site_email',$setting->site_email)); ?>" placeholder="Enter site email address (i.e. support@crowbar.me)">
                                            <?php if($errors->has('site_email')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('site_email')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('copyright_text')): ?>has-error <?php endif; ?>">
                                            <label>Copyright Text:</label>
                                            <input type="text" class="form-control" name="copyright_text" value="<?php echo e(old('copyright_text',$setting->copyright_text)); ?>" placeholder="Enter copyright text">
                                            <?php if($errors->has('copyright_text')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('copyright_text')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('ios_version')): ?>has-error <?php endif; ?>">
                                            <label>iOS version:</label>
                                            <input type="text" class="form-control" name="ios_version" value="<?php echo e(old('ios_version',$setting->ios_version)); ?>" placeholder="Enter iOS version">
                                            <?php if($errors->has('ios_version')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('ios_version')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('android_version')): ?>has-error <?php endif; ?>">
                                            <label>Android version:</label>
                                            <input type="text" class="form-control" name="android_version" value="<?php echo e(old('android_version',$setting->android_version)); ?>" placeholder="Enter android version">
                                            <?php if($errors->has('android_version')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('android_version')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('update_version_message')): ?>has-error <?php endif; ?>">
                                            <label>Update version Message:</label>
                                            <input type="text" class="form-control" name="update_version_message" value="<?php echo e(old('update_version_message',$setting->update_version_message)); ?>" placeholder="Enter android version">
                                            <?php if($errors->has('update_version_message')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('update_version_message')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('google_analytics_code')): ?>has-error <?php endif; ?>">
                                            <label>Google Analytics Code:</label>
                                            <textarea class="form-control" name="google_analytics_code" placeholder="Enter android version" rows="10"><?php echo e(old('google_analytics_code',$setting->google_analytics_code)); ?></textarea>
                                            <?php if($errors->has('google_analytics_code')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('google_analytics_code')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('completing_profile')): ?>has-error <?php endif; ?>">
                                            <label>Notifications for completing profile: </label>
                                            <input type="radio" name="completing_profile"<?php echo e($setting->completing_profile == 'off' ? ' checked="checked"' : ''); ?> value="off"> Off
                                            <input type="radio" name="completing_profile"<?php echo e($setting->completing_profile == '7' ? ' checked="checked"' : ''); ?> value="7"> Once in a week
                                            <input type="radio" name="completing_profile"<?php echo e($setting->completing_profile == '30' ? ' checked="checked"' : ''); ?> value="30"> Once in a month

                                            <?php if($errors->has('completing_profile')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('completing_profile')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('daily_working_hours')): ?>has-error <?php endif; ?>">
                                            <label>Daily Working Hours:</label>
                                            <input type="text" class="form-control" name="daily_working_hours" value="<?php echo e(old('daily_working_hours',$setting->daily_working_hours)); ?>" placeholder="Enter daily working hours">
                                            <?php if($errors->has('daily_working_hours')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('daily_working_hours')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="form-group <?php if($errors->has('paypal_commission')): ?>has-error <?php endif; ?>">
                                            <label>Paypal Commission Percentage(in %):</label>
                                            <input type="text" class="form-control" name="paypal_commission" value="<?php echo e(old('paypal_commission',$setting->paypal_commission)); ?>" placeholder="Enter paypal commission">
                                            <?php if($errors->has('paypal_commission')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('paypal_commission')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group <?php if($errors->has('paypal_commission_flat')): ?>has-error <?php endif; ?>">
                                            <label>Paypal Commission (Flat):</label>
                                            <input type="text" class="form-control" name="paypal_commission_flat" value="<?php echo e(old('paypal_commission_flat',$setting->paypal_commission_flat)); ?>" placeholder="Enter paypal commission">
                                            <?php if($errors->has('paypal_commission_flat')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('paypal_commission_flat')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group <?php if($errors->has('commission')): ?>has-error <?php endif; ?>">
                                            <label>Crowbar Commission Percentage(in %):</label>
                                            <input type="text" class="form-control" name="commission" value="<?php echo e(old('commission',$setting->commission)); ?>" placeholder="Enter crowbar commission">
                                            <?php if($errors->has('commission')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('commission')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                            
                                        <div class="form-group <?php if($errors->has('raise_dispute_commission')): ?>has-error <?php endif; ?>">
                                            <label>Raise Dispute Commission Percentage(in %):</label>
                                            <input type="text" class="form-control" name="raise_dispute_commission" value="<?php echo e(old('raise_dispute_commission',$setting->raise_dispute_commission)); ?>" placeholder="Enter paypal commission">
                                            <?php if($errors->has('raise_dispute_commission')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('raise_dispute_commission')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div> 
                            
                                        <div class="form-group <?php if($errors->has('cancellation_commission')): ?>has-error <?php endif; ?>">
                                            <label>Cancellation Commission Percentage(in %):</label>
                                            <input type="text" class="form-control" name="cancellation_commission" value="<?php echo e(old('cancellation_commission',$setting->cancellation_commission)); ?>" placeholder="Enter paypal commission">
                                            <?php if($errors->has('cancellation_commission')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('cancellation_commission')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>                                     
                            
                                        <div class="form-group <?php if($errors->has('minimum_profile_percentage')): ?>has-error <?php endif; ?>">
                                            <label>Minimum Required Profile Percentage (in %):</label>
                                            <input type="text" class="form-control" name="minimum_profile_percentage" value="<?php echo e(old('minimum_profile_percentage',$setting->minimum_profile_percentage)); ?>" placeholder="Enter crowbar minimum_profile_percentage">
                                            <?php if($errors->has('minimum_profile_percentage')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('minimum_profile_percentage')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group <?php if($errors->has('user_disable_cron_status')): ?>has-error <?php endif; ?>">
                                            <label>User Disable Cron Status: </label>
                                            <input type="radio" name="user_disable_cron_status"<?php echo e($setting->user_disable_cron_status == 'yes' ? ' checked="checked"' : ''); ?> value="yes"> Yes
                                            <input type="radio" name="user_disable_cron_status"<?php echo e($setting->user_disable_cron_status == 'no' ? ' checked="checked"' : ''); ?> value="no"> No

                                            <?php if($errors->has('user_disable_cron_status')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('user_disable_cron_status')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>                                        

                                        <div class="row">
                                            <p class="lead" style="margin-bottom:10px;">Footer Information<hr style="margin-top:5px;"></p>
                                        </div>

                                        <div class="form-group <?php if($errors->has('ios_download_app_url')): ?>has-error <?php endif; ?>">
                                            <label>iOS App Download Link:</label>
                                            <input type="text" class="form-control" name="ios_download_app_url" value="<?php echo e(old('ios_download_app_url',$setting->ios_download_app_url)); ?>" placeholder="Enter iOS App Download Link">
                                            <?php if($errors->has('ios_download_app_url')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('ios_download_app_url')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('android_download_app_url')): ?>has-error <?php endif; ?>">
                                            <label>Android App Download Link:</label>
                                            <input type="text" class="form-control" name="android_download_app_url" value="<?php echo e(old('android_download_app_url',$setting->android_download_app_url)); ?>" placeholder="Enter Android App Download Link">
                                            <?php if($errors->has('android_download_app_url')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('android_download_app_url')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="row">
                                            <p class="lead" style="margin-bottom:10px;">Social Information<hr style="margin-top:5px;"></p>
                                        </div>
                                        <div class="form-group <?php if($errors->has('social_youtube_url')): ?>has-error <?php endif; ?>">
                                            <label>Youtube:</label>
                                            <input type="text" class="form-control" name="social_youtube_url" value="<?php echo e(old('social_youtube_url',$setting->social_youtube_url)); ?>" placeholder="Enter youtube follow link">
                                            <?php if($errors->has('social_youtube_url')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('social_youtube_url')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('social_facebook_url')): ?>has-error <?php endif; ?>">
                                            <label>Facebook:</label>
                                            <input type="text" class="form-control" name="social_facebook_url" value="<?php echo e(old('social_facebook_url',$setting->social_facebook_url)); ?>" placeholder="Enter facebook follow link">
                                            <?php if($errors->has('social_facebook_url')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('social_facebook_url')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('social_twitter_url')): ?>has-error <?php endif; ?>">
                                            <label>Twitter:</label>
                                            <input type="text" class="form-control" name="social_twitter_url" value="<?php echo e(old('social_twitter_url',$setting->social_twitter_url)); ?>" placeholder="Enter twitter follow link">
                                            <?php if($errors->has('social_twitter_url')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('social_twitter_url')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('social_linkedin_url')): ?>has-error <?php endif; ?>">
                                            <label>Linked-in:</label>
                                            <input type="text" class="form-control" name="social_linkedin_url" value="<?php echo e(old('social_linkedin_url',$setting->social_linkedin_url)); ?>" placeholder="Enter linked-in follow link">
                                            <?php if($errors->has('social_linkedin_url')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('social_linkedin_url')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('social_instagram_url')): ?>has-error <?php endif; ?>">
                                            <label>Instagram:</label>
                                            <input type="text" class="form-control" name="social_instagram_url" value="<?php echo e(old('social_instagram_url',$setting->social_instagram_url)); ?>" placeholder="Enter instagram follow link">
                                            <?php if($errors->has('social_instagram_url')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('social_instagram_url')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('social_googleplus_url')): ?>has-error <?php endif; ?>">
                                            <label>Google Plus:</label>
                                            <input type="text" class="form-control" name="social_googleplus_url" value="<?php echo e(old('social_googleplus_url',$setting->social_googleplus_url)); ?>" placeholder="Enter instagram follow link">
                                            <?php if($errors->has('social_googleplus_url')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('social_googleplus_url')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="row">
                                            <p class="lead" style="margin-bottom:10px;">SMTP Information<hr style="margin-top:5px;"></p>
                                        </div>
                                        <div class="form-group <?php if($errors->has('smtp_host')): ?>has-error <?php endif; ?>">
                                            <label>Host:</label>
                                            <input type="text" class="form-control" name="smtp_host" value="<?php echo e(old('smtp_host',$setting->smtp_host)); ?>" placeholder="Enter host url">
                                            <?php if($errors->has('smtp_host')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('smtp_host')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('smtp_username')): ?>has-error <?php endif; ?>">
                                            <label>Username:</label>
                                            <input type="text" class="form-control" name="smtp_username" value="<?php echo e(old('smtp_username',$setting->smtp_username)); ?>" placeholder="Enter smtp username">
                                            <?php if($errors->has('smtp_username')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('smtp_username')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('smtp_password')): ?>has-error <?php endif; ?>">
                                            <label>Password:</label>
                                            <input type="password" class="form-control" name="smtp_password" value="<?php echo e(old('smtp_password',$setting->smtp_password)); ?>" placeholder="Enter smtp password">
                                            <?php if($errors->has('smtp_password')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('smtp_password')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('smtp_port')): ?>has-error <?php endif; ?>">
                                            <label>Port:</label>
                                            <input type="text" class="form-control" name="smtp_port" value="<?php echo e(old('smtp_port',$setting->smtp_port)); ?>" placeholder="Enter smtp port">
                                            <?php if($errors->has('smtp_port')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('smtp_port')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group <?php if($errors->has('smtp_mode')): ?>has-error <?php endif; ?>">
                                            <label>Mode:</label>
                                            <input type="text" class="form-control" name="smtp_mode" value="<?php echo e(old('smtp_mode',$setting->smtp_mode)); ?>" placeholder="Enter smtp mode">
                                            <?php if($errors->has('smtp_mode')): ?>
                                                <span class="help-block">
                                                    <?php echo e($errors->first('smtp_mode')); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button class="btn btn-default" data-request="ajax-submit" data-target=[role="add-talent"] type="button">Save Changes</button>
                                </div>                                    
                            </form>
                            <div class="clearfix"></div>
                        </div>
                    <?php }else{ ?>
                        <div class="panel-body">
                            <div class="row" id="form-content">
                                <button class="hide" data-request="inline-form" data-target="#form-content" data-url="<?php echo e(url(sprintf('%s/general/%s/add',ADMIN_FOLDER,$page))); ?>"></button>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="clearfix"></div>
                                    <div class="table-responsive">
                                        <?php echo $html->table();; ?>

                                    </div>
                                </div>
                            </div>
                        </div>   
                    <?php } ?>
            </div>
        </div>    
    </section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('requirejs'); ?>    
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php if(!empty($page) && $page !== 'basic'): ?>
        <?php echo $html->scripts(); ?>

        <script type="text/javascript">
            $(window).load(function(){
                if($('[data-request="inline-form"]').length > 0){
                    $('[data-request="inline-form"]').trigger('click');
                }
            });
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>