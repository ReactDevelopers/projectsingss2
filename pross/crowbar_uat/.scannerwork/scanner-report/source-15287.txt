<div style="padding-top:10px;">
    <form role="form-add-degree" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-degree'))}}" method="post">
        <div class="col-md-1 form-group">
            <i class="fa fa-save fa-2x"></i>
        </div>
        <div class="col-md-5 form-group">
            <input type="text" class="form-control" name="degree" value="{{ !empty($degree) ? $degree->degree_name : '' }}" placeholder="{{ trans('admin.A0039') }}" style="width:100%;"/>
        </div>
        <input type="hidden" name="id_degree" value="{{ !empty($degree) ? ___encrypt($degree->id_degree) : '' }}">
        <input type="hidden" name="action" value="submit">
        <div class="col-md-2 form-group">
            <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-degree]">Save</button>
        </div>
    </form>
</div>
<div class="clearfix"></div>