<form class="form-horizontal" role="employer_step_one" action="{{url(sprintf('%s/profile/process/two',EMPLOYER_ROLE_TYPE))}}" method="post" accept-charset="utf-8">
    <div class="inner-profile-section">                        
        <div class="login-inner-wrapper edit-inner-wrapper">
        
            {{ csrf_field() }}
            <input type="hidden" name="step_type" value="edit">

            <div class="row">
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0142')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="first_name" value="{{ old('first_name',$user['first_name']) }}" placeholder="{{trans('website.W0142')}}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0143')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="last_name" value="{{ old('last_name',$user['last_name']) }}" placeholder="{{trans('website.W0143')}}" class="form-control">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0144')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="email" value="{{ old('email',$user['email']) }}" placeholder="{{trans('website.W0144')}}" class="form-control">
                        </div>
                    </div>   
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0053')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="close-fields-wrapper">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <div class="custom-dropdown countrycode-dropdown">
                                            <select name="country_code" class="form-control" data-placeholder="{{trans('website.W0432')}}"></select>
                                        </div>                                                        
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                        <input type="text" name="mobile" value="{{ old('mobile',$user['mobile']) }}" placeholder="{{trans('website.W0071')}}" class="form-control">
                                    </div>                             
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0245')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="close-fields-wrapper">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <div class="custom-dropdown countrycode-dropdown">
                                            <select name="other_country_code" class="form-control" data-placeholder="{{trans('website.W0432')}}"></select>
                                            {{-- <select name="other_country_code" class="form-control">
                                            {!!___dropdown_options($country_phone_codes,'',$user['other_country_code'])!!} --}}
                                            </select>
                                        </div>                                                        
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-8">
                                        <input type="text" name="other_mobile" value="{{ old('other_mobile',$user['other_mobile']) }}" placeholder="{{trans('website.W0071')}}" class="form-control">
                                    </div>
                                </div>                             
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0054')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="address" value="{{ old('address',$user['address']) }}" placeholder="{{trans('website.W0072')}}" class="form-control">
                        </div>
                    </div>                                    
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{sprintf(trans('website.W0055'),'')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="custom-dropdown">
                                <select class="form-control" name="country"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{sprintf(trans('website.W0056'),'')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="custom-dropdown">
                                <select class="form-control" name="state"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0057')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="postal_code" value="{{ old('postal_code',$user['postal_code']) }}" placeholder="{{trans('website.W0073')}}" class="form-control">
                        </div>
                    </div>                                        
                </div>
            </div>
        </div>
    </div>                       

    <div class="row form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">      
                <div class="col-md-4 col-sm-4 col-xs-12">
                    @if(in_array('two',$steps))
                        <a href="{{ url(sprintf('%s/profile/edit/one',EMPLOYER_ROLE_TYPE)) }}" class="greybutton-line">{{trans('website.W0196')}}</a>
                    @endif
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <button type="button" data-request="ajax-submit" data-target='[role="employer_step_one"]' name="save" class="button" value="Save">
                        {{trans('website.W0058')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@push('inlinescript')
    <style type="text/css">.modal-backdrop{display: none;} #SGCreator-modal{background: rgba(216, 216, 216, 0.7);}</style>
    <script type="text/javascript">
        setTimeout(function(){
            $('[name="country_code"]').select2({
                formatLoadMore   : function() {return 'Loading more...'},
                ajax: {
                    url: base_url+'/country_phone_codes',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{
                    id: '{{$user['country_code']}}',
                    text: '{{ !empty($user['country_code']) ? sprintf('%s (%s)',$user['country_code_name'],$user['country_code']) : ''}}'
                }],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            });

            $('[name="other_country_code"]').select2({
                formatLoadMore   : function() {return 'Loading more...'},
                ajax: {
                    url: base_url+'/country_phone_codes',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{
                    id: '{{$user['other_country_code']}}',
                    text: '{{ !empty($user['other_country_code']) ? sprintf('%s (%s)',$user['other_country_code_name'],$user['other_country_code']) : ''}}'
                }],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            });            

            $('[name="country"]').select2({
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
                data: [{id: '{{$user['country']}}', text: '{{$user['country_name']}}'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            }).on('change',function(){
                $('[name="state"]').val('').trigger('change');
                $('[name="city"]').val('').trigger('change');
            });


            $('[name="state"]').select2({
                ajax: {
                    url: base_url+'/states',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            country: $('[name="country"]').val(),
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{id: '{{$user['state']}}', text: '{{$user['state_name']}}'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            }).on('change',function(){
                $('[name="city"]').val('').trigger('change');
            });
        },2000);
    </script>
@endpush
