<section class="added-membes-section">
	<div class="container-fluid">
		{{-- <div class="invite-heading">
			<h3>Added members</h3>
		</div> --}}

		<div class="currentJobOne-section accepted-proposals-listing">
	        <div class="datatable-listing no-padding-cell shift-up-5px">
	            {!! $html->table(); !!}
	        </div>
	    </div>
		<div class="row">
			<div class="col-md-12 col-sm-6 col-xs-12">
				<a href="{{url($backUrl)}}" class="btn btn-default">Back</a>
			</div>
		</div>

	</div>
	 <div class="modal fade upload-modal-box add-payment-cards" id="hire-me" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
</section>
	
@push('inlinescript')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}

    <script type="text/javascript">
        $(function(){
            $('#dataTableBuilder_wrapper .row:first').remove();
    	});
    </script>
@endpush


