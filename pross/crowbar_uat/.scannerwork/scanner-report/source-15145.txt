<div class="modal-dialog member_modal_style" role="add-member">
    <div class="modal-content">
        <button type="button" class="button close_modal" data-dismiss="modal"><img src="{{asset('images/close-me.png')}}" /></button>
        <div >
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="talent_icon text-center">
                    <img src="{{asset('images/send-invitation.png')}}" />
                </div>            
                <div class="col-sm-12 text-center member_modal_text">
                    <span class="hire-title">{{trans('website.W0892')}}</span>
                    <p class="fontMedium">{{trans('website.W0893')}}<b>{{$user_name}}</b> {{trans('website.W0894')}}</p>
                </div>
                <div class="clearfix"></div>
                <div class="member_modal_btn">
                	<a class="greybutton-line" data-target="#add-member" data-request="ajax-modal" href="javascript:void(0);" data-url="{{ url(sprintf('%s/add-to-circle?talent_id=%s&page=%s',TALENT_ROLE_TYPE,$talent_id,'addnote')) }}" data-user="{{$talent_id}}">
                        {{trans('website.W0895')}}
                    </a>
                    <a class="button" href="javascript:void(0);" data-request="add-member" data-url="{{ url(sprintf('%s/addmember?talent_id=%s',TALENT_ROLE_TYPE,$talent_id)) }}" data-user="{{$talent_id}}">
                        {{trans('website.W0229')}}
                    </a>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>