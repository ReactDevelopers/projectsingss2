<!-- Main Content -->
<div class="contentWrapper job-details-section find-jobs-list-section">
    <div class="container">
        <div class="row mainContentWrapper">
            <div class="col-md-9 job-details-left">
                <div id="parentHorizontalTab">
                    <div class="heading-filter">
                        <div class="checkbox checkbox-blue" id="hide-description">                
                            <input type="checkbox" id="loc0612">
                            <label for="loc0612"><span class="check"></span> {{trans('job.J0036')}}</label>
                        </div>
                    </div>
                    {!! ___getmenu('talent-myjobs','<span class="toggle-menu-wrap"><buttton class="open-toggle-menu">%s</buttton><ul class="resp-tabs-list hor_1 clearfix">%s</ul></span>','resp-tab-active',true,true) !!}
                    <div class="resp-tabs-container hor_1">
                        <div>
                            <div id="job_listing" class="timeline timeline-inverse"></div>
                            <div>
                               <div id="loadmore">
                                    <span class="btn btn-default btn-block btn-lg hide" data-request="paginate" data-url="{{ url(sprintf('%s/my-jobs/current?page=%s&search=%s',TALENT_ROLE_TYPE,1,Request::get('search')))}}" data-target="#job_listing" data-showing="#paginate_showing" data-loadmore="#loadmore">{{ trans('website.W0254') }}</span>
                                </div>
                            </div>   
                        </div>
                    </div>
                </div>
                    
            </div>
            <div class="col-md-3 job-details-right">
                @includeIf('talent.includes.my-profile-percentage')
            </div>            
        </div>
    </div>
</div>
<!-- /Main Content -->