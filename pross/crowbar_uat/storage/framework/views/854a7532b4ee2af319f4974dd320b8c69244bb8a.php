<form class="form-horizontal" role="talent_step_one" action="<?php echo e(url(sprintf('%s/profile/step/process/one',TALENT_ROLE_TYPE))); ?>" method="post" accept-charset="utf-8">
    <div class="login-inner-wrapper">
        <?php echo e(csrf_field()); ?>

        <?php if(!empty($edit)): ?><input type="hidden" name="process" value="edit"><?php endif; ?>
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0142')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="first_name" value="<?php echo e(old('first_name',$user['first_name'])); ?>" placeholder="<?php echo e(trans('website.W0142')); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0143')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="last_name" value="<?php echo e(old('last_name',$user['last_name'])); ?>" placeholder="<?php echo e(trans('website.W0143')); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0144')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="email" value="<?php echo e(old('email',$user['email'])); ?>" placeholder="<?php echo e(trans('website.W0144')); ?>" class="form-control">
                    </div>
                </div>                         
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0047')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12 close-fields-wrapper">
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-4 day-select">
                                <div class="custom-dropdown">
                                    <select name="birthdate" class="form-control">
                                        <?php echo ___dropdown_options(___range(range(1, 31)),trans('website.W0192'),$user['birthdate'],true); ?>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4 month-select">
                                <div class="custom-dropdown">
                                    <select name="birthmonth" class="form-control">
                                    <?php echo ___dropdown_options(trans('website.W0048'),trans('website.W0100'),$user['birthmonth']); ?>

                                    </select>
                                </div>
                            </div>
                            <?php  $year_min_limit = ((int)date('Y'))+BIRTHDAY_MIN_YEAR_LIMIT; $year_max_limit = ((int)date('Y'))+BIRTHDAY_MAX_YEAR_LIMIT;  ?>
                            <div class="col-md-4 col-sm-4 col-xs-4 year-select">
                                <div class="custom-dropdown">
                                    <select name="birthyear" class="form-control">
                                    <?php echo ___dropdown_options(___range(range($year_min_limit,$year_max_limit)),trans('website.W0103'),$user['birthyear']); ?>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="birthday">
                    </div>
                </div>
                 <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0049')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <?php $__currentLoopData = gender(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <div class="radio radio-inline">                
                                <input name="gender" type="radio" <?php echo e(($user['gender']==$value['label']) ? 'checked' : ''); ?> value="<?php echo e($value['label']); ?>" id="gen0-<?php echo e($value['label']); ?>">
                                <label for="gen0-<?php echo e($value['label']); ?>"><?php echo e($value['label_name']); ?></label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0053')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12 phonenumber-field">
                        <div class="close-fields-wrapper">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="custom-dropdown countrycode-dropdown">
                                        <select name="country_code" class="form-control" data-placeholder="<?php echo e(trans('website.W0432')); ?>"></select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <input type="text" name="mobile" value="<?php echo e(old('mobile',$user['mobile'])); ?>" placeholder="<?php echo e(trans('website.W0071')); ?>" class="form-control">
                                </div>                                                        
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0054')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="address" value="<?php echo e(old('address',$user['address'])); ?>" placeholder="<?php echo e(trans('website.W0072')); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(sprintf(trans('website.W0055'),'')); ?><span class="required">*</span></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="custom-dropdown">
                            <select class="form-control" name="country" data-placeholder="<?php echo e(trans('website.W0055')); ?>"></select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(sprintf(trans('website.W0056'),'')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="custom-dropdown">
                            <select class="form-control" name="state" data-placeholder="<?php echo e(trans('website.W0056')); ?>"></select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(sprintf(trans('website.W0294'),'')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="custom-dropdown">
                            <select class="form-control" name="city" data-placeholder="<?php echo e(trans('website.W0294')); ?>"></select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0057')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="postal_code" maxlength="6" value="<?php echo e(old('postal_code',$user['postal_code'])); ?>" placeholder="<?php echo e(trans('website.W0073')); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 col-xs-12">
                        <div class="checkbox small-checkbox profile-checkbox">                
                            <input name="agree" type="checkbox" id="agree">
                            <label for="agree">
                                <span class="check"></span>
                                <span>
                                    <?php echo sprintf(
                                            trans('website.W0149'),
                                            "<a class='underline' target='_blank' href='".url('/page/terms-and-conditions')."'>".trans('website.W0147')."</a>",
                                            "<a class='underline' target='_blank' href='".url('/page/privacy-policy')."'>".trans('website.W0148')."</a>"
                                        ); ?>

                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <?php if(in_array('two',$steps)): ?>
                        <a href="<?php echo e(url(sprintf('%s/profile/%sstep/%s',TALENT_ROLE_TYPE,$edit_url,$steps[count($steps)-2]))); ?>" class="greybutton-line"></a>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <a href="<?php echo e($skip_url); ?>" class="greybutton-line">
                        <?php echo e(trans('website.W0186')); ?>

                    </a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <button type="button" data-request="ajax-submit" data-target='[role="talent_step_one"]' name="save" class="button" value="Save">
                        <?php echo e(trans('website.W0659')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->startPush('inlinescript'); ?>
    <style>.phonenumber-field .help-block{top: 0px;}</style>
    <script type="text/javascript">
        setTimeout(function(){
            $('[name="country_code"]').select2({
                formatLoadMore   : function() {return 'Loading more...'},
                ajax: {
                    url: base_url+'/country_phone_codes',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{
                    id: '<?php echo e($user['country_code']); ?>',
                    text: '<?php echo e(!empty($user['country_code']) ? sprintf('%s (%s)',$user['country_code_name'],$user['country_code']) : ''); ?>'
                }],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            });

            $('[name="country"]').select2({
                formatLoadMore   : function() {return 'Loading more...'},
                ajax: {
                    url: base_url+'/countries',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{id: '<?php echo e($user['country']); ?>', text: '<?php echo e($user['country_name']); ?>'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            }).on('change',function(){
                $('[name="state"]').val('').trigger('change');
                $('[name="city"]').val('').trigger('change');
            });


            $('[name="state"]').select2({
                formatLoadMore   : function() {return 'Loading more...'},
                ajax: {
                    url: base_url+'/states',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            country: $('[name="country"]').val(),
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{id: '<?php echo e($user['state']); ?>', text: '<?php echo e($user['state_name']); ?>'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            }).on('change',function(){
                $('[name="city"]').val('').trigger('change');
            });

            $('[name="city"]').select2({
                ajax: {
                    url: base_url+'/cities',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            state: $('[name="state"]').val(),
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{id: '<?php echo e($user['city']); ?>', text: '<?php echo e($user['city_name']); ?>'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            });
        },2000);

        $(document).ready(function(){
            $("input[name='first_name']").prop('tabindex',1);
            $("input[name='last_name']").prop('tabindex',2);
            $("input[name='email']").prop('tabindex',3);
            $("select[name='birthdate']").prop('tabindex',4);
            $("select[name='birthmonth']").prop('tabindex',5);
            $("select[name='birthyear']").prop('tabindex',6);
            $("input[name='gender']").prop('tabindex',7);
            $("select[name='country_code']").prop('tabindex',8);
            $("input[name='mobile']").prop('tabindex',9);
            $("input[name='address']").prop('tabindex',10);
            $("select[name='country']").prop('tabindex',11);
            $("select[name='state']").prop('tabindex',12);
            $("select[name='city']").prop('tabindex',13);
            $("input[name='postal_code']").prop('tabindex',14);
            $("input[name='agree']").prop('tabindex',15);

        });
    </script>
<?php $__env->stopPush(); ?>