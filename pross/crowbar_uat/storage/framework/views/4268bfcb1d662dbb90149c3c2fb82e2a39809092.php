<header class="main-header">
    <a href="javascript:;" style="cursor:default;" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="<?php echo e(asset('images/small-logo.png')); ?>" alt="<?php echo SITE_NAME; ?>" width="35"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><img src="<?php echo e(asset('images/splashLogo.png')); ?>" alt="<?php echo SITE_NAME; ?>" width="130"></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="javascript:;" class="sidebar-toggle" data-toggle="offcanvas" role="button" data-url="<?php echo e(url('ajax/togglesidebar')); ?>">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <span class="page-title"><?php echo e(isset($page_title) ? $page_title : ''); ?></span>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown user user-menu">
                    <a href="javascript:;" style="padding: 9px; cursor: default; height: 50px;">
                        <span class="pull-left image">
                            <?php echo e(___get_text_avatar(Auth::guard('admin')->user()->name,32)); ?>

                        </span>
                        <span class="hidden-xs pull-left" style="position: relative; top: 6px;"><?php echo e(Auth::guard('admin')->user()->name); ?></span>
                    </a>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="<?php echo e(___url('logout','backend')); ?>" title="Sign Out"><i class="fa fa-power-off"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
