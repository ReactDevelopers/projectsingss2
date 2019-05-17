<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="form-add-industry" action="<?php echo e(url(sprintf("%s/%s",ADMIN_FOLDER,'industry/add'))); ?>" method="post">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="question">ENGLISH</label>
                                <input type="text" class="form-control" name="en" value="<?php echo e(!empty($industry) ? $industry->en : ''); ?>" placeholder="ENGLISH" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">INDONESIA</label>
                                <input type="text" class="form-control" name="id" value="<?php echo e(!empty($industry) ? $industry->id : ''); ?>" placeholder="INDONESIA" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">MANDARIN</label>
                                <input type="text" class="form-control" name="cz" value="<?php echo e(!empty($industry) ? $industry->cz : ''); ?>" placeholder="MANDARIN" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">TAMIL</label>
                                <input type="text" class="form-control" name="ta" value="<?php echo e(!empty($industry) ? $industry->ta : ''); ?>" placeholder="TAMIL" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">HINDI</label>
                                <input type="text" class="form-control" name="hi" value="<?php echo e(!empty($industry) ? $industry->hi : ''); ?>" placeholder="HINDI" style="width:100%;"/>
                            </div>
                            <?php if(!empty($industry) && !empty($industry->image)): ?>
                                <div class="form-group">
                                    <img src="<?php echo e(asset(str_replace('industry/', 'industry/resize/',$industry->image))); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="id_industry" value="<?php echo e(!empty($industry) ? ___encrypt($industry->id_industry) : ''); ?>">
                        <input type="hidden" name="action" value="submit">
                        <input type="hidden" name="industry_image" value="" >
                        <button class="hide" id="industry-form" type="button" data-request="ajax-submit" data-target='[role="form-add-industry"]' name="submit" class="button" value="Submit">
                            <?php echo e(trans('job.J0029')); ?>

                        </button>                                            
                    </form>
                    <form class="form-horizontal" action="<?php echo e(url(sprintf('%s/industry/image',ADMIN_FOLDER))); ?>" role="doc-submit" method="post" accept-charset="utf-8">
                        <div class="custom-image-upload clearfix">
                            <div class="col-md-7 top-margin-20px">
                                <div class="upload-box row">
                                    <!-- PLACE FOR DYNAMICALLY MULTIPLE ADDED IMAGE  -->
                                    <div class="col-md-6 bottom-margin-10px single-remove">
                                        <label class="btn-bs-file add-image-box">
                                            <span class="add-image-wrapper">
                                                <img src="<?php echo e(asset('images/add-icon.png')); ?>" />
                                                <span class="add-icon-title"><?php echo e(trans('website.W0325')); ?></span>
                                                <input type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-after-upload=".single-remove" data-target='[role="doc-submit"]' data-place="prepend"  data-single="true"/>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="panel-footer">
                        <div class="row form-group button-group">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row form-btn-set">
                                    <div class="col-md-12 col-sm-5 col-xs-6">
                                        <a href="<?php echo e($backurl); ?>" class="btn btn-default">Back</a>
                                        <button 
                                            type="button" 
                                            data-request="trigger-proposal" 
                                            data-target="#industry-form" 
                                            data-copy-source='[name="documents[]"]' 
                                            data-copy-destination='[name="industry_image"]' 
                                            value="Submit" 
                                            class="btn btn-default">
                                            Save
                                        </button>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                                      
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>