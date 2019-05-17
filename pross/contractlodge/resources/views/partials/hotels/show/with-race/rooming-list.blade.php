@if(isset($rooming_list_guests) && ! $rooming_list_guests->isEmpty())
    <hr class="my-5">

    <div class="form-row mb-1">
        <div class="col-sm-12">
            <h5 class="mb-3">{{__('Rooming List')}}</h5>
        </div>
        <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-5 pt-1"><label for="list_sent_on">List Sent On:</label></div>
                <div class="col-sm-7 mb-2">{{ $meta->friendly_rooming_list_sent }}</div>
            </div>
            <div class="row">
                <div class="col-sm-5 pt-1"><label for="list_confirmed_on">List Confirmed On:</label></div>
                <div class="col-sm-7 mb-2">{{ $meta->friendly_rooming_list_confirmed }}</div>
            </div>
        </div>

        @include('partials.common.rooming-list-room-type-breakdown')

        <div class="col-sm-4 text-right">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('races.hotels.reservations', [
                        'race' => $race->id,
                        'hotel' => $hotel->id,
                        ]) }}" class="btn btn-primary btn-sm ml-3 float-right">
                        <i class="fa fa-edit mr-2"></i> {{__('Edit Rooming List')}}
                    </a>
                    <a href="{{ route('races.hotels.reservations.export', [
                        'race' => $race->id,
                        'hotel' => $hotel->id
                        ]) }}"
                        class="btn btn-primary btn-sm ml-5 float-right">
                        <i class="fa fa-download mr-2"></i> {{__('Export')}}
                    </a>
                </div>
                <div class="col-md-12 mt-4">
                    <button v-on:click="resetSearchTable('rooming_list')" 
                        class="btn btn-primary btn-sm ml-2 float-right">
                        Reset
                    </button>
                    <input type="text" name="q" id="q" value="" 
                        v-on:keyup="searchTable('rooming_list')" 
                        placeholder="Search for guest name" 
                        class="form-control col-sm-12 col-md-8 col-lg-4 float-right">
                </div>
            </div>
        </div>
        <div class="col-sm-12 mb-3">
            <label for="">Notes to Hotel: </label>
            {{ $meta->rooming_list_notes }}
        </div>

        @include('partials.common.rooming-list-legend')
    </div>

    {{-- FIXME: Make showing this conditional upon there being signed confirmations --}}
    @include('partials.common.rooming-listing')
@endif
