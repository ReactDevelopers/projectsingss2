@isset($with_hotel_name)
    <label><strong>{{ $hotel->name }}</strong></label> <br>
@endif

{{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->region }} {{ $hotel->postal_code }} <br>

@isset($hotel->country->name)
    {{ $hotel->country->name }} <br>
@endif

@isset($hotel->phone)
    {{ $hotel->phone }} <br>
@endif

@isset($hotel->website)
    {{ $hotel->website }} <br>
@endif

@isset($contact_hotel->name)
    Attn: {{ $contact_hotel->name }}
@endif
