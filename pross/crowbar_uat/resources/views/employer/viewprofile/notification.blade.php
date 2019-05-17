@include('employer.viewprofile.includes.sidebar-tabs',$user)                    
<div class="view-information shift-up-5px" id="personal-infomation">
    <div class="payment-tabs job-related-tabs">
        <div class="datatable-listing">
            <div data-target="reviews">
                <div class="no-table">
                    {!! $html->table() !!}
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
            $('.no-table thead').remove();
            $('#dataTableBuilder_wrapper > .row:first').remove();

            $(document).on('click','[data-request="datatable"]',function(e){
                e.preventDefault();
                
                var $this = $(this);

                $('.active-tab').removeClass('active-tab');
                $this.parent().addClass('active-tab');
                
                LaravelDataTables["dataTableBuilder"].on('preXhr.dt', function ( e, settings, data ) {
                    data.type   = $this.data('type');
                }); 

                window.LaravelDataTables.dataTableBuilder.draw();
            });
        });
    </script>
@endpush