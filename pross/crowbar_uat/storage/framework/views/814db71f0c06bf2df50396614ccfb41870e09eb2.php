<div class="col-md-12 no-padding-xs">
    <div class="approved-proposals no-padding">
        <h2 class="form-heading">
            <?php echo e(trans('website.W0225')); ?><span id="totalproposal"></span>
        </h2>
        <div class="datatable-listing">
            <?php echo $html->table();; ?>

        </div>
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
    <style type="text/css">
        .view-profile-name .last-viewed-icon {
            position: absolute;
            right: 24px;
            margin-top: 8px;
        }
    </style>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap.js')); ?>"></script>
    <?php echo $html->scripts(); ?>


    <script type="text/javascript">
        $(function(){
            $('.filter-option').html('<div class="row">'+
                '<div class="col-md-6 col-sm-6 col-xs-6">'+
                    '<select name="sort" class="filter form-control select" style="width:100%;">'+
                        '<option value=""><?php echo e(trans("website.W0335")); ?></option>'+
                        '<option value="name-asc"><?php echo e(trans("website.W0336")); ?></option>'+
                        '<option value="name-desc"><?php echo e(trans("website.W0337")); ?></option>'+
                        '<option value="proposal_sent-asc"><?php echo e(trans("website.W0338")); ?></option>'+
                        '<option value="proposal_sent-desc"><?php echo e(trans("website.W0339")); ?></option>'+
                        '<option value="quoted_price-asc"><?php echo e(trans("website.W0615")); ?></option>'+
                        '<option value="quoted_price-desc"><?php echo e(trans("website.W0616")); ?></option>'+
                    '</select>'+
                '</div>'+
                '<div class="col-md-6 col-sm-6 col-xs-6">'+
                    '<select name="filter" class="filter form-control select" style="width:100%;">'+
                        '<option value=""><?php echo e(trans("website.W0340")); ?></option>'+
                        '<option value="tagged_listing"><?php echo e(trans("website.W0341")); ?></option>'+
                        '<option value="accepted_proposal"><?php echo e(trans("website.W0780")); ?></option>'+
                        '<option value="applied_proposal"><?php echo e(trans("website.W0756")); ?></option>'+
                        '<option value="declined_proposal"><?php echo e(trans("website.W0755")); ?></option>'+
                    '</select>'+
                '</div>'+
            '</div>');

            $('select.filter').select2({placeholder: function(){$(this).find('option[value!=""]:first').html();}});
            $('.datatable-listing .dataTables_filter input[type="search"]').attr("placeholder","<?php echo e(trans('website.W0342')); ?>");

            $(document).on('change','.filter',function(){
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.sort    = $('[name="sort"]').val();
                    data.filter  = $('[name="filter"]').val();
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });

            $(document).ajaxStop(function($response,$data) {
                var $totalproposal = $('#dataTableBuilder_info').text().split(" ");
                $('#totalproposal').text(" ("+$totalproposal[5]+")");
            });
        });
    </script>
<?php $__env->stopPush(); ?>
