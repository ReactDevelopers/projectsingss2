<?php

namespace App\Jobs\Clients;

use Auth;
use App\User;
use App\Client;
use App\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\Clients\ClientStoreRequest;

class ClientsCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Client
     *
     * @var array
     */
    public $client;

    public function __construct(array $client)
    {
        $this->client = $client;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client([
            'name'        => $this->client['name'],
            'address'     => $this->client['address'],
            'city'        => $this->client['city'],
            'region'      => $this->client['region'],
            'postal_code' => $this->client['postal_code'],
            'country_id'  => $this->client['country_id'],
            'email'       => $this->client['email'],
            'website'     => $this->client['website'],
            'phone'       => $this->client['phone'],
            'created_by'  => auth()->user()->id,
            'code'        => $this->client['code'],
        ]);
        $client->save();

        if (isset($this->client['country_id']) && ! empty($this->client['country_id'])) {
            $client->load('country');
        }

        if (! isset($this->client['contacts']) || empty($this->client['contacts'])) {
            return $client;
        }

        foreach ($this->client['contacts'] as $contact) {
            if (! empty($contact['email'])) {
                $contact = $this->createContactIfNotExist($contact);
                $contact_array_ids[] = $contact->id;
            }
        }

        $client->contacts()->sync($contact_array_ids);
        $contact->refresh();
        $client->load('contacts');

        return $client;
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
