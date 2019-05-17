{{-- <div class="header-navigation">
    <div class="container-fluid">
        <a href="javascript:void(0);" class="mobile-menu"><span></span> Menu</a>
        {!! ___getmenu('employer-top-after-login','%s<ul class="navigation-group-list clearfix">%s</ul>','active',false,false) !!}
    </div>
</div> --}}
<div class="clearfix"></div>
<div class="header-navigation">
    <div class="">
        <a href="javascript:void(0);" class="mobile-menu"><span></span> Menu</a>
        @if(Request::segment(2) =='employer' && Request::segment(3) != 'network')
        {!! ___getTalentMenu('employer-sub-top-after-login','employer-top-after-login','%s %s','active',false,false) !!}
        @elseif(Request::segment(2) =='network')
        {!! ___getTalentMenu('employer-sub-top-after-login','employer-network','%s %s','active',false,false) !!}
        @else
        {!! ___getTalentMenu('employer-sub-top-after-login','employer-top-after-login','%s %s','active',false,false) !!}
        @endif
    </div>
</div>