<spark-security :user="user" inline-template>
	<div>
	    <!-- Update Password -->
	    <?php echo $__env->make('spark::settings.security.update-password', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

	    <!-- Two-Factor Authentication -->
	    <?php if(Spark::usesTwoFactorAuth()): ?>
	    	<div v-if="user && ! user.uses_two_factor_auth">
	    		<?php echo $__env->make('spark::settings.security.enable-two-factor-auth', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	    	</div>

	    	<div v-if="user && user.uses_two_factor_auth">
	    		<?php echo $__env->make('spark::settings.security.disable-two-factor-auth', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	    	</div>

			<!-- Two-Factor Reset Code Modal -->
	    	<?php echo $__env->make('spark::settings.security.modals.two-factor-reset-code', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	    <?php endif; ?>
    </div>
</spark-security>
