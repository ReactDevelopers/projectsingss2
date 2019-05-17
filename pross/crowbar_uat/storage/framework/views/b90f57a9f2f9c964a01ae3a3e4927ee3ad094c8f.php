    
    <?php $__env->startSection('requirecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <div class="contentWrapper">
            <div class="greyBar-Heading">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h4><?php echo e(trans('website.W0133')); ?></h4>
                        </div>
                    </div>
                </div>
            </div>  
            <section class="login-section m-t-15">
                <div class="container">                    
                    <div class="row has-vr">
                        <div class="col-md-12 col-sm-12 col-xs-12">                            
                            <form method="POST" action="<?php echo e(url(sprintf('/_forgotpassword'))); ?>" class="form-horizontal" autocomplete="off">
                                <div class="login-inner-wrapper">
                                    <h2 class="form-heading" style="line-height:32px;"><?php echo e(trans('website.W0161')); ?></h2>
                                    <?php echo e(csrf_field()); ?>

                                    <p><?php echo e(trans('website.W0162')); ?></p>
                                    <p><?php echo e(trans('website.W0163')); ?></p>
                                    <br>
                                    <div class="message">
                                        <?php echo e(___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'')); ?>

                                    </div>
                                    <div class="row">
                                        
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            
                                            <div class="form-group has-feedback toggle-social<?php echo e($errors->has(LOGIN_EMAIL) ? ' has-error' : ''); ?>">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input name="<?php echo e(LOGIN_EMAIL); ?>" value="<?php echo e(old(LOGIN_EMAIL,(!empty(${LOGIN_EMAIL}))?${LOGIN_EMAIL}:'')); ?>" type="test" class="form-control" placeholder="<?php echo e(trans('website.W0144')); ?>">
                                                    <?php if($errors->has(LOGIN_EMAIL)): ?>
                                                        <span class="help-block"><?php echo e($errors->first(LOGIN_EMAIL)); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>                                    
                                            
                                            <div class="form-group button-group">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <div class="row form-btn-set">                                        
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <button type="submit" class="btn btn-sm redShedBtn"><?php echo e(trans('website.W0013')); ?></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                 
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.front.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>