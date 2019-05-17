<?php namespace App\Mummy\Api\V1\Requests\Search;

use App\Mummy\Api\V1\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class SearchByNameRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}

