<div class="modal-dialog" role="document">
    <div class="modal-content">
        <h3 class="modal-title bottom-margin-10px">{{trans('job.J00117')}}</h3>
        <div class="modal-body bg-white">
            <div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{ trans('job.J00119') }}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        {{$invitation['job_title']}}
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12">{{ trans('job.J00121') }}</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        {{$invitation['message']}}
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="form-group button-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row form-btn-set">
                        <div class="col-md-7 col-sm-7 col-xs-6">
                            <button type="button" class="button-line" value="cancel" data-dismiss="modal">
                                {{trans('website.W0355')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>