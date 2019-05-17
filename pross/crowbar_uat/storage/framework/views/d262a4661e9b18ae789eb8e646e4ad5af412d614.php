<div class="panel-body">
    
    <div class="company-name-wrapper">
        <div class="info-row row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <label class="company-label">Company Name</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-12">
                    <span class="company-name-info"><?php echo e($companydata->company_name); ?></span>
                </div>
        </div>
        <div class="info-row row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <label class="company-label">Company Website</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-12">
                    <span class="company-name-info"><?php echo e($companydata->company_website); ?></span>
                </div>
        </div>
        <div class="info-row row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <label class="company-label">About the Company</label>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-12">
                    <span class="company-name-info"><?php echo e($companydata->company_biography); ?></span>
                </div>
        </div>
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
<script type="text/javascript">

</script>
<?php $__env->stopPush(); ?>
