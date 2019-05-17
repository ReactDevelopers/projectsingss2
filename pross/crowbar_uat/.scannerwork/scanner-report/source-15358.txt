<div class="panel-body">
    <div class="form-group">
        <label for="name">Industry Affiliations</label><br />
        @if(!empty(array_column($user['industry'],'name')))
            {!! ___tags(array_column($user['industry'],'name'),'<span class="small-tags">%s</span>','') !!}
        @else
            {{ N_A }}
        @endif
    </div>
    
    <div class="form-group">
        <label for="name">Specialization</label><br />
        @if(!empty(array_column($user['subindustry'],'name')))
            {!! ___tags(array_column($user['subindustry'],'name'),'<span class="small-tags">%s</span>','') !!}
        @else
            {{ N_A }}
        @endif
    </div>

    <div class="form-group">
        <label for="name">Skill</label><br>
        @if(!empty($user['skills']))
            {!! ___tags(array_column($user['skills'], 'skill_name'),'<span class="small-tags">%s</span>','') !!}
        @else
            {{ N_A }}
        @endif
    </div>

    <div class="form-group">
        <label for="name">Expertise Level: </label>
        <br />{{!empty($user['expertise']) ? ucfirst($user['expertise']) : N_A}}
    </div>
    <div class="form-group">
        <label for="name">No. of Years(in Years): </label>
        <br />{{ !empty($user['experience']) ? $user['experience'] : N_A }}
    </div>

    <div class="form-group">
        <label for="name">Comments: </label>
        <br />{{!empty($user['workrate_information']) ? $user['workrate_information'] : N_A}}
    </div>

    <div class="form-group">
        <label for="name">Certificates: </label><br />
        @php
            if(!empty($user['certificate_attachments'])){
                foreach ($user['certificate_attachments'] as $item) {
                    echo sprintf(RESUME_TEMPLATE,
                        $item['id_file'],
                        url(sprintf('/download/file?file_id=%s',___encrypt($item['id_file']))),
                        asset('/'),
                        substr($item['filename'],0,3),
                        $item['size'],
                        '',
                        $item['id_file'],
                        asset('/')
                    );  
                }
            }else{
                echo N_A;
            }
        @endphp
    </div>

    <!---Education section-->
    <div class="form-group">
        <label for="name">Education</label>

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
                    @if(!empty($education_list))
                        @foreach($education_list as $e)
                            <tr id="list-{{$e['id_education']}}">
                                <td width="3%">{{$counter++}}</td>
                                <td>{{$e['college']}}</td>
                                <td>{{$e['passing_year']}}</td>
                                <td>{{$e['degree_name']}}</td>
                                <td>{{$e['degree_country_name']}}</td>
                                <td>{{$e['area_of_study']}}</td>
                                <td width="10">
                                    <a href="javascript:;" data-id-edu="{{$e['id_education']}}" data-url="{{url('administrator/talent/delete-education/'.$e['id_education'].'/'.$id_user)}}" class="badge bg-red delete-edu" >Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="6">No Records</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!---Work section-->
    <div class="form-group">
        <label for="name">Work</label>

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
                    @if(!empty($user['work_experiences']))
                        @foreach($user['work_experiences'] as $e)
                            <tr id="list-{{$e['id_experience']}}">
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
                                    <a href="javascript:;" data-url="{{url('administrator/talent/delete-experience/'.$e['id_experience'].'/'.$id_user)}}" data-id-user="{{$id_user}}" data-id-experience="{{$e['id_experience']}}" class="delete-exp badge bg-red" >Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                    <tr>
                        <th colspan="6">No Records</th>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('inlinescript')
<script type="text/javascript">
$(document).ready(function(){
    $('#industry').change(function(){
        var industry = $('#industry').val();
        var url = $('#industry').data('url');
        if(industry > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: industry}
            })
            .done(function(data) {
                $('#subindustry').html(data);
            });
        }
    });

    $('.delete-edu').click(function(){
        var id_edu = $(this).data('id-edu');
        var url = $(this).data('url');
        var res = confirm('Do you really want to continue with this action?');

        if(res){
            $.ajax({
            method: "GET",
            url: url
            })
            .done(function(data) {
                $('#list-'+id_edu).remove();
            });
        }
    });

    $('.delete-exp').click(function(){
        var id_experience = $(this).data('id-experience');
        var url = $(this).data('url');
        var res = confirm('Do you really want to continue with this action?');

        if(res){
            $.ajax({
            method: "GET",
            url: url
            })
            .done(function(data) {
                $('#list-'+id_experience).remove();
            });
        }
    });
});
</script>
@endpush
