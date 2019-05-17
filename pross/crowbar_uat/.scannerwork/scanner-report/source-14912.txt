
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Logged Google User List</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-danger" href="{{ route('glogin') }}"> Login with Google</a>
            </div>
        </div>
    </div>
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
        </tr>
    @foreach ($users as $user)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $user->name }}</td> 
    </tr>
    @endforeach
    </table>
    {!! $users->render() !!}
