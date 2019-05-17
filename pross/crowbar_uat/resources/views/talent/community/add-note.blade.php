<div class="modal-dialog member_modal_style" role="add-member">
    <div class="modal-content">
        <button type="button" class="button close_modal" data-dismiss="modal"><img src="{{asset('images/close-me.png')}}" /></button>
        <div >
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="talent_icon text-center">
                    <img src="{{asset('images/add-note.png')}}" />
                </div>            
                <div class="col-sm-12 text-center member_modal_text">
                    <span class="hire-title">{{trans('website.W0889')}}</span>
                    <p class="fontMedium">{{trans('website.W0890')}}</p>
                </div>
                <div class="clearfix"></div>
                <div class="member_modal_btn addnotetext">
                    <form class="form-horizontal" role="addmembernote" action="{{url(sprintf('%s/addmember/note?talent_id=%s',TALENT_ROLE_TYPE,$talent_id))}}" method="post" accept-charset="utf-8">
                        <div class="form-group">
                            <textarea name="note" placeholder="{{trans('website.W0891')}}" class="form-control member-add-note" data-request="live-length" data-maxlength="200"></textarea>
                    	</div>
                        <a class="greybutton-line" data-dismiss="modal">{{trans('website.W0355')}}</a>
                        <button type="button" class="button" value="Submit" data-request="ajax-submit" data-target="[role=&quot;addmembernote&quot;]">{{trans('website.W0229')}}</button>
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>