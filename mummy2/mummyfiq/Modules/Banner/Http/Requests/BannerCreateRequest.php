<?php namespace Modules\Banner\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class BannerCreateRequest extends BaseFormRequest {
	
	public function rules() {
		//dd(\Request::all());

		return [ 
			'title' => 'required',
			// 'country' => 'required',
			// 'category' => 'required',
			// 'vendor' => 'required',
			// 'keywords' => 'required|array',
			//'keywords' => 'required',
			// 'link' => 'required',
			'link' => 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
		];


	}
	public function authorize() {
		return true;
	}
	public function messages() {
		return [
			'title.required' => 'The title field is required.',
			// 'country.required' => 'The country field is required.',
			// 'category.required' => 'The category field is required.',
			// 'vendor.required' => 'The vendor field is required.',
			// 'keywords.required' => 'The keywords field is required.',
			// 'link.required' => 'The external link field is required.',
		];
	}


	public function validator($factory) {
		
		$rules = $this->container->call([$this, 'rules']);
        $attributes = $this->attributes();
        $messages = [];

        $translationsAttributesKey = rtrim($this->translationsAttributesKey, '.') . '.';

        foreach ($this->requiredLocales() as $localeKey => $locale) {
            $this->localeKey = $localeKey;
            foreach ($this->container->call([$this, 'translationRules']) as $attribute => $rule) {
                $key = $localeKey . '.' . $attribute;
                $rules[$key] = $rule;
                $attributes[$key] = trans($translationsAttributesKey . $attribute);
            }

            foreach ($this->container->call([$this, 'translationMessages']) as $attributeAndRule => $message) {
                $messages[$localeKey . '.' . $attributeAndRule] = $message;
            }
        }

        $val = $rules;
        // $msg = [];
        // $val['title'] = 'required';
        // $msg['title.required'] = 'The title field is required.';
        if($this->type==0)
        {
            $val['link'] = 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
        }

		$rules = $val;
		// $this->container->call([$this, 'rules']);
        // $attributes = $this->attributes();
        // $messages = $msg;

        // dd($rules,$messages,$attributes,$this->messages());

        $validator = $factory->make(
            $this->all(), $rules, array_merge($this->messages(), $messages), $attributes
        );

  //       if(($this->type==1)){

  //       	foreach ((array) $this->keywords as $key) {
  //       		if(empty($key)){
  //       			$validator->after(function ($validator) {
			    
		// 	    		$validator->errors()->add('keywords', 'The keywords field is required.');
			    
		// 			});
  //       		}
  //       	}
		// }

		if(($this->type==1) && empty($this->country)){
			$validator->after(function ($validator) {
			    
			    $validator->errors()->add('country', 'The country field is required.');
			    
			});
		}
		if(($this->type==1) && empty($this->category)){
			$validator->after(function ($validator) {
			    
			    $validator->errors()->add('category', 'The category field is required.');
			    
			});
		}
		if(($this->type==1) && empty($this->vendor)){
			$validator->after(function ($validator) {
			    
			    $validator->errors()->add('vendor', 'The vendor field is required.');
			    
			});
		}
		// if(($this->type==1) && empty($this->subcategory)){
		// 	$validator->after(function ($validator) {
			    
		// 	    $validator->errors()->add('subcategory', 'The sub category field is required.');
			    
		// 	});
		// }


		if(($this->type==0) && empty($this->link)){
			$validator->after(function ($validator) {
			    
			    $validator->errors()->add('link', 'The external link field is required.');
			    
			});
		}

		// dd($this->all());
		return $validator;
		
	}
}
