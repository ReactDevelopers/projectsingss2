<div class="col-md-12 no-padding-xs">
    <div class="approved-proposals no-padding">
        <h2 class="form-heading">
            {{trans('website.W0225')}}<span id="totalproposal"></span>
        </h2>
        <div class="datatable-listing">
            {!! $html->table(); !!}
        </div>
    </div>
</div>
@push('inlinescript')
    <style type="text/css">
        .view-profile-name .last-viewed-icon {
            position: absolute;
            right: 24px;
            margin-top: 8px;
        }
    </style>
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}

    <script type="text/javascript">
        $(function(){
            $('.filter-option').html('<div class="row">'+
                '<div class="col-md-6 col-sm-6 col-xs-6">'+
                    '<select name="sort" class="filter form-control select" style="width:100%;">'+
                        '<option value="">{{ trans("website.W0335") }}</option>'+
                        '<option value="name-asc">{{ trans("website.W0336") }}</option>'+
                        '<option value="name-desc">{{ trans("website.W0337") }}</option>'+
                        '<option value="proposal_sent-asc">{{ trans("website.W0338") }}</option>'+
                        '<option value="proposal_sent-desc">{{ trans("website.W0339") }}</option>'+
                        '<option value="quoted_price-asc">{{ trans("website.W0615") }}</option>'+
                        '<option value="quoted_price-desc">{{ trans("website.W0616") }}</option>'+
                    '</select>'+
                '</div>'+
                '<div class="col-md-6 col-sm-6 col-xs-6">'+
                    '<select name="filter" class="filter form-control select" style="width:100%;">'+
                        '<option value="">{{ trans("website.W0340") }}</option>'+
                        '<option value="tagged_listing">{{ trans("website.W0341") }}</option>'+
                        '<option value="accepted_proposal">{{ trans("website.W0780") }}</option>'+
                        '<option value="applied_proposal">{{ trans("website.W0756") }}</option>'+
                        '<option value="declined_proposal">{{ trans("website.W0755") }}</option>'+
                    '</select>'+
                '</div>'+
            '</div>');

            $('select.filter').select2({placeholder: function(){$(this).find('option[value!=""]:first').html();}});
            $('.datatable-listing .dataTables_filter input[type="search"]').attr("placeholder","{{ trans('website.W0342') }}");

            $(document).on('change','.filter',function(){
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.sort    = $('[name="sort"]').val();
                    data.filter  = $('[name="filter"]').val();
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });

            $(document).ajaxStop(function($response,$data) {
                var $totalproposal = $('#dataTableBuilder_info').text().split(" ");
                $('#totalproposal').text(" ("+$totalproposal[5]+")");
            });
        });
    </script>
@endpush
