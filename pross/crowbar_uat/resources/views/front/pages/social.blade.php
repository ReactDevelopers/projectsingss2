<div class="modal-dialog" role="document">
    <div class="modal-content">
        <h3 class="form-heading m-b-10px no-padding">{{trans('website.W0820')}}</h3>
        <div class="singuplanding-section no-padding">   
            <div class="login-inner-wrapper">   
                <div class="row has-vr">
                    <div class="col-md-6 col-sm-6 col-xs-12 p-b-n">
                        <img src="{{ asset('images/hiretalent-icon.png') }}" />
                        <p>{{trans('website.W0135')}}</p>
                        <span>{!!trans('website.W0136')!!}</span>   
                        <a href="{{ url('/signup/employer') }}" class="button">{{trans('website.W0137')}}</a>                         
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 p-b-n">
                        <img src="{{ asset('images/worktalent-icon.png') }}" />
                        <p>{{trans('website.W0138')}}</p>
                        <span>{!!trans('website.W0139')!!}</span>
                        <a href="{{ url('/signup/talent') }}" class="button">{{trans('website.W0140')}}</a>
                    </div>
                    <div class="vertical-divison"><span class="optional-or">{{trans('website.W0132')}}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>