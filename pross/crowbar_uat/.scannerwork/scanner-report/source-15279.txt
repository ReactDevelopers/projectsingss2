<div style="padding-top:10px;">
    <form role="form-add-skill" action="{{url(sprintf("%s/%s/%s",ADMIN_FOLDER,'general','skill/add'))}}" method="post">
        <div class="col-md-1 form-group">
            <i class="fa fa-save fa-2x"></i>
        </div>
        <div class="col-md-4 form-group">
            <div>
                <select class="form-control" name="industry_id">
                    {!! ___dropdown_options($subindustries_name,trans("admin.A0018"),!empty($skill) ? $skill->industry_id : '') !!}
                </select>
            </div>
        </div>                                            
        <div class="col-md-5 form-group">
            <input type="text" class="form-control" name="skill_name" value="{{ !empty($skill) ? $skill->skill_name : '' }}" placeholder="{{ trans('admin.A0051') }}" style="width:100%;"/>
        </div>
        <input type="hidden" name="id_skill" value="{{ !empty($skill) ? $skill->id_skill : '' }}">
        <input type="hidden" name="action" value="submit">
        <div class="col-md-2 form-group">
            <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-skill]">Save</button>
        </div>
    </form>
</div>
<div class="clearfix"></div>