{{-- <div class="col-md-4 col-sm-4 clearfix">
    <div class="user-info-wrapper user-info-greyBox">
        <div class="user-profile-image">
            <div class="user-display-details">
                <div class="user-display-image cropper" data-request="cropper" data-class="profile" data-width="192" data-height="192" data-folder="{{TALENT_PROFILE_PHOTO_UPLOAD}}" data-record="0" data-column="profile" style="background: url('{{ $user['picture'] }}') no-repeat center center;background-size:100% 100%"></div>
            </div>
        </div>    
    </div>
</div>
 --}}


<form class="form-horizontal" role="talent_step_one" action="{{url(sprintf('%s/profile/step/process/one',TALENT_ROLE_TYPE))}}" method="post" accept-charset="utf-8">
    <div class="login-inner-wrapper">
        {{ csrf_field() }}
        @if(!empty($edit))<input type="hidden" name="process" value="edit">@endif
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
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0047')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12 close-fields-wrapper">
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-4 day-select">
                                <div class="custom-dropdown">
                                    <select name="birthdate" class="form-control">
                                        {!!___dropdown_options(___range(range(1, 31)),trans('website.W0192'),$user['birthdate'],true) !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4 month-select">
                                <div class="custom-dropdown">
                                    <select name="birthmonth" class="form-control">
                                    {!!___dropdown_options(trans('website.W0048'),trans('website.W0100'),$user['birthmonth']) !!}
                                    </select>
                                </div>
                            </div>
                            @php $year_min_limit = ((int)date('Y'))+BIRTHDAY_MIN_YEAR_LIMIT; $year_max_limit = ((int)date('Y'))+BIRTHDAY_MAX_YEAR_LIMIT; @endphp
                            <div class="col-md-4 col-sm-4 col-xs-4 year-select">
                                <div class="custom-dropdown">
                                    <select name="birthyear" class="form-control">
                                    {!!___dropdown_options(___range(range($year_min_limit,$year_max_limit)),trans('website.W0103'),$user['birthyear'])!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="birthday">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0049')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        @foreach(gender()  as $key => $value)
                            <div class="radio radio-inline">                
                                <input name="gender" type="radio" {{($user['gender']==$value['label']) ? 'checked' : ''}} value="{{$value['label']}}" id="gen0-{{$value['label']}}">
                                <label for="gen0-{{$value['label']}}">{{$value['label_name']}}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                @php
                    $isConnected = \Models\companyConnectedTalent::where('id_user',\Auth::user()->id_user)->where('user_type','user')->count();
                @endphp
                    <div class="form-group">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0369')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="radio radio-inline">                
                                <input name="company_profile" type="radio"  value="individual" id="individual"  {{($user['company_profile'] == 'individual') ? 'checked=""' : '' }}  class="company_type">
                                <label for="individual">{{trans('website.W0545')}}</label>
                            </div>
                            <div class="radio radio-inline">                
                                <input name="company_profile" type="radio"  value="company" {{($user['company_profile'] == 'company') ? 'checked=""' : '' }} id="company" class="company_type">
                                <label for="company">{{trans('website.W0943')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="company_info">
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">Company Name </label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input name="company_name" value="{{!empty($companydata->company_name) ? @$companydata->company_name : ''}}" type="text" class="form-control" data-toggle="tooltip" title="" data-original-title="">
                        </div>
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">Company Website </label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input name="company_website" value="{{!empty($companydata->company_website) ? @$companydata->company_website : ''}}" type="text" class="form-control" data-toggle="tooltip" title="" data-original-title="">
                        </div>
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">Tell us more about your company </label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <textarea name="company_biography" class="form-control" placeholder="Enter description here">{{!empty($companydata->company_biography) ? @$companydata->company_biography : ''}}</textarea>
                        </div>
                        <label class="col-md-12 col-sm-12 col-xs-12 control-label">Company Logo</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <img src="{{@$companydata->company_logo}}" height="150px" width="150px"> 
                            <input type="file" name="company_logo" accept="image/png">
                        </div>
                    </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0053')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12 phonenumber-field">
                        <div class="close-fields-wrapper">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="custom-dropdown countrycode-dropdown">
                                        <select name="country_code" class="form-control" data-placeholder="{{trans('website.W0432')}}"></select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <input type="text" name="mobile" value="{{ old('mobile',$user['mobile']) }}" placeholder="{{trans('website.W0071')}}" class="form-control">
                                </div>                                                        
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0054')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="address" value="{{ old('address',$user['address']) }}" placeholder="{{trans('website.W0072')}}" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{sprintf(trans('website.W0055'),'')}}<span class="required">*</span></label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="custom-dropdown">
                            <select class="form-control" name="country" data-placeholder="{{trans('website.W0055')}}"></select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{sprintf(trans('website.W0056'),'')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="custom-dropdown">
                            <select class="form-control" name="state" data-placeholder="{{trans('website.W0056')}}"></select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{sprintf(trans('website.W0294'),'')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="custom-dropdown">
                            <select class="form-control" name="city" data-placeholder="{{trans('website.W0294')}}"></select>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0057')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="postal_code" maxlength="6" value="{{ old('postal_code',$user['postal_code']) }}" placeholder="{{trans('website.W0073')}}" class="form-control">
                    </div>
                </div>
                @if($user['company_profile'] == 'company')
                    <div class="form-group">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0995')}}</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="radio radio-inline">                
                                <input name="show_profile" type="radio" id="yes"  value="yes" {{($user['show_profile'] == 'yes') ? 'checked=""' : '' }}  class="company_type">
                                <label for="yes">{{trans('website.W0976')}}</label>
                            </div>
                            <div class="radio radio-inline">                
                                <input name="show_profile" type="radio" id="no"  value="no" {{($user['show_profile'] == 'no') ? 'checked=""' : '' }}  class="company_type">
                                <label for="no">{{trans('website.W0102')}}</label>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <div class="col-sm-12 col-xs-12">
                        <div class="checkbox small-checkbox profile-checkbox">                
                            <input name="agree" type="checkbox" id="agree">
                            <label for="agree">
                                <span class="check"></span>
                                <span>
                                    {!!
                                        sprintf(
                                            trans('website.W0149'),
                                            "<a class='underline' target='_blank' href='".url('/page/terms-and-conditions')."'>".trans('website.W0147')."</a>",
                                            "<a class='underline' target='_blank' href='".url('/page/privacy-policy')."'>".trans('website.W0148')."</a>"
                                        )
                                    !!}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    @if(in_array('two',$steps))
                        <a href="{{ url(sprintf('%s/profile/%sstep/%s',TALENT_ROLE_TYPE,$edit_url,$steps[count($steps)-2])) }}" class="greybutton-line"></a>
                    @endif
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <a href="{{ $skip_url }}" class="greybutton-line">
                        {{trans('website.W0186')}}
                    </a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <button type="button" data-request="ajax-submit" data-target='[role="talent_step_one"]' name="save" class="button" value="Save">
                        {{trans('website.W0659')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@push('inlinescript')
    <style>.phonenumber-field .help-block{top: 0px;}</style>
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
                data: [{id: '{{$user['country']}}', text: '{{$user['country_name']}}'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            }).on('change',function(){
                $('[name="state"]').val('').trigger('change');
                $('[name="city"]').val('').trigger('change');
            });


            $('[name="state"]').select2({
                formatLoadMore   : function() {return 'Loading more...'},
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

            $('[name="city"]').select2({
                ajax: {
                    url: base_url+'/cities',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            state: $('[name="state"]').val(),
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                },
                data: [{id: '{{$user['city']}}', text: '{{$user['city_name']}}'}],
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            });
        },2000);

        $(document).ready(function(){
            $("input[name='first_name']").prop('tabindex',1);
            $("input[name='last_name']").prop('tabindex',2);
            $("input[name='email']").prop('tabindex',3);
            $("select[name='birthdate']").prop('tabindex',4);
            $("select[name='birthmonth']").prop('tabindex',5);
            $("select[name='birthyear']").prop('tabindex',6);
            $("input[name='gender']").prop('tabindex',7);
            $("select[name='country_code']").prop('tabindex',8);
            $("input[name='mobile']").prop('tabindex',9);
            $("input[name='address']").prop('tabindex',10);
            $("select[name='country']").prop('tabindex',11);
            $("select[name='state']").prop('tabindex',12);
            $("select[name='city']").prop('tabindex',13);
            $("input[name='postal_code']").prop('tabindex',14);
            $("input[name='agree']").prop('tabindex',15);

            if($('#individual').is(':checked') == true){
                $('#company_info').hide();
            }else if($('#company').is(':checked') == true){
                $('#company_info').show();
            }

            $('.company_type').on('change',function(){
                console.log($(this).val());
                if($(this).val()=='individual'){
                    $('#company_info').hide();
                }else if($(this).val()=='company'){
                    $('#company_info').show();
                }
            });
        });

        // $(".cropper2").SGCropper({
        //     viewMode: 1,
        //     aspectRatio: "2/3",
        //     cropBoxResizable: false,
        //     formContainer:{
        //         actionURL:"{{ url(sprintf('ajax/crop?imagename=image&user_id=%s&type=article',Auth::user()->id_user)) }}",
        //         modelTitle:"{{ trans('website.W0970') }}",
        //         modelSuggestion:"{{ trans('website.W0263') }}",
        //         modelDescription:"{{ trans('website.W0264') }}",
        //         modelSeperator:"{{ trans('website.W0265') }}",
        //         uploadLabel:"{{ trans('website.W0266') }}",
        //         fieldLabel:"",
        //         fieldName: "image",
        //         btnText:"{{ trans('website.W0971') }}",
        //         defaultImage: "../images/product_sample.jpg",
        //         loaderImage: "{{asset('images/loader.gif')}}",
        //     }
        // });
    </script>
@endpush