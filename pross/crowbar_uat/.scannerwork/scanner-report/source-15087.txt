<div class="contentWrapper postjob-permanent-section">
    <div class="postjob-beforesubmit">
        <div class="container">
            <div class="right-sidebar payment-detail-sec no-padding-left no-padding-bottom">
                <h2 class="no-margin-bottom">{{ trans('website.W0375') }}</h2>
                <ul class="payment-blocks clearfix">
                    <li> <a href="javascript:void(0);" class="total-amount-block"> <span class="payment-text"> <h3>{{ trans('website.W0376') }}</h3> <span>{{ $payment_summary['total_received'] }}</span> </span> </a> </li>
                    <li> <a href="javascript:void(0);" class="total-amount-due-block"> <span class="payment-text"> <h3>{{ trans('website.W0377') }}</h3> <span>{{ $payment_summary['total_due'] }}</span> </span> </a> </li>
                    <li> <a href="javascript:void(0);" class="total-job-posted-block"> <span class="payment-text"> <h3>{{ trans('website.W0729') }}</h3> <span>{{ $payment_summary['total_completed_job'] }}</span> </span> </a> </li>
                </ul>
                <div class="row">
                    <div class="col-md-9 col-sm-12 col-xs-12">
                        <div id="parentHorizontalTab">
                            {!! ___getmenu('talent-wallet','%s<ul class="payment-tabs-wrapper">%s</ul>','active-tab',true,false) !!}
                            <div class="payment-tabs">
                                <div>
                                    <div class="datatable-listing no-padding-cell">
                                        {!! $html->table(); !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('inlinescript')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.js') }}"></script>
    {!! $html->scripts() !!}

    <script type="text/javascript">
        $(function(){
            var $tab_counter = {!! json_encode($payment_summary['payments']); !!}
            $('.payment-tabs-wrapper li a').each(function($item){
                if($tab_counter[$item]){
                    $(this).text($(this).text()+" ("+$tab_counter[$item]+")");
                }
            });

            // $('.table-heading').html('<h4>'+$('.resp-tabs-list li.resp-tab-active').text().split('(')[0]+'</h4>');
            // $('.filter-option').html('<div class="row">'+
            //     '<div class="col-md-12 col-sm-12 col-xs-12">'+
            //         '<select name="sort" class="filter form-control select" style="width:100%;">'+
            //             '<option value="">{{ trans("website.W0335") }}</option>'+
            //             '<option value="title-asc">{{ trans("website.W0336") }}</option>'+
            //             '<option value="title-desc">{{ trans("website.W0337") }}</option>'+
            //             '<option value="created-asc">{{ trans("website.W0338") }}</option>'+
            //             '<option value="created-desc">{{ trans("website.W0339") }}</option>'+
            //         '</select>'+
            //     '</div>'+
            // '</div>');

            // $('select.filter').select2({placeholder: function(){$(this).find('option[value!=""]:first').html();}});
            // $('.datatable-listing .dataTables_filter input[type="search"]').attr("placeholder","{{ trans('website.W0342') }}");

            // $(document).on('change','.filter',function(){
            //     LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
            //         data.sort  = $('[name="sort"]').val();
            //     }); 

            //     window.LaravelDataTables.dataTableBuilder.draw();
            // });
        });
    </script>
@endpush
