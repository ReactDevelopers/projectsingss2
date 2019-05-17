<?php if ($__env->exists('employer.viewprofile.includes.sidebar-tabs',$user)) echo $__env->make('employer.viewprofile.includes.sidebar-tabs',$user, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>                    
<div class="view-information">
    <div>
        <div class="payment-tabs job-related-tabs shift-up-5px">
            <div class="datatable-listing">
                <div data-target="reviews">
                    <div class="no-table">
                        <?php echo $html->table(); ?>

                    </div>
                </div>                        
            </div>
        </div>
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>

    
    <script type="text/javascript">
        $(function(){
            $('.no-table thead').remove();
            $('#dataTableBuilder_wrapper > .row:first').remove();

            $(document).on('click','[data-request="datatable"]',function(e){
                e.preventDefault();
                
                var $this = $(this);

                $('.active-tab').removeClass('active-tab');
                $this.parent().addClass('active-tab');
                
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.type    = $this.data('type');
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });
        });
    </script>
<?php $__env->stopPush(); ?>
