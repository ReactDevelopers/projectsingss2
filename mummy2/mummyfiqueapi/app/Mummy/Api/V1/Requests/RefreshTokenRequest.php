<?php namespace App\Mummy\Api\V1\Requests;

use App\Mummy\Api\V1\Requests\ApiRequest;

class RefreshTokenRequest extends ApiRequest
{
    public function rules()
    {

        return [
            'grant_type' => 'required',
            'request_token' => 'token',
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
