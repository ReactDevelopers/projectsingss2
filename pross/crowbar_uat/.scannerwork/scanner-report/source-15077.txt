@section('content')
    @include('talent.profile.includes.header')
    <!-- Main Content -->
    <div class="contentWrapper">
        <div class="afterlogin-section viewProfile">
            <div class="container">
                <div class="row mainContentWrapper">
                    @include('talent.profile.includes.sidebar')
                    <div class="col-md-8 col-sm-8 right-sidebar availability-sidebar">
                        @include('talent.profile.includes.navigationBar')
                        
                        <form role="talent_step_four" class="form-horizontal calendar-form" action="{{url(sprintf('%s/_step_four_set_availability',TALENT_ROLE_TYPE))}}" method="post" accept-charset="utf-8">
                            <div class="login-inner-wrapper">
                                <h2 class="form-heading">{{trans('website.W0173')}}</h2>
                                <div class="message"></div>
                                <!-- ALL AVAILABILITY -->
                                <div class="availability-box">
                                    {!! ___availability_list($user['availability']) !!}
                                </div>
                                
                                <input type="hidden" name="id_availability">
                                <div class="calendar-box form-group">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 col-xs-12">
                                            <div class="selected-date-box">
                                                <span class="start-date">{{trans('website.W0098')}}</span>
                                                <div class="selected-date">
                                                    {{ ___d(date('Y-m-d',strtotime($selected_date))) }}
                                                </div>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-8 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0175')}}</label>
                                                <div class="col-md-9">
                                                    <div class="custom-dropdown">
                                                        <select name="year" class="form-control" data-request="calendar" data-url="{{ url('ajax/validate-calendar') }}">
                                                            {!! ___dropdown_options(___range(range(date('Y'),date('Y')+5)),trans('website.W0103'),date('Y',strtotime($selected_date)),false) !!}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-12">{{trans('website.W0176')}}</label>
                                                <div class="col-md-12">
                                                    <div class="btn-group radio-btn-group month-btn-group" data-toggle="buttons">
                                                        @foreach(array_keys(months()) as $item)<label class="btn @if(date('m',strtotime($item)) == date('m',strtotime($selected_date))) active @endif"><input type="radio" data-request="calendar" data-url="{{ url('ajax/validate-calendar') }}" value="{{ date('m',strtotime($item)) }}" name="month" id="month-{{ $item }}" @if(date('m',strtotime($item)) == date('m',strtotime($selected_date))) checked="checked" @endif autocomplete="off"><span class="input-value">{{ $item }}</span></label>@endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-12">{{trans('website.W0177')}}</label>
                                                <div class="col-md-12">
                                                    <div class="date-section btn-group radio-btn-group day-btn-group" data-toggle="buttons">
                                                        @foreach(range(1,date('t')) as $item)<label class="btn @if(sprintf('%\'.02d',$item) == date('d',strtotime($selected_date))) active @endif"><input type="radio" name="day" data-request="calendar" data-url="{{ url('ajax/validate-calendar') }}" value="{{ sprintf('%\'.02d',$item) }}" id="day-{{ sprintf('%\'.02d',$item) }}" @if(sprintf('%\'.02d',$item) == date('d',strtotime($selected_date))) checked="checked" @endif autocomplete="off"><span class="input-value">{{$item}}</span></label>@endforeach
                                                    </div>
                                                </div>
                                            </div>    
                                            <input type="hidden" name="availability_date">
                                            <div class="availability-sub-details">                              
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{trans('website.W0178')}}</label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-4 col-xs-4 hours-select">
                                                                <div class="custom-dropdown">
                                                                    <select name="from_time_hour" class="form-control">
                                                                        {!! ___dropdown_options(___range(range(00, 12)),trans('website.W0187'),'13',true) !!}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 minutes-select">
                                                                <div class="custom-dropdown">
                                                                    <select name="from_time_minute" class="form-control">
                                                                        {!! ___dropdown_options(___range(range(00, 59,5)),trans('website.W0188'),'60',true) !!}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 meridian-select">
                                                                <div class="btn-group meridian-btn-group" data-toggle="buttons">
                                                                    <label class="btn btn-default @if(date('a',strtotime($selected_date)) == 'am') active @endif">
                                                                        <input type="radio" value="AM" name="from_time_meridian" @if(date('a',strtotime($selected_date)) == 'am') checked="checked" @endif id="from_time_am" autocomplete="off"> {{trans('website.W0189')}}
                                                                    </label>
                                                                    <label class="btn btn-default @if(date('a',strtotime($selected_date)) == 'pm') active @endif">
                                                                        <input type="radio" value="PM" name="from_time_meridian" @if(date('a',strtotime($selected_date)) == 'pm') checked="checked" @endif id="from_time_pm" autocomplete="off"> {{trans('website.W0190')}}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <input type="hidden" name="from_time">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{trans('website.W0179')}}</label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-4 col-xs-4 hours-select">
                                                                <div class="custom-dropdown">
                                                                    <select name="to_time_hour" class="form-control">
                                                                        {!! ___dropdown_options(___range(range(00, 12)),trans('website.W0187'),'13',true) !!}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 minutes-select">
                                                                <div class="custom-dropdown">
                                                                    <select name="to_time_minute" class="form-control">
                                                                        {!! ___dropdown_options(___range(range(00, 59,5)),trans('website.W0188'),'60',true) !!}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4 col-xs-4 meridian-select">
                                                                <div class="btn-group meridian-btn-group" data-toggle="buttons">
                                                                    <label class="btn btn-default @if(date('a',strtotime($selected_date)) == 'am') active @endif">
                                                                        <input type="radio" value="AM" name="to_time_meridian" @if(date('a',strtotime($selected_date)) == 'am') checked="checked" @endif id="to_time_am" autocomplete="off"> {{trans('website.W0189')}}
                                                                    </label>
                                                                    <label class="btn btn-default @if(date('a',strtotime($selected_date)) == 'pm') active @endif">
                                                                        <input type="radio" value="PM" name="to_time_meridian" @if(date('a',strtotime($selected_date)) == 'pm') checked="checked" @endif id="to_time_pm" autocomplete="off"> {{trans('website.W0190')}}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <input type="hidden" name="to_time">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group radio-wrapper">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{trans('website.W0180')}}</label>
                                                    <div class="col-md-9">
                                                        @foreach(employment_types('talent_availability') as $item)
                                                            <div class="radio radio-inline">                
                                                                <input name="repeat" data-request="show-hide" data-condition="weekly" data-target="[name='repeat']" data-true-condition=".weekly-availability-section" data-false-condition=".normal-section" type="radio" id="repeat-{{$item['type']}}" value="{{$item['type']}}" @if($item['type'] == 'daily') checked="checked" @endif>
                                                                <label for="repeat-{{$item['type']}}"> {{$item['type_name']}}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="form-group radio-wrapper">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{trans('website.W0612')}}</label>
                                                    <div class="col-md-9">
                                                        @foreach(availablity_type() as $item)
                                                            <div class="radio radio-inline">
                                                                <input name="availability_type" data-request="show-hide" data-target="[name='availability_type']" data-false-condition=".normal-section" type="radio" id="repeat-{{$item['type']}}" value="{{$item['type']}}" @if($item['type'] == 'available') checked="checked" @endif>
                                                                <label for="repeat-{{$item['type']}}"> {{$item['type_name']}}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="normal-section"></div>
                                                <div class="form-group weekly-availability-section" style="display: none;">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">End Of Repeat</label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12 message-group">
                                                        <div class="btn-group radio-btn-group days-btn-group" data-toggle="buttons">
                                                            @foreach(array_keys(days()) as $item)<label class="btn"><input type="checkbox" value="{{ $item }}" name="availability_day[]" id="availability-day-{{ $item }}" autocomplete="off"><span class="input-value">{{ $item }}</span></label>@endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{trans('website.W0184')}}</label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <div class='input-group datepicker' id='datetimepicker'>
                                                            <input type='text' class="form-control" name="deadline"/>
                                                            <span class="input-group-addon"></span>
                                                        </div>
                                                        <div class="add-more">
                                                            <a href="javascript:void(0);" data-box=".availability-box" data-request="multi-ajax" data-target='[role="talent_step_four"]' data-toremove="availability" data-box-id='[name="id_availability"]' data-message=".message">{{trans('website.W0185')}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>     
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group button-group">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="row form-btn-set">
                                        <div class="col-md-7 col-sm-7 col-xs-6">
                                            <a href="{{ $skip_url }}" class="greybutton-line">
                                                {{trans('website.W0186')}}
                                            </a>
                                        </div>
                                        <div class="col-md-5 col-sm-5 col-xs-6">
                                            <a href="{{ url(sprintf('%s/profile/verify-account',TALENT_ROLE_TYPE)) }}" class="button">
                                                {{trans('website.W0013')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

