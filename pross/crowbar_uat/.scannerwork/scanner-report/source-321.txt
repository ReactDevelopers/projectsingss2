<div class="">
	<div class="form-group">
		<div class="user-profile-image artice-detail-cropper-section">
			<div class="user-info-wrapper user-info-greyBox artice-detail-cropper">
				<div class="user-display-details">
					<div class="user-display-image cropper" data-request="cropper" data-class="profile" data-width="192" data-height="192" data-folder="<?php echo e(TALENT_ARTICLE_PHOTO_UPLOAD); ?>" data-record="0" data-column="profile" style="background: url('<?php echo e($user['picture']); ?>') no-repeat center center;background-size:100% 100%"><span class="add-image-text">Add Image</span></div>
				</div>
			</div>
		</div>
	</div>
</div>
<form class="form-horizontal" role="talent-article" action="<?php echo e(url('/mynetworks/article/add')); ?>" method="post" accept-charset="utf-8">
	<?php echo e(csrf_field()); ?>

	<div class="login-inner-wrapper form-cropper-section">
		<h2 class="form-heading no-padding"><?php echo e(trans('website.W0965')); ?></h2>
		<div class="row">
			<div class="inner-text-boxes">
				<div class="form-group form-group-wrapper">
					<div class="custom-dropdown">
						<input type="hidden" id="file_id" name="file_id" value="">
						<input type="text" name="title" class="form-control" placeholder="<?php echo e(trans('website.W0966')); ?>" value="">
					</div>  
				</div>
			</div>
		</div>
		<div class="form-group textbox-wrapper special-error">
			<div class="form-group-description clearfix">
				<textarea id="description" name="description" placeholder="<?php echo e(trans('website.W0653')); ?>" class="form-control" ></textarea>
			</div>
		</div>
		<br/>
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
        <div class="row form-btn-set">
			<div class="col-md-5 col-sm-7 col-xs-6">
				<a href="<?php echo e(url('network/article')); ?>" class="greybutton-line">
					<?php echo e(trans('website.W0355')); ?>

				</a>
			</div>
			<div class="col-md-5 col-sm-5 col-xs-6">
				<input data-request="ajax-submit" data-target='[role="talent-article"]' type="button" class="button" value="<?php echo e(trans('website.W0229')); ?>" />
			</div>
		</div>
	</div>
</form>
<?php $__env->startPush('inlinescript'); ?>
	<style>
		.cke_inner {
		    background: none!important;
		}
	</style>
	<script src="//cdn.ckeditor.com/4.7.3/standard/ckeditor.js"></script>
	<script type="text/javascript">
		CKEDITOR.config.allowedContent = true;
		CKEDITOR.config.forcePasteAsPlainText = true;
		CKEDITOR.config.extraAllowedContent = "div(*)";
		CKEDITOR.config.toolbar = 'Basic';
		CKEDITOR.config.height = '250px';
		CKEDITOR.config.toolbar_Basic = [
			['Undo','Redo'],
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ items : ['Bold','Italic','Strike']},
			{ items : ['RemoveFormat']},
			{ items : ['NumberedList', 'BulletedList','-','Outdent', 'Indent', '-', 'Blockquote','-','Link', 'Unlink','-','Image','Table','-','Maximize','-','Scayt']}
		];
		CKEDITOR.replace('description',{contentsCss: "<?php echo e(asset('css/iframe.ckeditor.css')); ?>"});

		$(document).on('click','[data-request="ajax-submit"]',function(){
			$('#description').val(CKEDITOR.instances.description.getData());
	    });

	    $(".cropper").SGCropper({
            viewMode: 1,
            aspectRatio: "2/3",
            cropBoxResizable: false,
            formContainer:{
                actionURL:"<?php echo e(url(sprintf('ajax/crop?imagename=image&user_id=%s&type=article',Auth::user()->id_user))); ?>",
                modelTitle:"<?php echo e(trans('website.W0970')); ?>",
                modelSuggestion:"<?php echo e(trans('website.W0263')); ?>",
                modelDescription:"<?php echo e(trans('website.W0264')); ?>",
                modelSeperator:"<?php echo e(trans('website.W0265')); ?>",
                uploadLabel:"<?php echo e(trans('website.W0266')); ?>",
                fieldLabel:"",
                fieldName: "image",
                btnText:"<?php echo e(trans('website.W0971')); ?>",
                defaultImage: "../images/product_sample.jpg",
                loaderImage: "<?php echo e(asset('images/loader.gif')); ?>",
            }
        });

	</script>
<?php $__env->stopPush(); ?>