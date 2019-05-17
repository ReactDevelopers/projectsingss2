<form role="form" method="post" enctype="multipart/form-data" action="{{ url('administrator/talent-users/'.$user['id_user'].'/update-education') }}">
    <input type="hidden" name="_method" value="PUT">
    {{ csrf_field() }}

    <div class="panel-body">
        <div class="table-responsive">
            {!! $html->table(); !!}
        </div>

    </div>

</form>
@push('inlinescript')
    {!! $html->scripts() !!}
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
