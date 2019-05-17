<section class="content">
	<div class="row">
		<div class="col-md-6 pull-right">
			<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
			    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
			    <span></span> <b class="caret"></b>
			</div>
		</div>
		<div class="col-md-2 pull-right">
			<button class="pull-right btn btn-info" id="export1" title="Export"><i class="fa fa-download" aria-hidden="true"></i></button>
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="col-md-4">
			<div class="small-box bg-yellow">
                <div class="inner text-center">
                    <h3 id="talent_start_job">0</h3>
                    <p>Job(s) Started</p>
                </div>
            </div>
		</div>
		<div class="col-md-4">
			<div class="small-box bg-yellow">
                <div class="inner text-center">
                    <h3 id="talent_completed_job">0</h3>
                    <p>Job(s) Completed</p>
                </div>
            </div>
		</div>
		<div class="col-md-4">
			<div class="small-box bg-yellow">
                <div class="inner text-center">
                    <h3 id="talent_submit_proposal">0</h3>
                    <p>Proposal(s) Submitted</p>
                </div>
            </div>
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
                    <div class="table-responsive">
                        <?php echo $html->table();; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->startPush('inlinescript'); ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
	$('#export1').click(function(){
		var params = window.LaravelDataTables['dataTableBuilder'].ajax.params();
		params.download = 'csv';
		params.user_id = '<?php echo e($id_user); ?>';
		var paramsQuery = $.param(params);

		window.location.href= '<?php echo url('administrator/users/talent/edit/activity_log'); ?>'+'?'+paramsQuery;			
	});
	var start = moment().subtract(29, 'days');
	var end = moment().add(1,'days');
	$(function() {

	    function cb(s, e) {
	        $('#reportrange span').html(s.format('D MMMM, YYYY') + ' - ' + e.format('D MMMM, YYYY'));
	        start = s;
	        end = e;
	        window.LaravelDataTables['dataTableBuilder'].ajax.reload();
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
        data.end_date 	= end.format('YYYY-MM-D');
    });

    var startDate = start.format('YYYY-MM-D');
	var endDate   = end.format('YYYY-MM-D');

	$(document).ready(function(){
		startDate = start.format('YYYY-MM-D');
		endDate   = end.format('YYYY-MM-D');
		callAjax(startDate,endDate);
	});

	$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
		startDate = picker.startDate;
		endDate   = picker.endDate;
		startDate = startDate.format('YYYY-MM-D');
		endDate   = endDate.format('YYYY-MM-D');
		callAjax(startDate,endDate);
	});

	function callAjax($start_date,$end_date){
		$.ajax({
			url: '<?php echo e(url('administrator/activity-log/talent/countActivity')); ?>', 
			type: 'post', 
			data: {
					'talent_id'	: '<?php echo e($id_user); ?>',
					'start_date': $start_date,
					'end_date'	: $end_date,
				}, 
			success: function($response){
				$('#talent_start_job').html($response.activities_count.talent_start_job);
				$('#talent_completed_job').html($response.activities_count.talent_completed_job);
				$('#talent_submit_proposal').html($response.activities_count.talent_submit_proposal);
			},error: function($error){

			}
		});
	}
</script>
<?php echo $html->scripts(); ?>

<?php $__env->stopPush(); ?>
<?php $__env->startSection('inlinecss'); ?>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<?php $__env->stopSection(); ?>