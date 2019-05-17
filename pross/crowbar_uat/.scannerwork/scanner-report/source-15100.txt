@include('talent.viewprofile.includes.sidebar-tabs',$user)
{{-- <div class="inner-profile-section addNewProjects">
    <div>    
        <!-- <h2 class="form-heading">{{ trans('website.W0324') }}</h2> -->
        <div class="amazingProductBox">
            <div class="row">
                @php
                    foreach ($get_file as $key => $item) {
                        echo sprintf(
                            PORTFOLIO_LIST_TEMPLATE,
                            $item['id_portfolio'],
                            (!empty($item['file'][0]))?asset(sprintf("%s%s%s",$item['file'][0]['folder'],'thumbnail/',$item['file'][0]['filename'])):asset(sprintf('images/%s',DEFAULT_AVATAR_IMAGE)),
                            url(sprintf("%s/profile/portfolio/view?portfolio_id=%s",TALENT_ROLE_TYPE,___encrypt($item['id_portfolio']))),
                            $item['portfolio'],
                            url(sprintf("%s/profile/portfolio/edit?portfolio_id=%s",TALENT_ROLE_TYPE,___encrypt($item['id_portfolio']))),
                            sprintf(url('ajax/%s?id_portfolio=%s'), DELETE_PORTFOLIO, $item['id_portfolio'] ),
                            $item['id_portfolio']
                        );
                    }
                @endphp
                <div class="col-md-4 col-sm-6 col-xs-6 portfolio-outer">
                    <a href="{{url(sprintf('%s/profile/portfolio/add',TALENT_ROLE_TYPE))}}">
                        <label class="btn-bs-file add-image-box add-image-box-block">
                            <span class="add-image-wrapper">
                                <img src="{{ asset('images/add-icon.png') }}" />
                                <span class="add-icon-title">{{ trans('website.W0326') }}</span>
                            </span>
                        </label>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
</div> --}}
<div class="view-information shift-up-5px" id="personal-infomation">
    <div class="payment-tabs job-related-tabs">
        <div class="datatable-listing">
            <div data-target="reviews">
                <div class="no-table portfolio-section-table">
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