<form class="form-horizontal" role="talent_step_four" action="{{url(sprintf('%s/profile/step/process/four',TALENT_ROLE_TYPE))}}" method="POST" accept-charset="utf-8">
    <div class="login-inner-wrapper">
        {{ csrf_field() }}
        @if(!empty($edit))<input type="hidden" name="process" value="edit">@endif
        <h4 class="form-sub-heading">{{sprintf(trans('website.W0661'),'')}}</h4>
        <div class="row">
            <div class="col-md-3">  
                <label class="control-label t-u">{{trans('website.W0286')}}</label>
            </div>
            <div class="col-md-4">
                <label class="control-label t-u">{{trans('website.W0660')}}</label>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <ul class="filter-list-group clear-list">
                    @foreach(employment_types('web_post_job') as $key => $value)
                        <li>
                            <div class="row">
                                <div class="col-md-3">                
                                    <div class="checkbox">                
                                        <input type="checkbox" id="employement-{{ $value['type'] }}" name="interests[{{$key}}]" value="{{ $value['type'] }}" @if((!empty($user['interested'][$value['type']]))) checked="checked" @endif data-request="focus-input-checkbox">
                                        <label for="employement-{{ $value['type'] }}"><span class="check"></span> {{ strtolower($value['type_name']) }}</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="enrollment-range">
                                        <input type="text" name="workrate[{{$key}}]" class="form-control m-t-5px" @if(!empty($user['interested'][$value['type']])) value="{{ $user['interested'][$value['type']] }}" @endif data-request="focus-input-checkbox">
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>        
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0664')}}</label>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <textarea name="workrate_information" placeholder="{{ trans('website.W0665') }}" class="form-control" data-request="live-length" data-maxlength="{{DESCRIPTION_COUNTER_LENGTH}}">{{$user['workrate_information']}}</textarea>
            </div>
        </div> 
    </div>    
</form>        
<div class="form-group button-group">
    <div class="row form-btn-set">
        <div class="col-md-4 col-sm-4 col-xs-12">
            @if(in_array('two',$steps))
                <a href="{{ url(sprintf('%s/profile/%sstep/%s',TALENT_ROLE_TYPE,$edit_url,$steps[count($steps)-2])) }}" class="greybutton-line">{{trans('website.W0196')}}</a>
            @endif
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <a href="{{ $skip_url }}" class="greybutton-line">
                {{trans('website.W0186')}}
            </a>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <button type="button" class="button" data-request="ajax-submit" data-target='[role="talent_step_four"]' value="Save">{{trans('website.W0659')}}</button>
        </div>
    </div>
</div>

@push('inlinecss')
    <style>
        .enrollment-range input[type=text].form-control{
            padding-left: 28px;
        }
        .enrollment-range::before{
            content: "{{\Cache::get('currencies')[\Session::get('site_currency')]}}";
        }
    </style>
@endpush
@push('inlinescript')
    <script type="text/javascript">
        var $words_text = '{{trans('website.W0723')}}';
        $(function(){
            setTimeout(function(){
                $('[data-request="live-length"]').trigger('keyup');
            },2000);
        })
    </script>
@endpush