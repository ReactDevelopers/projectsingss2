
<div class="contentWrapper postjob-permanent-section">
    <div class="postjob-beforesubmit">
        <div class="container">
            <div class="right-sidebar no-padding-left no-padding-right">
                <h2 class="form-heading">{{ trans('website.W0258') }}</h2>
                <form class="form-horizontal row" role="post-job" action="{{url(sprintf('%s/_post_job',EMPLOYER_ROLE_TYPE))}}" method="post" accept-charset="utf-8">
                    <div class="col-md-7 col-sm-12 col-xs-12">
                        <div class="login-inner-wrapper">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="control-label col-md-12">{{ trans('website.W0286') }}</label>
                                <div class="col-md-12">
                                    <div class="row">
                                        @foreach(employment_types('web_post_job') as $item)
                                            <div class="col-md-3 col-xs-3">
                                                <div class="radio radio-inline">
                                                    <input data-request="show-hide" data-condition="fulltime" data-target="[name='employment']" data-true-condition=".permanent-job-section" data-false-condition=".normal-job-section" data-tag="{{ job_types_rates_postfix($item['type']) }}" type="radio" id="{{$item['type']}}" name="employment" value="{{$item['type']}}" @if($item['type'] == old('employment','hourly')) checked="checked" @endif>
                                                    <label for="{{$item['type']}}"><span class="check"></span> {{$item['type_name']}}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="common-section">
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0285') }}</label>
                                    <div class="col-md-12">
                                        <div class="custom-dropdown">
                                            <select name="title" class="form-control" data-request="tags">
                                                {!!___dropdown_options($job_titles,trans('website.W0285'),trans('website.W0285'),'',false)!!}
                                            </select>                  
                                        </div>              
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0284') }}</label>
                                    <div class="col-md-12">
                                        <textarea name="description" class="form-control" placeholder="{{ trans('website.W0284') }}"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{sprintf(trans('website.W0059'),'')}}</label>
                                    <div class="col-md-12">
                                        <div class="custom-dropdown">
                                            <select name="industry" class="form-control" data-request="option" data-url="{{ url('ajax/industry-subindustry-list') }}">
                                                {!!___dropdown_options($industries_name,sprintf(trans('website.W0059'),trans('website.W0068')), '',false)!!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ sprintf(trans('website.W0060'),"") }}</label>
                                    <div class="col-md-12">
                                        <div class="custom-dropdown">
                                            <select name="subindustry" class="form-control" data-request="option" data-url="{{ url('ajax/subindustry-skills-list') }}">
                                                {!!___dropdown_options($subindustries_name,sprintf(trans('website.W0060'),trans('website.W0068')),'',false)!!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0281') }}</label>
                                    <div class="col-md-12">
                                        <div class="custom-dropdown">
                                            <select id="skills" name="required_skills[]" data-placeholder="{{ trans('website.W0288') }}" data-request="tags" multiple="true" class="form-control">
                                                {!! ___dropdown_options(\Cache::get('skills'),trans('website.W0193')) !!}
                                            </select>
                                            <div class="js-example-tags-container"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="normal-job-section">
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0280') }}</label>
                                    <div class="col-md-12">
                                        @foreach(expertise_levels() as $item)
                                            <div class="radio radio-inline"> 
                                                <input type="radio" name="expertise" id="expertise-level-{{ $item['level'] }}" value="{{ $item['level'] }}">
                                                <label for="expertise-level-{{ $item['level'] }}"><span class="check"></span> {{ $item['level_name'] }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">
                                        {{ sprintf(trans('website.W0283'),\Cache::get('currencies')[$user['currency']]) }}
                                        <span class="employment_type_postfix"></span>
                                    </label>
                                    <div class="col-md-8">
                                        <div class="col-md-6 col-xs-6">
                                            <div class="margin-right-none margin-bottom-none form-group">
                                                <input type="text" name="price" placeholder="{{ trans('website.W0273') }}" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xs-6">
                                            <div class="margin-left-none margin-bottom-none form-group">
                                                <input type="text" name="price_max" placeholder="{{ trans('website.W0274') }}" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0278') }}</label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-4 col-xs-4 day-select">
                                                <div class="custom-dropdown">
                                                    <select name="start_date" class="form-control">
                                                        {!!___dropdown_options(___range(range(1, 31)),trans('website.W0192'),'',true) !!}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4 col-xs-4 month-select">
                                                <div class="custom-dropdown">
                                                    <select name="start_month" class="form-control">
                                                    {!!___dropdown_options(trans('website.W0048'),trans('website.W0100')) !!}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4 col-xs-4 year-select">
                                                <div class="custom-dropdown">
                                                    <select name="start_year" class="form-control">
                                                    {!! ___dropdown_options(___range(range(date('Y'),date('Y')+JOB_YEAR_RANGE)),trans('website.W0103')) !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="startdate" /> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0277') }}</label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-4 col-xs-4 day-select">
                                                <div class="custom-dropdown">
                                                    <select name="enddate_date" class="form-control">
                                                        {!!___dropdown_options(___range(range(1, 31)),trans('website.W0192'),'',true) !!}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4 col-xs-4 month-select">
                                                <div class="custom-dropdown">
                                                    <select name="enddate_month" class="form-control">
                                                    {!!___dropdown_options(trans('website.W0048'),trans('website.W0100')) !!}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4 col-xs-4 year-select">
                                                <div class="custom-dropdown">
                                                    <select name="enddate_year" class="form-control">
                                                    {!!___dropdown_options(___range(range(date('Y'),date('Y')+JOB_YEAR_RANGE)),trans('website.W0103'))!!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="enddate" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="permanent-job-section" style="display: none;">
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0282') }}</label>
                                    <div class="col-md-12">
                                        <div class="custom-dropdown">
                                            <select name="required_qualifications[]" data-placeholder="{{ trans('website.W0287') }}" class="form-control" multiple="true" data-request="tags">
                                                {!!___dropdown_options($degree_name,sprintf(trans('website.W0059'),trans('website.W0068')),'',false)!!}
                                            </select>
                                            <div class="js-example-tags-container"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0279') }}</label>
                                    <div class="col-md-12">
                                        <div class="col-md-6 col-xs-6">
                                            <div class="margin-right-none margin-bottom-none form-group">
                                                <input type="text" name="price" placeholder="{{ trans('website.W0273') }}" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xs-6">
                                            <div class="margin-left-none margin-bottom-none form-group">
                                                <input type="text" name="price_max" placeholder="{{ trans('website.W0274') }}" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0289') }}</label>
                                    <div class="col-md-12">
                                        <input type="text" name="bonus" placeholder="{{ trans('website.W0275') }}" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0290') }}</label>
                                    <div class="col-md-12">
                                        <input type="text" name="other_perks" placeholder="{{ trans('website.W0276') }}" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-12">{{ trans('website.W0291') }}</label>
                                    <div class="col-md-12">
                                        <div class="custom-dropdown">
                                            <select name="location" class="form-control" data-action="filter">
                                                {!!___dropdown_options(@\Cache::get('cities'),trans('website.W0257'),'',false)!!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group button-group">
                        <div class="col-md-7 col-sm-12 col-xs-12">
                            <div class="row form-btn-set">
                                <div class="col-md-5 col-sm-5 col-xs-6">
                                    <button type="button" class="button pull-right" value="Save" data-request="ajax-submit" data-target='[role="post-job"]'>Post Job</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
