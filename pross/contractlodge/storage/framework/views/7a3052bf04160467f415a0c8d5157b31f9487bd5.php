<?php if(Spark::billsUsingStripe()): ?>
    <?php echo $__env->make('spark::auth.register-stripe', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php else: ?>
    <?php echo $__env->make('spark::auth.register-braintree', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>
