<form method="post" role="find-jobs" accept-charset="utf-8" class="form-horizontal" autocomplete="off" onkeypress="return event.keyCode != 13">
<div class="login-inner-wrapper setting-wrapper ownership-searchbox">

    <div class="row bottom-border">
        <div class="col-md-5 col-sm-8 col-xs-12">
             <div class="header-block">
                <h2>{{trans('website.W0977')}}</h2>
            </div>
        </div>
        <div class="col-md-7 col-sm-8 col-xs-12">
              <div class="search-wrapper detail-search-wrapper">
        <input type="text" value="{{ \Request::get('_search') }}" name="search" placeholder="Search" class="form-control" id="search_networks" data-request="search"/>
        <buttton class="btn button searching">
            <img src="{{asset('images/white-search-icon.png')}}">
        </button>
    </div> 
        </div>
    </div>
  
    <div class="">
        <div class="form-group">
            <a href="javascript:void(0);" class="sidebar-menu"><span></span> Filter</a>
            <div class="">
                <div id="article_listing" class="timeline timeline-inverse"></div>
                <div class="pager text-center"><img src="{{ asset('images/loader.gif') }}"></div>
                <div>
                   <div id="loadmore">
                       <button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="{{ url(sprintf('/talent/__transferownership')) }}" data-target="#article_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role='find-jobs']">{{ trans('website.W0254') }}</button>
                   </div>
               </div>
               <input type="hidden" name="page" value="1">
            </div>
        </div>                                
    </div>               
</div>
</form>
<div class="modal fade upload-modal-box add-payment-cards" id="add-member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
@push('inlinescript')
    <script src="{{ asset('script/articlefilter.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(".searching").on('click',function(e) {
            isLoadMore = false;
            $('[name="page"]').val(1);
            $('[data-request="filter-paginate"]').trigger('click');
        });

        // $(function() {
        //         $('#search_networks').val(srch);
        //         $('[data-request="filter-paginate"]').trigger('click');
        // });
    </script>
@endpush