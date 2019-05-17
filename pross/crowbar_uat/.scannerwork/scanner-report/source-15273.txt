<div style="padding-top:10px;">
    <form role ="form-add-state" action="{{url(sprintf("%s/%s/%s",ADMIN_FOLDER,'general','states/add'))}}" method="post">
        <div class="clearfix">
            <div class="col-md-3 form-group">
                <div>
                    <select class="form-control" name="country">
                        {!! ___dropdown_options($countries,trans("admin.A0008"),!empty($state) ? $state->country_id : '') !!}
                    </select>
                </div>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="en" value="{{ !empty($state) ? $state->en : '' }}" placeholder="ENGLISH" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="id" value="{{ !empty($state) ? $state->id : '' }}" placeholder="INDONESIA" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="cz" value="{{ !empty($state) ? $state->cz : '' }}" placeholder="MANDARIN" style="width:100%;"/>
            </div>
            
        </div>
        <div class="clearfix">
            
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="ta" value="{{ !empty($state) ? $state->ta : '' }}" placeholder="TAMIL" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="hi" value="{{ !empty($state) ? $state->hi : '' }}" placeholder="HINDI" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="iso_code" value="{{ !empty($state) ? $state->iso_code : '' }}" placeholder="ISO CODE" style="width:100%;"/>
            </div>
            <input type="hidden" name="action" value="submit">
            <input type="hidden" name="id_state" value="{{ !empty($state) ? ___encrypt($state->id_state) : '' }}">
            <div class="col-md-3 form-group">
                <input type="button" class="btn btn-default btn-block" value="Save" data-request="inline-submit" data-target="[role=form-add-state]">
            </div>
        </div>
    </form>
</div>
<div class="clearfix"></div>
<hr style="margin-top:0;">
