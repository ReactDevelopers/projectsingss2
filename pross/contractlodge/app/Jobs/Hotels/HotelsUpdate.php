<?php

namespace App\Jobs\Hotels;

use Auth;
use App\Race;
use App\Hotel;
use App\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\Hotels\HotelsStoreRequest;

class HotelsUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * App\Http\Requests\Hotels\HotelsStoreRequest
     *
     * @var Request
     */
    private $request;

    /**
     * Hotel id
     * @var integer
     */
    private $hotel_id;

    /**
     * Race
     * @var \App\Race
     */
    private $race;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(HotelsStoreRequest $request, int $hotel_id, Race $race)
    {
        $this->request = $request;
        $this->hotel_id = $hotel_id;
        $this->race = $race;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $hotel = Hotel::find($this->hotel_id);
        $hotel->name        = $this->request->name;
        $hotel->address     = $this->request->address;
        $hotel->city        = $this->request->city;
        $hotel->region      = $this->request->region;
        $hotel->postal_code = $this->request->postal_code;
        $hotel->country_id  = $this->request->country_id;
        $hotel->email       = $this->request->email;
        $hotel->website     = $this->request->website;
        $hotel->phone       = $this->request->phone;
        $hotel->notes       = $this->request->notes;
        $hotel->created_by  = auth()->user()->id;
        $hotel->code        = $this->request->code;
        $hotel->save();
        if (! isset($this->request->contacts) || empty($this->request->contacts)) {
            $hotel->contacts()->delete();
            return $hotel;
        }
        if (isset($this->request['contacts']) && ! empty($this->request['contacts'])) {
            foreach ($this->request['contacts'] as $contact) {
                if (! empty($contact['email'])) {
                    $contact = $this->createContactIfNotExist($contact);
                    $contact_array_ids[] = $contact->id;
                }
            }
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
