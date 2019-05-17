{!! ___getmenu('employer-myjobs','<span class="hide">%s</span><ul class="user-profile-links">%s</ul>','active',true,true) !!}
<div class="shift-up-5px">
    <div>
        <div class="no-table datatable-listing">
            {!! $html->table(); !!}
        </div>   
    </div>
</div>
@push('inlinescript')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}
    <script type="text/javascript">$(function(){$('#dataTableBuilder_wrapper .row:first').remove();});</script>
@endpush
