<form class="form-horizontal" role="talent_step_four" action="<?php echo e(url(sprintf('%s/profile/step/process/four',TALENT_ROLE_TYPE))); ?>" method="POST" accept-charset="utf-8">
    <div class="login-inner-wrapper">
        <?php echo e(csrf_field()); ?>

        <?php if(!empty($edit)): ?><input type="hidden" name="process" value="edit"><?php endif; ?>
        <h4 class="form-sub-heading"><?php echo e(sprintf(trans('website.W0661'),'')); ?></h4>
        <div class="row">
            <div class="col-md-3">  
                <label class="control-label t-u"><?php echo e(trans('website.W0286')); ?></label>
            </div>
            <div class="col-md-4">
                <label class="control-label t-u"><?php echo e(trans('website.W0660')); ?></label>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <ul class="filter-list-group clear-list">
                    <?php $__currentLoopData = employment_types('web_post_job'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <li>
                            <div class="row">
                                <div class="col-md-3">                
                                    <div class="checkbox">                
                                        <input type="checkbox" id="employement-<?php echo e($value['type']); ?>" name="interests[<?php echo e($key); ?>]" value="<?php echo e($value['type']); ?>" <?php if((!empty($user['interested'][$value['type']]))): ?> checked="checked" <?php endif; ?> data-request="focus-input-checkbox">
                                        <label for="employement-<?php echo e($value['type']); ?>"><span class="check"></span> <?php echo e(strtolower($value['type_name'])); ?></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="enrollment-range">
                                        <input type="text" name="workrate[<?php echo e($key); ?>]" class="form-control m-t-5px" <?php if(!empty($user['interested'][$value['type']])): ?> value="<?php echo e($user['interested'][$value['type']]); ?>" <?php endif; ?> data-request="focus-input-checkbox">
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </ul>        
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0664')); ?></label>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <textarea name="workrate_information" placeholder="<?php echo e(trans('website.W0665')); ?>" class="form-control" data-request="live-length" data-maxlength="<?php echo e(DESCRIPTION_COUNTER_LENGTH); ?>"><?php echo e($user['workrate_information']); ?></textarea>
            </div>
        </div> 
    </div>    
</form>        
<div class="form-group button-group">
    <div class="row form-btn-set">
        <div class="col-md-4 col-sm-4 col-xs-12">
            <?php if(in_array('two',$steps)): ?>
                <a href="<?php echo e(url(sprintf('%s/profile/%sstep/%s',TALENT_ROLE_TYPE,$edit_url,$steps[count($steps)-2]))); ?>" class="greybutton-line"><?php echo e(trans('website.W0196')); ?></a>
            <?php endif; ?>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <a href="<?php echo e($skip_url); ?>" class="greybutton-line">
                <?php echo e(trans('website.W0186')); ?>

            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <button type="button" class="button" data-request="ajax-submit" data-target='[role="talent_step_four"]' value="Save"><?php echo e(trans('website.W0659')); ?></button>
        </div>
    </div>
</div>

<?php $__env->startPush('inlinecss'); ?>
    <style>
        .enrollment-range input[type=text].form-control{
            padding-left: 28px;
        }
        .enrollment-range::before{
            content: "<?php echo e(\Cache::get('currencies')[\Session::get('site_currency')]); ?>";
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('inlinescript'); ?>
    <script type="text/javascript">
        var $words_text = '<?php echo e(trans('website.W0723')); ?>';
        $(function(){
            setTimeout(function(){
                $('[data-request="live-length"]').trigger('keyup');
            },2000);
        })
    </script>
<?php $__env->stopPush(); ?>