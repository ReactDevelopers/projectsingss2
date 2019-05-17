<div class="greyBar-Heading">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h4>
                    <?php if(!empty($back)): ?>
                        <a href="<?php echo e($back); ?>" class="white-link">&larr;</a>
                    <?php endif; ?>
                    <span class="hire-me-title"><?php echo e($title); ?></span>
                    
                </h4>
            </div>
        </div>
    </div>
</div>
<div class="contentWrapper postjob-permanent-section">
    <div class="postjob-beforesubmit">
        <div class="container">
            <div class="right-sidebar payment-detail-sec no-padding-left no-padding-bottom">
                <ul class="payment-blocks clearfix">
                    <li> <a href="javascript:void(0);" class="total-amount-block"> <span class="payment-text"> <h3><?php echo e(trans('website.W0372')); ?></h3> <span><?php echo e($payment_summary['total_paid']); ?></span> </span> </a> </li>
                    <li> <a href="javascript:void(0);" class="total-amount-due-block"> <span class="payment-text"> <h3><?php echo e(trans('website.W0371')); ?></h3> <span><?php echo e($payment_summary['total_balance']); ?></span> </span> </a> </li>
                    <li> <a href="javascript:void(0);" class="total-job-posted-block"> <span class="payment-text"> <h3><?php echo e(trans('website.W0373')); ?></h3> <span><?php echo e($payment_summary['total_posted_jobs']); ?></span> </span> </a> </li>
                </ul>
                <div class="row">
                    <div class="col-md-9 col-sm-12 col-xs-12">
                        <div id="parentHorizontalTab">
                            <?php echo ___getmenu('employer-payments','%s<ul class="payment-tabs-wrapper">%s</ul>','active-tab',true,false); ?>

                            <div class="payment-tabs">
                                <div>
                                    <div class="datatable-listing no-padding-cell">
                                        <?php echo $html->table();; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
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
            var $tab_counter = <?php echo json_encode($payment_summary['payments']);; ?>

            $('.payment-tabs-wrapper li a').each(function($item){
                if($tab_counter[$item]){
                    $(this).text($(this).text()+" ("+$tab_counter[$item]+")");
                }
            });

           /* $('.table-heading').html('<h4>'+$('.resp-tabs-list li.resp-tab-active').text().split('(')[0]+'</h4>');
            $('.filter-option').html('<div class="row">'+
                '<div class="col-md-12 col-sm-12 col-xs-12">'+
                    '<select name="sort" class="filter form-control select" style="width:100%;">'+
                        '<option value=""><?php echo e(trans("website.W0335")); ?></option>'+
                        '<option value="title-asc"><?php echo e(trans("website.W0336")); ?></option>'+
                        '<option value="title-desc"><?php echo e(trans("website.W0337")); ?></option>'+
                        '<option value="created-asc"><?php echo e(trans("website.W0338")); ?></option>'+
                        '<option value="created-desc"><?php echo e(trans("website.W0339")); ?></option>'+
                    '</select>'+
                '</div>'+
            '</div>');

            $('select.filter').select2({placeholder: function(){$(this).find('option[value!=""]:first').html();}});
            $('.datatable-listing .dataTables_filter input[type="search"]').attr("placeholder","<?php echo e(trans('website.W0342')); ?>");

            $(document).on('change','.filter',function(){
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.sort  = $('[name="sort"]').val();
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });*/
        });
    </script>
<?php $__env->stopPush(); ?>
<!-- Main Content -->
