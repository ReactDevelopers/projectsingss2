<div class="col-md-4 col-sm-8 col-xs-12 invite-talent">   
    <form role="invite-talent" method="POST" action="{{ url(sprintf('%s/__invitetalent',EMPLOYER_ROLE_TYPE)) }}" class="row" autocomplete="off">
        {{ csrf_field() }}
        <div class="col-md-8 col-sm-8 col-xs-8 search-input-wrapper">
            <div class="form-group no-margin-bottom">
                <input name="email" type="text" class="form-control" placeholder="{{ trans('website.W0731') }}" />
                <input type="text" class="hide">
            </div>
        </div>      
        <div class="col-md-4 col-sm-4 col-xs-4 search-button-wrapper">
            <button type="button" data-request="ajax-submit" data-target='[role="invite-talent"]' class="btn redShedBtn btn-small">{{trans('website.W0697')}}</button>
        </div>
    </form>
</div>