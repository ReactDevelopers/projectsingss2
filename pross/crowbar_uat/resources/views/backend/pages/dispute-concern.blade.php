<div style="padding-top:10px;">
    <form role="form-add-concern" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'general/dispute-concern/add'))}}" method="post">
        <div class="clearfix">
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="en" value="{{ !empty($concern) ? $concern->en : '' }}" placeholder="ENGLISH" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="id" value="{{ !empty($concern) ? $concern->id : '' }}" placeholder="INDONESIA" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="cz" value="{{ !empty($concern) ? $concern->cz : '' }}" placeholder="MNADARIN" style="width:100%;"/>
            </div>
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="ta" value="{{ !empty($concern) ? $concern->ta : '' }}" placeholder="TAMIL" style="width:100%;"/>
            </div>
        </div>
        <div class="clearfix">
            <div class="col-md-3 form-group">
                <input type="text" class="form-control" name="hi" value="{{ !empty($concern) ? $concern->hi : '' }}" placeholder="HINDI" style="width:100%;"/>
            </div>         
            <input type="hidden" name="id_concern" value="{{ !empty($concern) ? ___encrypt($concern->id_concern) : '' }}">
            <input type="hidden" name="action" value="submit">
            <div class="col-md-3 form-group">
                <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-concern]">Save</button>
            </div>
        </div>
    </form>
</div>
<div class="clearfix"></div>
<hr style="margin-top:0;">