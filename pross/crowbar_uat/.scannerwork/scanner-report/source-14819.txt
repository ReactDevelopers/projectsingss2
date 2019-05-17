@include('employer.viewprofile.includes.sidebar-tabs',$user)                    
<div class="inner-profile-section">
    <div class="view-information">
        <div>
            <div class="pager text-center"><img src="{{ asset('images/loader.gif') }}"></div>
            <ul class="btn-block nav navbar-nav" id="notification-list"></ul>
            <div>
                <div id="loadmore">
                    <span class="btn btn-default btn-block btn-lg hide" data-request="paginate" data-url="{{ url(sprintf('%s/notifications/list?page=%s',EMPLOYER_ROLE_TYPE,1)) }}" data-target="#notification-list" data-showing="#paginate_showing" data-loadmore="#loadmore">{{ trans('website.W0254') }}</span>
                </div>
            </div>
            <div class="clearfix"></div>   
        </div>   
    </div>
</div>