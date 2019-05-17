<div class="col-md-8 col-sm-8 col-xs-12 no-padding-xs">
    <form role="settings" method="POST" action="<?php echo e(url(sprintf('%s/__notificationsettings',EMPLOYER_ROLE_TYPE))); ?>" class="form-horizontal" autocomplete="off">
        <?php echo e(csrf_field()); ?>

        <div class="login-inner-wrapper setting-wrapper">
            <div class="row">
                <div class="col-md-6 col-sm-6 colxs-12">
                    <div class="settingList">
                        <h2 class="form-heading">
                            <div class="checkbox">
                                <input type="checkbox" id="email_check_all" value="">
                                <label for="email_check_all"><span class="check"></span> <b><?php echo e(trans('website.W0307')); ?></b></label>
                            </div>
                        </h2>
                        <ul>
                            <?php $__currentLoopData = $settings['email']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <li class="checkbox">
                                    <input type="checkbox" value="<?php echo e($item['setting']); ?>" <?php if($item['status'] == DEFAULT_YES_VALUE): ?> checked="checked" <?php endif; ?> id="email_<?php echo e($item['setting']); ?>" name="email[<?php echo e($item['setting']); ?>]">
                                    <label for="email_<?php echo e($item['setting']); ?>"><span class="check"></span> <?php echo e(trans(sprintf('general.%s',$item['setting']))); ?></label>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 colxs-12">
                    <div class="settingList">
                        <h2 class="form-heading">
                            <div class="checkbox">
                                <input type="checkbox" id="mobile_check_all" value="">
                                <label for="mobile_check_all"><span class="check"></span> <b><?php echo e(trans('website.W0315')); ?></b></label>
                            </div>
                        </h2>
                        <ul>
                            <?php $__currentLoopData = $settings['mobile']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <li class="checkbox">
                                    <input type="checkbox" value="<?php echo e($item['setting']); ?>" <?php if($item['status'] == DEFAULT_YES_VALUE): ?> checked="checked" <?php endif; ?> id="mobile_<?php echo e($item['setting']); ?>" name="mobile[<?php echo e($item['setting']); ?>]">
                                    <label for="mobile_<?php echo e($item['setting']); ?>"><span class="check"></span> <?php echo e(trans(sprintf('general.%s',$item['setting']))); ?></label>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                        </ul>
                    </div>
                </div>                
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group button-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row form-btn-set">
                    <div class="col-md-5 col-sm-5 col-xs-6">
                        <button type="button" data-request="ajax-submit" data-target='[role="settings"]' class="btn btn-sm redShedBtn pull-right"><?php echo e(trans('website.W0058')); ?></button>
                    </div>
                </div>      
            </div>
        </div>
    </form>
</div>
<?php $__env->startPush('inlinescript'); ?>
    <script type="text/javascript">
        $(function(){  
            $("#email_check_all").click(function(){
                $('[name*="email"]').not(this).prop('checked', this.checked);
            });

            $('[name*="email"]').click(function(){
                if($('[name*="email"]:checked').length == $('[name*="email"]').length){
                    $("#email_check_all").prop('checked', true);
                }else{
                    $("#email_check_all").prop('checked', false);    
                }
            });

            if($('[name*="email"]:checked').length == $('[name*="email"]').length){
                $("#email_check_all").prop('checked', true);
            }else{
                $("#email_check_all").prop('checked', false);    
            }

            $("#mobile_check_all").click(function(){
                $('[name*="mobile"]').not(this).prop('checked', this.checked);
            });

            $('[name*="mobile"]').click(function(){
                if($('[name*="mobile"]:checked').length == $('[name*="mobile"]').length){
                    $("#mobile_check_all").prop('checked', true);
                }else{
                    $("#mobile_check_all").prop('checked', false);
                }
            });

            if($('[name*="mobile"]:checked').length == $('[name*="mobile"]').length){
                $("#mobile_check_all").prop('checked', true);
            }else{
                $("#mobile_check_all").prop('checked', false);
            }
        });
    </script>
<?php $__env->stopPush(); ?>