<form role="form" method="post" enctype="multipart/form-data" action="{{ url($url.'/talent-users/'.$user['id_user'].'/update-education') }}">
    <input type="hidden" name="_method" value="PUT">
    {{ csrf_field() }}

    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>School / College</th>
                        <th>Year of Graduation</th>
                        <th>Degree</th>
                        <th>Country</th>
                        <th>Area of Study</th>
                        <th width="10">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $counter = 1;
                    @endphp
                    @foreach($education_list as $e)
                        <tr>
                            <td width="3%">{{$counter++}}</td>
                            <td>{{$e['college']}}</td>
                            <td>{{$e['passing_year']}}</td>
                            <td>{{$e['degree_name']}}</td>
                            <td>{{$e['degree_country_name']}}</td>
                            <td>{{$e['area_of_study']}}</td>
                            <td width="10">
                                <a href="javascript:;" data-id-education="{{$e['id_education']}}" data-id-user="{{$id_user}}" data-url="{{url('ajax/edit-talent-education')}}" class="badge bg-light-blue edit-edu">Edit</a>
                                <a href="{{url('talent/delete-education/'.$e['id_education'].'/'.$id_user)}}" onclick="return confirm('Do you really want to continue with this action?');" class="badge bg-red" >Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-group @if ($errors->has('college'))has-error @endif">
            <label for="name">School / College</label>
            <input type="text" class="form-control" name="college" id="college" placeholder="School / College" value="{{ old('college') }}">
            @if ($errors->first('college'))
                <span class="help-block">
                    {{ $errors->first('college')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('degree'))has-error @endif">
            <label for="name">Degree</label>
            @php
            $degree = old('degree');
            @endphp
            <select class="form-control" name="degree" id="degree" placeholder="Degree">
                <option value="">Select Degree</option>
                @foreach($db_degree as $c)
                    <option{{$degree == $c->id_degree ? ' selected="selected"' : ''}} value="{{$c->id_degree}}">{{$c->degree_name}}</option>
                @endforeach
            </select>
            @if ($errors->first('degree'))
                <span class="help-block">
                    {{ $errors->first('degree')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('passing_year'))has-error @endif">
            <label for="name">Year of Graduation</label>
            @php
            $forpassing_year = date('Y') - 10;
            $passing_year = old('passing_year');
            @endphp
            <select class="form-control" name="passing_year" id="passing_year" placeholder="Year of Graduation">
                <option value="">Select Year of Graduation</option>
                @for($x = $forpassing_year; $x <= date('Y'); $x++)
                    <option{{$passing_year == $x ? ' selected="selected"' : ''}} value="{{$x}}">{{$x}}</option>
                @endfor
            </select>
            @if ($errors->first('passing_year'))
                <span class="help-block">
                    {{ $errors->first('passing_year')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('area_of_study'))has-error @endif">
            <label for="name">Area of Study</label>
            <input type="text" class="form-control" id="area_of_study" name="area_of_study" placeholder="Area of Study" value="{{ old('area_of_study') }}">
            @if ($errors->first('area_of_study'))
                <span class="help-block">
                    {{ $errors->first('area_of_study')}}
                </span>
            @endif
        </div>

        @php
        $degree_status = old('degree_status');
        @endphp
        <div class="form-group @if ($errors->has('degree_status'))has-error @endif">
            <label for="name">Degree Status</label>
            <select class="form-control" name="degree_status" id="degree_status" placeholder="Degree Status">
                <option value="">Select Degree Status</option>
                <option{{$degree_status == 'passed' ? ' selected="selected"' : ''}} value="passed">Passed</option>
                <option{{$degree_status == 'appearing' ? ' selected="selected"' : ''}} value="appearing">Appearing</option>
            </select>
            @if ($errors->first('degree_status'))
                <span class="help-block">
                    {{ $errors->first('degree_status')}}
                </span>
            @endif
        </div>

        @php
        $degree_country = old('degree_country');
        @endphp
        <div class="form-group @if ($errors->has('degree_country'))has-error @endif">
            <label for="name">Country</label>
            <select class="form-control" name="degree_country" id="degree_country" data-url="{{ url('ajax/state-list') }}" placeholder="Country">
                <option value="">Select Country</option>
                @foreach($countries as $c)
                    <option{{ $degree_country == $c->id_country ? ' selected="selected"' : ''}} value="{{$c->id_country}}">{{$c->country_name}}</option>
                @endforeach
            </select>
            @if ($errors->first('degree_country'))
                <span class="help-block">
                    {{ $errors->first('degree_country')}}
                </span>
            @endif
        </div>
        <input type="hidden" name="id_education" id="id_education" value="{{ old('id_education') }}" />

    </div>
    <div class="panel-footer">
        <a href="{{url($backurl.'/user-list?page=talent')}}" class="btn btn-default">Back</a>
        <button type="submit" class="btn btn-default">Save</button>
    </div>
</form>
@push('inlinescript')
<script type="text/javascript">
$(document).ready(function(){
    $('.edit-edu').click(function(){
        var id_education = $(this).data('id-education');
        var id_user = $(this).data('id-user');
        var url = $(this).data('url');

        if(id_education > 0)
        {
            $.ajax({
            method: "POST",
            url: url,
            data: { id_education: id_education, id_user: id_user}
            })
            .done(function(respond) {
                $('#college').val(respond.data.college);
                $('#degree').val(respond.data.degree);
                $('#passing_year').val(respond.data.passing_year);
                $('#area_of_study').val(respond.data.area_of_study);
                $('#degree_status').val(respond.data.degree_status);
                $('#degree_country').val(respond.data.degree_country);
                $('#id_education').val(respond.data.id_education);
                $('select').trigger('change');
            });
        }
    });
});
</script>
@endpush
