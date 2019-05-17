<spark-api inline-template>
    <div>
        <!-- Create API Token -->
        <div>
            <?php echo $__env->make('spark::settings.api.create-token', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>

        <!-- API Tokens -->
        <div>
            <?php echo $__env->make('spark::settings.api.tokens', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
</spark-api>
