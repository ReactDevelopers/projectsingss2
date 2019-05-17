<form class="form-horizontal" role="talent_step_two" action="<?php echo e(url(sprintf('%s/profile/step/process/two',TALENT_ROLE_TYPE))); ?>" method="POST" accept-charset="utf-8">
    <?php echo e(csrf_field()); ?>

    <?php if(!empty($edit)): ?><input type="hidden" name="process" value="edit"><?php endif; ?>
    <div class="login-inner-wrapper">
        <div>
            <h4 class="form-sub-heading"><?php echo e(trans('website.W0030')); ?><span class="required">*</span></h4>
            <div class="custom-dropdown single-tag-selection">
                <select name="industry[]" style="max-width: 400px;"  class="form-control" data-request="single-tags" data-placeholder="<?php echo e(trans('website.W0644')); ?>" id="single_tags">
                    <?php echo ___dropdown_options(___cache('industries_name'),sprintf(trans('website.W0644'),trans('website.W0068')),array_column($user['industry'],'id_industry'),false); ?>

                </select>
                <div class="js-example-tags-container white-tags"></div>
            </div>
        </div>
        
        
        <div id="is_register_show">
            <div class="form-group" id="is_register_div">
                <div class="col-md-12">
                    <label class="control-label"><?php echo e(trans('website.W0945')); ?></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="radio radio-inline">                
                            <input name="is_register" type="radio" <?php echo e(($user['is_register']=='Y') ? 'checked' : ''); ?> value="Y" id="gen0-1">
                            <label for="gen0-1">Yes</label>
                        </div>
                        <div class="radio radio-inline">
                            <input name="is_register" type="radio" <?php echo e(($user['is_register']=='N') ? 'checked' : ''); ?> value="N" id="gen0-2">
                            <label for="gen0-2">No</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group" id="identification_no_div" style="display:none;">
                <div class="col-md-12">
                    <label class="control-label"><?php echo e(trans('website.W0937')); ?></label>
                    <input type="text" name="identification_no" placeholder="<?php echo e(trans('website.W0938')); ?>" value="<?php echo e(!empty($user['identification_no'])? $user['identification_no']:''); ?>" style="max-width: 400px;" class="form-control">
                </div>
            </div>
        </div>
        
    </div>
    <div class="login-inner-wrapper">
        <div class="">
            <h4 class="form-sub-heading"><?php echo e(trans('website.W0206')); ?></h4>
            <div class="skills-filter">
                <div class="custom-dropdown">
                    <select id="skills" name="skills[]" style="max-width: 400px;" class="filter form-control" data-request="tags" multiple="true" data-placeholder="<?php echo e(trans('website.W0193')); ?>">
                        <?php echo ___dropdown_options(___cache('skills'),'',array_column($user['skills'],'skill_name'),false); ?>

                    </select>
                    <div class="js-example-tags-container white-tags"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="login-inner-wrapper">
        <h4 class="form-sub-heading"><?php echo e(trans('website.W0280')); ?></h4>
        <div class="">
            <div class="row">
                <ul class="filter-list-group clearfix p-b-0">
                    <?php $__currentLoopData = expertise_levels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <li class="col-md-4">
                            <div class="checkbox radio-checkbox">                
                                <input type="radio" id="expertise-<?php echo e($value['level']); ?>" name="expertise" value="<?php echo e($value['level']); ?>" data-action="filter" <?php if(($user['expertise'] == $value['level'])): ?> checked="checked" <?php endif; ?>>
                                <label for="expertise-<?php echo e($value['level']); ?>"><span class="check"></span><?php echo e($value['level_name']); ?> <?php echo e($value['level_exp']); ?></label>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </ul>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <label class="control-label"><?php echo e(trans('website.W0667')); ?></label>
                <input type="text" name="experience" data-request="numeric" maxlength="4" placeholder="<?php echo e(trans('website.W0074')); ?>" value="<?php echo e($user['experience']); ?>" style="max-width: 400px;" class="form-control">
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
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
                    <button type="button" class="button" data-request="ajax-submit" data-target='[role="talent_step_two"]' value="Save"><?php echo e(trans('website.W0659')); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->startPush('inlinescript'); ?>
<script>
    var identification_nums = "<?php echo e($payout_management_list); ?>";
    var identification_nums_arr = identification_nums.split(",");
    var country_id          = "<?php echo e($country_id); ?>";
    var $url = "<?php echo e(___image_base_url()); ?>";
    // country_id = 0;
    var indus_id = $('#single_tags').val();

    $(document).on('change', 'select[name*="industry"]', function(){
        var industry_id = $(this).val();
        // alert(industry_id + '===' + indus_id);
        if(country_id == 0 && industry_id > 0){
            swal({
                title: 'Please select country field',
                showLoaderOnConfirm: false,
                showCancelButton: false,
                showCloseButton: false,
                allowEscapeKey: false,
                allowOutsideClick:false,
                customClass: 'swal-custom-class',
                confirmButtonText: $close_botton_text,
                cancelButtonText: $cancel_botton_text,
                preConfirm: function (res) {
                    return new Promise(function (resolve, reject) {
                        if (res === true) {
                            window.location = $url+'/en/talent/profile/step/one';
                            resolve();              
                        }
                    })
                }
            }).then(function(isConfirm){},function (dismiss){}).catch(swal.noop);
        }
        else if (country_id != 0 && industry_id != '' && identification_nums!='' && identification_nums_arr.indexOf(industry_id) > -1 ){
            $('#is_register_show').show();
            var show_identification_check_all = "<?php echo e($show_identification_check_all); ?>";
            var show_identification_check_all_arr = show_identification_check_all.split(",");
            $.each( show_identification_check_all_arr, function( key, value ) {

                if($("#single_tags").val() == value){
                    $('#identification_no_div').show();
                }else{
                    $('#identification_no_div').hide();
                }
            });
            // $('#is_register_div').show();
        }else{
            $('#is_register_show').hide();
            // $('#is_register_div').hide();
            $("input[name=is_register][value='N']").attr('checked', 'checked');

            $('input[name="identification_no"]').val('');
        }
    });

    var show_identification_check = '<?php echo e($show_identification_check); ?>';
    $(document).ready(function(){
        if(show_identification_check == 1){
            $('#identification_no_div').show();
        }else{
            $('#identification_no_div').hide();
            $("input[name=is_register][value='N']").attr('checked', 'checked');
            $('input[name="identification_no"]').val('');
        }
    });

    $(function(){
        setTimeout(function(){
            if($("input[name=is_register]:checked").val()=='Y'){
                $('#identification_no_div').show();
            }else{ 
                $('#identification_no_div').hide();
            }
        },500);

    });
    $('input[type="radio"]').click(function(){

        var show_identification_check_all = "<?php echo e($show_identification_check_all); ?>";
        var show_identification_check_all_arr = show_identification_check_all.split(",");
        console.log(show_identification_check_all_arr);

        // console.log('checked>>> ' + $("#single_tags").val());

        $.each( show_identification_check_all_arr, function( key, value ) {

            if($("#single_tags").val() == value){
                $('#identification_no_div').show();
            }else{
                $('#identification_no_div').hide();
            }
        });

        /*if($("input[name=is_register]:checked").val()=='Y'){
            if(show_identification_check == 1){
                $('#identification_no_div').show();
            }else{ 
                $('#identification_no_div').hide();
            }
        }
        else{
            $('#identification_no_div').hide();
        }*/
    });
</script>
<?php $__env->stopPush(); ?>