@extends('spark::layouts.app')

@section('content')
<clients :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                {{-- Support flash messaging using URL querystring params --}}
                @include('partials.common.flash-url-message')

                <div class="card card-default">
                    <div class="card-header">
                        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm float-right mr-3" role="button" dusk="add-client" data-offline="disabled">
                            <i class="fa fa-plus"></i> {{__('Add Client')}}
                        </a>

                        @isset($showArchived)
                            <a href="{{ route('clients.index') }}" class="btn btn-success btn-sm float-right mr-3" role="button">
                                <i class="fa fa-bolt mr-2"></i> {{ __('View Active Clients')}}
                            </a>
                        @else
                            <a href="{{ route('clients.archived') }}" class="btn btn-secondary btn-sm float-right mr-3"
                                role="button" dusk="view-client-archive">
                                <i class="fa fa-archive mr-2"></i> {{ __('View Archived Clients')}}
                            </a>
                        @endif

                        @isset($showArchived)
                            {{__('Archived')}}
                        @else
                            {{__('Active')}}
                        @endif

                        {{__('Clients')}}
                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            @if (! $clients->count())
                                @isset($showArchived)
                                    <div class="col-sm-12"><p>There are currently no archived clients.</p></div>
                                @else
                                    <div class="col-sm-12">
                                        <p>
                                            No clients found.
                                            <a href="{{ route('clients.create') }}">Click here</a> to add a client.
                                        </p>
                                    </div>
                                @endif
                            @else
                                <div class="col-sm-12">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Client</th>
                                                <th class="text-right">Num Invoices/Confirmations</th>
                                                <th class="text-right">Invoice Amount</th>
                                                <th class="text-right">Amount Paid</th>
                                                <th class="text-right">Outstanding</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($clients as $client)
                                                <tr>
                                                    <td>
                                                        @isset($showArchived)
                                                            {{ $client->name }}
                                                        @else
                                                            <a href="{{ route('clients.show', [
                                                                'client' => $client->id
                                                                ]) }}">{{ $client->name }}</a>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        {{ ($client->invoices->count() + $client->confirmations->count()) }}
                                                    </td>
                                                    <td class="text-right">
                                                        (coming soon)
                                                    </td>
                                                    <td class="text-right">
                                                        (coming soon)
                                                    </td>
                                                    <td class="text-right">
                                                        (coming soon)
                                                    </td>
                                                    <td class="text-center">
                                                        @isset($showArchived)
                                                            <form action="{{ route('clients.unarchive', [
                                                                'client_id' => $client->id
                                                                ]) }}" method="POST">
                                                                {{ method_field('PUT') }}
                                                                {{ csrf_field() }}
                                                                <button onclick="return confirm('Are you sure you want to unarchive this client?');"
                                                                    class="btn btn-success btn-sm" dusk="client-unarchive">
                                                                    <i class="fa fa-undo mr-2"></i> Unarchive
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('clients.destroy', ['client' => $client->id]) }}" method="POST">
                                                                {{ method_field('DELETE') }}
                                                                {{ csrf_field() }}
                                                                <button onclick="return confirm('Are you sure you want to archive this client?');"
                                                                    class="btn btn-secondary btn-sm" dusk="client-archive">
                                                                    <i class="fa fa-archive mr-2"></i> Archive
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Totals</strong></td>
                                                <td class="text-right"><strong>(coming soon)</strong></td>
                                                <td class="text-right"><strong>(coming soon)</strong></td>
                                                <td class="text-right"><strong>(coming soon)</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</clients>
@endsection
