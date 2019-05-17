<form class="form-horizontal" role="employer_step_two" action="{{url(sprintf('%s/hire/talent/process/three',EMPLOYER_ROLE_TYPE))}}" method="post" accept-charset="utf-8">
	<div class="login-inner-wrapper">
		{{ csrf_field() }}
		<div class="row">
			<div class="messages"></div>
		</div>
		<h4 class="form-sub-heading">{{sprintf(trans('website.W0661'),'')}}</h4>
		<div class="form-group step-three">
			<div class="">
				<div class="col-md-3">  
					<label class="control-label">{{trans('website.W0286')}}</label>
				</div>
				<div class="col-md-4">
					<label class="control-label">{{trans('website.W0660')}}</label>
				</div>
			</div>
			<div class="col-md-12">
				<ul class="filter-list-group clear-list">
					@foreach(employment_types('web_post_job') as $key => $value)
						<li>
							<div class="row">
								<div class="col-md-3">                
									<div class="checkbox radio-checkbox">                
										<input type="radio" id="employement-{{ $value['type'] }}" name="employment" value="{{ $value['type'] }}" @if(($project['employment'] == $value['type']) || (empty($project['employment']) && $value['type'] == 'hourly')) checked="checked" @endif data-request="focus-input">
										<label for="employement-{{ $value['type'] }}"><span class="check"></span> {{ strtolower($value['type_name']) }}</label>
									</div>
								</div>
								<div class="col-md-4">
									<div class="price-range">
		                                <div class="leftLabel form-control">
		                                    <input type="text" name="price[]" class="form-control text-left" @if($project['employment'] == $value['type']) value="{{ $project['price'] }}" @endif data-request="focus-input">
		                                </div>
		                            </div>
								</div>
							</div>
						</li>
					@endforeach
				</ul>
			</div>
		</div>
		<input type="hidden" name="id_project" value="{{$project['id_project']}}">
		<input type="text" class="hide" name="talent_id" value="{{$talent_id}}">
		<input type="text" class="hide" name="action" value="{{$action}}">
	</div>                  
	<div class="form-group button-group">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="row form-btn-set">
				<div class="col-md-7 col-sm-7 col-xs-6">
					@if(in_array('two',$steps))
                        <a href="{{ url(sprintf("%s/hire/talent/{$action_url}%s{$project_id_postfix}",EMPLOYER_ROLE_TYPE,$steps[count($steps)-2])) }}" class="greybutton-line">{{trans('website.W0196')}}</a>
                    @endif
				</div>
				<div class="col-md-5 col-sm-5 col-xs-6">
					<button type="button" class="button" data-request="ajax-submit" data-target='[role="employer_step_two"]'>
						{{trans('website.W0659')}}
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
@push('inlinecss')
	<style type="text/css">
        .price-range .form-control{
            padding-left: 28px;
        }
        .price-range .form-control.text-left{
        	padding:0;
        	width:100%;
        }
        .price-range .form-control::before{
        	top:4px;
            content: "{{___cache('currencies')[\Session::get('site_currency')]}}";
        }
        .leftLabel.form-control {
		    width: 200px;
		}
    </style>
@endpush