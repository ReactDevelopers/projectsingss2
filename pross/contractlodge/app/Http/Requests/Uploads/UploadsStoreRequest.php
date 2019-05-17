<?php

namespace App\Http\Requests\Uploads;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UploadsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'upload_file' => 'required|mimes:pdf,doc,docx,png,jpg,jpeg,gif,xls,xlsx|max:2024'
        ];

        return $rules;
    }
     /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'File is required',
            'upload_file.max' => 'The file may not be larger than 2 MB in size',
            'upload_file.mimes' => 'The file type you uploaded is not supported',
        ];
    }
}
