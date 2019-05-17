<form role="form" method="post" enctype="multipart/form-data" action="<?php echo e(url($url.'/talent-users/'.$user['id_user'].'/update-education')); ?>">
    <input type="hidden" name="_method" value="PUT">
    <?php echo e(csrf_field()); ?>


    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Account</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="3%">1</td>
                        <td>
                            <img src="<?php echo e(asset('images/connect-instagram.png')); ?>" />&nbsp;&nbsp;<?php echo e(sprintf(trans('website.W0115'),trans('website.W0131'))); ?>

                        </td>
                        <td>
                            <?php if(!$user['instagram_id']): ?>
                                <span class="button-green"><?php echo e(trans('admin.social_not_connected')); ?></span>
                            <?php else: ?>
                                <span class="button-grey"><?php echo e(trans('admin.social_connected')); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="3%">2</td>
                        <td>
                            <img src="<?php echo e(asset('images/connect-facebook.png')); ?>" />&nbsp;&nbsp;<?php echo e(sprintf(trans('website.W0115'),trans('website.W0116'))); ?>

                        </td>
                        <td>
                            <?php if(!$user['facebook_id']): ?>
                                <span class="button-green"><?php echo e(trans('admin.social_not_connected')); ?></span>
                            <?php else: ?>
                                <span class="button-grey"><?php echo e(trans('admin.social_connected')); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td width="3%">3</td>
                        <td>
                            <img src="<?php echo e(asset('images/connect-twitter.png')); ?>" />&nbsp;&nbsp;<?php echo e(sprintf(trans('website.W0115'),trans('website.W0119'))); ?>

                        </td>
                        <td>
                            <?php if(!$user['twitter_id']): ?>
                                <span class="button-green"><?php echo e(trans('admin.social_not_connected')); ?></span>
                            <?php else: ?>
                                <span class="button-grey"><?php echo e(trans('admin.social_connected')); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td width="3%">4</td>
                        <td>
                            <img src="<?php echo e(asset('images/connect-linked-in.png')); ?>" />&nbsp;&nbsp;<?php echo e(sprintf(trans('website.W0115'),trans('website.W0120'))); ?>

                        </td>
                        <td>
                            <?php if(!$user['linkedin_id']): ?>
                                <span class="button-green"><?php echo e(trans('admin.social_not_connected')); ?></span>
                            <?php else: ?>
                                <span class="button-grey"><?php echo e(trans('admin.social_connected')); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="3%">5</td>
                        <td>
                            <img src="<?php echo e(asset('images/connect-google-plus.png')); ?>" />&nbsp;&nbsp;<?php echo e(sprintf(trans('website.W0115'),trans('website.W0121'))); ?>

                        </td>
                        <td>
                            <?php if(!$user['googleplus_id']): ?>
                                <span class="button-green"><?php echo e(trans('admin.social_not_connected')); ?></span>
                            <?php else: ?>
                                <span class="button-grey"><?php echo e(trans('admin.social_connected')); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="3%">6</td>
                        <td>
                            <img src="<?php echo e(asset('images/connect-phone-verification.png')); ?>" />&nbsp;&nbsp;<?php echo e(sprintf(trans('website.W0115'),trans('website.W0122'))); ?>

                        </td>
                        <td>
                            <?php if($user['is_mobile_verified'] != 'yes'): ?>
                                <span class="button-green"><?php echo e(trans('admin.phone_not_connected')); ?></span>
                            <?php else: ?>
                                <span class="button-grey"><?php echo e(trans('admin.phone_connected')); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>
