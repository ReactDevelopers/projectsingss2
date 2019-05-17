<?php $__env->startSection('content'); ?>
<section class="content">
       

	<div class="row form-group">
		<div class="col-md-4 pull-right">
			<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
			    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
			    <span></span> <b class="caret"></b>
			</div>
		</div>
		<div class="col-md-4 pull-right">
			<select class="form-control" name="status">
				<option value="closed_not_paid">Completed & Not Paid</option>
				<option value="closed_paid">Completed & Paid</option>
				<option value="initiated">Open</option>
			</select>
		</div>
		<div class="col-md-2 pull-right">
		<button class="pull-right btn btn-info" id="export1" title="Export"><i class="fa fa-download" aria-hidden="true"></i></button>
		</div>

	</div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                	<?php if(Session::has('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?php echo e(Session::get('success')); ?>

                        </div>
                    <?php endif; ?>
                	<div class="table-responsive"><?php echo $html->table(); ?></div>
    			</div>
    		</div>
    	</div>
    </div>	


</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('inlinescript'); ?>
	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
	
	<script type="text/javascript">

		$('#export1').click(function(){
			var params = window.LaravelDataTables['dataTableBuilder'].ajax.params();
			params.download = 'csv';
			var paramsQuery = $.param(params);

			window.location.href= '<?php echo url('administrator/report'); ?>'+'?'+paramsQuery;			
		});
		var start = moment().subtract(29, 'days');
		var end = moment();
		$(function() {

		    function cb(s, e, needToLoad) {
		        $('#reportrange span').html(s.format('D MMMM, YYYY') + ' - ' + e.format('D MMMM, YYYY'));
		        start = s;
		        end = e;
		       // if(needToLoad !== undefined && needToLoad) {
		        	window.LaravelDataTables['dataTableBuilder'].ajax.reload();
		    	//}
		    }

		    $('#reportrange').daterangepicker({
		        startDate: start,
		        endDate: end,
		        locale: {
			      format: 'DD/MM/YYYY'
			    },
		        ranges: {
		           'Today': [moment(), moment()],
		           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
		           'Last 10 Days': [moment().subtract(9, 'days'), moment()],
		           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
		           'Last 60 Days': [moment().subtract(59, 'days'), moment()],
		           'This Month': [moment().startOf('month'), moment().endOf('month')],
		           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		        }
		    }, cb);

		    cb(start, end);		    
		    
		});

		$("#dataTableBuilder").on('preXhr.dt', function ( e, settings, data ) {

	        data.start_date = start.format('YYYY-MM-D');
	        data.end_date = end.format('YYYY-MM-D');
	        data.status = $('select[name="status"]').val();
	    });

	    $('select[name="status"]').change(function(){

	    	window.LaravelDataTables['dataTableBuilder'].ajax.reload();
	    });
	</script>
	<?php echo $html->scripts(); ?>

<?php $__env->stopPush(); ?>

<?php $__env->startSection('inlinecss'); ?>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.backend.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>