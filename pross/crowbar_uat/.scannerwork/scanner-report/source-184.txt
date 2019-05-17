<?php echo $__env->make('talent.viewprofile.includes.sidebar-tabs',$user, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="login-inner-wrapper edit-inner-wrapper">
    <div class="inner-profile-section addNewProjects">
        <!-- <h2 class="form-heading"><?php echo e(trans('website.W0327')); ?></h2> -->
        <form class="form-horizontal" action="<?php echo e(url(sprintf('%s/profile/portfolio/__add',TALENT_ROLE_TYPE))); ?>" role="submit_porfolio" method="post" accept-charset="utf-8">
            <div class="form-group">
                <label class="col-md-7 control-label"><?php echo e(trans('website.W0329')); ?></label>
                <div class="col-md-7">
                    <input type="text" name="portfolio" placeholder="<?php echo e(trans('website.W0330')); ?>" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-7 control-label"><?php echo e(trans('website.W0331')); ?></label>
                <div class="col-md-7">
                    <textarea type="text" name="description" placeholder="<?php echo e(trans('website.W0332')); ?>" class="form-control" /></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-7 special-error">
                    <label class="control-label">Document</label>
                    <input type="hidden" name="portfolio_docs" >
                </div>
            </div>
            <button class="hide" id="portflio-form" type="button" data-request="ajax-submit" data-target='[role="submit_porfolio"]' name="submit" class="button" value="Submit">
                <?php echo e(trans('job.J0029')); ?>

            </button>                                
        </form>
        <form class="form-horizontal special-file" role="doc-submit" action="<?php echo e(url(sprintf('%s/port-doc-submit',TALENT_ROLE_TYPE))); ?>" method="POST" accept-charset="utf-8">
                <label class="upload-label pull-right" for="portfolio" id="for_portfolio"><?php echo e(trans('website.W0113')); ?></label>
            <div class="attachment-group row clearfix">                               
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="upload-box">
                        
                    </div>
                    <div class="fileUpload upload-docx ">
                        <input id="portfolio" type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-after-upload=".single-remove" data-target='[role="doc-submit"]' data-single="true"/>
                    </div>
                    <span class="upload-hint">Attachments can be PDF's, TXT's, DOC's, XLS's OR JPG's, GIF's, PNG's.</span>
                </div>
            </div>
        </form> 

          
    </div>
</div>
<div class="row form-group button-group">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row form-btn-set">
            <div class="col-md-7 col-sm-7 col-xs-6">
                <a href="<?php echo e(url()->previous()); ?>" class="greybutton-line" value="Cancel"><?php echo e(trans('job.J0028')); ?></a>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-6">
                <button id="doc-button" type="button" data-request="trigger-proposal" data-target="#portflio-form" data-copy-source='[name="documents"]' data-copy-destination='[name="portfolio_docs"]' class="button" value="Submit">
                <?php echo e(trans('website.W0013')); ?>

            </button>
            </div>
        </div>
    </div>
</div> 