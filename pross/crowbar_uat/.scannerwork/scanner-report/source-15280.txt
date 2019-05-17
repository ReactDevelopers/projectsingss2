<div style="padding-top:10px;">
    <form id="form-addcountry" action="{{url(sprintf("%s/%s",$url,'ajax/addcountry'))}}" method="post">
        <div class="col-md-1 form-group">
            <i class="fa fa-save fa-2x"></i>
        </div>
        <div class="col-md-3 form-group">
            <input type="text" class="form-control" name="iso_code" placeholder="ISO CODE" style="width:100%;"/>
        </div>
        <div class="col-md-3 form-group">
            <input type="text" class="form-control" name="phone_country_code" placeholder="PHONE CODE" style="width:100%;"/>
        </div>
        <div class="col-md-3 form-group">
            <input type="text" class="form-control" name="country_name" placeholder="COUNTRY NAME" style="width:100%;"/>
        </div>
        <div class="col-md-2 form-group">
            <input type="button" class="btn btn-default btn-block" value="Save" data-request="save" data-target="#form-addcountry">
        </div>
    </form>
</div>



<?php }else if($page == 'api'){ ?>
    <div class="col-md-12">
        <div class="row">
            {!! Form::open(array('url'=> sprintf("%s/%s",$url,'upload/collection'),'method'=>'POST', 'files'=>true)) !!}             
                {{ csrf_field() }}
                <p class="lead" style="margin-bottom:10px;">API Information</p><hr style="margin-top:5px;">
                <div class="col-md-6">
                    <div class="form-group @if ($errors->has('file'))has-error @endif">
                        <label>Upload Collection:</label>
                        {!! Form::file('file',['class' => 'form-control']) !!}
                        @if ($errors->has('file'))
                            <span class="help-block">
                                {{ $errors->first('file')}}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-default" type="submit" style="margin-top:23px;">Upload</button>
                </div>
            {!! Form::close() !!}
        </div>
        <div class="row">
            <p class="lead" style="margin-bottom:10px;">API Reference</p><hr style="margin:5px 0;">
            <div class="table-responsive">
                <table class="table table-striped" id="table-messages">
                    <thead>
                        <tr>
                            <th colspan="4" class="row" style="padding-bottom:10px;">
                                <form id="form-addmessage" action="{{url(sprintf("%s/%s",$url,'ajax/addmessage'))}}" method="post">
                                    <div class="col-md-1 form-group">
                                        <i class="fa fa-save fa-2x"></i>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <input type="text" class="form-control" name="message_code" placeholder="CODE" style="width:100%;"/>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <input type="text" class="form-control" name="message_description" placeholder="DESCRIPTION" style="width:100%;"/>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <input type="text" class="form-control" name="message_section" placeholder="SECTION" style="width:100%;"/>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <input type="button" class="btn btn-default btn-block" value="Save" data-request="save" data-target="#form-addmessage">
                                    </div>
                                </form>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>MESSAGE CODE</th>
                            <th>MESSAGE DESCRIPTION</th>
                            <th>SECTION</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
<?php if($page == 'states'){?>
    <div class="table-responsive">
        <div style="padding-top:10px;">
            <form role ="form-add-state" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-state'))}}" method="post">
                <div class="col-md-1 form-group">
                    <i class="fa fa-save fa-2x"></i>
                </div>
                <div class="col-md-3 form-group">
                    <div>
                        <select class="form-control" name="country">
                            {!! ___dropdown_options($countries,trans("admin.A0008")) !!}
                        </select>
                    </div>
                </div>
                <div class="col-md-3 form-group">
                    <input type="text" class="form-control" name="state" placeholder="State name" style="width:100%;"/>
                </div>
                <div class="col-md-3 form-group">
                    <input type="text" class="form-control" name="iso_code" placeholder="ISO CODE" style="width:100%;"/>
                </div>
                <input type="hidden" name="record_id">
                <div class="col-md-2 form-group">
                    <input type="button" class="btn btn-default btn-block" value="Save" data-request="inline-submit" data-target="[role=form-add-state]">
                </div>
            </form>
        </div>
        <div class="clearfix"></div>
        <hr style="margin-top:0;">
        {!! $html->table(); !!}
    </div>
    <div class="clearfix"></div>                                    
<?php }else if($page == 'city'){?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">City Listing</p><hr style="margin:5px 0;">
                <div class="table-responsive">
                    <div style="padding-top:10px;">
                        <form role="form-add-city" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-city'))}}" method="post">
                            <div class="col-md-1 form-group">
                                <i class="fa fa-save fa-2x"></i>
                            </div>
                            <div class="col-md-4 form-group">
                                <div>
                                    <select class="form-control" name="state">
                                        {!! ___dropdown_options($states,trans("admin.A0009")) !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5 form-group">
                                <input type="text" class="form-control" name="city" placeholder="CITY" style="width:100%;"/>
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="button" class="btn btn-default btn-block" value="Save" data-request="inline-submit" data-target="[role=form-add-city]">
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
<?php }else if($page == 'industry'){?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">Industry Listing</p><hr style="margin:5px 0;">
                <div class="table-responsive">
                    <div style="padding-top:10px;">
                        <form role="form-add-industry" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-industry'))}}" method="post">
                            <div class="col-md-1 form-group">
                                <i class="fa fa-save fa-2x"></i>
                            </div>
                            <div class="col-md-3 form-group">
                                <input type="text" class="form-control" name="industry" placeholder="Industry Name" style="width:100%;"/>
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="button" class="btn btn-default btn-block" value="Save" data-request="inline-submit" data-target="[role=form-add-industry]">
                            </div>                                            
                        </form>
                    </div>
                    <div class="clearfix"></div>
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>                     
<?php }else if($page == 'sub_industry'){?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">Sub Industry Listing</p><hr style="margin:5px 0;">
                <div class="table-responsive">
                    <div style="padding-top:10px;">
                        <form role="form-add-sub-industry" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-sub-industry'))}}" method="post">
                            <div class="col-md-1 form-group">
                                <i class="fa fa-save fa-2x"></i>
                            </div>
                            <div class="col-md-4 form-group">
                                <div>
                                    <select class="form-control" name="industry_parent_id">
                                        {!! ___dropdown_options($industries,trans("admin.A0018")) !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5 form-group">
                                <input type="text" class="form-control" name="industry" placeholder="{{ trans("admin.A0021") }}" style="width:100%;"/>
                            </div>
                            <input type="hidden" name="action" value="submit">
                            <div class="col-md-2 form-group">
                                <input type="button" class="btn btn-default btn-block" value="Save" data-request="inline-submit" data-target="[role=form-add-sub-industry]">
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
<?php }else if($page == 'abusive_words'){?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">Abusive Word Listing</p><hr style="margin:5px 0;">
                <div class="table-responsive">
                    <div style="padding-top:10px;">
                        <form role="form-add-abusive-words" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-abusive-word'))}}" method="post">
                            <div class="col-md-1 form-group">
                                <i class="fa fa-save fa-2x"></i>
                            </div>
                            <div class="col-md-5 form-group">
                                <input type="text" class="form-control" name="abusive_word" placeholder="{{ trans("admin.A0033") }}" style="width:100%;"/>
                            </div>
                            <input type="hidden" name="action" value="submit">
                            <div class="col-md-2 form-group">
                                <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-abusive-words]">Save</button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>             
<?php }else if($page == 'degree'){?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">{{ sprintf(trans('admin.A0035'), trans('admin.A0039')) }}</p><hr style="margin:5px 0;">
                <div class="table-responsive">
                    <div style="padding-top:10px;">
                        <form role="form-add-degree" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-degree'))}}" method="post">
                            <div class="col-md-1 form-group">
                                <i class="fa fa-save fa-2x"></i>
                            </div>
                            <div class="col-md-5 form-group">
                                <input type="text" class="form-control" name="degree" placeholder="{{ trans('admin.A0039') }}" style="width:100%;"/>
                            </div>
                            <input type="hidden" name="action" value="submit">
                            <div class="col-md-2 form-group">
                                <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-degree]">Save</button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>           
<?php }else if($page == 'certificate'){?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">{{ sprintf(trans('admin.A0035'), trans('admin.A0043')) }}</p><hr style="margin:5px 0;">
                <div class="table-responsive">
                    <div style="padding-top:10px;">
                        <form role="form-add-certificate" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-certificate'))}}" method="post">
                            <div class="col-md-1 form-group">
                                <i class="fa fa-save fa-2x"></i>
                            </div>
                            <div class="col-md-5 form-group">
                                <input type="text" class="form-control" name="certificate_name" placeholder="{{ trans('admin.A0043') }}" style="width:100%;"/>
                            </div>
                            <input type="hidden" name="action" value="submit">
                            <div class="col-md-2 form-group">
                                <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-certificate]">Save</button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>                                         
<?php }else if($page == 'college'){?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">{{ sprintf(trans('admin.A0035'), trans('admin.A0047')) }}</p><hr style="margin:5px 0;">
                <div class="table-responsive">
                    <div style="padding-top:10px;">
                        <form role="form-add-college" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-college'))}}" method="post">
                            <div class="col-md-1 form-group">
                                <i class="fa fa-save fa-2x"></i>
                            </div>
                            <div class="col-md-5 form-group">
                                <input type="text" class="form-control" name="college_name" placeholder="{{ trans('admin.A0047') }}" style="width:100%;"/>
                            </div>
                            <input type="hidden" name="action" value="submit">
                            <div class="col-md-2 form-group">
                                <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-college]">Save</button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>                                                             
<?php }else if($page == 'skill'){?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">{{ sprintf(trans('admin.A0035'), trans('admin.A0051')) }}</p><hr style="margin:5px 0;">
                <div class="table-responsive">
                    <div style="padding-top:10px;">
                        <form role="form-add-skill" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'add-skill'))}}" method="post">
                            <div class="col-md-1 form-group">
                                <i class="fa fa-save fa-2x"></i>
                            </div>
                            <div class="col-md-4 form-group">
                                <div>
                                    <select class="form-control" name="industry_id">
                                        {!! ___dropdown_options($subindustries_name,trans("admin.A0018")) !!}
                                    </select>
                                </div>
                            </div>                                            
                            <div class="col-md-5 form-group">
                                <input type="text" class="form-control" name="skill_name" placeholder="{{ trans('admin.A0051') }}" style="width:100%;"/>
                            </div>
                            <input type="hidden" name="action" value="submit">
                            <div class="col-md-2 form-group">
                                <button type="button" class="btn btn-default btn-block" data-request="inline-submit" data-target="[role=form-add-skill]">Save</button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"></div>
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>                                                                                 
<?php }else if($page == 'countries'){ ?>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <p class="lead" style="margin-bottom:10px;">Country Listing</p><hr style="margin:5px 0;">
                <div class="clearfix"></div>
                <div class="table-responsive">
                    <hr style="margin-top:0;">
                    {!! $html->table(); !!}
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } ?>
                    