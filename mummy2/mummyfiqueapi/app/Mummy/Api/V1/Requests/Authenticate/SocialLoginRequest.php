<?php namespace App\Mummy\Api\V1\Requests\Authenticate;

use App\Mummy\Api\V1\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
