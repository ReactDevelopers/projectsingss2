<?php namespace App\Mummy\Api\V1\Requests;

use App\Mummy\Api\V1\Requests\ApiRequest;

class AuthClientRequest extends ApiRequest
{
    public function rules()
    {

        return [
            'username' => 'required|email',
            'password' => 'required',
            'grant_type' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}
