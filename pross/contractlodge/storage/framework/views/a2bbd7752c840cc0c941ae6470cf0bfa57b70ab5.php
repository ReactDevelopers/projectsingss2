<?php $__env->startSection('content'); ?>
<clients :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <?php echo $__env->make('flash::message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                
                <?php echo $__env->make('partials.common.flash-url-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <div class="card card-default">
                    <div class="card-header">
                        <a href="<?php echo e(route('clients.create')); ?>" class="btn btn-primary btn-sm float-right mr-3" role="button" dusk="add-client" data-offline="disabled">
                            <i class="fa fa-plus"></i> <?php echo e(__('Add Client')); ?>

                        </a>

                        <?php if(isset($showArchived)): ?>
                            <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-success btn-sm float-right mr-3" role="button">
                                <i class="fa fa-bolt mr-2"></i> <?php echo e(__('View Active Clients')); ?>

                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('clients.archived')); ?>" class="btn btn-secondary btn-sm float-right mr-3"
                                role="button" dusk="view-client-archive">
                                <i class="fa fa-archive mr-2"></i> <?php echo e(__('View Archived Clients')); ?>

                            </a>
                        <?php endif; ?>

                        <?php if(isset($showArchived)): ?>
                            <?php echo e(__('Archived')); ?>

                        <?php else: ?>
                            <?php echo e(__('Active')); ?>

                        <?php endif; ?>

                        <?php echo e(__('Clients')); ?>

                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            <?php if(! $clients->count()): ?>
                                <?php if(isset($showArchived)): ?>
                                    <div class="col-sm-12"><p>There are currently no archived clients.</p></div>
                                <?php else: ?>
                                    <div class="col-sm-12">
                                        <p>
                                            No clients found.
                                            <a href="<?php echo e(route('clients.create')); ?>">Click here</a> to add a client.
                                        </p>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="col-sm-12">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Client</th>
                                                <th class="text-right">Num Invoices/Confirmations</th>
                                                <th class="text-right">Invoice Amount</th>
                                                <th class="text-right">Amount Paid</th>
                                                <th class="text-right">Outstanding</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td>
                                                        <?php if(isset($showArchived)): ?>
                                                            <?php echo e($client->name); ?>

                                                        <?php else: ?>
                                                            <a href="<?php echo e(route('clients.show', [
                                                                'client' => $client->id
                                                                ])); ?>"><?php echo e($client->name); ?></a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo e(($client->invoices->count() + $client->confirmations->count())); ?>

                                                    </td>
                                                    <td class="text-right">
                                                        (coming soon)
                                                    </td>
                                                    <td class="text-right">
                                                        (coming soon)
                                                    </td>
                                                    <td class="text-right">
                                                        (coming soon)
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if(isset($showArchived)): ?>
                                                            <form action="<?php echo e(route('clients.unarchive', [
                                                                'client_id' => $client->id
                                                                ])); ?>" method="POST">
                                                                <?php echo e(method_field('PUT')); ?>

                                                                <?php echo e(csrf_field()); ?>

                                                                <button onclick="return confirm('Are you sure you want to unarchive this client?');"
                                                                    class="btn btn-success btn-sm" dusk="client-unarchive">
                                                                    <i class="fa fa-undo mr-2"></i> Unarchive
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <form action="<?php echo e(route('clients.destroy', ['client' => $client->id])); ?>" method="POST">
                                                                <?php echo e(method_field('DELETE')); ?>

                                                                <?php echo e(csrf_field()); ?>

                                                                <button onclick="return confirm('Are you sure you want to archive this client?');"
                                                                    class="btn btn-secondary btn-sm" dusk="client-archive">
                                                                    <i class="fa fa-archive mr-2"></i> Archive
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Totals</strong></td>
                                                <td class="text-right"><strong>(coming soon)</strong></td>
                                                <td class="text-right"><strong>(coming soon)</strong></td>
                                                <td class="text-right"><strong>(coming soon)</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</clients>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('spark::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>