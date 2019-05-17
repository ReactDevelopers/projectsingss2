<?php if(isset($with_hotel_name)): ?>
    <label><strong><?php echo e($hotel->name); ?></strong></label> <br>
<?php endif; ?>

<?php echo e($hotel->address); ?>, <?php echo e($hotel->city); ?>, <?php echo e($hotel->region); ?> <?php echo e($hotel->postal_code); ?> <br>

<?php if(isset($hotel->country->name)): ?>
    <?php echo e($hotel->country->name); ?> <br>
<?php endif; ?>

<?php if(isset($hotel->phone)): ?>
    <?php echo e($hotel->phone); ?> <br>
<?php endif; ?>

<?php if(isset($hotel->website)): ?>
    <?php echo e($hotel->website); ?> <br>
<?php endif; ?>

<?php if(isset($contact_hotel->name)): ?>
    Attn: <?php echo e($contact_hotel->name); ?>

<?php endif; ?>
