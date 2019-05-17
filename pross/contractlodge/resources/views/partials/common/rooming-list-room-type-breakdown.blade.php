<div class="col-sm-4">
    <h6 class="mb-3 pt-1">Room Type Breakdown</h6>
    <div class="form-row">
        <div class="col-sm-12">
            <dl class="row room-types-brk">
                @isset($room_type_breakdown)
                    @php
                        $signed_confirmations = collect();
                    @endphp
                    @foreach($room_type_breakdown as $room_type)
                        @if(isset($room_type->race_hotel->signed_confirmations) && count($room_type->race_hotel->signed_confirmations) > 0)
                            @php
                                $signed_confirmations = $room_type->race_hotel->signed_confirmations;
                            @endphp
                        @endif
                        <dt class="col-sm-10 col-md-4 col-lg-4 room-type-brk">
                            {{ get_room_name($room_type->races_hotels_inventory_id) }}
                        </dt>
                        <dd class="col-sm-2 col-md-2 col-lg-2 room-type-brk">
                            {{ get_total_confirmed_rooms($signed_confirmations, $room_type->races_hotels_inventory_id) }}
                        </dd>
                    @endforeach
                @endif
            </dl>
        </div>
    </div>
</div>
