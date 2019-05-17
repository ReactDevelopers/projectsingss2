<!-- Modal Window for Upload -->
<div class="modal fade upload-modal-box interview-popup" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <h2 class="form-heading">{{trans('website.W0398')}}</h2>
        <div class="interview-content-section">
            <div class="interview-popup-wrapper"> 
                <div class="interview-popup-content">
                    <h3>{{trans('website.W0399')}}</h3>
                    <p>{{trans('website.W0400')}}</p>                   
                </div>
                <p class="time-left">{{trans('website.W0401')}} <span class="interview-days">{{$remaining_day}} day{{$remaining_day == 1 ? '' : 's'}}</span> {{trans('website.W0404')}}</p>
            </div>
        </div>        
        <div class="form-group button-group clearfix">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row form-btn-set">
                    <div class="col-md-7 col-sm-7 col-xs-6">
                        <button typetton" class="greybutton-line" value="cancel" data-dismiss="modal">{{trans('website.W0402')}}</button>
                    </div>
                    <div class="col-md-5 col-sm-5 col-xs-6">
                        <a href="{{$interview_url}}">
                            <button type="button" class="button" value="set as profile picture">{{trans('website.W0403')}}</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@php
    $scriptCode = "";
    if($talentAnswerExist == 0 && $user['is_interview_popup_appeared'] == 'no'){
        $scriptCode = "
            $(document).ready(function(){
                $('#uploadModal').modal('show');
            });
        ";
    }
@endphp
<!-- /.Modal Window for Upload -->
@push('inlinescript')
<script type="text/javascript">
{!! $scriptCode !!}
</script>
@endpush
