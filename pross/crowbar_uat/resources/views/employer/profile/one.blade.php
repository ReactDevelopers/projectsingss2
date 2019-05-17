<form class="form-horizontal" role="employer_step_two" action="{{url(sprintf('%s/profile/process/one?redirect=%s',EMPLOYER_ROLE_TYPE,\Request::get('redirect')))}}" method="post" accept-charset="utf-8">
    <div class="inner-profile-section">                        
        <div class="login-inner-wrapper edit-inner-wrapper">
            {{ csrf_field() }}
            <input type="hidden" name="step_type" value="edit">
            <div class="form-group">
                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{ trans('website.W0492')}}</label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    @foreach(company_profile() as $item)
                        <div class="radio radio-inline">
                            <input data-request="show-hide" data-condition="company" data-target="[name='company_profile']" data-true-condition=".company-section" data-false-condition=".normal-section" name="company_profile" value="{{ $item['level'] }}" type="radio" id="{{ $item['level'] }}" @if(old('company_profile',$user['company_profile']) == $item['level']) {{'checked="checked"'}} @endif>
                            <label for="{{ $item['level'] }}"> {{ $item['level_name'] }}</label>
                        </div>
                    @endforeach
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{ trans('website.W0096')}}</label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <input type="text" name="company_name" placeholder="{{ trans('website.W0097')}}" class="form-control" value="{{ old('company_name',$user['company_name']) }}"/>
                </div>
            </div>        
            <div class="company-section">                       
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{ trans('website.W0248')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="contact_person_name" placeholder="{{ trans('website.W0493')}}" class="form-control"  value="{{ old('contact_person_name',$user['contact_person_name']) }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{ trans('website.W0249')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="company_website" placeholder="{{ trans('website.W0494')}}" class="form-control"  value="{{ old('company_website',$user['company_website']) }}"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{ trans('website.W0924')}}</label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="custom-dropdown">
                        <select class="form-control" name="company_work_field">
                            {!! ___dropdown_options(___cache('skills'), trans('website.W0924'),$user['company_work_field'],false) !!}
                        </select>
                    </div>
                </div>
            </div>
            @if(0)
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{ trans('website.W0788')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="skills-filter">
                            <div class="custom-dropdown">
                                <select id="certificates" name="certificates[]" class="filter form-control" data-request="tags-true" multiple="true" data-placeholder="{{trans('website.W0080')}}">
                                    {!!___dropdown_options(___cache('certificates'),'',$user['certificates'],false)!!}
                                </select>
                                <div class="js-example-tags-container white-tags"></div>
                            </div>
                        </div>           
                    </div>           
    {{--                 <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" maxlength="80" placeholder="{{trans('website.W0080')}}" class="form-control custom-tags-input" />
                        <div class="add-more"><a href="javascript:void(0);" data-request="custom-tags" data-target=".certification-box" data-source=".custom-tags-input" data-name="certificates" data-tags="{{ json_encode($user['certificates']) }}">{{trans('website.W0081')}}</a></div>

                        <div class="clearfix"></div>
                        <div class="custom-tags certification-box">

                        </div>
                    </div> --}}
                </div>
            @endif
            <div class="company-section">
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0252')}}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <textarea name="company_biography" class="form-control" placeholder="{{trans('website.W0497')}}">{{ old('company_biography',$user['company_biography']) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>                    
    <div class="row form-group button-group">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-btn-set">                                    
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <button type="button" class="button" value="Submit" data-request="ajax-submit" data-target='[role="employer_step_two"]'>{{ trans('website.W0058') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
@push('inlinescript')
    <style type="text/css">.modal-backdrop{display: none;}#SGCreator-modal{background: rgba(216, 216, 216, 0.7);}</style>

    <script type="text/javascript" src="https://dev.doctoranywhere.com/js/webcam.js"></script>
 
    {{-- <script type="text/javascript">
        Webcam.set({
            width: 320,
            height: 240,
            dest_width: 640,
            dest_height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            force_flash: false
        });

        $('#take_picture').on('click',function(){
            Webcam.attach( '#box__input' );
            $('#another_picture').show();
            $('#save_picture').show();
        });

        $('#click_picture').on('click',function(){
            Webcam.snap( function(data_uri) {
                document.getElementById('box__input').innerHTML = '<img src="'+data_uri+'"/>';
            } );
        });

        $('#another_picture').on('click',function(){
            Webcam.reset();
            Webcam.attach( '#box__input' );
        });

        $('#save_picture').on('click',function(){

            // take snapshot and get image data
            Webcam.snap(function (data_uri) {
                console.log("data_uri>> "+data_uri);
                document.getElementById('box__input').innerHTML = '<img src="'+data_uri+'"/>';
            });

            var data_uri = Webcam.snap();

            console.log("data_uri");
            console.log(data_uri);

            Webcam.upload(data_uri, 'pages/ajax/save-base64-image', function (code, text) {
                   console.log("code>>> "); //response status code
                   console.log(code); //response status code
            });
            Webcam.reset();

        });
    </script> --}}
@endpush
