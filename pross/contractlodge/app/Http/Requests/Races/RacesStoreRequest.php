<?php

namespace App\Http\Requests\Races;

use App\Race;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class RacesStoreRequest extends FormRequest
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
        if ($this->route()->methods[0] == 'PUT') {

            $race_id = $this->route()->parameters['race']->toArray()['id'];

            $rules = [
                'year' => 'required|digits:4|integer|min:'.\Carbon\Carbon::now()->year.'|max:3000',
                'name' => 'required',
                'start_on' => 'sometimes|nullable|date_format:d/m/Y|after_or_equal:'.date('1/1/Y'),
                'end_on' => 'sometimes|nullable|date_format:d/m/Y',
                'race_code' => 'required|min:3|max:50|regex:/^[a-zA-Z]+(-[0-9]*[0-9])+$/'
            ];

            if (Race::where('race_code', '=', Request::input('race_code'))->where('id', '!=', $race_id)->first()) {
                $rules['race_code'] = 'required|min:3|max:50|unique:races,race_code';
            }

            return $rules;
        }

        $rules = [
            'year'      => 'required|digits:4|integer|min:'.\Carbon\Carbon::now()->year.'|max:3000',
            'name'      => 'required|regex:/^[a-zA-Z].+$/',
            'start_on'  => 'required|nullable|date_format:d/m/Y|future_date',
            'end_on'    => 'required|nullable|date_format:d/m/Y|future_date|after_date:start_on',
            'race_code' => 'required|min:3|max:50|unique:races,race_code|regex:/^[a-zA-Z]+(-[0-9]*[0-9])+$/',
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
          'year.required' => 'Year is required',
          'year.min' => 'Year should not be less than current year',
          'name.required' => 'Race name is required',
          'name.regex' => 'Race name should be string',
          'start_on.date' => 'Start date must be a date',
          'start_on.after_or_equal' => 'Race start on date must be >= current year',
          'end_on.date' => 'End date must be a date',
          'race_code.required' => 'Race code is required',
          'future_date' => 'Must be a date in the future',
          'after_date' => 'End date should be prior to start date',
          'start_on.required' => 'Race start date is required',
          'end_on.required' => 'Race end date is required',
          'race_code.regex' => 'Race code should be like "Race-1" format'
        ];
    }
}
