<?php namespace App\Mummy\Api\V1\Requests\Review;

use App\Mummy\Api\V1\Requests\ApiRequest;

class ReviewRequest extends ApiRequest
{
    public function rules()
    {

        return [
            'review_id' => 'required|numeric',
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
