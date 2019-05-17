<form role="form" method="post" enctype="multipart/form-data" action="{{ url('administrator/talent-users/'.$user['id_user'].'/update-education') }}">
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
                </tbody>
            </table>
        </div>

    </div>

</form>
@push('inlinescript')
<script type="text/javascript">
$(document).ready(function(){
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
});
</script>
@endpush
