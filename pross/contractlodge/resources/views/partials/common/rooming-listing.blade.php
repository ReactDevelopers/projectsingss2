@if(isset($rooming_list_guests) && $meta->inventory_min_check_in != NULL && $meta->inventory_min_check_in != NULL)
    <div class="form-row mb-5">
        <div class="col-sm-12 z-index-1">
            <table id="search_table_by_column" ref="sortTableByColumn" class="table table-sm table-striped override-table table-bordered table-fixed">
                <thead>
                    <tr>
                        @php
                            $total_nights = 0;
                            $dates_range = generate_date_range_rooming_list($meta->inventory_min_check_in->subDay(7), $meta->inventory_min_check_out->addDay(5));
                        @endphp
                        @if(@isset($export) && $export)
                            <th class="text-center" colspan="{{ count($dates_range) + 6 }}">
                                Legends:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'N' = Unused  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'R' = Unused (resell requested) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'S' = Resold &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  'U' = Used &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  'E' = Used and Early Check-in &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'L' = Used and Late Check-out  <br><br>
                                Notes to Hotel: {{ $meta->rooming_list_notes }}
                            </th>
                        @endif
                    <tr>
                        @if(@isset($export) && $export)
                            <th class="text-center">Row<i class="pull-right fa fa-sort"></i></th>
                        @else
                            <th class="text-center" @click.prevent="sortTable(0, 'number')">Row<i class="pull-right fa fa-sort"></i></th>
                        @endif
                        <th>Client</th>
                        <th>Room Type</th>
                        @if(@isset($export) && $export)
                            <th>Guest Name<i class="pull-right fa fa-sort"></i></th>
                        @else
                            <th @click.prevent="sortTable(3)">Guest Name<i class="pull-right fa fa-sort"></i></th>
                        @endif
                        @foreach($dates_range as $dates)
                            <th class="text-center">
                                @php
                                    $date = explode(' ',$dates);
                                @endphp
                                {{ $date[0] }}<br>{{ $date[1] }}<br><span class="text-uppercase">{{ $date[2] }}</span>
                            </th>
                        @endforeach
                        <th>Notes</th>
                        <th>Conf. Nº</th>
                    </tr>
                </thead>
                <tbody>
                        @php
                            $set_client_id = 0;
                            $dates_range_td = [];
                        @endphp
                        @foreach($rooming_list_guests as $roomlist_guest)
                            @if($set_client_id != $roomlist_guest->client_id && $set_client_id != 0 && !isset($export))
                                <tr class="table-dark"><td colspan="{{ count($dates_range) + 3 }}"></td><td></td><td></td><td></td></tr>
                            @endif
                            @php $set_client_id = $roomlist_guest->client_id; @endphp
                            <tr>
                                <td class="text-center">{{ $roomlist_guest->getRowNumberAttribute() }}</td>
                                @php
                                    $client_name = isset($roomlist_guest->client_id)? get_client_name($roomlist_guest->client_id) : "";
                                @endphp
                                <td>{{ $client_name }}</td>
                                <td>{{ get_room_name($roomlist_guest->races_hotels_inventory_id) }}</td>
                                <td class="nowrap">{{ $roomlist_guest->guest_name }}</td>
                                @php
                                    $dates_range_td = generate_date_range($meta->inventory_min_check_in->subDay(7), $meta->inventory_min_check_out->addDay(5));
                                @endphp
                                @foreach($dates_range_td as $date)
                                    @php
                                        $date_used = check_night_status($roomlist_guest->id,$roomlist_guest->race_hotel_id,$date,App\RoomingListGuestNight::STATUS_USED);
                                        $date_unused = check_night_status($roomlist_guest->id,$roomlist_guest->race_hotel_id,$date,App\RoomingListGuestNight::STATUS_UNUSED);
                                        $date_resel = check_night_status($roomlist_guest->id,$roomlist_guest->race_hotel_id,$date,App\RoomingListGuestNight::STATUS_RESELL);
                                        $date_early_checkin = check_night_status($roomlist_guest->id,$roomlist_guest->race_hotel_id,$date,App\RoomingListGuestNight::STATUS_EARLY_CHECKIN);
                                        $date_late_checkout = check_night_status($roomlist_guest->id,$roomlist_guest->race_hotel_id,$date,App\RoomingListGuestNight::STATUS_LATE_CHECKOUT);
                                        $date_resold = check_night_status($roomlist_guest->id,$roomlist_guest->race_hotel_id,$date,App\RoomingListGuestNight::STATUS_RESOLD);

                                        $check_min_nights_range = check_min_night_range($meta->inventory_min_check_in,$meta->inventory_min_check_out,$date);

                                    @endphp
                                    @if($date_used && $roomlist_guest->client_id !='')
                                        <td class="text-center {{ $check_min_nights_range ? 'table-success':'' }}">
                                            <i class="fa fa-check text-success"></i>
                                            @if(@isset($export) && $export)
                                                {{ Config::get('rooming_list.statuses_abbreviations.'.App\RoomingListGuestNight::STATUS_USED) }}
                                            @endif
                                        </td>
                                    @elseif($date_unused && $roomlist_guest->client_id !='')
                                        <td class="text-center table-warning" >
                                            <i class="text-success"></i>
                                            @if(@isset($export) && $export)
                                                {{ Config::get('rooming_list.statuses_abbreviations.'.App\RoomingListGuestNight::STATUS_UNUSED) }}
                                            @endif
                                        </td>
                                    @elseif($date_resel && $roomlist_guest->client_id !='')
                                        <td class="text-center table-success table-warning" >
                                            <i class="fa fa-retweet text-success"></i>
                                            @if(@isset($export) && $export)
                                                {{ Config::get('rooming_list.statuses_abbreviations.'.App\RoomingListGuestNight::STATUS_RESELL) }}
                                            @endif
                                        </td>
                                    @elseif($date_early_checkin && $roomlist_guest->client_id !='')
                                        <td class="text-center" >
                                            @if(!@isset($export))
                                                <i class="fa text-success">EC</i>
                                            @endif
                                            @if(@isset($export) && $export)
                                                {{ Config::get('rooming_list.statuses_abbreviations.'.App\RoomingListGuestNight::STATUS_EARLY_CHECKIN) }}
                                            @endif
                                        </td>
                                    @elseif($date_late_checkout && $roomlist_guest->client_id !='')
                                        <td class="text-center" >
                                            @if(!@isset($export))
                                                <i class="fa text-success">LC</i>
                                            @endif
                                            @if(@isset($export) && $export)
                                                {{ Config::get('rooming_list.statuses_abbreviations.'.App\RoomingListGuestNight::STATUS_LATE_CHECKOUT) }}
                                            @endif
                                        </td>
                                    @elseif($date_resold && $roomlist_guest->client_id !='')
                                        <td class="text-center table-warning" >
                                            <i class="fa fa-close text-danger"></i>
                                            @if(@isset($export) && $export)
                                                {{ Config::get('rooming_list.statuses_abbreviations.'.App\RoomingListGuestNight::STATUS_RESOLD) }}
                                            @endif
                                        </td>
                                    @elseif($check_min_nights_range)
                                        <td class="text-center table-success">
                                            <i class="text-success"></i>
                                        </td>
                                    @elseif($roomlist_guest->client_id !='' && @isset($export) && $export)
                                        <td class="text-center">

                                        </td>
                                    @else
                                        <td class="text-center"></td>
                                    @endif
                                @endforeach
                                <td>{{ $roomlist_guest->notes }}</td>
                                <td>{{ $roomlist_guest->confirmation_number }}</td>
                            </tr>
                        @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="table-secondary text-center"></td>
                        <td class="table-secondary"></td>
                        <td class="table-secondary"></td>
                        <td class="table-secondary"></td>
                        @foreach($dates_range_td as $date)
                            <td class="table-secondary text-center">{{ get_rows_total_nights($meta->id, $date) }}</td>
                        @endforeach
                        <td class="table-secondary"></td>
                        <td class="table-secondary"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@else
    <div class="form-row mb-5">
        <span>
            <strong>
                No rooming list to display.
            </strong>
        </span>
    </div>
@endif
