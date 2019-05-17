    
    <?php $__env->startSection('requirecss'); ?>
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('inlinecss'); ?>
        
    <?php $__env->stopSection(); ?>
    

    
    <?php $__env->startSection('requirejs'); ?>
        <script src="<?php echo e(asset('js/app.js')); ?>" type="text/javascript"></script>
    <?php $__env->stopSection(); ?>
    
    
    
    <?php $__env->startSection('inlinejs'); ?>
        
    <?php $__env->stopSection(); ?>
    

    <?php $__env->startSection('content'); ?>
        <!-- Banner Section -->
        <?php if(Request::get('stream') != 'mobile'): ?>
            <div class="static-heading-sec">
                <div class="container-fluid">
                    <div class="static Heading">                    
                        <h1><?php echo e($title); ?></h1>                        
                    </div>                    
                </div>
            </div>
        <?php endif; ?>
        <!-- /Banner Section -->
        <!-- Main Content -->
        <div class="contentWrapper">
            <section class="aboutSection questions-listing ask-question-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="left-question-section">
                                <?php if(!empty(\Auth::user())): ?>
                                    <form role="add-talent" action="<?php echo e(url('/mynetworks/community/forum/question/add')); ?>" method="POST" class="question-form">
                                        <input type="hidden" name="_method" value="PUT">
                                        <?php echo e(csrf_field()); ?>

                                        <div class="questionform-box">
                                            <h2 class="form-heading"><?php echo e(trans('website.W0955')); ?></h2>
                                            <label><?php echo e(trans('website.W0956')); ?></label>
                                            <div class="form-group form-element">
                                                <textarea name="question_description" class="form-control" placeholder="<?php echo e(trans('website.W0957')); ?>"></textarea>
                                            </div>
                                            <?php if($company_profile != 'individual'): ?>
	                                            <div class="form-group form-element">
	                                            	<div>
			                                            <select name="type">
														  	<option value="individual" selected="selected">Post as <?php echo e(\Auth::user()->name); ?></option>
														  	<option value="firm">Post as firm</option>
														</select>                                            	
	                                            	</div>
	                                            </div>
	                                        <?php else: ?>
	                                        	<div class="form-group form-element" style="display:none;">
	                                            	<div>
			                                            <select name="type">
														  	<option value="individual" selected="selected">Post as <?php echo e(\Auth::user()->name); ?></option>
														</select>                                            	
	                                            	</div>
	                                            </div>
                                            <?php endif; ?>
	                                        <div class="row form-group ask-question-btns button-group">
	                                            <div class="col-md-12 col-sm-12 col-xs-12">
	                                                <div class="row form-btn-set inner-btns">
	                                                    <div class="col-md-7 col-sm-7 col-xs-6">
	                                                        <a href="<?php echo e(url('/network/community/forum')); ?>" class="greybutton-line" value="<?php echo e(trans('website.W0196')); ?>">
	                                                            <?php echo e(trans('website.W0355')); ?>

	                                                        </a>
	                                                    </div>
	                                                    <div class="col-md-5 col-sm-5 col-xs-6">
	                                                        <input data-request="ajax-submit" data-target='[role="add-talent"]' type="button" class="button" value="<?php echo e(trans('website.W0393')); ?>" />
	                                                    </div>
	                                                </div>
	                                            </div>
	                                        </div>                                
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="related-questions ask-related-question grey-bg">
                                <h3 class="form-heading top-margin-20px"><?php echo e(trans('website.W0958')); ?></h3>
                                <div class="ask-question-list">
                                	<label><?php echo e(trans('website.W0959')); ?></label>
                                	<p><?php echo e(trans('website.W0960')); ?></p>
                                </div>
                                <div class="ask-question-list">
                                	<label><?php echo e(trans('website.W0961')); ?></label>
                                	<p><?php echo e(trans('website.W0962')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> 
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('inlinescript'); ?>
    <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.talent.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>