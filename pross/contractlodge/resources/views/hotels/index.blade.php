@extends('spark::layouts.app')

@section('content')
<hotels :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <div class="card card-default">
                    <div class="card-header">
                        <a href="{{ route('hotels.create') }}" class="btn btn-primary btn-sm float-right mr-3" role="button" dusk="add-hotel" dusk="add-hotel" data-offline="disabled">
                            <i class="fa fa-plus"></i> {{__('Add Hotel')}}
                        </a>

                        @isset($showArchived)
                            <a href="{{ route('hotels.index') }}" class="btn btn-success btn-sm float-right mr-3" role="button">
                                <i class="fa fa-bolt mr-2"></i> {{ __('View Active Hotels')}}
                            </a>
                        @else
                            <a href="{{ route('hotels.archived') }}" class="btn btn-secondary btn-sm float-right mr-3" role="button" dusk="view-hotel-archive">
                                <i class="fa fa-archive mr-2"></i> {{ __('View Archived Hotels')}}
                            </a>
                        @endif

                        @isset($showArchived)
                            {{__('Archived')}}
                        @else
                            {{__('Active')}}
                        @endif

                        {{__('Hotels')}}
                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            @if (! $hotels->count())
                                @isset($showArchived)
                                    <div class="col-sm-12"><p>There are currently no archived hotels.</p></div>
                                @else
                                    <div class="col-sm-12"><p>No hotels found. <a href="{{ route('hotels.create') }}">Click here</a> to add a hotel.</p></div>
                                @endif
                            @else
                                <div class="col-sm-12">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Hotel</th>
                                                <th>City</th>
                                                <th>State/Region</th>
                                                <th>Country</th>
                                                <th class="text-right">Num Races</th>
                                                <th class="text-right">Balance</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($hotels as $hotel)
                                                <tr>
                                                    <td>
                                                        @isset($showArchived)
                                                            {{ $hotel->name }}
                                                        @else
                                                            <a href="{{ route('hotels.show', ['hotel' => $hotel->id]) }}">{{ $hotel->name }}</a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $hotel->city }}</td>
                                                    <td>{{ $hotel->region }}</td>
                                                    <td>{{ $hotel->country->name }}</td>
                                                    <td class="text-right">{{ $hotel->races->count() }}</td>
                                                    <td class="text-right">
                                                        (coming soon)
                                                    </td>
                                                    <td class="text-center">
                                                        @isset($showArchived)
                                                            <form action="{{ route('hotels.unarchive', ['hotel_id' => $hotel->id]) }}" method="POST">
                                                                {{ method_field('PUT') }}
                                                                {{ csrf_field() }}
                                                                <button onclick="return confirm('Are you sure you want to unarchive this hotel?');"
                                                                    class="btn btn-success btn-sm" dusk="hotel-unarchive">
                                                                    <i class="fa fa-undo mr-2"></i> Unarchive
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('hotels.destroy', ['hotel' => $hotel->id]) }}" method="POST">
                                                                {{ method_field('DELETE') }}
                                                                {{ csrf_field() }}
                                                                <button onclick="return confirm('Are you sure you want to archive this hotel?');"
                                                                    class="btn btn-secondary btn-sm" dusk="hotel-archive">
                                                                    <i class="fa fa-archive mr-2"></i> Archive
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</hotels>
@endsection
