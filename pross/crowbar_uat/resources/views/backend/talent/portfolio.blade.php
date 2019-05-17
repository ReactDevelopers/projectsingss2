<div class="amazingProductBox">
    <div class="row">
        @php
            if(!empty($get_file)){
                foreach ($get_file as $key => $item) {
                    echo sprintf(
                        ADMIN_PORTFOLIO_LIST_TEMPLATE,
                        $item['id_portfolio'],
                        (!empty($item['file'][0]))?url(sprintf("%s%s%s",$item['file'][0]['folder'],'thumbnail/',$item['file'][0]['filename'])):url(sprintf('images/%s',DEFAULT_AVATAR_IMAGE)),
                        $item['portfolio'],
                        sprintf(
                            url('ajax/%s?id_portfolio=%s'),
                            DELETE_PORTFOLIO,
                            $item['id_portfolio']
                        ),
                        $item['id_portfolio']
                    );
                }
            }else{
                echo '<div class="col-md-12">'.N_A.'</div>';
            }
        @endphp
    </div>
</div>
@push('inlinescript')
    <script type="text/javascript">
        $(document).on('click','[data-request="delete"]',function(){
            var $this           = $(this);
            var $url            = $this.data('url');
            var data_id         = $this.data($this.data('edit-id'));
            var toremove        = $this.data('toremove');
            var ask             = $this.data('ask');
            var after_upload    = $this.data('after-upload');
            swal({
                title: '',
                text: ask,
                showLoaderOnConfirm: true,
                showCancelButton: true,
                showCloseButton: false,
                allowEscapeKey: false,
                allowOutsideClick:false,
                confirmButtonText: '<i class="fa fa-check-circle-o"></i> Confirm',
                cancelButtonText: '<i class="fa fa-times-circle-o"></i> Cancel',
                confirmButtonColor: '#0FA1A8',
                cancelButtonColor: '#CFCFCF',
                preConfirm: function (res) {
                    return new Promise(function (resolve, reject) {
                        if (res === true) {
                            $.ajax({
                                url         : $url,
                                type        : 'post',
                                dataType    : 'json',
                                success:function(response){
                                    $('#'+toremove+'-'+data_id).fadeOut();
                                    setTimeout(function(){
                                        $('#'+toremove+'-'+data_id).remove();
                                    },1000);
                                    if($this.data('single') === true){
                                        $(after_upload).show();
                                    }
                                    resolve()
                                }
                            })
                        }
                    })
                }
            })
            .then(function(isConfirm){

            },function (dismiss){
                // console.log(dismiss);
            })
            .catch(swal.noop);
        });
    </script>
@endpush
