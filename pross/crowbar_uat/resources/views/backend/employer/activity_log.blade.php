<section class="content">
	<div class="row">
		<div class="col-md-6 pull-right">
			<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
			    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
			    <span></span> <b class="caret"></b>
			</div>
		</div>
		<div class="col-md-2 pull-right">
			<button class="pull-right btn btn-info" id="export2" title="Export"><i class="fa fa-download" aria-hidden="true"></i></button>
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="col-md-4">
			<div class="small-box bg-yellow">
                <div class="inner text-center">
                    <h3 id="employer_post_job">0</h3>
                    <p>Job(s) Posted</p>
                </div>
            </div>
		</div>
		<div class="col-md-4">
			<div class="small-box bg-yellow">
                <div class="inner text-center">
                    <h3 id="employer_close_job">0</h3>
                    <p>Job(s) Closed</p>
                </div>
            </div>
		</div>
		<div class="col-md-4">
			<div class="small-box bg-yellow">
                <div class="inner text-center">
                    <h3 id="raise_dispute">0</h3>
                    <p>Dispute(s) Raised</p>
                </div>
            </div>
		</div>
	</div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                    @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        {!! $html->table(); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('inlinescript')
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">

	$('#export2').click(function(){
		var params = window.LaravelDataTables['dataTableBuilder'].ajax.params();
		params.download = 'csv';
		params.user_id = '{{$id_user}}';
		var paramsQuery = $.param(params);

		window.location.href= '{!!url('administrator/users/employer/edit/activity_log')!!}'+'?'+paramsQuery;			
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
    var endDate = end.format('YYYY-MM-D');

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
	function callAjax(startDate,endDate){
		$.ajax({
            url: '{{url('administrator/activity-log/employer/countActivity')}}', 
            type: 'post', 
            data: {
                'employer_id' : '{{$id_user}}',
                'start_date'  : startDate,
                'end_date'	  : endDate,
            }, 
            success: function($response){ 
            	$('#employer_post_job').html($response.activities_count.employer_post_job);
            	$('#employer_close_job').html($response.activities_count.employer_close_job);
            	$('#raise_dispute').html($response.activities_count.raise_dispute);
			},error: function($error){
                  
            }
        });
	}
</script>
{!! $html->scripts() !!}
@endpush
@section('inlinecss')
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
@endsection