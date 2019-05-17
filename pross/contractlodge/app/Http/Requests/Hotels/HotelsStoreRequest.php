<?php

namespace App\Http\Requests\Hotels;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class HotelsStoreRequest extends FormRequest
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
            'name'             => 'required|unique:hotels',
            'address'          => 'sometimes|nullable|max:350',
            'city'             => 'sometimes|nullable|regex:/(^[A-Za-z ]+$)+/',
            'region'           => 'sometimes|nullable|alpha',
            'country_id'       => 'required',
            'postal_code'      => 'sometimes|nullable|integer|digits:5|numeric',
            'website'          => 'sometimes|nullable|url',
            'email'            => 'sometimes|nullable|email',
            'code'             => 'sometimes|nullable|max:50',
            'contact_email'    => 'sometimes|nullable|email',
            'contact_name'     => 'required_with:contact_email',
            'contact_role'     => 'max:50|required_with:contact_email',
            'contacts.*.email' => 'sometimes|nullable|email',
            'contacts.*.name'  => 'required_with:contacts.*.email',
            'contacts.*.name'  => 'sometimes|regex:/(^[A-Za-z ]+$)+/',
            'contacts.*.role'  => 'max:50|required_with:contacts.*.email',
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
            'name.required'                 => 'Unique hotel name is required',
            'city.regex'                    => 'The city may only contain letters.',
            'country_id.required'           => 'Country is required',
            'code.max'                      => 'Enter code up to 50 characters',
            'contacts.*.email.email'        => 'The contact email must be a valid email address',
            'contacts.*.name.required_with' => 'The contact name is required when contact email is present',
            'contacts.*.name.regex'         => 'The contact name only contain characters',
            'contacts.*.role.required_with' => 'The contact role is required when contact email is present'
        ];
    }
}
