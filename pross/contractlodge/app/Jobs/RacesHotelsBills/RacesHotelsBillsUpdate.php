<?php

namespace App\Jobs\RacesHotelsBills;

use Auth;
use App\Bill;
use App\Payment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Requests\RacesHotelsBills\RacesHotelsBillsStoreRequest;

class RacesHotelsBillsUpdate implements ShouldQueue
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
    public function __construct(RacesHotelsBillsStoreRequest $request)
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
        $bills = Bill::firstOrCreate([
            'race_hotel_id' => $this->request->race_hotel_id,
            'created_by' => $this->request->created_by,
        ]);

        $bills->update([
            'contract_signed_on' => format_input_date_to_system($this->request->contract_signed_on),
            'currency_id' => $this->request->currency_id,
            'exchange_currency_id' => $this->request->exchange_currency_id
        ]);

        $this->createOrUpdatePaymentBill($bills);

        return $bills;


    }


    /**
     * Method to create or update payments for bills
     * @param \App\Bill $bill
     * @return void
     */
    private function createOrUpdatePaymentBill(Bill $bill)
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

        $this->syncPaymentBill(
            $bill->payments,
            collect($payment_items_data),
            [],
            $bill
        );
    }

    /**
     * Delete, create or update the records as necessary.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     * @param  array      $create_attributes Additional attributes to add when creating a record
     * @param \App\Bill $bill
     * @return void
     */
    private function syncPaymentBill(Collection $db_rows, Collection $submitted_rows, array $create_attributes = [], Bill $bill)
    {
        $this->deleteExistingBillPayment($db_rows, $submitted_rows);
        $this->createOrUpdateBillPayment($submitted_rows, $create_attributes, $bill);
    }


    /**
     * Deletes objects from the db which are not in the submitted data.
     * @param  Collection $db_rows
     * @param  Collection $submitted_rows
     * @return void
     */
    private function deleteExistingBillPayment(Collection $db_rows, Collection $submitted_rows)
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
     * @param  \App\Bill $bill
     * @return void
     */
    private function createOrUpdateBillPayment(Collection $submitted_rows, array $attributes = [], Bill $bill)
    {
        $submitted_rows->each(function ($room) use ($attributes, $bill) {

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
            $bill->payments()->syncWithoutDetaching([$payment->id]);
            $payment->refresh();
        });
    }
}
