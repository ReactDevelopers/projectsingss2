<hr class="my-5">

<div class="form-row mb-4">
    <div class="col-sm-6">
        <h5 class="mb-3">Client Breakdown</h5>
    </div>
    <div class="col-sm-6 text-right">
        @if(isset($meta->inventory_min_check_in) && ! empty($meta->inventory_min_check_in))
            <a href="{{ route('races.hotels.invoices.create', [
                'race' => $race->id,
                'hotel' => $hotel->id,
                'invoice_type' => 'confirmations'
                ]) }}" class="btn btn-primary btn-sm ml-2" data-offline="disabled" dusk="add-room-confirmation">
                <i class="fa fa-plus mr-2"></i> Add Rooms Confirmation
            </a>
        @endif
        <a href="{{ route('races.hotels.invoices.create', [
            'race' => $race->id,
            'hotel' => $hotel->id,
            'invoice_type' => 'extras'
            ]) }}" class="btn btn-primary btn-sm ml-2" data-offline="disabled" dusk="add-extras-invoice">
            <i class="fa fa-plus mr-2"></i> Add Extras Invoice
        </a>
    </div>
</div>

<div class="form-row">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th class="text-center">Confirmed</th>
                    <th class="text-center">Rooms On Offer</th>
                    <th class="text-center">P&P Nts On Offer</th>
                    @php
                        $signed_inventories = [];
                    @endphp
                    @if (isset($inventories) && !empty($inventories))
                        @foreach ($inventories as $inventory)
                            @if (isset($inventory->race_hotel->signed_confirmations) && (count($inventory->race_hotel->signed_confirmations) > 0) && isset($inventory->confirmation_items) && (count($inventory->confirmation_items) > 0))
                                @php $show_room_type = false; @endphp
                                @foreach ($inventory->confirmation_items as $confirmation_item)
                                    @if (isset($confirmation_item->confirmation->signed_on) && ! empty($confirmation_item->confirmation->signed_on))
                                        @php
                                            $show_room_type = true;
                                            $signed_inventories[$inventory->id] = $inventory->id;
                                            break;
                                        @endphp
                                    @endif
                                @endforeach
                                @if ($show_room_type)
                                    <th class="text-right">
                                        {{ $inventory->room_name }}
                                    </th>
                                @endif
                            @endif
                        @endforeach
                    @endif
                    <th class="text-right">Extras</th>
                    <th class="text-right">Totals</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $all_clients_extras = 0;
                    $all_clients_total = 0;
                    $room_type_totals = [];
                    $all_clients_min_stays_sold = 0;
                    $all_clients_min_stays_on_offer = 0;
                    $all_clients_pre_post_nights_on_offer = 0;
                @endphp
                @isset($clients)
                    @foreach($clients as $client)
                        @php
                            $client_total = 0;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('races.hotels.clients.show', [
                                    'race' => $race->id,
                                    'hotel' => $hotel->id,
                                    'client' => $client->id
                                    ]) }}">
                                    {{ $client->name }}
                                </a>
                            </td>
                            <td class="text-center">
                                @php
                                    $client_total += $client->room_types->sum('amount');
                                @endphp
                                @php $all_clients_min_stays_sold += $client->min_stays_sold @endphp
                                {{ $client->min_stays_sold }}
                            </td>
                            <td class="text-center">
                                {{ $client->min_stays_on_offer }}
                                @php $all_clients_min_stays_on_offer += $client->min_stays_on_offer @endphp
                            </td>
                            <td class="text-center">
                                {{ $client->pre_post_nights_on_offer }}
                                @php $all_clients_pre_post_nights_on_offer += $client->pre_post_nights_on_offer @endphp
                            </td>
                            @php $counter = 0; @endphp
                            @if (isset($inventories) && !empty($inventories))
                                @foreach($inventories as $inventory)
                                    @if (isset($inventory->race_hotel->signed_confirmations) && (count($inventory->race_hotel->signed_confirmations) > 0) && isset($inventory->confirmation_items) && (count($inventory->confirmation_items) > 0) && in_array($inventory->id, $signed_inventories))
                                        <td class="text-right">
                                            @php
                                                $room_type = $client->room_types->where('id', $inventory->id)->first();
                                                $amount = isset($room_type['amount']) ? $room_type['amount'] : 0;
                                                if (isset($room_type_totals[$counter])) {
                                                    $room_type_totals[$counter] += $amount;
                                                } else {
                                                    $room_type_totals[] = $amount;
                                                }
                                                $counter++;
                                            @endphp
                                            @money($amount, $meta->currency->symbol)
                                        </td>
                                    @endif
                                @endforeach
                            @endif
                            <td class="text-right">
                                @php
                                    $client_total += $client->invoices->sum('amount');
                                    $all_clients_extras += $client->invoices->sum('amount');
                                @endphp
                                @money($client->invoices->sum('amount'), $meta->currency->symbol)
                            </td>
                            <td class="text-right">
                                @money($client_total, $meta->currency->symbol)
                                @php $all_clients_total += $client_total; @endphp
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td><strong>Totals</strong></td>
                    <td class="text-center">
                        <strong>
                            {{ $all_clients_min_stays_sold }}
                        </strong>
                    </td>
                    <td class="text-center"><strong>{{ $all_clients_min_stays_on_offer }}</strong></td>
                    <td class="text-center"><strong>{{ $all_clients_pre_post_nights_on_offer }}</strong></td>
                    @php $counter = 0; @endphp
                    @if (isset($inventories) && ! empty($inventories))
                        @foreach ($inventories as $inventory)
                            @if (isset($inventory->race_hotel->signed_confirmations) && (count($inventory->race_hotel->signed_confirmations) > 0) && isset($inventory->confirmation_items) && (count($inventory->confirmation_items) > 0) && in_array($inventory->id, $signed_inventories))
                                <th class="text-right">
                                    @isset($room_type_totals[$counter])
                                        <strong>@money($room_type_totals[$counter], $meta->currency->symbol)</strong>
                                    @endif
                                </th>
                                @php $counter++; @endphp
                            @endif
                        @endforeach
                    @endif
                    <td class="text-right"><strong>@money($all_clients_extras, $meta->currency->symbol)</strong></td>
                    <td class="text-right"><strong>@money($all_clients_total, $meta->currency->symbol)</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
