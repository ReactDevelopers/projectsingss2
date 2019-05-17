<?php namespace App\Mummy\Api\V1\Requests\Favourite;

use App\Mummy\Api\V1\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class FavouriteRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vendor_id' => 'required',
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

