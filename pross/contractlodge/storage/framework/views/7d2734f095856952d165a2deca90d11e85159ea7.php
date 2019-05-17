<spark-create-token :available-abilities="availableAbilities" inline-template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <?php echo e(__('Create API Token')); ?>

            </div>

            <div class="card-body">
                <form role="form">
                    <!-- Token Name -->
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right"><?php echo e(__('Token Name')); ?></label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" name="name" v-model="form.name"  :class="{'is-invalid': form.errors.has('name')}">

                            <span class="invalid-feedback" v-show="form.errors.has('name')">
                                {{ form.errors.get('name') }}
                            </span>
                        </div>
                    </div>

                    <!-- Token Abilities -->
                    <div class="form-group row" v-if="availableAbilities.length > 0">
                        <label class="col-md-4 col-form-label text-md-right"><?php echo e(__('Token Can')); ?></label>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <button class="btn btn-default" @click.prevent="assignAllAbilities" v-if=" ! allAbilitiesAssigned">
                                    <i class="fa fa-btn fa-check"></i> <?php echo e(__('Assign All Abilities')); ?>

                                </button>

                                <button class="btn btn-default" @click.prevent="removeAllAbilities" v-if="allAbilitiesAssigned">
                                    <i class="fa fa-btn fa-times"></i> <?php echo e(__('Remove All Abilities')); ?>

                                </button>
                            </div>

                            <div v-for="ability in availableAbilities">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"
                                            @click="toggleAbility(ability.value)"
                                            :class="{'is-invalid': form.errors.has('abilities')}"
                                            :checked="abilityIsAssigned(ability.value)">

                                            {{ ability.name }}
                                    </label>
                                </div>
                            </div>

                            <span class="invalid-feedback" v-show="form.errors.has('abilities')">
                                {{ form.errors.get('abilities') }}
                            </span>
                        </div>
                    </div>

                    <!-- Create Button -->
                    <div class="form-group row mb-0">
                        <div class="offset-md-4 col-md-6">
                            <button type="submit" class="btn btn-primary"
                                    @click.prevent="create"
                                    :disabled="form.busy">

                                <?php echo e(__('Create')); ?>

                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Show Token Modal -->
        <div class="modal" id="modal-show-token" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" v-if="showingToken">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?php echo e(__('API Token')); ?>

                        </h5>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <?php echo e(__('Here is your new API token.')); ?>

                             <strong><?php echo e(__('This is the only time the token will ever be displayed, so be sure not to lose it!')); ?></strong>
                            <?php echo e(__('You may revoke the token at any time from your API settings.')); ?>

                        </div>

                        <textarea id="api-token" class="form-control"
                                  @click="selectToken"
                                  rows="5">{{ showingToken }}</textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="selectToken">
                        <span v-if="copyCommandSupported"><?php echo e(__('Copy To Clipboard')); ?></span>
                        <span v-else><?php echo e(__('Select All')); ?></span>
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</spark-create-token>
