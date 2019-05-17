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
use App\Http\Requests\RacesHotelsInvoices\RacesHotelsInvoicesUpdateRequest;

class RacesHotelsInvoicesUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * App\Http\Requests\RacesHotelsInvoices\RacesHotelsInvoicesUpdateRequest
     * @var Request
     */
    public $request;

    /**
     * String of either "confirmations" or "extras"
     * @var string
     */
    public $invoice_type;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(RacesHotelsInvoicesUpdateRequest $request)
    {
        $this->request = $request;
        $this->invoice_type = $this->request->invoice_type;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        if ($this->invoice_type == 'confirmations') {

            $confirmation = $this->updateConfirmation();
            $this->createOrUpdateConfirmationItems($confirmation);
            $this->createOrUpdatePayment($confirmation);

            return $confirmation->refresh();

        } else {

            $invoice = $this->updateInvoice();
            $this->createOrUpdateInvoiceItems($invoice);
            $this->createOrUpdatePaymentInvoice($invoice);

            return $invoice->refresh();
        }

    }

    /**
     * Method to update confirmation
     * @return \App\Confirmation
     */
    private function updateConfirmation()
    {
        $confirmation = Confirmation::find($this->request->confirmation_id);
        $confirmation->client_id     = $this->request->client_id;
        $confirmation->race_hotel_id = $this->request->race_hotel_id;
        $confirmation->currency_id   = $this->request->currency_id;
        $confirmation->created_by    = $this->request->created_by;
        $confirmation->notes         = $this->request->notes;
        $confirmation->expires_on    = format_input_date_to_system($this->request->expires_on);
        $confirmation->save();
        $confirmation->load('confirmation_items');

        return $confirmation;
    }

    /**
     * Method to create or update confirmation items for the given confirmation
     * @param \App\Confirmation $confirmation
     * @return void
     */
    private function createOrUpdateConfirmationItems(Confirmation $confirmation)
    {
        if (! isset($this->request->confirmation_items) || empty($this->request->confirmation_items)) {
            return;
        }

        $counter = 1;
        $confirmation_items_data = [];

        foreach ($this->request->confirmation_items as $item) {
            $confirmation_items_data[$counter]['id']                        = isset($item['id']) ? $item['id'] : null;
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
            if (isset($room['id']) && $room['id'] !== null && is_numeric($room['id'])) {

                $confirmation = ConfirmationItem::find($room['id']);

                if ($confirmation) {

                    $confirmation->update(array_except($room, [
                            'id',
                            'created_at',
                            'deleted_at',
                            'updated_at',
                            'confirmation_id',
                        ]));

                    unset($room['id']);

                    return true;
                }
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
                return $submitted_row['id'] == $room->id;
            });
            return empty($first);
        });

        ConfirmationItem::destroy($deletable->pluck('id'));
    }

    /**
     * Method to create or update payments for confirmation
     * @param \App\Confirmation $confirmation
     * @return void
     */
    private function createOrUpdatePayment(Confirmation $confirmation)
    {
        if (! isset($this->request->payments) || empty($this->request->payments)) {
            return;
        }

        $counter = 1;
        $payment_items_data = [];

        foreach ($this->request->payments as $payment_data) {
            $payment_items_data[$counter]['id']             = isset($payment_data['id']) ? $payment_data['id'] : null;
            $payment_items_data[$counter]['payment_name']   = $payment_data['payment_name'];
            $payment_items_data[$counter]['currency_id']    = $this->request->currency_id;
            $payment_items_data[$counter]['amount_due']     = (isset($payment_data['amount_due']) && ! empty($payment_data['amount_due'])) ? $payment_data['amount_due'] : 0;
            $payment_items_data[$counter]['due_on']         = (isset($payment_data['due_on']) && ! empty($payment_data['due_on'])) ? format_input_date_to_system($payment_data['due_on']) : null;
            $payment_items_data[$counter]['amount_paid']    = (isset($payment_data['amount_paid']) && ! empty($payment_data['amount_paid'])) ? $payment_data['amount_paid'] : 0;
            $payment_items_data[$counter]['paid_on']        = (isset($payment_data['paid_on']) && ! empty($payment_data['paid_on'])) ? format_input_date_to_system($payment_data['paid_on']) : null;
            $payment_items_data[$counter]['created_by']     = auth()->user()->id;
            $payment_items_data[$counter]['to_accounts_on'] = (isset($payment_data['to_accounts_on']) && ! empty($payment_data['to_accounts_on'])) ? format_input_date_to_system($payment_data['to_accounts_on']) : null;
            $payment_items_data[$counter]['invoice_number'] = (isset($payment_data['invoice_number']) && ! empty($payment_data['invoice_number'])) ? $payment_data['invoice_number'] : null;
            $payment_items_data[$counter]['invoice_date']   = (isset($payment_data['invoice_date']) && ! empty($payment_data['invoice_date'])) ? format_input_date_to_system($payment_data['invoice_date']) : null;

            $counter++;
        }

        $this->syncPayment(
            $confirmation->payments,
            collect($payment_items_data),
            [],
            $confirmation
        );
    }


    /**
     * Delete, create or update the records as necessary.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     * @param  array      $create_attributes Additional attributes to add when creating a record
     * @param \App\Confirmation $confirmation
     * @return void
     */
    private function syncPayment(Collection $db_rows, Collection $submitted_rows, array $create_attributes = [], Confirmation $confirmation)
    {
        $this->deleteExistingPayment($db_rows, $submitted_rows);
        $this->createOrUpdateConfirmationPayment($submitted_rows, $create_attributes, $confirmation);
    }


    /**
     * Deletes objects from the db which are not in the submitted data.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     * @return void
     */
    private function deleteExistingPayment(Collection $db_rows, Collection $submitted_rows)
    {

        $deletable = $db_rows->filter(function ($room) use ($submitted_rows) {
            $first = array_first($submitted_rows, function ($submitted_row) use ($room) {
                return $submitted_row['id'] == $room->id;
            });
            return empty($first);
        });

        $payment = Payment::whereIn('id', $deletable->pluck('id'));
        $payment->update(['deleted_by' => auth()->user()->id]);
        Payment::destroy($deletable->pluck('id'));
    }


    /**
     * Creates new objects in the db from the submitted data
     * @param  Collection $submitted_rows
     * @param  array      $attributes
     * @param \App\Confirmation $confirmation
     * @return void
     */
    private function createOrUpdateConfirmationPayment(Collection $submitted_rows, array $attributes = [], Confirmation $confirmation)
    {
        $submitted_rows->each(function ($room) use ($attributes, $confirmation) {

            // Update if necessary
            if (isset($room['id']) && $room['id'] !== null && is_numeric($room['id'])) {

                $payment = Payment::find($room['id']);

                if ($payment) {
                    $payment->update(array_except($room, [
                            'id',
                            'created_at',
                            'deleted_at',
                            'updated_at',
                        ]));
                    unset($room['id']);
                    return true;
                }
            }

            // Otherwise create
            if (! empty($attributes)) {
                $room = array_merge($room, $attributes);
            }

            $payment = Payment::create($room);
            $confirmation->payments()->syncWithoutDetaching([$payment->id]);
            $payment->refresh();
        });
    }

    /**
     * Method to update invoice
     * @return \App\CustomInvoice
     */
    private function updateInvoice()
    {

        $invoice = CustomInvoice::find($this->request->invoiceId);

        $invoice->client_id     = $this->request->client_id;
        $invoice->race_hotel_id = $this->request->race_hotel_id;
        $invoice->race_id       = $this->request->race_id;
        $invoice->currency_id   = $this->request->currency_id;
        $invoice->due_on        = $this->request->due_on;
        $invoice->notes         = $this->request->notes;
        $invoice->created_by    = auth()->user()->id;

        $invoice->save();

        $invoice->load('invoice_items');

        return $invoice;

    }

    /**
     * Method to create or update Invoice items for the given invoice
     * @param \App\CustomInvoice $invoice
     * @return void
     */
    private function createOrUpdateInvoiceItems(CustomInvoice $invoice)
    {
        if (! isset($this->request->invoice_items) || empty($this->request->invoice_items)) {
            return;
        }

        $counter = 1;
        $invoice_items_data = [];

        foreach ($this->request->invoice_items as $item) {

            $invoice_items_data[$counter]['id']                = isset($item['id']) ? $item['id'] : null;
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
            ['custom_invoice_id' => $invoice->id]
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
     * Creates new objects or update object in the db from the submitted data
     * @param  Collection $submitted_rows
     * @param  array      $attributes
     */
    private function createOrUpdateCustom(Collection $submitted_rows, array $attributes = [])
    {
        $submitted_rows->each(function ($room) use ($attributes) {

            if (isset($room['id']) && $room['id'] !== null && is_numeric($room['id'])) {

                $custom_invoice_item = CustomInvoiceItem::find($room['id']);

                if ($custom_invoice_item) {

                    $custom_invoice_item->update(array_except($room, [
                            'id',
                            'created_at',
                            'deleted_at',
                            'updated_at',
                            'custom_invoice_id',
                        ]));

                    unset($room['id']);

                    return true;
                }
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
                return $submitted_row['id'] == $room->id;
            });
            return empty($first);
        });

        CustomInvoiceItem::destroy($deletable->pluck('id'));
    }

    /**
     * Method to create or update payments for custom invoice
     * @param \App\CustomInvoice $invoice
     * @return void
     */
    private function createOrUpdatePaymentInvoice(CustomInvoice $invoice)
    {
        if (! isset($this->request->payments) || empty($this->request->payments)) {
            return;
        }

        $counter = 1;
        $payment_items_data = [];

        foreach ($this->request->payments as $payment_data) {
            $payment_items_data[$counter]['id']             = isset($payment_data['id']) ? $payment_data['id'] : null;
            $payment_items_data[$counter]['payment_name']   = $payment_data['payment_name'];
            $payment_items_data[$counter]['currency_id']    = $this->request->currency_id;
            $payment_items_data[$counter]['amount_due']     = (isset($payment_data['amount_due']) && ! empty($payment_data['amount_due'])) ? $payment_data['amount_due'] : 0;
            $payment_items_data[$counter]['due_on']         = (isset($payment_data['due_on']) && ! empty($payment_data['due_on'])) ? format_input_date_to_system($payment_data['due_on']) : null;
            $payment_items_data[$counter]['amount_paid']    = (isset($payment_data['amount_paid']) && ! empty($payment_data['amount_paid'])) ? $payment_data['amount_paid'] : 0;
            $payment_items_data[$counter]['paid_on']        = (isset($payment_data['paid_on']) && ! empty($payment_data['paid_on'])) ? format_input_date_to_system($payment_data['paid_on']) : null;
            $payment_items_data[$counter]['created_by']     = auth()->user()->id;
            $payment_items_data[$counter]['to_accounts_on'] = (isset($payment_data['to_accounts_on']) && ! empty($payment_data['to_accounts_on'])) ? format_input_date_to_system($payment_data['to_accounts_on']) : null;
            $payment_items_data[$counter]['invoice_number'] = (isset($payment_data['invoice_number']) && ! empty($payment_data['invoice_number'])) ? $payment_data['invoice_number'] : null;
            $payment_items_data[$counter]['invoice_date']   = (isset($payment_data['invoice_date']) && ! empty($payment_data['invoice_date'])) ? format_input_date_to_system($payment_data['invoice_date']) : null;

            $counter++;
        }

        $this->syncPaymentInvoice(
            $invoice->payments,
            collect($payment_items_data),
            [],
            $invoice
        );
    }

    /**
     * Delete, create or update the records as necessary.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     * @param  array      $create_attributes Additional attributes to add when creating a record
     * @param \App\CustomInvoice $invoice
     * @return void
     */
    private function syncPaymentInvoice(Collection $db_rows, Collection $submitted_rows, array $create_attributes = [], CustomInvoice $invoice)
    {
        $this->deleteExistingInvoicePayment($db_rows, $submitted_rows);
        $this->createOrUpdateInvoicePayment($submitted_rows, $create_attributes, $invoice);
    }


    /**
     * Deletes objects from the db which are not in the submitted data.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     * @return void
     */
    private function deleteExistingInvoicePayment(Collection $db_rows, Collection $submitted_rows)
    {

        $deletable = $db_rows->filter(function ($room) use ($submitted_rows) {
            $first = array_first($submitted_rows, function ($submitted_row) use ($room) {
                return $submitted_row['id'] == $room->id;
            });
            return empty($first);
        });

        $payment = Payment::whereIn('id', $deletable->pluck('id'));
        $payment->update(['deleted_by' => auth()->user()->id]);
        Payment::destroy($deletable->pluck('id'));
    }


    /**
     * Creates new objects in the db from the submitted data
     * @param  Collection $submitted_rows
     * @param  array      $attributes
     * @param  \App\CustomInvoice $invoice
     * @return void
     */
    private function createOrUpdateInvoicePayment(Collection $submitted_rows, array $attributes = [], CustomInvoice $invoice)
    {
        $submitted_rows->each(function ($room) use ($attributes, $invoice) {

            // Update if necessary
            if (isset($room['id']) && $room['id'] !== null && is_numeric($room['id'])) {

                $payment = Payment::find($room['id']);

                if ($payment) {
                    $payment->update(array_except($room, [
                            'id',
                            'created_at',
                            'deleted_at',
                            'updated_at',
                        ]));
                    unset($room['id']);
                    return true;
                }
            }

            // Otherwise create
            if (! empty($attributes)) {
                $room = array_merge($room, $attributes);
            }

            $payment = Payment::create($room);
            $invoice->payments()->syncWithoutDetaching([$payment->id]);
            $payment->refresh();
        });
    }


}
