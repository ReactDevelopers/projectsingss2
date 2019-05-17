<?php namespace App\Mummy\Api\V1\Requests\Review;

use App\Mummy\Api\V1\Requests\ApiRequest;

class ReportReviewRequest extends ApiRequest
{
    public function rules()
    {

        return [
            'review_id' => 'required|numeric',
            'content' => 'required',
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
