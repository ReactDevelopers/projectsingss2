<div class="row mb-3">
    <div class="col-sm-2 col-md-1">
        <label><strong>Race Code</strong></label>
        <?php if($errors->has('race_code')): ?>
            <input type="text" class="form-control is-invalid" placeholder="Race Code"
                name="race_code" dusk="race-code" value="<?php echo e(old('race_code', @$race->race_code)); ?>">
            <span class="invalid-feedback">
                <?php echo e($errors->first('race_code')); ?>

            </span>
        <?php else: ?>
            <input type="text" class="form-control" dusk="race-code" placeholder="Race Code" name="race_code"
                value="<?php echo e(old('race_code', @$race->race_code)); ?>">
        <?php endif; ?>
        <small class="form-text text-muted">
            Ex: "AUS-1"
        </small>
    </div>
    <div class="col-sm-2 col-md-1">
        <label><strong>Year</strong></label>
        <?php if($errors->has('year')): ?>
            <input type="text" class="form-control is-invalid" placeholder="2025" name="year"
                value="<?php echo e(old('year', @$race->year)); ?>" dusk="year">
            <span class="invalid-feedback">
                <?php echo e($errors->first('year')); ?>

            </span>
        <?php else: ?>
            <input type="text" class="form-control" placeholder="2025" name="year"
                value="<?php echo e(old('year', @$race->year)); ?>" dusk="year">
        <?php endif; ?>
        <small class="form-text text-muted">
            Ex: "2025"
        </small>
    </div>
    <div class="col-sm-2 col-md-4">
        <label><strong>Race</strong></label>
        <?php if($errors->has('name')): ?>
            <input type="text" class="form-control is-invalid" placeholder="Race Location / Name"
                name="name" value="<?php echo e(old('name', @$race->name)); ?>" dusk="name">
            <span class="invalid-feedback">
                <?php echo e($errors->first('name')); ?>

            </span>
        <?php else: ?>
            <input type="text" class="form-control" placeholder="Race Location / Name" name="name"
                value="<?php echo e(old('name', @$race->name)); ?>" dusk="name">
        <?php endif; ?>
        <small class="form-text text-muted">
            Ex: "US Grand Prix"
        </small>
    </div>
    <div class="col-sm-2">
        <label><strong>Start Date</strong></label>
            <div class="navbar-item" id="date_picker">
                <?php if(isset($set_default_end_date)): ?>
                    <date-pick name="start_on" @input="setDefaultEndDate()"
                        v-model="start_on"
                        :input-attributes="{
                            name: 'start_on',
                            class: 'form-date-picker form-control <?php echo e($errors->has('start_on') ? 'is-invalid' : ''); ?>',
                            placeholder: 'dd/mm/yyyy',
                            autocomplete: 'off'
                        }"
                        :display-format="'DD/MM/YYYY'"
                        :start-week-on-sunday="true">
                    </date-pick>
                <?php else: ?>
                    <date-pick name="start_on"
                        v-model="start_on"
                        :input-attributes="{
                            name: 'start_on',
                            class: 'form-date-picker form-control <?php echo e($errors->has('start_on') ? 'is-invalid' : ''); ?>',
                            placeholder: 'dd/mm/yyyy',
                            autocomplete: 'off'
                        }"
                        :display-format="'DD/MM/YYYY'"
                        :start-week-on-sunday="true">
                    </date-pick>
                <?php endif; ?>
            </div>
            <?php if($errors->has('start_on')): ?>
                <span class="invalid-feedback">
                    <?php echo e($errors->first('start_on')); ?>

                </span>
            <?php endif; ?>
        <small class="form-text text-muted">
            Ex: "31/12/2020"
        </small>
    </div>
    <div class="col-sm-2">
        <label><strong>End Date</strong></label>
            <div class="navbar-item" id="date_picker">
                <date-pick name="end_on"
                    v-model="end_on"
                    :input-attributes="{
                        name: 'end_on',
                        class: 'form-date-picker form-control <?php echo e($errors->has('end_on') ? 'is-invalid' : ''); ?>',
                        placeholder: 'dd/mm/yyyy',
                        autocomplete: 'off'
                    }"
                    :display-format="'DD/MM/YYYY'"
                    :start-week-on-sunday="true">
                </date-pick>
            </div>
            <?php if($errors->has('end_on')): ?>
                <span class="invalid-feedback">
                    <?php echo e($errors->first('end_on')); ?>

                </span>
            <?php endif; ?>
        <small class="form-text text-muted">
            Ex: "31/12/2020"
        </small>
    </div>
    <div class="col-sm-2">
        <label><strong>Currency</strong></label>
        <select class="form-control" name="currency_id">
            <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(isset($race)  && !empty($race)): ?>
                    <option value="<?php echo e($currency->id); ?>" <?php echo e($currency->id === $race->currency_id ? 'selected' : ''); ?>>
                        <?php echo e($currency->name); ?>

                    </option>
                <?php else: ?>
                    <option value="<?php echo e($currency->id); ?>"><?php echo e($currency->name); ?></option>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <small class="form-text text-muted">
            Ex: "USD"
        </small>
    </div>
</div>
