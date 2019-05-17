<?php

namespace App\Jobs\Hotels;

use Auth;
use App\Race;
use App\Hotel;
use App\Contact;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\Hotels\HotelsStoreRequest;

class HotelsCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * HotelsStoreRequest
     *
     * @var array
     */
    public $hotelData;

    /**
     * Race
     * @var App\Race
     */
    public $race;

    /**
     * Undocumented function
     *
     * @param Array $hotelData
     * @param App\Race $race
     * @param App\User $user
     */
    public function __construct(Array $hotelData, Race $race)
    {
        $this->hotelData = $hotelData;
        $this->race = $race;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $hotel = new Hotel([
            'name'       => $this->hotelData['name'],
            'address'    => $this->hotelData['address'],
            'city'       => $this->hotelData['city'],
            'region'     => $this->hotelData['region'],
            'email'      => $this->hotelData['email'],
            'phone'      => $this->hotelData['phone'],
            'postal_code'=> $this->hotelData['postal_code'],
            'country_id' => $this->hotelData['country_id'],
            'website'    => $this->hotelData['website'],
            'notes'      => $this->hotelData['notes'],
            'created_by' => auth()->user()->id,
            'code'       => $this->hotelData['code'],
        ]);

        if (! empty($this->race->id)) {
            $this->race->hotels()->save(
                $hotel,
                [
                    'inventory_currency_id' => $this->race->currency_id
                ]
            );
        } else {
            $hotel->save();
        }

        if (! empty($this->hotelData['contacts'])) {
            foreach ($this->hotelData['contacts'] as $contact) {
                if (! empty($contact['email'])) {
                    $contact = $this->createContactIfNotExist($contact);
                    $contact_array_ids[] = $contact->id;
                }
            }
            $hotel->contacts()->sync($contact_array_ids);
        }

        if(! empty($contact_array_ids)) {
            $hotel->contacts()->sync($contact_array_ids);
        }

        $hotel->refresh();
        return $hotel;
    }

   /**
     * Method to create a new contact if the contact email doesn't exist
     * @param Array $contact
     * @return App\Contact
     */
    private function createContactIfNotExist($contact)
    {
        $contact = Contact::firstOrCreate(
            ['email' => $contact['email']],
            [
                'name' => $contact['name'],
                'phone' => $contact['phone'],
                'role' => $contact['role'],
                'created_by' => auth()->user()->id,
            ]
        );
        $contact->refresh();
        return $contact;
    }
}
