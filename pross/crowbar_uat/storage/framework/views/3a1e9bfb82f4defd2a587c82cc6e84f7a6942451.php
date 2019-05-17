<div class="new-upload" id="files-<?php echo e($file['id_file']); ?>">
    <div class="uploaded-docx clearfix">
        <a href="<?php echo e(url(sprintf('/download/file?file_id=%s',___encrypt($file['id_file'])))); ?>">
            <div class="grey-attachement-image">
                <img src="<?php echo e(asset('images/pink-attachement.png')); ?>">
            </div>
        </a>
        <div class="upload-info">
            <p><?php echo e($file['extension']); ?></p>
            <p class="attachement-name"><?php echo e($file['filename']); ?></p>
        </div>
        <a href="javascript:void(0);" data-url="<?php echo e(sprintf(url('ajax/%s?id_file=%s'), DELETE_DOCUMENT, $file['id_file'] )); ?>" data-single="true" data-after-upload=".single-remove" data-toremove="files" title="Delete" data-request="delete" data-file_id="<?php echo e($file['id_file']); ?>" data-delete-id="file_id" data-edit-id="file_id" class="delete-attachment c-p" data-ask="<?php echo e(trans('website.W0688')); ?>"><img src="<?php echo e(asset('images/delete-icon.png')); ?>" />
        </a>
		<input type="hidden" name="documents[]" value="<?php echo e($file['id_file']); ?>">
    </div>
</div>