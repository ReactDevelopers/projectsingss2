<form role="settings" method="POST" action="{{ url(sprintf('%s/__notificationsettings',TALENT_ROLE_TYPE)) }}" class="form-horizontal" autocomplete="off">
    {{ csrf_field() }}
    <div class="login-inner-wrapper setting-wrapper">
        <div class="row">
            <div class="col-md-6 col-sm-6 colxs-12">
                <div class="settingList">
                    <h2 class="form-heading">
                        <div class="checkbox">
                            <input type="checkbox" id="email_check_all" value="">
                            <label for="email_check_all"><span class="check"></span> <b>{{ trans('website.W0307') }}</b></label>
                        </div>
                    </h2>
                    <ul>
                        @foreach($settings['email'] as $item)
                            <li class="checkbox">
                                <input type="checkbox" value="{{$item['setting']}}" @if($item['status'] == DEFAULT_YES_VALUE) checked="checked" @endif id="email_{{$item['setting']}}" name="email[{{$item['setting']}}]">
                                <label for="email_{{$item['setting']}}"><span class="check"></span> {{ trans(sprintf('general.%s',$item['setting'])) }}</label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 colxs-12">
                <div class="settingList">
                    <h2 class="form-heading">
                        <div class="checkbox">
                            <input type="checkbox" id="mobile_check_all" value="">
                            <label for="mobile_check_all"><span class="check"></span> <b>{{ trans('website.W0315') }}</b></label>
                        </div>
                    </h2>
                    <ul>
                        @foreach($settings['mobile'] as $item)
                            <li class="checkbox">
                                <input type="checkbox" value="{{$item['setting']}}" @if($item['status'] == DEFAULT_YES_VALUE) checked="checked" @endif id="mobile_{{$item['setting']}}" name="mobile[{{$item['setting']}}]">
                                <label for="mobile_{{$item['setting']}}"><span class="check"></span> {{ trans(sprintf('general.%s',$item['setting'])) }}</label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>                
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">
                <div class="col-md-5 col-sm-5 col-xs-6">
                    <button type="button" data-request="ajax-submit" data-target='[role="settings"]' class="btn btn-sm redShedBtn pull-right">{{trans('website.W0058')}}</button>
                </div>
            </div>      
        </div>
    </div>
</form>
@push('inlinescript')
    <script type="text/javascript">
        $(function(){  
            $("#email_check_all").click(function(){
                $('[name*="email"]').not(this).prop('checked', this.checked);
            });

            $('[name*="email"]').click(function(){
                if($('[name*="email"]:checked').length == $('[name*="email"]').length){
                    $("#email_check_all").prop('checked', true);
                }else{
                    $("#email_check_all").prop('checked', false);    
                }
            });

            if($('[name*="email"]:checked').length == $('[name*="email"]').length){
                $("#email_check_all").prop('checked', true);
            }else{
                $("#email_check_all").prop('checked', false);    
            }

            $("#mobile_check_all").click(function(){
                $('[name*="mobile"]').not(this).prop('checked', this.checked);
            });

            $('[name*="mobile"]').click(function(){
                if($('[name*="mobile"]:checked').length == $('[name*="mobile"]').length){
                    $("#mobile_check_all").prop('checked', true);
                }else{
                    $("#mobile_check_all").prop('checked', false);
                }
            });

            if($('[name*="mobile"]:checked').length == $('[name*="mobile"]').length){
                $("#mobile_check_all").prop('checked', true);
            }else{
                $("#mobile_check_all").prop('checked', false);
            }
        });
    </script>
@endpush