<ul class="post-project-link">
    <li class="language-selector <?php if(\Cache::get('configuration')['is_language_enabled'] === 'Y'): ?> currency-selector <?php endif; ?>">
        <form method="get" action="<?php echo e(url('/currency')); ?>">
            <select name="currency" onchange="submit()" class="form-control">
                <?php $__currentLoopData = currencies(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency => $sign): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <option value="<?php echo e($currency); ?>" <?php if(\Session::get('site_currency') == $currency): ?> selected="selected" <?php endif; ?>><?php echo e($currency); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
            </select>
        </form>
    </li>
    <?php if(\Cache::get('configuration')['is_language_enabled'] === 'Y'): ?>
        <li class="language-selector">
            <form method="get" action="<?php echo e(url('/language')); ?>">
                <select name="language" onchange="submit()" class="form-control">
                    <?php $__currentLoopData = language(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $language): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <option value="<?php echo e($code); ?>" <?php if(\App::getLocale() == $code): ?> selected="selected" <?php endif; ?>><?php echo e(strtoupper($code)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </select>
            </form>
        </li>
    <?php endif; ?>
    <?php if(!empty($project_link)): ?>
        <li class="post-project-link"><a href="<?php echo e(url('/signup/employer')); ?>" class="navyblueBtn">Post a project</a></li>
    <?php endif; ?>
</ul>