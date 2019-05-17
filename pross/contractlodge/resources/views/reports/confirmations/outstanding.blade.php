@extends('spark::layouts.app')

@section('content')
<reports-confirmation :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <div class="card card-default">
                    <div class="card-header">
                        Reports /
                        {{__('Outstanding Confirmations')}}
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            @empty ($confirmations)
                                <div class="col-sm-12"><p>There are currently no outstanding confirmaitons.</p></div>
                            @else
                                <div class="col-sm-12">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Confirmation Nº</th>
                                                <th>Race</th>
                                                <th>Client</th>
                                                <th>Sent on</th>
                                                <th>Expires on</th>
                                                <th class="text-right">Amount</th>
                                                <th class="text-center">Rooms/Nts</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($confirmations as $confirmation)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('races.hotels.clients.confirmations.show', [
                                                            'race' => $confirmation->race_hotel->race->id,
                                                            'hotel' => $confirmation->race_hotel->hotel->id,
                                                            'client' => $confirmation->client->id,
                                                            'confirmation' => $confirmation->id
                                                            ]) }}">
                                                            {{ $confirmation->race_hotel->race->race_code }}-{{ $confirmation->id}}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{ $confirmation->race_hotel->race->name }}
                                                    </td>
                                                    <td>
                                                        {{ $confirmation->client->name }}
                                                    </td>
                                                    <td>
                                                        {{ $confirmation->friendly_sent_on }}
                                                    </td>
                                                    <td>
                                                        {{ $confirmation->friendly_expires_on}}
                                                    </td>
                                                    <td class="text-right">
                                                        @money(get_confirmation_total_amount($confirmation), $confirmation->currency->symbol)
                                                        ({{ $confirmation->currency->name }})
                                                    </td>
                                                    <td class="text-center">
                                                        {{ get_confirmation_total_rooms($confirmation) }}
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
</reports-confirmation>
@endsection
