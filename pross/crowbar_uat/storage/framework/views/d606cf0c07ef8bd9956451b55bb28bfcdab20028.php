<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Folders</h3>
                        <div class="box-tools">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-pills nav-stacked">
                            <li><a href="<?php echo e(url('administrator/messages/inbox')); ?>"><i class="fa fa-inbox"></i> Inbox</a>
                            <li><a href="<?php echo e(url('administrator/messages/closed')); ?>"><i class="fa fa-power-off"></i> Closed</a></li>
                            <li><a href="<?php echo e(url('administrator/messages/trashed')); ?>"><i class="fa fa-trash-o"></i> Trash</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel">
                    <div class="box">
                        <form role="add-talent" method="post" enctype="multipart/form-data" action="<?php echo e(url('administrator/messages/reply/'.$message['id_message'])); ?>">
                            <input type="hidden" name="_method" value="PUT">
                            <?php echo e(csrf_field()); ?>


                            <div class="box-body no-padding">
                                <div class="mailbox-read-info">
                                    <h3 class="break-all"><?php echo e($message['message_subject']); ?></h3>
                                    <span class="mailbox-read-time pull-right"></span>
                                </div>
                                <div class="clear-fix"></div>
                                <div class="mailbox-read-message" style="border-bottom:1px solid #f1f1f1;">
                                    <div class="row" style="border-bottom:1px solid #f1f1f1;margin-bottom:5px;padding-bottom:10px;">
                                        <div class="col-md-6 text-left">
                                            <a href="javascript:;" class="mailbox-attachment-name">From: <?php echo e($message['sender_name']); ?> (<?php echo e($message['sender_email']); ?>)</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div>
                                                <?php echo e($message['message_content']); ?>

                                                <div class="clear-fix"></div>
                                            </div>
                                            <span class="mailbox-attachment-size"><?php echo e(___ago($message['created'])); ?></span>
                                        </div>
                                    </div>
                                    <?php if($message['message_ticket_status'] == 'open'): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="name">Reply</label>
                                                <textarea name="message_content" class="form-control"></textarea>
                                                <input name="record_id" value="<?php echo e($message['id_message']); ?>" type="hidden">
                                                <div class="clear-fix"></div>
                                                <div class="btn-group pull-right" style="margin-top:10px;">
                                                    <button data-request="ajax-submit" data-target='[role="add-talent"]' name="reply" class="btn btn-default" type="button" value="reply">Reply</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if(!empty($message_replay)): ?>
                                        <div class="row" style="border-bottom:1px solid #f1f1f1;margin-bottom:5px;padding-bottom:10px;">
                                            <div class="col-md-6 text-left">
                                                <a href="javascript:;" class="mailbox-attachment-name">From: CrowBar Admin</a>
                                            </div>
                                        </div>
                                        <?php $__currentLoopData = $message_replay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div>
                                                    <?php echo e($m['message_content']); ?>

                                                    <div class="clear-fix"></div>
                                                </div>
                                                <span class="mailbox-attachment-size"><?php echo e(___ago($m['created'])); ?></span>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </form>
                        <?php if($message['message_status'] != 'trashed'): ?>
                        <div class="box-footer">
                            <a href="<?php echo e(url('administrator/messages/delete/'.$message['id_message'])); ?>" onclick="return confirm('Do you really want to continue with this action?');">
                                <button class="btn btn-default" name="delete" value="do_delete" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('inlinescript'); ?>
<script type="text/javascript">

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>