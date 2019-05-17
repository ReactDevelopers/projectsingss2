<div class="headerWrapper">
    <div class="afterlogin-header employer-header">
        <nav class="navbar navbar-default">
            <div class="container-fluid">            
                <?php if ($__env->exists('language')) echo $__env->make('language', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="navbar-header">
                            <a href="<?php echo e(url(sprintf('%s/find-jobs',TALENT_ROLE_TYPE))); ?>" class="navbar-brand logo">
                                <img src="<?php echo e(asset('images/splashLogo.png')); ?>" class="web-logo">
                                <img src="<?php echo e(asset('images/responsive-logo.png')); ?>" class="responsive-logo">
                            </a>
                        </div>
                    </div>                
                    <div class="col-md-4 col-sm-4 col-xs-12 pull-right account-block">
                        <div class="header-innerWrapper">
                            <div class="collapse navbar-collapse pull-right" id="bs-example-navbar-collapse-1">
                                <ul class="nav navbar-nav">
                                    <li>
                                        <a href="<?php echo e(url(sprintf('/%s/chat',TALENT_ROLE_TYPE))); ?>" class="message-notification"><span data-target="chat-count" style="display: none;"></span></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" id="notification-toggle" class="notification notification-toggle"><span data-target="notification-count" style="display: none;"></span></a>
                                        <ul class="dropdown-submenu notification-submenu" data-target="notification-list">
                                            <li><img style="margin: 30px auto 0;display: inherit;height: 20px;" src="<?php echo e(asset('/images/loading.gif')); ?>"></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a id="usermenu-toggle" href="javascript:void(0);" class="username">
                                            <span class="hidden-xs" style="display: inline-block;text-align: right;"><span class="hello-msg">Hello</span><br><?php echo e(sprintf("%s",$user['first_name'])); ?></span>
                                            <?php if(0): ?>
                                                <img src="<?php echo e(url($user['picture'])); ?>" height="37" alt="<?php echo e(sprintf("%s",$user['first_name'])); ?>" />
                                            <?php endif; ?>
                                           <img src="<?php echo e(asset('images/user-icon.png')); ?>" alt="user">
                                        </a>
                                        <?php 
                                            $isConnected = \Models\companyConnectedTalent::where('id_user',\Auth::user()->id_user)->where('user_type','user')->count();
                                         ?>
                                        <ul class="dropdown-submenu usermenu-submenu">
                                            <li><a href="<?php echo e(url(sprintf('%s/profile/view',TALENT_ROLE_TYPE))); ?>"><?php echo e(trans('website.W0606')); ?></a></li>
                                            <li><a href="<?php echo e(url(sprintf('%s/profile/edit/step/one',TALENT_ROLE_TYPE))); ?>"><?php echo e(trans('website.W0610')); ?></a></li>
                                            <li><a href="<?php echo e(url(sprintf('%s/settings',TALENT_ROLE_TYPE))); ?>"><?php echo e(trans('website.W0598')); ?></a></li>
                                            <?php if(0): ?>
                                                <li><a href="<?php echo e(url(sprintf('%s/add/card',TALENT_ROLE_TYPE))); ?>"><?php echo e(trans('website.W0611')); ?></a></li>
                                            <?php endif; ?>
                                            <?php if($isConnected == 0): ?>
                                                <li>
                                                    <a href="<?php echo e(url(sprintf('%s/talent-connect',TALENT_ROLE_TYPE))); ?>"><?php echo e(trans('website.W0692')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                            <li><a href="<?php echo e(url('logout')); ?>"><?php echo e(trans('website.W0609')); ?></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>                    
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 search-block">
                        <?php if(substr(url()->current(), strrpos(url()->current(), '/') + 1) !== 'find-jobs'): ?>
                            <div class="searchbar-wrap searchForJob">                            
                                <div class="header-searchBar">
                                    <form action="<?php echo e(url(sprintf('%s/find-jobs',TALENT_ROLE_TYPE))); ?>" method="get">
                                        <div class="form-group">
                                            <input type="text" name="_search" value="<?php echo e(\Request::get('_search')); ?>" class="form-control" placeholder="<?php echo e(trans('website.W0500')); ?>">
                                            <button type="submit" class="btn searchBtn"><?php echo e(trans('website.W0342')); ?></button>
                                        </div>
                                    </form>
                                </div>  
                            </div>
                        <?php else: ?>
                            <div class="clearfix"></div>
                        <?php endif; ?>
                    </div>            
                </div>
            </div>
        </nav>
    </div>
    <?php if ($__env->exists($subheader)) echo $__env->make($subheader, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
