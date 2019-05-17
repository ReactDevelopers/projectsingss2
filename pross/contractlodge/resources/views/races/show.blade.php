@extends('spark::layouts.app')

@section('content')
<races-show :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <div class="card card-default">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ route('races.index') }}">
                                    {{__('Races')}}
                                </a> / {{ $race->full_name }}
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="/races/{{ $race->id }}/invoices/extras/create"
                                    class='btn btn-primary btn-sm ml-3'
                                    data-offline="disabled" dusk="add-invoice">
                                    <i class="fa fa-plus mr-2"></i> {{__('Add Extras Invoice')}}
                                </a>
                                <a href="/races/{{ $race->id }}/edit"
                                    class="btn btn-primary btn-sm ml-3"
                                    dusk="race-edit"
                                    data-offline="disabled">
                                    <i class="fa fa-edit mr-2"></i> {{__('Edit Race')}}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            <div class="col-sm-6">
                                <h5>{{ $race->full_name }}</h5>
                            </div>
                        </div>

                        <div class="form-row mt-3 mb-4">
                            <div class="col-sm-1">
                                <label><strong>Year</strong></label> <br>
                                {{ $race->year }}
                            </div>
                            <div class="col-sm-3">
                                <label><strong>Race</strong></label> <br>
                                {{ $race->name }}
                            </div>
                            <div class="col-sm-2">
                                <label><strong>Start Date</strong></label> <br>
                                {{ $race->friendly_start_on }}
                            </div>
                            <div class="col-sm-2">
                                <label><strong>End Date</strong></label> <br>
                                {{ $race->friendly_end_on }}
                            </div>
                            <div class="col-sm-2">
                                <label><strong>Default Currency</strong></label> <br>
                                {{ $race->currency->name }}
                            </div>
                            <div class="col-sm-2">
                                <p class="text-right mr-3">
                                    Total Rooms Booked:
                                    <strong class="ml-3">{{ $inventory_stats['sum_row_total_min_stays_contracted'] }}</strong>
                                </p>
                                <p class="text-right mr-3">
                                    Total Rooms Sold:
                                    <strong class="ml-3">{{ $inventory_stats['sum_row_total_min_stays_sold'] }}</strong>
                                </p>
                                <p class="text-right mr-3">
                                    Total Rooms Available:
                                    <strong class="ml-3">{{ $inventory_stats['sum_available_total'] }}</strong>
                                </p>
                            </div>
                        </div>
                        <hr class="my-5">

                        <div class="form-row mt-4 mb-4">
                            <div class="col-sm-6">
                                <h5>{{__('Hotel Inventory')}}</h5>
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="{{ route('races.hotels.search', ['race' => $race->id]) }}"
                                    class="btn btn-primary btn-sm"
                                    dusk="add-race-hotel"
                                    data-offline="disabled">
                                    <i class="fa fa-plus mr-2"></i> Add Hotel
                                </a>
                            </div>
                        </div>
                        <div class="form-row mt-3 mb-4">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped override-table">
                                    @php $room_array = []; @endphp
                                    @foreach($race->hotels as $hotel)
                                        @php
                                            if (isset($hotel->meta->id)
                                                && isset($inventory_stats['inventories'][$hotel->meta->id])
                                                && ! empty($inventory_stats['inventories'][$hotel->meta->id])
                                            ) {
                                                $inventories = $inventory_stats['inventories'][$hotel->meta->id];
                                            } else {
                                                $inventories = [];
                                            }
                                        @endphp

                                        @foreach ($inventories as $inventory)
                                            @php
                                                $room_array[$inventory->id] = $inventory->id;
                                            @endphp
                                        @endforeach
                                    @endforeach

                                    @foreach($race->hotels as $hotel)
                                        @php
                                            if (isset($hotel->meta->id)
                                                && isset($inventory_stats['inventories'][$hotel->meta->id])
                                                && ! empty($inventory_stats['inventories'][$hotel->meta->id])
                                            ) {
                                                $inventories = $inventory_stats['inventories'][$hotel->meta->id];
                                            } else {
                                                $inventories = [];
                                            }
                                        @endphp

                                        <tr class="header-row">
                                            <th>Name</th>
                                            <th>Status</th>
                                            @php
                                                $room_count = 0;
                                            @endphp
                                            @if (isset($inventories) && isset($room_array) && count($inventories) > 0 )
                                                @foreach ($room_array as $room)
                                                    @foreach ($inventories as $inventory)
                                                        @if ($inventory->id == $room)
                                                            <th class="text-center">{{ $inventory->room_name }}</th>
                                                            @php
                                                                $room_count++;
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            @endif
                                            @php $remaining_count = $inventory_stats['max_room_types'] - $room_count; @endphp
                                            @if ($remaining_count > 0)
                                                @for ($i = 1; $i<= $remaining_count; $i++)
                                                    <th></th>
                                                @endfor
                                            @endif

                                            <th class="text-center">Total</th>
                                        </tr>

                                        <tr class="bg-white">
                                            <td>
                                                <a href="{{ route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id]) }}">
                                                    <strong>{{ $hotel->name }}</strong>
                                                </a>
                                            </td>
                                            <td>Rooms Booked</td>
                                            @php
                                                $row_total_min_stays_contracted = 0;
                                            @endphp
                                            @if (isset($inventories) && isset($room_array) && count($inventories) > 0 )
                                                @foreach ($room_array as $room)
                                                    @foreach ($inventories as $inventory)
                                                        @if ($inventory->id == $room)
                                                            <td class="text-center">
                                                                {{ $inventory->min_stays_contracted }}
                                                                @php
                                                                    $row_total_min_stays_contracted += $inventory->min_stays_contracted;
                                                                    continue 2;
                                                                @endphp
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                    {{-- <td class="text-center"> </td> --}}
                                                @endforeach
                                                @if ($remaining_count > 0)
                                                    @for ($i = 1; $i<= $remaining_count; $i++)
                                                        <td class="text-center"> </td>
                                                    @endfor
                                                @endif
                                            @endif

                                            @if (count($inventories) == 0)
                                                @foreach ($race->room_type_inventories as $inventory)
                                                    <td class="text-center"> </td>
                                                @endforeach
                                            @endif

                                            <td class="text-center"><strong>{{ $row_total_min_stays_contracted }}</strong></td>
                                        </tr>
                                        <tr class="bg-gray-100">
                                            <td></td>
                                            <td>Rooms Sold</td>
                                            @php
                                                $row_total_min_stays_sold = 0;
                                            @endphp

                                            @if (isset($inventories) && isset($room_array) && count($inventories) > 0 )
                                                @foreach ($room_array as $room)
                                                    @foreach ($inventories as $inventory)
                                                        @if ($inventory->id == $room)
                                                            <td class="text-center">
                                                                {{ $inventory->min_stays_sold }}
                                                                @if (!isset($inventory->min_stays_sold))
                                                                    0
                                                                @endif
                                                                @php
                                                                    $row_total_min_stays_sold += $inventory->min_stays_sold;
                                                                continue 2;
                                                                @endphp
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                @if ($remaining_count > 0)
                                                    @for ($i = 1; $i<= $remaining_count; $i++)
                                                        <td class="text-center"> </td>
                                                    @endfor
                                                @endif
                                            @endif

                                            @if (count($inventories) == 0)
                                                @foreach ($race->room_type_inventories as $inventory)
                                                    <td class="text-center"> </td>
                                                @endforeach
                                            @endif

                                            <td class="text-center"><strong>{{ $row_total_min_stays_sold }}</strong></td>
                                        </tr>
                                        <tr class="bg-gray-200">
                                            <td></td>
                                            <td>Rooms Available</td>
                                            @php
                                                $available = 0;
                                                $available_total = 0;
                                            @endphp

                                            @if (isset($inventories) && isset($room_array) && count($inventories) > 0 )
                                                @foreach ($room_array as $room)
                                                    @foreach ($inventories as $inventory)
                                                        @if ($inventory->id == $room)
                                                            <td class="text-center">
                                                                @php
                                                                    $available = $inventory->min_stays_contracted - $inventory->min_stays_sold;
                                                                    $available_total += $available;
                                                                @endphp
                                                                {{ $available }}
                                                                @php
                                                                    continue 2;
                                                                @endphp
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                @if ($remaining_count > 0)
                                                    @for ($i = 1; $i<= $remaining_count; $i++)
                                                        <td class="text-center"> </td>
                                                    @endfor
                                                @endif
                                            @endif

                                            @if (count($inventories) == 0)
                                                @foreach ($race->room_type_inventories as $inventory)
                                                    <td class="text-center"> </td>
                                                @endforeach
                                            @endif

                                            <td class="text-center"><strong>{{ $available_total }}</strong></td>
                                        </tr>
                                        <tr class="bg-gray-300">
                                            <td></td>
                                            <td>Rooms On Offer</td>
                                            @php
                                                $row_total_min_stays_on_offer = 0;
                                            @endphp

                                            @if (isset($inventories) && isset($room_array) && count($inventories) > 0 )
                                                @foreach ($room_array as $room)
                                                    @foreach ($inventories as $inventory)
                                                        @if ($inventory->id == $room)
                                                            <td class="text-center">
                                                                {{ $inventory->min_stays_on_offer }}
                                                                @if (!isset($inventory->min_stays_on_offer))
                                                                    0
                                                                @endif
                                                                @php
                                                                    $row_total_min_stays_on_offer += $inventory->min_stays_on_offer;
                                                                continue 2;
                                                                @endphp
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                @if ($remaining_count > 0)
                                                    @for ($i = 1; $i<= $remaining_count; $i++)
                                                        <td class="text-center"> </td>
                                                    @endfor
                                                @endif
                                            @endif

                                            @if (count($inventories) == 0)
                                                @foreach ($race->room_type_inventories as $inventory)
                                                    <td class="text-center"> </td>
                                                @endforeach
                                            @endif

                                            <td class="text-center"><strong>{{ $row_total_min_stays_on_offer }}</strong></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</races-show>
@endsection
