<?php

namespace App\Jobs\Clients;

use Auth;
use App\Client;
use App\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\Clients\ClientsStoreRequest;

class ClientsUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * App\Http\Requests\Client\ClientsStoreRequest
     *
     * @var Request
     */
    private $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ClientsStoreRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return App\Client
     */
    public function handle()
    {
        $client = Client::find($this->request->client_id);
        $client->name        = $this->request->name;
        $client->address     = $this->request->address;
        $client->city        = $this->request->city;
        $client->region      = $this->request->region;
        $client->postal_code = $this->request->postal_code;
        $client->country_id  = $this->request->country_id;
        $client->email       = $this->request->email;
        $client->website     = $this->request->website;
        $client->phone       = $this->request->phone;
        $client->created_by  = auth()->user()->id;
        $client->code        = $this->request->code;

        if (! empty($this->request->contact_email)) {
            $contact = $this->updateOrCreateContact();
            $client->contacts()->sync([$contact->id]);
        }
        $client->save();

        if (! isset($this->request->contacts) || empty($this->request->contacts)) {
            $client->contacts()->delete();
            return $client;
        }

        if (isset($this->request->contacts) && ! empty($this->request->contacts)) {
            foreach ($this->request->contacts as $contact) {
                if (! empty($contact['email'])) {
                    $contact = $this->updateOrCreateContact($contact);
                    $contact_array_ids[] = $contact->id;
                }
            }
            $client->contacts()->sync($contact_array_ids);
            $contact->refresh();
        }
        return $client;
    }

    /**
     * Method to create or update a contact
     * @param Array $contact
     * @return App\Contact
     */
    private function updateOrCreateContact($contact)
    {
        $contact = Contact::updateOrCreate(
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
