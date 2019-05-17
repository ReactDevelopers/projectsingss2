<div class="form-row mt-5">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th scope="col">Room Type</th>
                    <th scope="col" class="text-center">Rooms</th>
                    <th scope="col" class="text-center">Check-in/out</th>
                    <th scope="col" class="text-center">Days</th>
                    <th scope="col" class="text-center">RmNts</th>
                    <th scope="col" class="text-right">Rate ({{ $confirmation->currency->name }})</th>
                    <th scope="col" class="text-right">Total ({{ $confirmation->currency->name }})</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_rooms = 0;
                    $total_room_nights = 0;
                    $total_revenue = 0;
                @endphp
                @isset($confirmation->confirmation_items)
                    @foreach ($confirmation->confirmation_items as $confirmation_item)
                        @php
                            $total_rooms += $confirmation_item->quantity;
                            $total_room_nights += $confirmation_item->friendly_room_nights;
                            $total_revenue += ($confirmation_item->friendly_room_nights * $confirmation_item->rate);
                        @endphp
                        <tr>
                            <td>{{ $confirmation_item->races_hotels_inventory->room_name }}</td>
                            <td class="text-center">{{ $confirmation_item->quantity }}</td>
                            <td class="text-center">
                                {{ $confirmation_item->friendly_check_in }}
                                - {{ $confirmation_item->friendly_check_out }}
                            </td>
                            <td class="text-center">{{ $confirmation_item->friendly_diff }}</td>
                            <td class="text-center">{{ $confirmation_item->friendly_room_nights }}</td>
                            <td class="text-right">
                                @money($confirmation_item->rate, $confirmation->currency->symbol)
                                @if ($confirmation->currency->symbol !== $meta->currency->symbol)
                                    <small class="text-muted ml-2">
                                        (est @money($confirmation_item->rate_exchanged, $meta->currency->symbol))
                                    </small>
                                @endif
                            </td>
                            <td class="text-right">
                                @money(($confirmation_item->friendly_room_nights * $confirmation_item->rate), $confirmation->currency->symbol)
                                @if ($confirmation->currency->symbol !== $meta->currency->symbol)
                                    <small class="text-muted ml-2">
                                        (est @money(($confirmation_item->friendly_room_nights * $confirmation_item->rate_exchanged), $meta->currency->symbol))
                                    </small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td><strong>Totals</strong></td>
                    <td class="text-center"><strong>{{ $total_rooms }}</strong></td>
                    <td class="text-right"><strong></strong></td>
                    <td class="text-center"><strong></strong></td>
                    <td class="text-center"><strong>{{ $total_room_nights }}</strong></td>
                    <td class="text-right"><strong></strong></td>
                    <td class="text-right">
                        <strong>
                            @money($total_revenue, $confirmation->currency->symbol)
                        </strong>
                        @if ($confirmation->currency->symbol !== $meta->currency->symbol)
                            <small class="text-muted ml-2">
                                (est @money(($total_revenue * $confirmation->exchange_rate), $meta->currency->symbol))
                            </small>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="form-row my-4">
    <div class="col-sm-12">
        <label><strong>Additional Notes</strong></label> <br>
        {{ $confirmation->notes }}
    </div>
</div>
@include('partials.common.uploads')