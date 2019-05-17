<?php if(Auth::user()->onTrial()): ?>
    <!-- Trial Reminder -->
    <h6 class="dropdown-header"><?php echo e(__('Trial')); ?></h6>

    <a class="dropdown-item" href="/settings#/subscription">
        <i class="fa fa-fw text-left fa-btn fa-shopping-bag"></i> <?php echo e(__('Subscribe')); ?>

    </a>

    <div class="dropdown-divider"></div>
<?php endif; ?>

<?php if(Spark::usesTeams() && Auth::user()->ownsCurrentTeam() && Auth::user()->currentTeamOnTrial()): ?>
    <!-- Team Trial Reminder -->
    <h6 class="dropdown-header"><?php echo e(__('teams.team_trial')); ?></h6>

    <a class="dropdown-item" href="/settings/<?php echo e(Spark::teamsPrefix()); ?>/<?php echo e(Auth::user()->currentTeam()->id); ?>#/subscription">
        <i class="fa fa-fw text-left fa-btn fa-shopping-bag"></i> <?php echo e(__('Subscribe')); ?>

    </a>

    <div class="dropdown-divider"></div>
<?php endif; ?>
