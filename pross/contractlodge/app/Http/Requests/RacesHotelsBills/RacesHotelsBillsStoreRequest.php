<?php

namespace App\Http\Requests\RacesHotelsBills;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class RacesHotelsBillsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $rules = [
            'contract_signed_on'    => 'required|date'
        ];

        foreach ($this->request->get('payments') as $key => $val) {
            $rules['payments.'.$key.'.payment_name']   = 'required';
            $rules['payments.'.$key.'.amount_due']     = 'required|currency';
            $rules['payments.'.$key.'.due_on']         = 'required|date';
            $rules['payments.'.$key.'.amount_paid']    = 'sometimes|nullable|currency';
            $rules['payments.'.$key.'.paid_on']        = 'sometimes|nullable|date';
            $rules['payments.'.$key.'.to_accounts_on'] = 'sometimes|nullable|date';
            $rules['payments.'.$key.'.invoice_number'] = 'sometimes|nullable|max:25';
            $rules['payments.'.$key.'.invoice_date']   = 'sometimes|nullable|date';
        }

        return $rules;
    }
     /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];

        $messages = [
            'contract_signed_on.required'    => 'Contract signed date is required',
        ];

        foreach ($this->request->get('payments') as $key => $val) {
            $messages['payments.'.$key.'.payment_name.required'] = 'Description is required';
            $messages['payments.'.$key.'.amount_due.required']   = 'Amount is required';
            $messages['payments.'.$key.'.amount_due.currency']   = 'Enter valid amount';
            $messages['payments.'.$key.'.due_on.required']       = 'Due on required';
            $messages['payments.'.$key.'.due_on.date']           = 'Enter valid date';
            $messages['payments.'.$key.'.amount_paid.currency']  = 'Enter valid amount';
            $messages['payments.'.$key.'.paid_on.date']          = 'Enter valid date';
            $messages['payments.'.$key.'.to_accounts_on.date']   = 'Enter valid date';
            $messages['payments.'.$key.'.invoice_number.max']    = 'Enter up to 25 characters';
            $messages['payments.'.$key.'.invoice_date.date']     = 'Enter valid date';
        }

        return $messages;
    }
}
