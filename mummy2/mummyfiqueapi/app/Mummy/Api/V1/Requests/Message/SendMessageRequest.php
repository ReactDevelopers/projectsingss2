<?php namespace App\Mummy\Api\V1\Requests\Message;

use App\Mummy\Api\V1\Requests\ApiRequest;

class SendMessageRequest extends ApiRequest
{
    public function rules()
    {

        return [
            'message' => 'required',
            'subject' => 'required',
            'receiver_id' => 'required|numeric',
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
