<?php $__env->startPush('scripts'); ?>
    <script src="https://js.stripe.com/v3/"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<spark-register-stripe inline-template>
    <div>
        <div class="spark-screen container">
            <!-- Common Register Form Contents -->
            <?php echo $__env->make('spark::auth.register-common', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <!-- Billing Information -->
            <div class="row justify-content-center" v-if="selectedPlan && selectedPlan.price > 0">
                <div class="col-lg-8">
                    <div class="card card-default">
                        <div class="card-header"><?php echo e(__('Billing Information')); ?></div>

                        <div class="card-body">
                            <!-- Generic 500 Level Error Message / Stripe Threw Exception -->
                            <div class="alert alert-danger" v-if="registerForm.errors.has('form')">
                                <?php echo e(__('We had trouble validating your card. It\'s possible your card provider is preventing us from charging the card. Please contact your card provider or customer support.')); ?>

                            </div>

                            <form role="form">
                                <!-- Cardholder's Name -->
                                <div class="form-group row">
                                    <label for="name" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Cardholder\'s Name')); ?></label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="name" v-model="cardForm.name">
                                    </div>
                                </div>

                                <!-- Card Details -->
                                <div class="form-group row">
                                    <label for="name" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Card')); ?></label>

                                    <div class="col-md-6">
                                        <div id="card-element"></div>
                                        <span class="invalid-feedback" v-show="cardForm.errors.has('card')">
                                            {{ cardForm.errors.get('card') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Billing Address Fields -->
                                <?php if(Spark::collectsBillingAddress()): ?>
                                    <?php echo $__env->make('spark::auth.register-address', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                <?php endif; ?>

                                <!-- ZIP Code -->
                                <div class="form-group row" v-if=" ! spark.collectsBillingAddress">
                                    <label class="col-md-4 col-form-label text-md-right"><?php echo e(__('ZIP / Postal Code')); ?></label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="zip" v-model="registerForm.zip" :class="{'is-invalid': registerForm.errors.has('zip')}">

                                        <span class="invalid-feedback" v-show="registerForm.errors.has('zip')">
                                            {{ registerForm.errors.get('zip') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Coupon Code -->
                                <div class="form-group row" v-if="query.coupon">
                                    <label class="col-md-4 col-form-label text-md-right"><?php echo e(__('Coupon Code')); ?></label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="coupon" v-model="registerForm.coupon" :class="{'is-invalid': registerForm.errors.has('coupon')}">

                                        <span class="invalid-feedback" v-show="registerForm.errors.has('coupon')">
                                            {{ registerForm.errors.get('coupon') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Terms And Conditions -->
                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" v-model="registerForm.terms">
                                                <?php echo __('I Accept :linkOpen The Terms Of Service :linkClose', ['linkOpen' => '<a href="/terms" target="_blank">', 'linkClose' => '</a>']); ?>

                                            </label>
                                            <span class="invalid-feedback" v-show="registerForm.errors.has('terms')">
                                                <strong>{{ registerForm.errors.get('terms') }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tax / Price Information -->
                                <div class="form-group row" v-if="spark.collectsEuropeanVat && countryCollectsVat && selectedPlan">
                                    <label class="col-md-4 col-form-label text-md-right">&nbsp;</label>

                                    <div class="col-md-6">
                                        <div class="alert alert-info" style="margin: 0;">
                                            <strong><?php echo e(__('Tax')); ?>:</strong> {{ taxAmount(selectedPlan) | currency }}
                                            <br><br>
                                            <strong><?php echo e(__('Total Price Including Tax')); ?>:</strong>
                                            {{ priceWithTax(selectedPlan) | currency }}
                                            {{ selectedPlan.type == 'user' && spark.chargesUsersPerSeat ? '/ '+ spark.seatName : '' }}
                                            {{ selectedPlan.type == 'user' && spark.chargesUsersPerTeam ? '/ '+ __('teams.team') : '' }}
                                            {{ selectedPlan.type == 'team' && spark.chargesTeamsPerSeat ? '/ '+ spark.teamSeatName : '' }}
                                            {{ selectedPlan.type == 'team' && spark.chargesTeamsPerMember ? '/ '+ __('teams.member') : '' }}
                                            / {{ __(selectedPlan.interval) | capitalize }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Register Button -->
                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary" @click.prevent="register" :disabled="registerForm.busy">
                                            <span v-if="registerForm.busy">
                                                <i class="fa fa-btn fa-spinner fa-spin"></i> <?php echo e(__('Registering')); ?>

                                            </span>

                                            <span v-else>
                                                <i class="fa fa-btn fa-check-circle"></i> <?php echo e(__('Register')); ?>

                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan Features Modal -->
        <?php echo $__env->make('spark::modals.plan-details', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
</spark-register-stripe>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>