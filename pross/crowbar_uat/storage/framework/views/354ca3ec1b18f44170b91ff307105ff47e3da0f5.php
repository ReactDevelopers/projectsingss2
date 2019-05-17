<form class="form-horizontal" role="employer_step_one" action="<?php echo e(url(sprintf('%s/profile/process/two',EMPLOYER_ROLE_TYPE))); ?>" method="post" accept-charset="utf-8">
    <div class="inner-profile-section">                        
        <div class="login-inner-wrapper edit-inner-wrapper">
        
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="step_type" value="edit">

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
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0053')); ?></label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="close-fields-wrapper">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <div class="custom-dropdown countrycode-dropdown">
                                            <select name="country_code" class="form-control" data-placeholder="<?php echo e(trans('website.W0432')); ?>"></select>
                                        </div>                                                        
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                        <input type="text" name="mobile" value="<?php echo e(old('mobile',$user['mobile'])); ?>" placeholder="<?php echo e(trans('website.W0071')); ?>" class="form-control">
                                    </div>                             
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0245')); ?></label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="close-fields-wrapper">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <div class="custom-dropdown countrycode-dropdown">
                                            <select name="other_country_code" class="form-control" data-placeholder="<?php echo e(trans('website.W0432')); ?>"></select>
                                            
                                            </select>
                                        </div>                                                        
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                        <input type="text" name="other_mobile" value="<?php echo e(old('other_mobile',$user['other_mobile'])); ?>" placeholder="<?php echo e(trans('website.W0071')); ?>" class="form-control">
                                    </div>
                                </div>                             
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0054')); ?></label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="address" value="<?php echo e(old('address',$user['address'])); ?>" placeholder="<?php echo e(trans('website.W0072')); ?>" class="form-control">
                        </div>
                    </div>                                    
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(sprintf(trans('website.W0055'),'')); ?></label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="custom-dropdown">
                                <select class="form-control" name="country"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(sprintf(trans('website.W0056'),'')); ?></label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="custom-dropdown">
                                <select class="form-control" name="state"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12"><?php echo e(trans('website.W0057')); ?></label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="postal_code" value="<?php echo e(old('postal_code',$user['postal_code'])); ?>" placeholder="<?php echo e(trans('website.W0073')); ?>" class="form-control">
                        </div>
                    </div>                                        
                </div>
            </div>
        </div>
    </div>                       

    <div class="row form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">      
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <?php if(in_array('two',$steps)): ?>
                        <a href="<?php echo e(url(sprintf('%s/profile/edit/one',EMPLOYER_ROLE_TYPE))); ?>" class="greybutton-line"><?php echo e(trans('website.W0196')); ?></a>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <button type="button" data-request="ajax-submit" data-target='[role="employer_step_one"]' name="save" class="button" value="Save">
                        <?php echo e(trans('website.W0058')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->startPush('inlinescript'); ?>
    <style type="text/css">.modal-backdrop{display: none;} #SGCreator-modal{background: rgba(216, 216, 216, 0.7);}</style>
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

            $('[name="other_country_code"]').select2({
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
                    id: '<?php echo e($user['other_country_code']); ?>',
                    text: '<?php echo e(!empty($user['other_country_code']) ? sprintf('%s (%s)',$user['other_country_code_name'],$user['other_country_code']) : ''); ?>'
                }],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            });            

            $('[name="country"]').select2({
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
        },2000);
    </script>
<?php $__env->stopPush(); ?>
