<form role="add-talent" method="post" enctype="multipart/form-data" action="<?php echo e(url('administrator/talent-users/'.$user['id_user'].'/update')); ?>">
    <input type="hidden" name="_method" value="PUT">
    <?php echo e(csrf_field()); ?>


    <div class="panel-body">
        <div class="form-group <?php if($errors->has('first_name')): ?>has-error <?php endif; ?>">
            <label for="name">First Name</label>
            <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?php echo e((old('first_name'))?old('first_name'):$user['first_name']); ?>">
            <?php if($errors->first('first_name')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('first_name')); ?>

                </span>
            <?php endif; ?>
        </div>
        <div class="form-group <?php if($errors->has('last_name')): ?>has-error <?php endif; ?>">
            <label for="name">Last Name</label>
            <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?php echo e((old('last_name'))?old('last_name'):$user['last_name']); ?>">
            <?php if($errors->first('last_name')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('last_name')); ?>

                </span>
            <?php endif; ?>
        </div>
        <div class="form-group <?php if($errors->has('email')): ?>has-error <?php endif; ?>">
            <label for="name">Email</label>
            <input readonly="readonly" type="text" class="form-control" name="email" placeholder="Email" value="<?php echo e((old('email'))?old('email'):$user['email']); ?>">
            <?php if($errors->first('email')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('email')); ?>

                </span>
            <?php endif; ?>
        </div>
        <div class="form-group <?php if($errors->has('birthday')): ?>has-error <?php endif; ?>">
            <label for="name">Date of Birth</label>
            <?php  $birthday = old('birthday')?:($user['birthday']?:'');  ?>
            <input id="birthday" type="text" class="form-control" name="birthday" placeholder="Date of Birth" value="<?php echo e($birthday ? date('d-m-Y',strtotime($birthday)):''); ?>">
            <?php if($errors->first('birthday')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('birthday')); ?>

                </span>
            <?php endif; ?>
        </div>
        <div class="form-group <?php if($errors->has('gender')): ?>has-error <?php endif; ?>">
            <label for="name">Gender</label>
            <?php 
            if(old('gender')){
                $gender = old('gender');
            }
            else{
                $gender = $user['gender'];
            }
             ?>
            <select class="form-control" name="gender" placeholder="Gender">
                <option <?php echo e($gender=='male'?' selected="selected"':''); ?> value="male">Male</option>
                <option <?php echo e($gender=='female'?' selected="selected"':''); ?> value="female">Female</option>
                <option <?php echo e($gender=='other'?' selected="selected"':''); ?> value="other">Other</option>
            </select>
            <?php if($errors->first('gender')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('gender')); ?>

                </span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php if($errors->has('country_code')): ?>has-error <?php endif; ?>">
            <label for="name">Country Code</label>
            <?php 
            if(old('country_code')){
                $country_code = old('country_code');
            }
            else{
                $country_code = $user['country_code'];
            }
             ?>
            <div>
                <select class="form-control" name="country_code" placeholder="Country Code">
                </select>
            </div>
            <?php if($errors->first('country_code')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('country_code')); ?>

                </span>
            <?php endif; ?>
        </div>
        <div class="form-group <?php if($errors->has('mobile')): ?>has-error <?php endif; ?>">
            <label for="name">Mobile</label>
            <input type="text" class="form-control" name="mobile" placeholder="Mobile" value="<?php echo e((old('mobile'))?old('mobile'):$user['mobile']); ?>">
            <?php if($errors->first('mobile')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('mobile')); ?>

                </span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php if($errors->has('address')): ?>has-error <?php endif; ?>">
            <label for="name">Address</label>
            <textarea class="form-control" name="address" placeholder="Address"><?php echo e((old('address'))?old('address'):$user['address']); ?></textarea>
            <?php if($errors->first('address')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('address')); ?>

                </span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php if($errors->has('country')): ?>has-error <?php endif; ?>">
            <label for="name">Country</label>
            <select class="form-control" name="country" id="country" data-url="<?php echo e(url('ajax/state-list')); ?>" placeholder="Country">
            </select>
            <?php if($errors->first('country')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('country')); ?>

                </span>
            <?php endif; ?>
        </div>
        <div class="form-group <?php if($errors->has('state')): ?>has-error <?php endif; ?>">
            <label for="name">State</label>
            <select class="form-control" name="state" id="state" placeholder="State" data-url="<?php echo e(url('ajax/city-list')); ?>">
            </select>
            <?php if($errors->first('state')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('state')); ?>

                </span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php if($errors->has('city')): ?>has-error <?php endif; ?>">
            <label for="name">City</label>
            <select class="form-control" name="city" id="city" placeholder="State">
            </select>
            <?php if($errors->first('city')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('city')); ?>

                </span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php if($errors->has('postal_code')): ?>has-error <?php endif; ?>">
            <label for="name">Postal Code</label>
            <input type="text" class="form-control" name="postal_code" placeholder="Postal Code" value="<?php echo e((old('postal_code'))?old('postal_code'):$user['postal_code']); ?>">
            <?php if($errors->first('postal_code')): ?>
                <span class="help-block">
                    <?php echo e($errors->first('postal_code')); ?>

                </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="panel-footer">
        <a href="<?php echo e($backurl); ?>" class="btn btn-default">Back</a>
        <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
    </div>
</form>
<?php $__env->startSection('inlinecss'); ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<?php $__env->stopSection(); ?>
<?php $__env->startPush('inlinescript'); ?>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
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
            },1000);

            $( "#birthday" ).datepicker({
                dateFormat: 'dd-mm-yy',
                maxDate: new Date(<?php echo e(date('Y')+BIRTHDAY_MIN_YEAR_LIMIT); ?>, <?php echo e(date('m')-1); ?>, <?php echo e(date('d')); ?>),
                changeMonth: true,
                yearRange: '-100:+0',
                changeYear: true,
            });
        });
    </script>
<?php $__env->stopPush(); ?>
