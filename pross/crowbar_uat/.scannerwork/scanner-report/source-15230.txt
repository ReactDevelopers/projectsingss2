    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Job Title</th>
                        <th>Company Name</th>
                        <th>Start Date</th>
                        <th>Currently Working?</th>
                        <th>Type of Job</th>
                        <th>End Date</th>
                        <th>Country</th>
                        <th>State/ Province</th>
                        <th width="10">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $counter = 1;
                    @endphp
                    @foreach($work_experience_list as $e)
                        <tr>
                            <td width="3%">{{$counter++}}</td>
                            <td>{{$e['jobtitle']}}</td>
                            <td>{{$e['company_name']}}</td>
                            <td>{{$e['joining']}}</td>

                            <td>{{$e['is_currently_working'] == 'yes' ? 'Yes' : 'No'}}</td>
                            <td>{{$e['job_type'] == 'fulltime' ? 'Fulltime' : 'Temporary'}}</td>
                            <td>{{$e['joining']}}</td>

                            <td>{{$e['country_name']}}</td>
                            <td>{{$e['state_name']}}</td>
                            <td width="10">
                                <a href="javascript:;" data-id-user="{{$id_user}}" data-id-experience="{{$e['id_experience']}}" data-url="{{url('ajax/edit-talent-experience')}}" class="edit-exp badge bg-light-blue">Edit</a>
                                <a href="{{$url}}" onclick="return confirm('Do you really want to continue with this action?');" class="badge bg-red" >Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <form role="form" method="post" enctype="multipart/form-data" action="{{ url($url.'/talent-users/'.$user['id_user'].'/update-education') }}">
        <input type="hidden" name="_method" value="PUT">
        {{ csrf_field() }}

            <div class="form-group @if ($errors->has('jobtitle'))has-error @endif">
                <label for="name">Job Title</label>
                <input type="text" class="form-control" name="jobtitle" id="jobtitle" placeholder="Job Title" value="{{ old('jobtitle') }}">
                @if ($errors->first('jobtitle'))
                    <span class="help-block">
                        {{ $errors->first('jobtitle')}}
                    </span>
                @endif
            </div>
            <div class="form-group @if ($errors->has('company_name'))has-error @endif">
                <label for="name">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name" value="{{ old('company_name') }}">
                @if ($errors->first('company_name'))
                    <span class="help-block">
                        {{ $errors->first('company_name')}}
                    </span>
                @endif
            </div>

            @php
            $joining_month = old('joining_month');
            @endphp
            <div class="form-group @if ($errors->has('joining_month'))has-error @endif">
                <label for="name">Start Date</label>
                <select class="form-control" name="joining_month" id="joining_month" placeholder="Degree">
                    <option value="">Select Month</option>
                    @for($month=1; $month<=12; $month++)
                        @php
                        $month_num = date('m', mktime(0, 0, 0, $month, 1, 2011));
                        $month_name = date('F', mktime(0, 0, 0, $month, 1, 2011));
                        @endphp
                        <option{{$joining_month == $month_num ? ' selected="selected"' : '' }} value="{{$month_num}}">{{$month_name}}</option>
                    @endfor
                </select>
                @if ($errors->first('joining_month'))
                    <span class="help-block">
                        {{ $errors->first('joining_month')}}
                    </span>
                @endif
            </div>

            <div class="form-group @if ($errors->has('joining_year'))has-error @endif">
                <label for="name">Start Year</label>
                @php
                $mjoining_year = date('Y') - 10;
                $joining_year = old('joining_year');
                @endphp
                <select class="form-control" name="joining_year" id="joining_year" placeholder="Year of Graduation">
                    <option value="">Select Year</option>
                    @for($x = $mjoining_year; $x <= date('Y'); $x++)
                        <option{{$joining_year == $x ? ' selected="selected"' : ''}} value="{{$x}}">{{$x}}</option>
                    @endfor
                </select>
                @if ($errors->first('joining_year'))
                    <span class="help-block">
                        {{ $errors->first('joining_year')}}
                    </span>
                @endif
            </div>

            <div class="form-group @if ($errors->has('is_currently_working'))has-error @endif">
                <label for="name">Currently Working</label>
                @php
                $is_currently_working = old('is_currently_working');
                @endphp

                <div class="radio radio-inline">
                    <input type="radio" id="expert-yes" class="" name="is_currently_working"
                placeholder="Currently Working" value="yes"{{$is_currently_working == 'yes' ? ' checked="checked"' : ''}}>
                    <label for="expert-yes">Yes</label>
                </div>

                <div class="radio radio-inline">
                    <input type="radio" id="expert-no" class="" name="is_currently_working"
                placeholder="Currently Working" value="no"{{$is_currently_working == 'no' ? ' checked="checked"' : ''}}>
                    <label for="expert-no">No</label>
                </div>

                @if ($errors->first('is_currently_working'))
                    <span class="help-block">
                        {{ $errors->first('is_currently_working')}}
                    </span>
                @endif
            </div>

            <div class="form-group @if ($errors->has('job_type'))has-error @endif">
                <label for="name">Job Type</label>
                @php
                $job_type = old('job_type');
                @endphp

                <div class="radio radio-inline">
                    <input type="radio" id="expert-fulltime" class="" name="job_type"
                placeholder="Job Type" value="fulltime"{{$job_type == 'fulltime' ? ' checked="checked"' : ''}}>
                    <label for="expert-fulltime">Full Time</label>
                </div>
                <div class="radio radio-inline">
                    <input type="radio" id="expert-temporary" class="" name="job_type" placeholder="Job Type" value="temporary"{{$job_type == 'temporary' ? ' checked="checked"' : ''}}>
                    <label for="expert-temporary">Temporary</label>
                </div>

                @if ($errors->first('job_type'))
                    <span class="help-block">
                        {{ $errors->first('job_type')}}
                    </span>
                @endif
            </div>

            <div id="relieving-area">
                @php
                $relieving_month = old('relieving_month');
                @endphp
                <div class="form-group @if ($errors->has('relieving_month'))has-error @endif">
                    <label for="name">Relieving Month</label>
                    <select class="form-control" name="relieving_month" id="relieving_month" placeholder="Relieving Month">
                        <option value="">Select Month</option>
                        @for($month=1; $month<=12; $month++)
                            @php
                            $month_num = date('m', mktime(0, 0, 0, $month, 1, 2011));
                            $month_name = date('F', mktime(0, 0, 0, $month, 1, 2011));
                            @endphp
                            <option{{$relieving_month == $month_num ? ' selected="selected"' : ''}} value="{{$month_num}}">{{$month_name}}</option>
                        @endfor
                    </select>
                    @if ($errors->first('relieving_month'))
                        <span class="help-block">
                            {{ $errors->first('relieving_month')}}
                        </span>
                    @endif
                </div>

                <div class="form-group @if ($errors->has('relieving_year'))has-error @endif">
                    <label for="name">Relieving Year</label>
                    @php
                    $mrelieving_year = date('Y') - 10;
                    $relieving_year = old('relieving_year');
                    @endphp
                    <select class="form-control" name="relieving_year" id="relieving_year" placeholder="Relieving Year">
                        <option value="">Select Year</option>
                        @for($x = $mrelieving_year; $x <= date('Y'); $x++)
                            <option{{$relieving_year == $x ? ' selected="selected"' : ''}} value="{{$x}}">{{$x}}</option>
                        @endfor
                    </select>
                    @if ($errors->first('relieving_year'))
                        <span class="help-block">
                            {{ $errors->first('relieving_year')}}
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group @if ($errors->has('country'))has-error @endif">
                <label for="name">Country</label>
                @php
                $country = old('country');
                @endphp
                <select class="form-control" name="country" id="country" data-url="{{ url('ajax/state-list') }}" placeholder="Country">
                    <option value="">Select Country</option>
                    @foreach($countries as $c)
                        <option {{$country==$c->id_country?' selected="selected"':''}} value="{{$c->id_country}}">{{$c->country_name}}</option>
                    @endforeach
                </select>
                @if ($errors->first('country'))
                    <span class="help-block">
                        {{ $errors->first('country')}}
                    </span>
                @endif
            </div>
            <div class="form-group @if ($errors->has('state'))has-error @endif">
                <label for="name">State</label>
                @php
                $state = old('state');
                @endphp
                <select class="form-control" name="state" id="state" placeholder="State">
                    <option value="">Select State/ Province</option>
                </select>
                @if ($errors->first('state'))
                    <span class="help-block">
                        {{ $errors->first('state')}}
                    </span>
                @endif
            </div>
            @php
            if(old('cover_letter_description')){
                $cover_letter_description = old('cover_letter_description');
            }
            else{
                $cover_letter_description = $user['cover_letter_description'];
            }
            @endphp
            <div class="form-group @if ($errors->has('area_of_study'))has-error @endif">
                <label for="name">Cover Letter/ Other Description</label>
                <textarea type="text" class="form-control" name="cover_letter_description" placeholder="Cover Letter/ Other Description">{{ $cover_letter_description }}</textarea>
                @if ($errors->first('cover_letter_description'))
                    <span class="help-block">
                        {{ $errors->first('cover_letter_description')}}
                    </span>
                @endif
            </div>

        </form>

        <form class="form-horizontal" role="doc-submit" action="{{url('administrator/talent/doc-submit?id_user='.$id_user)}}" method="POST" accept-charset="utf-8">
            <div class="form-group">
                <label for="name">Attach Documents</label>

                <div class="upload-box">
                    @foreach($certificate_attachments as $att)
                        @php
                        $id_file = ___encrypt($att['id_file']);
                        @endphp
                        <div class="uploaded-docx clearfix" id="files-{{$att['id_file']}}">
                            <a href="{{url('download/file?file_id='.$id_file)}}" class="download-docx">
                                <img src="{{url('images/attachment-icon.png')}}">
                                <div class="upload-info">
                                    <p>{{$att['filename']}}</p>
                                    <span>{{$att['size']}}</span>
                                </div>
                            </a>
                            <a href="javascript:void(0);" data-id-user="{{$id_user}}" data-url="{{url('ajax/delete-user-document')}}" title="Delete" data-file-id="{{$att['id_file']}}" class="delete-docx">
                                <img src="{{url('images/close-icon-md.png')}}">
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="fileUpload upload-docx"><span>{{trans('website.W0113')}}</span><input type="file" name="file" class="upload" data-request="doc-submit" data-toadd =".upload-box" data-target='[role="doc-submit"]'/></div>
                <span class="upload-hint">{{trans('website.W0114')}}</span>

            </div>
        </form>

    </div>
    <div class="panel-footer">
        <a href="{{url($backurl.'/user-list?page=talent')}}" class="btn btn-default">Back</a>
        <button type="button" class="btn btn-default">Save</button>
    </div>

@push('inlinescript')
<script type="text/javascript">
$(document).ready(function(){
    $('#country').change(function(){
        var id_country = $('#country').val();
        var url = $('#country').data('url');
        if(id_country > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: id_country}
            })
            .done(function(data) {
                $('#state').html(data);
            });
        }
    });

    $('.radio-inline').click(function(){
        var val = $('input[name=is_currently_working]:radio:checked').val();
        if (val == 'yes'){
            $('#relieving-area').hide();
        }
        else{
            $('#relieving-area').show();
        }
    });

    $('.edit-exp').click(function(){
        var id_experience = $(this).data('id-experience');
        var id_user = $(this).data('id-user');
        var url = $(this).data('url');

        if(id_experience > 0)
        {
            $.ajax({
            method: "POST",
            url: url,
            data: { id_experience: id_experience, id_user: id_user}
            })
            .done(function(respond) {
                $('#jobtitle').val(respond.data.jobtitle);
                $('#company_name').val(respond.data.company_name);
                $('#joining_month').val(respond.data.joining_month);
                $('#joining_year').val(respond.data.joining_year);

                if(respond.data.is_currently_working == 'yes'){
                    $('#expert-yes').prop("checked", true)
                    $('#relieving-area').hide();
                }
                else{
                    $('#expert-no').prop("checked", true)
                    $('#relieving-area').show();
                }
                if(respond.data.job_type == 'fulltime'){
                    $('#expert-fulltime').prop("checked", true)
                }
                else{
                    $('#expert-temporary').prop("checked", true)
                }
                $('#relieving_month').val(respond.data.relieving_month);
                $('#relieving_year').val(respond.data.relieving_year);
                $('#country').val(respond.data.country);
                $('select').trigger('change');

                var id_country = respond.data.country;
                var id_state = respond.data.state;
                var url = $('#country').data('url');
                if(id_country > 0){
                    $.ajax({
                    method: "POST",
                    url: url,
                    data: { record_id: id_country}
                    })
                    .done(function(data) {
                        $('#state').html(data);
                        $('#state').val(id_state);
                    });
                }
            });
        }
    });

    $('.delete-docx').click(function(){
        var res = confirm('Do you realy want to delete the document?');

        if(res){
            var id_user = $(this).data('id-user');
            var url = $(this).data('url');
            var file_id = $(this).data('file-id');
            $.ajax({
            method: "POST",
            url: url,
            data: {id_file: file_id, id_user: id_user}
            })
            .done(function(data) {
                $('#files-'+file_id).remove();
            });
        }
    });
});
</script>
@endpush

