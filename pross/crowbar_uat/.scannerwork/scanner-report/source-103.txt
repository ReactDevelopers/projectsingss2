<?php $__env->startSection('content'); ?>
    <section class="content">
        <?php if(!empty(\Auth::guard('admin')->user()->type) && \Auth::guard('admin')->user()->type == 'superadmin'): ?>
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3><?php echo e($data[\Auth::guard('admin')->user()->type]['total_projects']); ?></h3>
                            <p>Total Projects</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-globe"></i>
                        </div>
                        <a href="<?php echo e(url(sprintf('%s/project/listing',ADMIN_FOLDER))); ?>" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3><?php echo e($data[\Auth::guard('admin')->user()->type]['total_talents']); ?></h3>
                            <p>Total Talents</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="<?php echo e(url(sprintf('%s/users/talent',ADMIN_FOLDER))); ?>" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3><?php echo e($data[\Auth::guard('admin')->user()->type]['total_employers']); ?></h3>
                            <p>Total Employers</p>
                        </div>
                        <div class="icon">
                            <i class="ion-ios-briefcase"></i>
                        </div>
                        <a href="<?php echo e(url(sprintf('%s/users/employer',ADMIN_FOLDER))); ?>" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3><?php echo e($data[\Auth::guard('admin')->user()->type]['total_disputes']); ?></h3>
                            <p>Total Disputes</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bell"></i>
                        </div>
                        <a href="<?php echo e(url(sprintf('%s/raise-dispute',ADMIN_FOLDER))); ?>" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-aqua">
                            <h3 class="no-margin widget-user-username">Recent Projects</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                <?php $__currentLoopData = $data[\Auth::guard('admin')->user()->type]['recent_projects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li><a><?php echo e(substr($item->title,0,12)); ?> <span class="pull-right badge bg-black"><?php echo e(___format($item->price,true,true)); ?></span></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="<?php echo e(url(sprintf('%s/project/listing',ADMIN_FOLDER))); ?>">See all projects</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-green">
                            <h3 class="no-margin widget-user-username">Recent Talents</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                <?php $__currentLoopData = $data[\Auth::guard('admin')->user()->type]['recent_talents']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li><a><?php echo e(substr($item->first_name.' '.$item->last_name,0,12)); ?> <span class="pull-right badge bg-black"><?php echo e(___ago($item->created)); ?></span></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="<?php echo e(url(sprintf('%s/users/talent',ADMIN_FOLDER))); ?>">See all talents</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-yellow">
                            <h3 class="no-margin widget-user-username">Recent Employers</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                <?php $__currentLoopData = $data[\Auth::guard('admin')->user()->type]['recent_employers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li><a><?php echo e(substr($item->first_name.' '.$item->last_name,0,12)); ?> <span class="pull-right badge bg-black"><?php echo e(___ago($item->created)); ?></span></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="<?php echo e(url(sprintf('%s/users/employer',ADMIN_FOLDER))); ?>">See all employers</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-purple">
                            <h3 class="no-margin widget-user-username">Recent Disputes</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                <?php $__currentLoopData = $data[\Auth::guard('admin')->user()->type]['recent_dispute']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li>
                                        <a><?php echo e(substr($item->title,0,12)); ?> 
                                        <span class="pull-right badge bg-black"><?php echo e(___ago($item->last_updated)); ?></span></a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="<?php echo e(url(sprintf('%s/raise-dispute',ADMIN_FOLDER))); ?>">See all disputes</a>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3><?php echo e($data[\Auth::guard('admin')->user()->type]['total_contacts']); ?></h3>
                            <p>Contacts</p>
                        </div>
                        <div class="icon">
                            <i class="ion-ios-email-outline"></i>
                        </div>
                        <a href="<?php echo e(url(sprintf('%s/messages/inbox',ADMIN_FOLDER))); ?>" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-red">
                            <h3 class="no-margin widget-user-username">Recent Contacts</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                <?php $__currentLoopData = $data[\Auth::guard('admin')->user()->type]['recent_contacts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li><a><?php echo e(substr($item->message_content,0,12)); ?> <span class="pull-right badge bg-black"><?php echo e(___ago($item->created)); ?></span></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="<?php echo e(url(sprintf('%s/messages/inbox',ADMIN_FOLDER))); ?>">See all contacts</a>
                        </div>
                    </div>
                </div>
            </div>
            

        <?php elseif(!empty(\Auth::guard('admin')->user()->type) && \Auth::guard('admin')->user()->type == 'sub-admin'): ?>
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3><?php echo e($data[\Auth::guard('admin')->user()->type]['total_abuses']); ?></h3>
                            <p>Total Abuses</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bullhorn"></i>
                        </div>
                        <a href="<?php echo e(url(sprintf('%s/report-abuse',ADMIN_FOLDER))); ?>" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3><?php echo e($data[\Auth::guard('admin')->user()->type]['total_dispute']); ?></h3>
                            <p>Total Disputes</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-road"></i>
                        </div>
                        <a href="<?php echo e(url(sprintf('%s/raise-dispute',ADMIN_FOLDER))); ?>" class="small-box-footer">
                            More info <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-aqua">
                            <h3 class="no-margin widget-user-username">Recent Abuses</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                <?php $__currentLoopData = $data[\Auth::guard('admin')->user()->type]['recent_abuses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li><a><?php echo e(substr($item->title,0,12)); ?> <span class="pull-right badge bg-black"><?php echo e(___ago($item->created)); ?></span></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="<?php echo e(url(sprintf('%s/report-abuse',ADMIN_FOLDER))); ?>">See all abusess</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-green">
                            <h3 class="no-margin widget-user-username">Recent Disputes</h3>                        
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked" style="min-height: 252px;">
                                <?php $__currentLoopData = $data[\Auth::guard('admin')->user()->type]['recent_dispute']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li><a><?php echo e(substr($item->title,0,12)); ?> <span class="pull-right badge bg-black"><?php echo e(___ago($item->created)); ?></span></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                        </div>
                        <div class="box-footer text-center">
                            <a href="<?php echo e(url(sprintf('%s/raise-dispute',ADMIN_FOLDER))); ?>">See all raised disputes</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>