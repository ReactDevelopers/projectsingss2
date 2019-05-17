@extends('spark::layouts.app')

@section('content')
<races :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <div class="card card-default">
                    <div class="card-header">
                        <a href="{{ route('races.create') }}" class="btn btn-primary btn-sm float-right mr-3" role="button" dusk="add-race" data-offline="disabled">
                            <i class="fa fa-plus"></i> {{__('Add Race')}}
                        </a>

                        @isset($showArchived)
                            <a href="{{ route('races.index') }}" class="btn btn-success btn-sm float-right mr-3" role="button">
                                <i class="fa fa-bolt mr-2"></i> {{ __('View Active Races')}}
                            </a>
                        @else
                            <a href="{{ route('races.archived') }}" class="btn btn-secondary btn-sm float-right mr-3" role="button" dusk="view-race-archive">
                                <i class="fa fa-archive mr-2"></i> {{ __('View Archived Races')}}
                            </a>
                        @endif

                        @isset($showArchived)
                            {{__('Archived')}}
                        @else
                            {{__('Active')}}
                        @endif

                        {{__('Races')}}
                    </div>

                    <div class="card-body">
                        @if (! $races->count())
                            @isset($showArchived)
                                <div class="col-sm-12"><p>There are currently no archived races.</p></div>
                            @else
                                <div class="col-sm-12"><p>No races found. <a href="{{ route('races.create') }}">Click here</a> to add a race.</p></div>
                            @endif
                        @else
                            <table class="table table-sm table-striped override-table">
                                <thead>
                                    <tr>
                                        <th>Race</th>
                                        <th>Start On</th>
                                        <th>End On</th>
                                        <th>Default Currency</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($races as $race)
                                        <tr>
                                            <td>
                                                @isset($showArchived)
                                                    {{ $race->full_name }}
                                                @else
                                                    <a href="{{ route('races.show', ['race' => $race->id]) }}" dusk="race-{{ $race->id }}">{{ $race->full_name }}</a>
                                                @endif
                                            </td>
                                            <td>{{ $race->friendly_start_on }}</td>
                                            <td>{{ $race->friendly_end_on }}</td>
                                            <td>{{ $race->currency->name }}</td>
                                            <td class="text-center">
                                                @isset($showArchived)
                                                    <form action="{{ route('races.unarchive', ['race_id' => $race->id]) }}" method="POST">
                                                        {{ method_field('PUT') }}
                                                        {{ csrf_field() }}
                                                        <button onclick="return confirm('Are you sure you want to unarchive this race?');"
                                                            class="btn btn-success btn-sm" dusk="race-unarchive">
                                                            <i class="fa fa-undo mr-2"></i> Unarchive
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('races.destroy', ['race' => $race->id]) }}" method="POST">
                                                        {{ method_field('DELETE') }}
                                                        {{ csrf_field() }}
                                                        <button onclick="return confirm('Are you sure you want to archive this race?');"
                                                            class="btn btn-secondary btn-sm" dusk="race-archive">
                                                            <i class="fa fa-archive mr-2"></i> Archive
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</races>
@endsection
