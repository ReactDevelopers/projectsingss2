@component('mail::message')
# Confirmations Expiring Today ({{ $today }})

@foreach($confirmations as $confirmation)
@component('mail::panel')
**{{ $confirmation->client->name }}**
at {{ $confirmation->race_hotel->hotel->name }}
(<a href="{{ route('races.hotels.clients.confirmations.show', [
    'race' => $confirmation->race_hotel->race->id,
    'hotel' => $confirmation->race_hotel->hotel->id,
    'client' => $confirmation->client->id,
    'confirmation' => $confirmation->id
    ]) }}">{{ $confirmation->race_hotel->race->race_code }}-{{ $confirmation->id}}</a>)

{{ get_confirmation_total_rooms($confirmation) }} rooms/nights for @money(get_confirmation_total_amount($confirmation), $confirmation->currency->symbol) ({{ $confirmation->currency->name }})

@if ($confirmation->friendly_sent_on)
<small>Originally sent: {{ $confirmation->friendly_sent_on }}</small>
@endif
@endcomponent
@endforeach

@component('mail::button', ['url' => '/reports/confirmations/outstanding'])
See All Outstanding Confirmations
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
