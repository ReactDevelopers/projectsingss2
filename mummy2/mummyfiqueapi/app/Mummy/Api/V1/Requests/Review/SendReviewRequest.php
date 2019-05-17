<?php namespace App\Mummy\Api\V1\Requests\Review;

use App\Mummy\Api\V1\Requests\ApiRequest;

class SendReviewRequest extends ApiRequest
{
    public function rules()
    {

        return [
            'rating' => 'required|numeric|min:0|max:5',
            'content' => 'required',
            'title' => 'required',
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
