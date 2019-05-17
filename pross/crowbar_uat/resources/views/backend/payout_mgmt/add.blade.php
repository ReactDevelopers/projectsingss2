@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/payout/management/add',ADMIN_FOLDER)) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}

                        <div class="panel-body">
	                        <div class="form-group">
	                        	<label for="name">Country</label>
								<div>
					                <select class="form-control" style="max-width: 400px;" name="country" placeholder="Country">
					                </select>
					            </div>
							</div>
						</div>

						<div class="add-payout-table">
					        <table style="width:100%;">
							  	<tr>
							    	<th>Profession:</th>
							    	<th>Is Registered:</th>
							    	<th>Accept Escrow:</th>
							    	<th>Pay Commission(in %):</th> 
							    	<th>Ask for Identification Number:</th>
							  	</tr>
				            	@foreach(___cache('industries_name') as $key => $value)
				            	<tr>
				            		<th>{{$value}}</th>
				            		<th>
				            			<div class="form-group">
					            			<div>
												<input type="radio" name="is_registered_{{$key}}" value="yes" checked="checked"> Yes
		                            			<input type="radio" name="is_registered_{{$key}}" value="no"> No
		                            		</div>
		                        		</div>
				            		</th>
							    	<th>
							    		<div class="form-group">
					            			<div>
												<label>Registered</label>
												<input type="radio" name="accept_escrow_{{$key}}" value="yes" checked="checked"> Yes
		                            			<input type="radio" name="accept_escrow_{{$key}}" value="no"> No
		                            		</div>
		                            		<div>
		                            			<label>Non Registered</label>
												<input type="radio" name="non_reg_accept_escrow_{{$key}}" value="yes" checked="checked"> Yes
		                            			<input type="radio" name="non_reg_accept_escrow_{{$key}}" value="no"> No
		                            		</div>
		                        		</div>
							    	</th>
							    	<th>
							    		<div class="form-group">
					            			<div>
		                            			<input type="text" class="pay_commision_val" name="pay_commision_percent_{{$key}}" value="0.00" style="width:65px" placeholder="Enter %">
		                            		</div>
		                        		</div>
							    	</th> 
							    	<th>
							    		<div class="form-group">
					            			<div>
												<input type="radio" name="identification_no_{{$key}}" value="yes" checked="checked"> Yes
		                            			<input type="radio" name="identification_no_{{$key}}" value="no"> No
		                            		</div>
		                        		</div>
							    	</th>
							    </tr>
				            	@endforeach
							</table>
						</div>

                        <div class="panel-footer">
                            <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('inlinescript')
<script type="text/javascript">
	setTimeout(function(){
	    $('[name="country"]').select2({
            formatLoadMore   : function() {return 'Loading more...'},
            ajax: {
                url: base_url+'/countries',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                }
            },
            placeholder: function(){
                $(this).find('option[value!=""]:first').html();
            }
        }).on('change',function(){
        });
	},1000);

	$(".pay_commision_val").bind('keypress', function(e) {    
        var k = e.which; 
        var ok = k >= 48 && k <= 57 || //0-9 
            k == 46 //.
        if (!ok){
            e.preventDefault();
        }
    });
</script>
@endpush
@section('inlinecss')
	<style type="text/css">
		.select2-results__option.select2-results__option--load-more{
			display: none;    
		}
		.add-payout-table{
			padding:15px;
		}
	</style>
@endsection