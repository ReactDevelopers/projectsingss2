<?php

namespace App\Http\Requests\Clients;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class ClientsStoreRequest extends FormRequest
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
            'name'             => 'required|unique:clients',
            'country_id'       => 'required',
            'email'            => 'sometimes|nullable|email',
            'code'             => 'sometimes|nullable|max:50',
            'contacts.*.email' => 'sometimes|nullable|email',
            'contacts.*.name'  => 'required_with:contacts.*.email',
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
            'name.required'                 => 'Unique client name is required',
            'country_id.required'           => 'Country is required',
            'code.max'                      => 'Enter code up to 50 characters',
            'contacts.*.email.email'        => 'The contact email must be a valid email address',
            'contacts.*.name.required_with' => 'The contact name is required when contact email is present',
            'contacts.*.role.required_with' => 'The contact role is required when contact email is present',
        ];
    }
}
