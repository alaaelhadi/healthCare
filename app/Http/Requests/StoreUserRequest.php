<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:13|unique:users',
            'job' => 'required|string',
            'job_other' => 'nullable|string',
            'country' => 'required|string',
            'city' => 'nullable|string',
            'job_role' => 'nullable|string',
            'job_role_other' => 'nullable|string',
            'company' => 'nullable|string',
            'organization_logo' => 'nullable|string'
        ];
    }

    public function messages(){
        return [    
                'title.required' => 'Car title is required ',
                'title.between ' => 'Car title must be between 3 and 255 characters.',
                'title.string ' => 'Car title must not be numbers',
                'title.unique'=>'car tile is already exist.',
                'price.required' => 'The price is required.',
                'price.numeric' => 'The price must be number.',
                'passengers.required' => 'passengers numbers is required.',
                'passengers.numeric' => 'numbers only',
                'doors.required' => 'doors numbers is required.',
                'doors.numeric' => 'numbers only.',
                'luggage.required' => 'luggage numbers required.',
                'luggage.numeric' => 'numbers only.',
                'description.required' => 'car description is required ',
                'description.string ' => 'car description must not be numbers',
                'category_id.required' => 'select  Category',
                'category_id.integer' => 'Category id must be integer',
                'category_id.exists' => 'Category id must exists in the table',
                'image.required' => 'select image',
                'image.mimes' => ' image extension must be png ,jpg or jpeg',
                'image.max' => ' image max size is 2GB',
        ];
    }
}