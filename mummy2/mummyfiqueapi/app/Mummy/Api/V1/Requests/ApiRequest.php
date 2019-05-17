<?php

namespace App\Mummy\Api\V1\Requests;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use \Illuminate\Contracts\Validation\Validator;

abstract class ApiRequest extends LaravelFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Get the failed validation response for the request.
     *
     * @param array $errors
     * @return JsonResponse
     */
    public function response(array $errors)
    {
        $transformed = [];

        foreach ($errors as $field => $message) {
            $transformed = [
                'field' => $field,
                'message' => $message[0]
            ];
        }
         return response()->json([
            'error' => $transformed,
             'code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ], 200);

//        return response()->json([
//            'errors' => $transformed
//        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->response(
            $this->formatErrors($validator)
        ));
    }
    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->getMessages();
    }
}