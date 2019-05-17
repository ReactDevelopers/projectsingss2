@isset($with_client_name)
    <label><strong>{{ $client->name }}</strong></label> <br>
@endif

{{ $client->address }}, {{ $client->city }}, {{ $client->region }} {{ $client->postal_code }} <br>

@isset($client->country->name)
    {{ $client->country->name }} <br>
@endif

@isset($client->phone)
    {{ $client->phone }} <br>
@endif

@isset($client->website)
    {{ $client->website }} <br>
@endif

@isset($contact_client->name)
    Attn: {{ $contact_client->name }}
@endif
