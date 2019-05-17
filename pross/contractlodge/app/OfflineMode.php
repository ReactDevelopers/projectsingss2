<?php

namespace App;

use DB;
use App\Race;
use App\Hotel;
use App\Client;
use App\RaceHotel;
use Carbon\Carbon;
use App\Confirmation;
use App\CustomInvoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class OfflineMode extends Model
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $urls_to_cache = [
        '/settings',
        '/home',
        '/races',
        '/races/archived',
        '/hotels',
        '/hotels/bills',
        '/hotels/archived',
        '/clients',
        '/clients/archived',
        '/notifications/recent',
        '/reports/confirmations/outstanding',
        '/img/green-approved-stamp-400x.png',
    ];

    /**
     * Action to create list of url's to cache.
     *
     * @return Response
     */
    public function getPageUrlList() {

        Cache::forget('urls_to_cache');
        if (Cache::has('urls_to_cache')) {
            $this->urls_to_cache = Cache::get('urls_to_cache');
        } else {
            // Get the list of ids for valid races.
            $races = Race::whereNull('deleted_at')
                ->pluck('id', 'id');
            // Call to the function to add urls to cache
            $this->addUrlsToCache($races, '/races/');

            // Get the list of ids for valid hotels.
            $hotels = Hotel::whereNull('deleted_at')
                ->pluck('id', 'id');
            // Call to the function to add urls to cache
            $this->addUrlsToCache($hotels, '/hotels/');

            // Get the list of ids for valid clients.
            $hotels = Client::whereNull('deleted_at')
                ->pluck('id', 'id');
            // Call to the function to add urls to cache
            $this->addUrlsToCache($hotels, '/clients/');

            // Call to the function to build race hotels urls.
            $this->buildRaceHotelUrls();

            // Call to the function to build client confirmations urls to cache
            $this->clientConfirmationUrls();

            // Call to the function to build invoices urls
            $this->getRaceHotelInvoices();

            $expires_at = Carbon::now()->addDay();
            Cache::store('file')->put('urls_to_cache', $this->urls_to_cache, $expires_at);
        }

        return $this->urls_to_cache;
    }

    /**
     * Function to add dynamically built urls to cache url's array
     *
     * @param Array  $ids
     * @param String $prefix
     *
     * @return void
     */
    private function addUrlsToCache($ids = [], $prefix = null) {
        // Run a loop on ids to add urls to cache.
        foreach ($ids as $id) {
            $this->urls_to_cache[] = $prefix . $id;
        }
    }

    /**
     * Function to build race hotel urls to cache
     *
     * @return void
     */
    private function buildRaceHotelUrls() {
        // Get list of race hotels.
        $race_hotels = RaceHotel::select('race_id', 'hotel_id')
            ->join('hotels', 'hotels.id', '=', 'races_hotels.hotel_id')
            ->join('races', 'races.id', '=', 'races_hotels.race_id')
            ->whereNull('races_hotels.deleted_at')
            ->whereNull('hotels.deleted_at')
            ->whereNull('races.deleted_at')
            ->get();

        if (! empty($race_hotels)) {

            foreach ($race_hotels as $race_hotel) {
                $this->urls_to_cache[] = "/races/{$race_hotel->race_id}/hotels/{$race_hotel->hotel_id}";
                $this->urls_to_cache[] = "/races/{$race_hotel->race_id}/hotels/{$race_hotel->hotel_id}/reconcile";
                $this->urls_to_cache[] = "/races/{$race_hotel->race_id}/hotels/{$race_hotel->hotel_id}/reservations/export";
            }
        }
    }

    /**
     * Function to build client confirmations urls.
     *
     * @return void
     */
    private function clientConfirmationUrls() {
        // Get the list of confirmations.
        $confirmations = DB::table('confirmations')
            ->join('races_hotels', 'races_hotels.id', '=', 'confirmations.race_hotel_id')
            ->join('races', 'races.id', '=', 'races_hotels.race_id')
            ->join('hotels', 'hotels.id', '=', 'races_hotels.hotel_id')
            ->select('confirmations.id', 'confirmations.client_id', 'confirmations.race_hotel_id', 'races_hotels.race_id', 'races_hotels.hotel_id')
            ->whereDate('confirmations.expires_on', '>=', date('Y-m-d'))
            ->whereNull('confirmations.deleted_at')
            ->whereNull('races_hotels.deleted_at')
            ->whereNull('races.deleted_at')
            ->whereNull('hotels.deleted_at')
            ->get();

        foreach ($confirmations as $confirmation) {
            $this->urls_to_cache[] = "/api/uploads/confirmations/{$confirmation->id}";
            $this->urls_to_cache[] = "/clients/{$confirmation->client_id}/confirmations/{$confirmation->id}";

            if (isset($confirmation->race_id) && isset($confirmation->hotel_id)) {
                if (! in_array("/races/{$confirmation->race_id}/hotels/{$confirmation->hotel_id}/clients/{$confirmation->client_id}", $this->urls_to_cache)) {
                    $this->urls_to_cache[] = "/races/{$confirmation->race_id}/hotels/{$confirmation->hotel_id}/clients/{$confirmation->client_id}";
                }

                $this->urls_to_cache[] = "/races/{$confirmation->race_id}/hotels/{$confirmation->hotel_id}/clients/{$confirmation->client_id}/confirmations/{$confirmation->id}";
                $this->urls_to_cache[] = "/races/{$confirmation->race_id}/hotels/{$confirmation->hotel_id}/clients/{$confirmation->client_id}/confirmations/{$confirmation->id}/pdf";
            }
        }
    }

    /**
     * Function to get race hotel invoices
     *
     * @return void
     */
    private function getRaceHotelInvoices() {
        // Get the list of custom invoices.
        $custom_invoices = DB::table('custom_invoices')
            ->join('races_hotels', 'races_hotels.id', '=', 'custom_invoices.race_hotel_id')
            ->join('races', 'races.id', '=', 'custom_invoices.race_id')
            ->join('hotels', 'hotels.id', '=', 'races_hotels.hotel_id')
            ->select('custom_invoices.id', 'custom_invoices.client_id', 'custom_invoices.race_hotel_id', 'races_hotels.race_id', 'races_hotels.hotel_id')
            ->whereNull('custom_invoices.deleted_at')
            ->whereNull('races_hotels.deleted_at')
            ->whereNull('races.deleted_at')
            ->whereNull('hotels.deleted_at')
            ->get();

        foreach ($custom_invoices as $custom_invoice) {
            $this->urls_to_cache[] = "/races/{$custom_invoice->race_id}/hotels/{$custom_invoice->hotel_id}/clients/{$custom_invoice->client_id}/extras/{$custom_invoice->id}";
            //$this->urls_to_cache[] = "/api/uploads/extras/{$custom_invoice->id}";
        }
    }

}
