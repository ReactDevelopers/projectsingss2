<div class="messages">
	{{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
</div>
<form class="form-horizontal" role="employer_step_two" action="{{url(sprintf('%s/hire/talent/process/five',EMPLOYER_ROLE_TYPE))}}" method="post" accept-charset="utf-8">
	<div class="login-inner-wrapper">
		{{ csrf_field() }}
		<div class="form-group">
			<div class="col-md-12">
				<h4 class="form-sub-heading">{{ trans('website.W0281') }}</h4>
				<div class="custom-dropdown">
					<select name="subindustry[]" style="max-width: 400px;"  class="form-control" data-request="tags-true" multiple="true"  data-placeholder="{{ trans('website.W0799') }}">
						{!!___dropdown_options(array_combine(array_column($subindustries_name,'name'), array_column($subindustries_name,'name')),sprintf(trans('website.W0060'),trans('website.W0068')),array_column(array_column($project['subindustries'], 'subindustries'), 'name'),false)!!}
					</select>
					<div class="js-example-tags-container white-tags"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="login-inner-wrapper">
		<h4 class="form-sub-heading">{{trans('website.W0280')}}</h4>
		<div class="form-group">
			<div class="">
				<ul class="filter-list-group">
					@foreach(expertise_levels() as $key=> $value)
						<li class="col-md-4">
							<div class="checkbox radio-checkbox">                
								<input type="radio" id="expertise-{{ $value['level'] }}" name="expertise" value="{{ $value['level'] }}" data-action="filter" @if(($project['expertise'] == $value['level']) || (empty($project['expertise']) && $value['level'] == 'novice')) checked="checked" @endif>
								<label for="expertise-{{ $value['level'] }}"><span class="check"></span>{{ $value['level_name'] }} {{ $value['level_exp'] }}</label>
							</div>
						</li>
					@endforeach
				</ul>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<label class="control-label">{{trans('website.W0658')}}</label>
				<input type="text" name="other_perks" maxlength="4" placeholder="{{ trans('website.W0074') }}" value="{{$project['other_perks']}}" style="max-width: 400px;" class="form-control">
				
			</div>
		</div>
		<input type="hidden" name="id_project" value="{{$project['id_project']}}">
		<input type="hidden" name="industry_id" value="{{current($project['industries'])['industries']['id_industry']}}">
		<input type="text" class="hide" name="talent_id" value="{{$talent_id}}">
		<input type="text" class="hide" name="action" value="{{$action}}">
		<div class="clearfix"></div>
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
						{{trans('website.W0229')}}
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
