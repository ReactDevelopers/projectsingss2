<hr class="my-5">

<div class="form-row mb-4">
    <div class="col-md-4">
        <h5 class="mb-3">
            Room Types and Rates
        </h5>
        @if (isset($meta->currency) && isset($meta->currency->name))
            <h6>
                Currency: {{ @$meta->currency->name }}
            </h6>
        @endif
        @if(isset($meta->inventory_min_check_in) && isset($meta->inventory_min_check_out))
            <h6>
                Min Nights: {{ $meta->friendly_min_check_in }} - {{ $meta->friendly_min_check_out }} ({{ $meta->num_min_nights }} nights)
            </h6>
        @endif
        @isset($meta->inventory_notes)
            <p>
                Notes: {{ $meta->inventory_notes }}
            </p>
        @endif
    </div>
    <div class="col-md-8">
        <a href="{{ route('races.hotels.edit', ['hotel' => $hotel->id, 'race' => $race->id]) }}"
            class="btn btn-primary btn-sm float-right"  data-offline="disabled" dusk="room-types-and-rates">
            <i class="fa fa-edit mr-2"></i> Edit Room Types and Rates
        </a>
        @include('partials.common.uploads-listings')
    </div>
</div>

<div class="form-row">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th class="text-right">Min/Nt Hotel ({{ $meta->currency->symbol }})</th>
                    <th class="text-right">Min/Nt Client ({{ $meta->currency->symbol }})</th>
                    <th class="text-center">Rooms Booked</th>
                    <th class="text-center">Rooms Sold</th>
                    <th class="text-center">Rooms On Offer</th>
                    <th class="text-right">P&P/Nt Hotel ({{ $meta->currency->symbol }})</th>
                    <th class="text-right">P&P/Nt Client ({{ $meta->currency->symbol }})</th>
                    <th class="text-center">Nts Booked</th>
                    <th class="text-center">Nts Sold</th>
                    <th class="text-center">Nts On Offer</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventories as $inventory)
                    <tr>
                        <td>{{ $inventory->room_name }}</td>
                        <td class="text-right">@money($inventory->min_night_hotel_rate, $meta->currency->symbol)</td>
                        <td class="text-right">@money($inventory->min_night_client_rate, $meta->currency->symbol)</td>
                        <td class="text-center">{{ $inventory->min_stays_contracted }}</td>
                        <td class="text-center">{{ $inventory->min_stays_sold }}</td>
                        <td class="text-center">{{ $inventory->min_stays_on_offer }}</td>
                        <td class="text-right">@money($inventory->pre_post_night_hotel_rate, $meta->currency->symbol)</td>
                        <td class="text-right">@money($inventory->pre_post_night_client_rate, $meta->currency->symbol)</td>
                        <td class="text-center">{{ $inventory->pre_post_nights_contracted }}</td>
                        <td class="text-center">{{ $inventory->pre_post_nights_sold }}</td>
                        <td class="text-center">{{ $inventory->pre_post_nights_on_offer }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td><strong>Totals</strong></td>
                    <td class="text-right"><strong>@money($totals['min_night_hotel_amount'], $meta->currency->symbol)</strong></td>
                    <td class="text-right"><strong>@money($totals['min_night_client_amount'], $meta->currency->symbol)</strong></td>
                    <td class="text-center"><strong>{{ $totals['min_stays_contracted'] }}</strong></td>
                    <td class="text-center"><strong>{{ $totals['min_stays_sold'] }}</strong></td>
                    <td class="text-center"><strong>{{ $totals['min_stays_on_offer'] }}</strong></td>
                    <td class="text-right"><strong>@money($totals['pre_post_night_hotel_amount'], $meta->currency->symbol)</strong></td>
                    <td class="text-right"><strong>@money($totals['pre_post_night_client_amount'], $meta->currency->symbol)</strong></td>
                    <td class="text-center"><strong>{{ $totals['pre_post_nights_contracted'] }}</strong></td>
                    <td class="text-center"><strong>{{ $totals['pre_post_nights_sold'] }}</strong></td>
                    <td class="text-center"><strong>{{ $totals['pre_post_nights_on_offer'] }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
