<div>
    <ul class="user-profile-links">
        @if($project->status == 'active')
            <li class="resp-tab-item">
                <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}">
                    {{trans('website.W0678')}}
                </a>
            </li>
            <li class="resp-tab-item">
                <a href="{{url('talent/find-jobs/reviews?job_id='.___encrypt($project->id_project))}}">
                    {{trans('website.W0679')}}
                </a>
            </li>
            <li>
                <a href="{{url('talent/find-jobs/about?job_id='.___encrypt($project->id_project))}}">
                    {{trans('website.W0680')}}
                </a>
            </li>
        @endif
        <li class="active">
            <a href="{{url('talent/find-jobs/proposal?job_id='.___encrypt($project->id_project))}}">
                {{ empty($project->proposal)? trans('website.W0683') : trans('website.W0689') }}
            </a>
        </li>
    </ul>
    <div class="clearfix"></div>
    <div class="job-detail-final">
        <div class="content-box find-job-listing clearfix">
            <div class="view-information no-padding">
                <h2>{{ trans('website.W0689') }} </h2>
            </div>
            @if(empty($project->proposal) || (\Request::get('action') == 'edit' && $project->proposal->status == 'applied'))
                <div>
                    <div class="messages"></div>
                    <div class="">
                        <form class="form-horizontal" action="{{ url(sprintf('%s/proposals/submit?project_id=%s',TALENT_ROLE_TYPE,___encrypt($project->id_project))) }}" role="submit_proposal" method="post" accept-charset="utf-8">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-11 col-sm-11 col-xs-12"> 
                                    <div class="form-group">
                                        <label class="control-label-small col-md-12">
                                            {!!sprintf(trans('job.J0021'), \Cache::get('currencies')[$user['currency']])!!}
                                        </label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="quoted_price" placeholder="{{trans('website.W0363')}}" class="form-control" data-request="numeric" @if(!empty($project->proposal->quoted_price)) value="{{$project->proposal->quoted_price}}" @endif maxlength="9" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-11 col-sm-11 col-xs-12"> 
                                    <div class="form-group">
                                        <label class="control-label-small col-md-12">
                                            {!!trans('website.coupon_code')!!}
                                        </label>
                                        <div class="col-md-8">
                                            @if(!empty($coupon_detail))
                                            <input type="text" class="form-control" name="coupon_code" value="{{$coupon_detail['code']}}" placeholder="{{trans('website.coupon_code')}}" class="form-control" data-request="" />
                                            @else
                                            <input type="text" class="form-control" name="coupon_code" placeholder="{{trans('website.coupon_code')}}" class="form-control" data-request="" />
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-11 col-sm-11 col-xs-12"> 
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-4 col-md-6">
                                            <div class="form-group m-b-n">
                                                <label class="control-label-small col-md-12">
                                                    Submission Fee (in {{\Cache::get('currencies')[$user['currency']]}})
                                                </label>
                                                <label class="control-label-small col-md-12">
                                                    {{$submission_fee_abs}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-11 col-sm-11 col-xs-12"> 
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-4 col-md-12">
                                            <div class="form-group m-b-n">
                                                <label class="control-label-small col-md-12">
                                                    Amount you will receive (in {{\Cache::get('currencies')[$user['currency']]}})
                                                </label>
                                                <label class="control-label-small col-md-12" id="amount_you_receive"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($project->employment === 'hourly')
                                    <div class="col-md-11 col-sm-11 col-xs-12"> 
                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-4 col-md-4">
                                                <div class="form-group m-b-n">
                                                    <label class="control-label-small col-md-12">
                                                        {{ trans('website.W0843') }}
                                                    </label>
                                                    <div class="col-md-12">
                                                        <div class="custom-dropdown">
                                                            <select name="from_time_hour" class="form-control">
                                                                {!! ___dropdown_options2(___range(range(01, 12)),trans('website.W0187'),((!empty($project->proposal->from_time))?date('h',strtotime($project->proposal->from_time)):''),true) !!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4 col-md-4">
                                                <div class="form-group m-b-n">
                                                    <label class="control-label-small col-md-12">
                                                        &nbsp;
                                                    </label>
                                                    <div class="col-md-12">
                                                        <div class="custom-dropdown">
                                                            <select name="from_time_minute" class="form-control">
                                                                {!! ___dropdown_options2(___range(['00','15','30','45']),trans('website.W0188'),((!empty($project->proposal->from_time))?date('i',strtotime($project->proposal->from_time)):''),true) !!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  
                                            <div class="col-xs-12 col-sm-4 col-md-4">
                                                <div class="form-group m-b-n">
                                                    <label class="control-label-small col-md-12">
                                                        &nbsp;
                                                    </label>
                                                    <div class="col-md-12">
                                                        <div class="btn-group meridian-btn-group" data-toggle="buttons">
                                                            <label class="btn btn-default 
                                                                @if(
                                                                !empty($project->proposal->from_time) 
                                                                    && 
                                                                date('a',strtotime($project->proposal->from_time)) == 'am') active @endif">
                                                                <input type="radio" value="AM" name="from_time_meridian" @if(!empty($project->proposal->from_time) && date('a',strtotime($project->proposal->from_time)) == 'am') checked="checked" @endif id="from_time_am" autocomplete="off"> {{trans('website.W0189')}}
                                                            </label>
                                                            <label class="btn btn-default 
                                                                @if(
                                                                !empty($project->proposal->from_time) 
                                                                    && 
                                                                date('a',strtotime($project->proposal->from_time)) == 'am') '' @else active @endif">
                                                                <input type="radio" value="PM" name="from_time_meridian" @if(!empty($project->proposal->from_time) && date('a',strtotime($project->proposal->from_time)) == 'am') '' @else checked="checked" @endif id="from_time_pm" autocomplete="off"> {{trans('website.W0190')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>       
                                            <div class="clearfix"></div> 
                                            <div class="col-md-12">
                                                <input type="hidden" name="from_time" />
                                            </div>   
                                        </div>
                                    </div>
                                    <div class="col-md-11 col-sm-11 col-xs-12"> 
                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-4 col-md-4">
                                                <div class="form-group m-b-n">
                                                    <label class="control-label-small col-md-12">
                                                        {{ trans('website.W0844') }}
                                                    </label>
                                                    <div class="col-md-12">
                                                        <div class="custom-dropdown">
                                                            <select name="to_time_hour" class="form-control">
                                                                {!! ___dropdown_options2(___range(range(01, 12)),trans('website.W0187'),((!empty($project->proposal->to_time))?date('h',strtotime($project->proposal->to_time)):''),true) !!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4 col-md-4">
                                                <div class="form-group m-b-n">
                                                    <label class="control-label-small col-md-12">
                                                        &nbsp;
                                                    </label>
                                                    <div class="col-md-12">
                                                        <div class="custom-dropdown">
                                                            <select name="to_time_minute" class="form-control">
                                                                {!! ___dropdown_options2(___range(['00','15','30','45']),trans('website.W0188'),((!empty($project->proposal->to_time))?date('i',strtotime($project->proposal->to_time)):''),true) !!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  
                                            <div class="col-xs-12 col-sm-4 col-md-4">
                                                <div class="form-group m-b-n">
                                                    <label class="control-label-small col-md-12">
                                                        &nbsp;
                                                    </label>
                                                    <div class="col-md-12">
                                                        <div class="btn-group meridian-btn-group" data-toggle="buttons">
                                                            <label class="btn btn-default @if(!empty($project->proposal->to_time) && date('a',strtotime($project->proposal->to_time)) == 'am') active @endif">
                                                                <input type="radio" value="AM" name="to_time_meridian" @if(!empty($project->proposal->to_time) && date('a',strtotime($project->proposal->to_time)) == 'am') checked ="checked" @endif id="to_time_am" autocomplete="off"> {{trans('website.W0189')}}
                                                            </label>
                                                            <label class="btn btn-default @if(!empty($project->proposal->to_time) && date('a',strtotime($project->proposal->to_time)) == 'am') '' @else active  @endif">
                                                                <input type="radio" value="PM" name="to_time_meridian" @if(!empty($project->proposal->to_time) && date('a',strtotime($project->proposal->to_time)) == 'am') '' @else checked ="checked" @endif id="to_time_pm" autocomplete="off"> {{trans('website.W0190')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  
                                            <div class="clearfix"></div> 
                                            <div class="col-md-12">
                                                <input type="hidden" name="to_time" />         
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-11 col-sm-11 col-xs-12">
                                    <input type="text" class="hide"/>
                                    <div class="form-group">
                                        <label class="control-label-small col-md-12">
                                            {!!trans('job.J0024')!!}
                                            <span>{{trans('job.J0025')}}</span>
                                        </label>
                                        <div class="col-md-12">
                                            <textarea style="height:auto;" type="text" name="comments" rows="4" placeholder="{{trans('job.J0031')}}" class="form-control">@if(!empty($project->proposal->quoted_price)){{$project->proposal->comments}}@endif</textarea>
                                        </div>
                                    </div>
                                    @if(!empty($project->proposal->file))
                                        <input type="hidden" name="proposal_docs" value="{{$project->proposal->file->id_file}}">
                                    @else
                                        <input type="hidden" name="proposal_docs" >
                                    @endif
                                    @if(!empty($project->proposal->id_proposal))
                                        <input type="hidden" value="{{$project->proposal->id_proposal}}" name="proposal_id" >
                                    @endif
                                    <button class="hide" id="proposal-form" type="button" data-request="confirm-ajax-submit" data-target='[role="submit_proposal"]' name="submit" class="button" value="Submit" data-title="{{trans('website.W0551')}}" data-ask="{{trans('website.submit_proposal')}}">
                                        {{trans('job.J0029')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="col-md-11 col-sm-11 col-xs-12">
                            <div class="row">
                                <form class="form-horizontal" action="{{ url(sprintf('%s/proposals/submit/document?project_id=%s',TALENT_ROLE_TYPE,$project->id_project)) }}" role="doc-submit" method="post" accept-charset="utf-8">
                                    <div class="form-group attachment-group">
                                        <label class="control-label-small col-md-12">{{trans('website.W0112')}}</label>
                                        <div class="col-md-12">
                                            <div class="upload-box">
                                                @if(!empty($project->proposal->file))
                                                    @includeIf('talent.jobdetail.includes.attachment',['file' => json_decode(json_encode($project->proposal->file),true)])
                                                @endif
                                            </div>
                                            <div class="single-remove" @if(!empty($project->proposal->file))style="display: none;"@endif>
                                                <div class="fileUpload upload-docx"><span>{{trans('website.W0519')}}</span>
                                                    <input type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-after-upload=".single-remove" data-target='[role="doc-submit"]' data-single="true"/>
                                                </div>
                                                <span class="upload-hint">{{trans('job.J0030')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @if(!empty($companydata))
                    <div class="company-name-wrapper">
                        <div class="info-row row">
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <label class="company-label">Company Name</label>
                                </div>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <span class="company-name-info">{{ $companydata->company_name }}</span>
                                </div>
                        </div>
                        <div class="info-row row">
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <label class="company-label">Company Website</label>
                                </div>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <span class="company-name-info">{{$companydata->company_website}}</span>
                                </div>
                        </div>
                        <div class="info-row row">
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <label class="company-label">About the Company</label>
                                </div>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <span class="company-name-info">{{$companydata->company_biography }}</span>
                                </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                @endif
                <div class="col-md-7">
                    <div class="content-box-description">
                        {{-- <div class="view-information no-padding">
                            <h2>{{ trans('website.W0689') }}</h2>
                        </div> --}}
                        <div class="view-information no-padding">
                            <h2>{{ trans('website.W0989') }} </h2>
                        </div>
                        @if(!empty($project->proposal->quoted_price))
                            <div class="item-list">
                                <span class="item-heading clearfix">{{trans('website.W0363')}}</span>
                                <span class="item-description">
                                    <span class="small-tags">{{$project->proposal->price_unit.___format($project->proposal->quoted_price,true,false)}}</span>
                                </span>
                            </div>
                        @endif

                        @if($project->employment == 'hourly')
                            <div class="item-list">
                                <span class="item-heading clearfix">{{trans('website.W0757')}}</span>
                                <span class="item-description">
                                    @if(!empty($project->proposal->working_hours))
                                        <span class="small-tags">{{substr($project->proposal->working_hours, 0, -3)}} {{trans('website.W0759')}}</span>
                                    @else
                                        <span class="small-tags">{{'00:00'}} {{trans('website.W0759')}}</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($coupon_detail))
                    <div class="content-box-description">
                        <div class="item-list">
                            <span class="item-heading clearfix">{{trans('website.applied_coupon_code')}}</span>
                            <span class="item-description">
                                <span class="small-tags">{{$coupon_detail['code']}}</span>
                            </span>
                        </div>
                        
                    </div>
                    @endif

                    <div class="m-b-20px">
                        <span class="item-heading clearfix">{{trans('website.W0664')}}</span>
                        @if(!empty($project->proposal->comments))
                            {!!___e(nl2br($project->proposal->comments))!!}
                        @else
                            {{N_A}}
                        @endif
                    </div>
                    
                    <div class="m-b-10px">
                        <span class="item-heading clearfix">{{trans('website.W0691')}}</span>
                        @if(!empty($project->proposal->file))
                            @includeIf('talent.jobdetail.includes.attachment',['file' => json_decode(json_encode($project->proposal->file),true)])
                        @else
                            {{N_A}}
                        @endif
                    </div>

                    <span class="review-time">
                        {{trans('website.W0690')}} {{___d($project->proposal->created)}}
                    </span>
                </div>
            @endif
            {{-- <div class="col-md-5 ">
                <div class="employer-detail-box">
                    <h2 class="heading-sm m-b-20px">{{trans('website.W0652')}}</h2>
                    <div class="form-group">
                        <h2 class="small-heading">{{trans('website.W0094')}}</h2>
                        <span>{{$project->title}}</span>
                    </div>
                    <div class="form-group">
                        <h2 class="small-heading">{{trans('website.W0846')}}</h2>
                        <span>
                            {{___format($project->price,true,true)}} / 
                            @if($project->employment == 'fixed')
                                {{$project->employment}}
                            @else
                                {{job_types_rates_postfix($project->employment)}}
                            @endif
                        </span>
                    </div>
                    <hr>
                    <div class="form-group">
                        <h2 class="small-heading">{{trans('website.W0682')}}</h2>
                        <span>
                            @if(!empty(strtotime($project->startdate) && strtotime($project->enddate)))
                                {{___date_difference($project->startdate, $project->enddate)}}
                            @endif
                        </span>
                    </div>
                    @if($project->employment == 'hourly')
                        <div class="form-group">
                            <h2 class="small-heading">{{trans('website.W0793')}}</h2>
                            <span>
                                @if(!empty(strtotime($project->expected_hour)))
                                    {{___hours($project->expected_hour)}}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div> --}}
            <div class="col-md-5">
                <div class="employer-detail-box">
                    <h2 class="heading-sm m-b-20px">{{trans('website.W0652')}}</h2>
                    <div class="form-group">
                        <h2 class="small-heading">{{trans('website.W0094')}}</h2>
                        <span>{{$project->title}}</span>
                    </div>
                    <div class="form-group price-list">
                        <h2 class="small-heading">{{trans('website.W0846')}}</h2>
                        <span>
                            {{___format($project->price,true,true)}} / 
                            @if($project->employment == 'fixed')
                                {{$project->employment}}
                            @else
                                {{job_types_rates_postfix($project->employment)}}
                            @endif
                        </span>
                    </div>  
                    <div class="form-group timeline">
                        <h2 class="small-heading">{{trans('website.W0682')}}</h2>
                        <span>
                            @if(!empty(strtotime($project->startdate) && strtotime($project->enddate)))
                                {{___date_difference($project->startdate, $project->enddate)}}
                            @endif
                        </span>
                    </div>
                    @if($project->employment == 'hourly')
                        <div class="form-group">
                            <h2 class="small-heading">{{trans('website.W0793')}}</h2>
                            <span>
                                @if(!empty(strtotime($project->expected_hour)))
                                    {{___hours($project->expected_hour)}}
                                @endif
                            </span>
                        </div>
                    @endif
                    <div class="form-group cancelation">
                        <h2 class="small-heading">{{trans('website.W0930')}}</h2>
                        @php
                            $commission = ___cache('configuration')['cancellation_commission'];
                            $commission_type = ___cache('configuration')['cancellation_commission_type'];

                            if($commission_type == 'per'){
                                $calculated_commission=___format(round(((($project->price*$commission)/100)),2)); 
                            }else{
                                $calculated_commission = ___format(round(($commission),2));
                            }

                            $refundable_amount = $project->price - $calculated_commission;
                        @endphp
                        <span>
                        {{___format($refundable_amount,true,true)}}
                        </span>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        @if(empty($project->proposal))
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        <div class="col-md-7 col-sm-7 col-xs-6">
                            <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}" class="greybutton-line">{{trans('job.J0028')}}</a>
                        </div>
                        <div class="col-md-5 col-sm-5 col-xs-6">
                            <button id="doc-button" type="button" data-trigger="proposal-clicked" data-request="trigger-proposal" data-target="#proposal-form" data-copy-source='[name="documents[]"]' data-copy-destination='[name="proposal_docs"]' class="button" value="Submit">
                                    {{trans('website.W0013')}}
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        @elseif((\Request::get('action') == 'edit' && $project->proposal->status == 'applied'))
            <div class="row form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        <div class="col-md-7 col-sm-7 col-xs-6">
                            <a href="{{url('talent/find-jobs/details?job_id='.___encrypt($project->id_project))}}" class="greybutton-line">{{trans('job.J0028')}}</a>
                        </div>
                        <div class="col-md-5 col-sm-5 col-xs-6">
                            <button id="doc-button" type="button" data-trigger="proposal-clicked" data-request="trigger-proposal" data-target="#proposal-form" data-copy-source='[name="documents[]"]' data-copy-destination='[name="proposal_docs"]' class="button" value="Submit">
                                    {{trans('website.W0013')}}
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Proposal button -->
        @if($project->project_status != 'closed' && \Request::get('action') !== 'edit')
            @if(!empty($project->proposal) && $project->project_status != 'closed' && $project->awarded === DEFAULT_NO_VALUE && $project->status != 'trashed')
                <a href="{{ url(sprintf('%s/find-jobs/proposal?job_id=%s&action=edit',TALENT_ROLE_TYPE,___encrypt($project->id_project))) }}" class="btn btn-secondary bottom-margin-10px pull-right" title="{{trans('website.W0810')}}">{{trans('website.W0810')}}</a>
            @endif
        @endif

    </div>
</div>

@if(!empty($project->proposal))
    @push('inlinecss')
        @if((\Request::get('action') !== 'edit' && $project->proposal->status !== 'applied'))
            <style>.education-box .edit-icon, .work-experience-box .edit-icon, [data-request="delete"]{display: none!important;}.meridian-btn-group{max-width: 100%;margin-left: 0;}</style>
        @endif
    @endpush
@endif
@push('inlinecss')
    <style type="text/css">.meridian-btn-group{max-width: 100%;margin-left: 0;}.new-upload .uploaded-docx{max-width: 100%;}</style>
@endpush

@push('inlinescript')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    {{-- <script type="text/javascript" src="{{asset('js/bootstrap-timepicker.min.js')}}"></script> --}}
    <script type="text/javascript">
        $(function () {
            $('.bootstrappicker').datetimepicker({
                format: 'LT'
            });
        });

        $(document).ready(function(){
            var quoted_price = $("[name='quoted_price']").val();
            if(quoted_price != ''){
                var price = quoted_price - '{{$submission_fee_abs}}';
                $('#amount_you_receive').html(price);
            }
        });

        $("[name='quoted_price']").keyup(function(){
            if($(this).val() != ''){
                var price = $(this).val() - '{{$submission_fee_abs}}';
                $('#amount_you_receive').html(price);
            }else{
                $('#amount_you_receive').html('');
            }

        });

        /* To stop browser from reloading if user has entered data and not submitted yet. */

        var check_proposal_click = '';
        $(document).on('click','[data-trigger="proposal-clicked"]', function(){
            check_proposal_click = 'yes';
        });

        $(window).bind('beforeunload', function(){
            var quoted_price = $("[name='quoted_price']").val();
            if(typeof quoted_price != 'undefined' && quoted_price != '' && check_proposal_click == '' ){
                return "You're leaving?";
            }
        });

    </script>
@endpush