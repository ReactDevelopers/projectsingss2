<div style="padding-top:10px;">
    <form role="form-add-city" action="{{url(sprintf("%s/%s/%s",ADMIN_FOLDER,'general','city/add'))}}" method="post">
        <div class="clearfix">
            <div class="col-md-3 form-group">
                <div>
                    <select class="form-control" name="state">
                        {!! ___dropdown_options($states,trans("admin.A0009"),!empty($city) ? $city->state_id : '') !!}
                    </select>
                </div>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="en" value="{{ !empty($city) ? $city->en : '' }}" placeholder="ENGLISH" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="id" value="{{ !empty($city) ? $city->id : '' }}" placeholder="INDONESIA" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="cz" value="{{ !empty($city) ? $city->cz : '' }}" placeholder="MANDARIN" style="width:100%;"/>
            </div>
        </div>
        <div class="clearfix">
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="ta" value="{{ !empty($city) ? $city->ta : '' }}" placeholder="TAMIL" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="hi" value="{{ !empty($city) ? $city->hi : '' }}" placeholder="HINDI" style="width:100%;"/>
            </div>
            <input type="hidden" name="id_city" value="{{ !empty($city) ? ___decrypt($city->id_city) : '' }}">
            <input type="hidden" name="action" value="submit">
            <div class="col-md-2 form-group">
                <input type="button" class="btn btn-default btn-block" value="Save" data-request="inline-submit" data-target="[role=form-add-city]">
            </div>
        </div>
    </form>
</div>
<div class="clearfix"></div>