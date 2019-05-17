<div style="padding-top:10px;">
    <form role="form-add-college" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-college'))}}" method="post">
        <div class="col-md-1 form-group">
            <i class="fa fa-save fa-2x"></i>
        </div>
        <div class="col-md-5 form-group">
            <input type="text" class="form-control" name="college_name" value="{{ !empty($college) ? $college->college_name : '' }}" placeholder="{{ trans('admin.A0047') }}" style="width:100%;"/>
        </div>
        <input type="hidden" name="id_college" value="{{ !empty($college) ? $college->id_college : ''  }}">
        <input type="hidden" name="action" value="submit">
        <div class="col-md-2 form-group">
            <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-college]">Save</button>
        </div>
    </form>
</div>
<div class="clearfix"></div>