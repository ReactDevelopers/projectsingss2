@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/payout/management/update/%s',ADMIN_FOLDER,$country_id)) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}
                        <div class="panel-body">
	                        <div class="form-group">
	                        	<label for="name">Country: {{$country_name}}</label>
					            <input type="hidden" name="country" value="{{$country_name}}">
							</div>
						</div>

						<div class="add-payout-table">
					        <table style="width:100%;">
							  	<tr>
							    	<th>Profession:</th>
							    	<th>Registration Exists?:</th>
							    	<th>Accept Escrow:</th>
							    	<th>Pay Commission(in %):</th> 
							    	<th>Ask for Identification Number:</th>
							  	</tr>
				            	@foreach(___cache('industries_name') as $key => $value)
					            	<tr>
					            		<input type="hidden" name="payout_id_{{$key}}" value="{{$payout_det[$key]['id']}}">
					            		<th>{{$value}}</th>
					            		<th>
					            			<div class="form-group">
						            			<div>
													<input type="radio" name="is_registered_{{$key}}" value="yes" @if($payout_det[$key]['is_registered_show']=='yes') checked="checked" @endif> Yes
			                            			<input type="radio" name="is_registered_{{$key}}" value="no" @if($payout_det[$key]['is_registered_show']=='no') checked="checked" @endif> No
			                            		</div>
			                        		</div>
					            		</th>
								    	<th>
								    		<div class="form-group">
						            			<div>
						            				<label>Registered</label>
													<input type="radio" name="accept_escrow_{{$key}}" value="yes" 
													@if($payout_det[$key]['accept_escrow']=='yes') checked="checked" @endif> Yes
			                            			<input type="radio" name="accept_escrow_{{$key}}" value="no" @if($payout_det[$key]['accept_escrow']=='no') checked="checked" @endif> No
			                            		</div>
			                            		<div>
			                            			<label>Non Registered</label>
													<input type="radio" name="non_reg_accept_escrow_{{$key}}" value="yes" @if($payout_det[$key]['non_reg_accept_escrow']=='yes') checked="checked" @endif> Yes
			                            			<input type="radio" name="non_reg_accept_escrow_{{$key}}" value="no" @if($payout_det[$key]['non_reg_accept_escrow']=='no') checked="checked" @endif> No
			                            		</div>
			                        		</div>
								    	</th>
								    	<th>
								    		<div class="form-group">
						            			<div>
			                            			<input type="text" class="pay_commision_val" name="pay_commision_percent_{{$key}}" value="{{$payout_det[$key]['pay_commision_percent']}}" style="width:65px" placeholder="Enter %">
			                            		</div>
			                        		</div>
								    	</th> 
								    	<th>
								    		<div class="form-group">
						            			<div>
													<input type="radio" name="identification_no_{{$key}}" value="yes" @if($payout_det[$key]['identification_number']=='yes') checked="checked" @endif> Yes
			                            			<input type="radio" name="identification_no_{{$key}}" value="no" @if($payout_det[$key]['identification_number']=='no') checked="checked" @endif> No
			                            		</div>
			                        		</div>
								    	</th>
								    </tr>
				            	@endforeach
							</table>
						</div>

                        <div class="panel-footer">
                            <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Update</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('inlinescript')
<script type="text/javascript">
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