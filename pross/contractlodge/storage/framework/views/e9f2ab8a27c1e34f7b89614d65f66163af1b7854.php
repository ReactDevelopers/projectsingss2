<spark-profile :user="user" inline-template>
    <div>
        <!-- Update Profile Photo -->
        <?php echo $__env->make('spark::settings.profile.update-profile-photo', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <!-- Update Contact Information -->
        <?php echo $__env->make('spark::settings.profile.update-contact-information', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <!-- Update Notification Options -->
        <?php echo $__env->make('settings.profile.update-notification-options', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
</spark-profile>
