<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'required|string|max:255',
            'thumbnail' => 'file|mimes:jpeg,png,jpg',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
            'status' => 'string|max:255',
            'specialist_required' => 'sometimes',
            'preparation_instruction' => 'sometimes|string',
            'doctor_ids' => 'sometimes|array',
        ];
    }
}
