@extends('layouts.backend.dashboard')
@section('content')
	<section class="content">
		<div class="row">
        	<div class="col-md-12 margin-bottom">
           		<span class="pull-right">
                	<a href="{{url(sprintf('%s/plan/features-list/features/add',ADMIN_FOLDER))}}" class="btn btn-app" style="height: 40px; padding: 10px; margin: 0px;">
                    	<i class="fa fa-plus-circle pull-left"></i> Add New
                	</a>
            	</span>
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
@endsection
@section('requirejs')    
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}

    
@endsection
