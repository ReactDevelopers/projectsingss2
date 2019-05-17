
@extends('layouts.master')

@section('content-header')
<h1>
    
</h1>
@stop

@section('content')
<div class="row">
<div class="form-group">
	<div class="col-xs-12">
	    <div class="box box-primary">
	        <div class="box-header">
	        </div>
	        <!-- /.box-header -->
	        <div class="box-body">
	            <table class="data-table table table-bordered table-hover">
	                <thead>
	                    <tr>
	                        <th>{!! trans('Date') !!}</th>
	                          <th>{!! trans('Amount') !!} (S$)</th>
	                          <th>{!! trans('Point') !!}</th> 
	                    </tr>
	                </thead>
	                <tbody>
	                @if(count($credits) > 0)
	                @foreach($credits as $key => $item)
	                    <tr>
	                      <td>{{ $item->created_at }}</td>
	                      <td>
                            {{ $item->amount }}
                        </td>
	                      <td>
	                      	  {{ $item->point }}
	                      </td>
	                      
	                    </tr>
	                @endforeach
	                @endif
	                </tbody>
	            </table>
	        <!-- /.box-body -->
	        </div>
          <div class="box-footer">
            <a href="{{ route('admin.credit.credit.index') }}" class="btn btn-default pull-right">{{ trans('Back') }}</a>
          </div>
	    <!-- /.box -->
		</div>
	<!-- /.col (MAIN) -->
	</div>
</div>
</div>

@stop

@section('scripts')
<?php $locale = App::getLocale(); ?>
<script type="text/javascript">
    $( document ).ready(function() {

        $(document).keypressAction({
            actions: [
                { key: 'c', route: "<?= route('admin.user.user.create') ?>" }
            ]
        });
    });
    $(function () {
        $('.data-table').dataTable({
            "paginate": false,
            "lengthChange": false,
            "filter": false,
            "sort": false,
            "info": false,
            "autoWidth": false,
            "order": [[ 0, "desc" ]],
            "language": {
                "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
            }
        });
    });
</script>
@stop






