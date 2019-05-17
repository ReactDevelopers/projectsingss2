<?php

namespace App\Jobs\RacesHotelsInvoices;

use Auth;
use App\Race;
use App\Hotel;
use App\Payment;
use App\RaceHotel;
use App\Confirmation;
use App\CustomInvoice;
use App\ConfirmationItem;
use App\CustomInvoiceitem;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\RacesHotelsInvoices\RacesHotelsInvoicesStoreRequest;

class RacesHotelsInvoicesCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * App\Http\Requests\RacesHotelsInvoices\RacesHotelsInvoicesStoreRequest
     * @var Request
     */
    public $request;
    public $invoice_type;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(RacesHotelsInvoicesStoreRequest $request, $invoice_type = 'extras')
    {
        $this->request = $request;
        $this->invoice_type = $invoice_type;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        if ($this->invoice_type == 'confirmations') {

            $confirmation = $this->createConfirmation();
            $this->createConfirmationItems($confirmation);
            $this->createPayment($confirmation);

            $confirmation->refresh();

            return $confirmation;

        } else {

            $invoice = $this->createInvoice();
            $this->createInvoiceItems($invoice);
            $this->createPaymentInvoice($invoice);

            $invoice->refresh();

            return $invoice;
        }

    }

    /**
     * Method to create a new confirmation
     * @return \App\Confirmation
     */
    private function createConfirmation()
    {
        $confirmation = Confirmation::create([
            'client_id'     => $this->request->client_id,
            'race_hotel_id' => $this->request->race_hotel_id,
            'currency_id'   => $this->request->currency_id,
            'created_by'    => $this->request->created_by,
            'notes'         => $this->request->notes,
            'expires_on'    => format_input_date_to_system($this->request->expires_on),
        ]);

        $confirmation->load('confirmation_items');

        return $confirmation;
    }

    /**
     * Method to create confirmation items for the given confirmation
     * @param \App\Confirmation $confirmation
     * @return void
     */
    private function createConfirmationItems(Confirmation $confirmation)
    {
        if (! isset($this->request->confirmation_items) || empty($this->request->confirmation_items)) {
            return;
        }

        $counter = 1;
        $confirmation_items_data = [];

        foreach ($this->request->confirmation_items as $item) {
            $confirmation_items_data[$counter]['races_hotels_inventory_id'] = $item['room']['id'];
            $confirmation_items_data[$counter]['quantity']                  = $item['quantity'];
            $confirmation_items_data[$counter]['check_in']                  = format_input_date_to_system($item['check_in']);
            $confirmation_items_data[$counter]['check_out']                 = format_input_date_to_system($item['check_out']);
            $confirmation_items_data[$counter]['rate']                      = $item['rate'];
            $counter++;
        }

        $this->sync(
            $confirmation->confirmation_items,
            collect($confirmation_items_data),
            ['confirmation_id' => $confirmation->id ]
        );
    }

    /**
     * Method to create payments
     * @param \App\Confirmation $confirmation
     * @return void
     */
    private function createPayment(Confirmation $confirmation)
    {
        if (! isset($this->request->payments) || empty($this->request->payments)) {
            return;
        }

        foreach ($this->request->payments as $payment_data) {
            $payment = new Payment;
            $payment = $this->createPaymentIfNotExist($payment_data,$this->request->currency_id);
            $confirmation->payments()->syncWithoutDetaching([$payment->id]);
            $payment->refresh();
        }
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
                ConfirmationItem::find($room['id'])
                    ->update(array_except($room, [
                        'id',
                        'created_at',
                        'deleted_at',
                        'updated_at',
                        'confirmation_id',
                        'races_hotels_inventory_id',
                    ]));

                return true;
            }

            // Otherwise create
            if (! empty($attributes)) {
                $room = array_merge($room, $attributes);
            }

            ConfirmationItem::create($room);
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
                return $submitted_row['races_hotels_inventory_id'] == $room->id;
            });
            return empty($first);
        });

        ConfirmationItem::destroy($deletable->pluck('id'));
    }

    /**
     * Method to create a new payment
     * @param  Array $payment_data
     * @param  int currency_id
     * @return App\Payment
     */
    private function createPaymentIfNotExist($payment_data,$currency_id)
    {
        $payment = Payment::create([
            'payment_name'   => $payment_data['payment_name'],
            'currency_id'    => $currency_id,
            'amount_due'     => (isset($payment_data['amount_due']) && ! empty($payment_data['amount_due'])) ? $payment_data['amount_due'] : 0,
            'due_on'         => (isset($payment_data['due_on']) && ! empty($payment_data['due_on'])) ? format_input_date_to_system($payment_data['due_on']) : null,
            'amount_paid'    => (isset($payment_data['amount_paid']) && ! empty($payment_data['amount_paid'])) ? $payment_data['amount_paid'] : 0,
            'paid_on'        => (isset($payment_data['paid_on']) && ! empty($payment_data['paid_on'])) ? format_input_date_to_system($payment_data['paid_on']) : null,
            'created_by'     => auth()->user()->id,
            'to_accounts_on' => (isset($payment_data['to_accounts_on']) && ! empty($payment_data['to_accounts_on'])) ? format_input_date_to_system($payment_data['to_accounts_on']) : null,
            'invoice_number' => (isset($payment_data['invoice_number']) && ! empty($payment_data['invoice_number'])) ? $payment_data['invoice_number'] : null,
            'invoice_date'   => (isset($payment_data['invoice_date']) && ! empty($payment_data['invoice_date'])) ? format_input_date_to_system($payment_data['invoice_date']) : null,

        ]);

        return $payment;
    }

    /**
     * Method to create new invoice
     * @return \App\CustomInvoice
     */
    private function createInvoice()
    {
        $invoice = CustomInvoice::create([
            'client_id'     => $this->request->client_id,
            'race_hotel_id' => $this->request->race_hotel_id,
            'race_id'       => $this->request->race_id,
            'currency_id'   => $this->request->currency_id,
            'due_on'        => format_input_date_to_system($this->request->due_on),
            'notes'         => $this->request->notes,
            'created_by'    => auth()->user()->id,
        ]);

        $invoice->load('invoice_items');

        return $invoice;
    }

    /**
     * Method to create Invoice items for the given invoice
     * @param \App\CustomInvoice $invoices
     * @return void
     */
    private function createInvoiceItems(CustomInvoice $invoice)
    {
        if (! isset($this->request->invoice_items) || empty($this->request->invoice_items)) {
            return;
        }

        $counter = 1;
        $invoice_items_data = [];

        foreach ($this->request->invoice_items as $item) {
            $invoice_items_data[$counter]['custom_invoice_id'] = $invoice->id;
            $invoice_items_data[$counter]['date']              = format_input_date_to_system(($item['date']));
            $invoice_items_data[$counter]['description']       = $item['description'];
            $invoice_items_data[$counter]['quantity']          = $item['quantity'];
            $invoice_items_data[$counter]['rate']              = $item['rate'];
            $counter++;
        }

        $this->syncCustom(
            $invoice->invoice_items,
            collect($invoice_items_data),
            ['custom_invoice_id' => $invoice->id ]
        );
    }

    /**
     * Delete, create or update the records as necessary.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     * @param  array      $create_attributes Additional attributes to add when creating a record
     */
    private function syncCustom(Collection $db_rows, Collection $submitted_rows, array $create_attributes = [])
    {
        $this->deleteExistingCustom($db_rows, $submitted_rows);
        $this->createOrUpdateCustom($submitted_rows, $create_attributes);
    }

    /**
     * Creates new objects in the db from the submitted data
     * @param  Collection $submitted_rows
     * @param  array      $attributes
     */
    private function createOrUpdateCustom(Collection $submitted_rows, array $attributes = [])
    {
        $submitted_rows->each(function ($room) use ($attributes) {

            // Update if necessary
            if (isset($room['id'])) {
                CustomInvoiceItem::find($room['id'])
                    ->update(array_except($room, [
                        'id',
                        'created_at',
                        'deleted_at',
                        'updated_at',
                        'custom_invoice_id',
                    ]));

                return true;
            }

            // Otherwise create
            if (! empty($attributes)) {
                $room = array_merge($room, $attributes);
            }

            CustomInvoiceItem::create($room);
        });
    }

    /**
     * Deletes objects from the db which are not in the submitted data.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     */
    private function deleteExistingCustom(Collection $db_rows, Collection $submitted_rows)
    {
        $deletable = $db_rows->filter(function ($room) use ($submitted_rows) {
            $first = array_first($submitted_rows, function ($submitted_row) use ($room) {
                return $submitted_row['custom_invoice_id'] == $room->id;
            });
            return empty($first);
        });

        CustomInvoiceItem::destroy($deletable->pluck('id'));
    }

    /**
     * Method to create payments for custom invoice model
     * @param \App\CustomInvoice $invoice
     * @return void
     */
    private function createPaymentInvoice(CustomInvoice $invoice)
    {
        if (! isset($this->request->payments) || empty($this->request->payments)) {
            return;
        }

        foreach ($this->request->payments as $payment_data) {
            $payment = new Payment;
            $payment = $this->createPaymentIfNotExist($payment_data, $this->request->currency_id);
            $invoice->payments()->syncWithoutDetaching([$payment->id]);
            $payment->refresh();
        }
    }
}
