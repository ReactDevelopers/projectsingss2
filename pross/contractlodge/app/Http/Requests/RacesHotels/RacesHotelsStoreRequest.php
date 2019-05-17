<?php
namespace App\Http\Requests\RacesHotels;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
class RacesHotelsStoreRequest extends FormRequest
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
        $rules = [
            'inventory_currency_id' => 'required',
            'inventory_min_check_in' => 'required|date',
            'inventory_min_check_out' => 'required|date',
            'inventory_rows.*.room_name' => 'required',
            'inventory_rows.*.min_night_hotel_rate' => 'sometimes|nullable|integer',
            'inventory_rows.*.min_night_client_rate' => 'sometimes|nullable|integer',
            'inventory_rows.*.pre_post_night_hotel_rate' => 'sometimes|nullable|integer',
            'inventory_rows.*.pre_post_night_client_rate' => 'sometimes|nullable|integer',
        ];
        return $rules;
    }
     /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'inventory_currency_id.required' => 'Currency is required',
            'inventory_rows.*.room_name.required' => 'Room type/name is required',
            'inventory_rows.*.min_night_hotel_rate.integer' => 'The min night hotel must be an number',
            'inventory_rows.*.min_night_client_rate.integer' => 'The min night client must be an number',
            'inventory_rows.*.pre_post_night_hotel_rate.integer' => 'The pre post night hotel must be an number',
            'inventory_rows.*.pre_post_night_client_rate.integer' => 'The pre post night client must be an number',
        ];
    }
    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (! empty($this->request->get('inventory_rows'))) {
                foreach ($this->request->get('inventory_rows') as $key => $val) {
                    if (isset($val['id'])) {
                        $min_stays_contracted  = $val['min_stays_contracted'];
                        $pre_post_nights_contracted  = $val['pre_post_nights_contracted'];
                        // Check if selected quantity/booked of min night contracted should not less than the sum of quantity of signed confirmations for particular room
                        $total_rooms_signed_confirmations_min_nights = getNumRoomsInSignedConfirmations($val['id'], $val['race_hotel_id'], 'min_nights');
                        if ($min_stays_contracted < $total_rooms_signed_confirmations_min_nights && $total_rooms_signed_confirmations_min_nights != 0) {
                            if ($min_stays_contracted == '' || $min_stays_contracted == null || $min_stays_contracted == 0) {
                                $validator->errors()->add('inventory_rows.'.$key.'.min_stays_contracted', "Please enter quantity");
                            } else {
                                $validator->errors()->add('inventory_rows.'.$key.'.min_stays_contracted', "Rooms less than sum of confirmations");
                            }
                        }
                        // Check if selected quantity/booked of pre post nights contracted should not less than the sum of quantity of signed confirmations for particular room
                        $total_rooms_signed_confirmations_pp_nights = getNumRoomsInSignedConfirmations($val['id'], $val['race_hotel_id'], 'pre_post_nights');
                        if ($pre_post_nights_contracted < $total_rooms_signed_confirmations_pp_nights && $total_rooms_signed_confirmations_pp_nights != 0) {
                            if ($pre_post_nights_contracted == '' || $pre_post_nights_contracted == null || $pre_post_nights_contracted == 0) {
                                $validator->errors()->add('inventory_rows.'.$key.'.pre_post_nights_contracted', "Please enter quantity");
                            } else {
                                $validator->errors()->add('inventory_rows.'.$key.'.pre_post_nights_contracted', "Rooms less than sum of confirmations");
                            }
                        }
                    }
                }
            }
            // validation for dusk
            if (! empty($this->request->get('inventory_min_check_in'))) {
                $min_check_in = strtotime($this->request->get('inventory_min_check_in'));
                $today = strtotime(date("Y-m-d"));
                if ($min_check_in < $today){
                    $validator->errors()->add('inventory_min_check_in', "Minimum check in date must be >= today date");
                }
            }
            if (! empty($this->request->get('inventory_min_check_in')) && ! empty($this->request->get('inventory_min_check_out'))) {
                $min_check_in = strtotime($this->request->get('inventory_min_check_in'));
                $min_check_out = strtotime($this->request->get('inventory_min_check_out'));
                if ($min_check_in > $min_check_out){
                    $validator->errors()->add('inventory_min_check_in', "Minimum Check-in date must be smaller than Minimum Check-out date");
                }
            }
        });
    }
}
