<div class="row mainContentWrapper">
    @includeIf('employer.viewprofile.includes.sidebar')
    <div class="col-md-8 col-sm-8 right-sidebar">
        <div class="no-table datatable-listing">
            {!! $html->table(); !!}
        </div>            
    </div>
</div>

@push('inlinescript')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}

    <script type="text/javascript">
        $(function(){
            $('.filter-option').html('<select name="filter" class="filter form-control select" style="width:100%;">'+
                '<option value="">{{ trans("website.W0340") }}</option>'+
                '<option value="pending">{{ trans("website.W0708") }}</option>'+
                '<option value="initiated">{{ trans("website.W0709") }}</option>'+
                '<option value="closed">{{ trans("website.W0710") }}</option>'+
            '</select>');

            $('.sort-option').html('<select name="sort" class="filter form-control select" style="width:100%;">'+
                '<option value="">{{ trans("website.W0335") }}</option>'+
                '<option value="created-asc">{{ trans("website.W0338") }}</option>'+
                '<option value="created-desc">{{ trans("website.W0339") }}</option>'+
            '</select>');

            $('select.filter').select2({placeholder: function(){$(this).find('option[value!=""]:first').html();}});
            $('.datatable-listing .dataTables_filter input[type="search"]').attr("placeholder","{{ trans('website.W0342') }}");

            $(document).on('change','.filter',function(){
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.filter = $('[name="filter"]').val();
                    data.sort = $('[name="sort"]').val();
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });
        });
    </script>
@endpush
