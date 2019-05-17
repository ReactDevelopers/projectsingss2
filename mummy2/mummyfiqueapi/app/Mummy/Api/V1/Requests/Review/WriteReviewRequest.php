<?php namespace App\Mummy\Api\V1\Requests\Review;

use App\Mummy\Api\V1\Requests\ApiRequest;

class WriteReviewRequest extends ApiRequest
{
    public function rules()
    {

        return [
            'vendor_id' => 'required',
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
