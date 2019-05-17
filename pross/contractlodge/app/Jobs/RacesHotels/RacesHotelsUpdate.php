<?php

namespace App\Jobs\RacesHotels;

use Auth;
use App\Race;
use App\Hotel;
use App\RaceHotel;
use Carbon\Carbon;
use App\RaceHotelInventory;
use Illuminate\Bus\Queueable;
use App\Events\InventoryChanged;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\RacesHotels\RacesHotelsStoreRequest;

class RacesHotelsUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * App\Http\Requests\RacesHotels\RacesHotelsStoreRequest
     *
     * @var Request
     */
    public $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RacesHotelsStoreRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $race_hotel = RaceHotel::firstOrCreate([
            'race_id' => $this->request->race_id,
            'hotel_id' => $this->request->hotel_id
        ]);

        $race_hotel->update([
            'inventory_currency_id' => $this->request->inventory_currency_id,
            'inventory_min_check_in' => format_input_date_to_system($this->request->inventory_min_check_in),
            'inventory_min_check_out' => format_input_date_to_system($this->request->inventory_min_check_out),
            'inventory_notes' => $this->request->inventory_notes,
        ]);

        if (isset($this->request->reservation_check) && $this->request->reservation_check != '') {
            $race_hotel->update([
                'rooming_list_sent' => format_input_date_to_system($this->request->rooming_list_sent),
                'rooming_list_confirmed' => format_input_date_to_system($this->request->rooming_list_confirmed),
                'rooming_list_notes' => $this->request->rooming_list_notes,
            ]);

            return $race_hotel;
        }

        // Prep for sync
        $race_hotel->load('room_type_inventories');

        $this->sync(
            $race_hotel->room_type_inventories,
            collect($this->request->inventory_rows),
            ['race_hotel_id' => $race_hotel->id]
        );

        event(new InventoryChanged($race_hotel));

        return $race_hotel->refresh();


    }

    /**
     * Delete, create or update the records as necessary.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     * @param  array      $create_attributes Additional attributes to add when creating a record
     */
    private function sync(Collection $db_rows, Collection $submitted_rows, array $create_attributes = [])
    {
        $this->deleteExisting($db_rows, $submitted_rows);
        $this->createOrUpdate($submitted_rows, $create_attributes);
    }

    /**
     * Creates new objects in the db from the submitted data
     * @param  Collection $submitted_rows
     * @param  array      $attributes
     */
    private function createOrUpdate(Collection $submitted_rows, array $attributes = [])
    {
        $submitted_rows->each(function ($room) use ($attributes) {

            // Update if necessary
            if (isset($room['id'])) {
                RaceHotelInventory::find($room['id'])
                    ->update(array_except($room, [
                        'id',
                        'created_at',
                        'deleted_at',
                        'updated_at',
                        'race_hotel_id'
                    ]));

                return true;
            }

            // Otherwise create
            if (! empty($attributes)) {
                $room = array_merge($room, $attributes);
            }

            RaceHotelInventory::create($room);
        });
    }

    /**
     * Deletes objects from the db which are not in the submitted data.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     */
    private function deleteExisting(Collection $db_rows, Collection $submitted_rows)
    {
        $deletable = $db_rows->filter(function ($room) use ($submitted_rows) {
            $first = array_first($submitted_rows, function ($submitted_row) use ($room) {
                return $submitted_row['id'] == $room->id;
            });
            return empty($first);
        });

        RaceHotelInventory::destroy($deletable->pluck('id'));
    }
}
